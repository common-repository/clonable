<?php

namespace Clonable\Services;

use Clonable\Helpers\Functions;
use Clonable\Models\Site;
use Clonable\Objects\CircuitBreaker;
use Clonable\Objects\CurlBuilder;
use Clonable\Objects\ClonableResponse;
use Exception;

class SubfolderService {
    private $circuit_breaker;

    private const SEMAPHORE_UPSTREAM = 62342001;
    private const SEMAPHORE_UPSTREAM_QUEUE = 62342002;

    public function __construct() {
        if (get_option('clonable_subfolder_service_enabled', 'on') !== 'on') {
            return;
        }

        $languages = $this->get_subfolder_languages();
        if (empty($languages) && !is_admin()) {
            // just even bother with trying hook registration when there are no subfolder clones.
            // should improve stability for non subfolder installations.
            return;
        }

        $this->circuit_breaker = new CircuitBreaker();
        add_filter('woocommerce_get_script_data', [$this, 'setup_subfolder_script_data'], 1, 2);
        add_action('plugins_loaded', array($this, 'subfolder_request_intercept'), 2);
    }

    /**
     * Changes the wc_ajax_url of specific root scripts, so that their JSON data can also be translated.
     * See https://woocommerce.github.io/code-reference/files/woocommerce-includes-class-wc-frontend-scripts.html.
     * @param $params array the parameters used to execute the ajax action.
     * @param $handle string which WooCommerce ajax action
     * @return array
     */
    public function setup_subfolder_script_data($params, $handle) {
        $server_data = $_SERVER; // this fixes some linting rules
        if (isset($server_data['HTTP_CLONABLE_CLONE_SUBFOLDER']) && $params !== false) {
            foreach ($params as $key => $value) {
                if ($key === 'wc_ajax_url') {
                    $params[$key] = rtrim($server_data['HTTP_CLONABLE_CLONE_SUBFOLDER'],  '/') . $value;
                }
            }
        }
        return $params;
    }

    private function get_subfolder_languages() {
        $response = get_option("clonable_site");
        if (empty($response)) {
            return array();
        }

        $site = new Site($response);
        return $site->get_subfolders();
    }

    public function subfolder_request_intercept() {
        $server_data = $_SERVER; // this fixes some linting rules

        $languages = $this->get_subfolder_languages();
        $is_clonable_page = false;
        $request_path = $server_data['REQUEST_URI'];

        if ($request_path == null || $request_path == '/' || !empty($server_data['HTTP_CLONABLE_CLONE_SUBFOLDER'])) {
            return;
        }

        // Check if this request is for a subfolder
        foreach ($languages as $language) {
            if (Functions::str_starts_with($request_path, $language)) {
                $is_clonable_page = true;
                break;
            }

            // Intercept cases where eg /en is requested. Redirect those to /en/
            $trimmed_lang = rtrim($language, '/');
            if ($request_path === $trimmed_lang) {
                wp_redirect($language, 302, 'WordPress - Clonable');
                exit();
            }
        }

        if ($is_clonable_page) {
            // Check the circuit breaker before actually sending the request to Clonable
            if ($this->circuit_breaker->isOpen()) {
                $this->circuit_breaker_response();
                // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
                exit;
            }

            $headers = getallheaders();
            $request_method = $server_data['REQUEST_METHOD'];
            $request_url = Functions::get_root_domain() . $request_path;
            $post_body = $this->retrieve_body_content($server_data['CONTENT_TYPE'] ?? null);
            $clonable_response = $this->make_curl_request($request_url, $headers, $request_method, $post_body);

            $this->circuit_breaker->handle(!$clonable_response->is_connection_error());
            if ($this->circuit_breaker->isOpen()) {
                $this->circuit_breaker_response();
                // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
                exit; // exit current script, otherwise the page gets loaded twice at the bottom of the page
            }

            // Set status code
            status_header($clonable_response->get_code());

            // Forward headers, except those that can cause issues
            foreach ($clonable_response->get_headers() as $key => $values) {
                if (!(strtolower($key) == 'content-length' || strtolower($key) == 'content-encoding' || strtolower($key) == 'transfer-encoding')) {
                    foreach ($values as $value) {
                        $replace_header = count($values) <= 1;
                        header($key . ':' . $value, $replace_header);
                    }
                }
            }


            // Clear output buffers to prevent html manipulation by plugins. We want to cleanly pass through the response.
            while(ob_get_level()) {
                $success = ob_end_clean();
                if (!$success) {
                    // break if the output buffer cannot be deleted.
                    break;
                }
            }

            // Send output and exit.
            // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $clonable_response->get_body();
            // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
            exit; // exit current script, otherwise the page gets loaded twice at the bottom of the page
        }
        // Don't need an else or return.
        // If it's not a custom route or the server parameters cannot be gotten, then use the standard response of the request.
    }

    private function circuit_breaker_response() {
        status_header(504);
        echo '<h1>Bad Gateway</h1><br/>';
        echo '<p>The server, while acting as a gateway or proxy, did not get a response in time from the upstream server that it needed in order to complete the request.</p>';
        if (Functions::can_log_sensitive()) {
            echo "<br/>";
            echo "<h2>Admin only:</h2>";
            $this->circuit_breaker->debug();
        }
    }

    /**
     * @param $content_type
     * @return false|string|null
     */
    private function retrieve_body_content($content_type) {
        if (Functions::str_starts_with($content_type, "multipart/form-data;")) {
            return $this->build_data_files($content_type);
        } else {
            return (file_get_contents('php://input') ?? null);
        }
    }

    /**
     * Builds a multipart form-data request, based on the input fields.
     * We have to construct this manually, because PHP does not read this data correctly.
     * For now, files are ignored by this method, but they can be added later.
     * Resource used: https://gist.github.com/maxivak/18fcac476a2f4ea02e5f80b303811d5f
     * @param $content_type string entire content type header of the request.
     * @param $fields
     * @return string
     */
    private function build_data_files($content_type){
        $data = '';
        $delimiter = str_replace("multipart/form-data; boundary=", "", $content_type);

        $eol = "\r\n";
        // convert post fields to multipart form-data variant
        foreach ($_POST as $name => $content) {
            $data .= "--" . $delimiter . $eol
                . 'Content-Disposition: form-data; name="' . $name . "\"".$eol.$eol
                . $content . $eol;
        }

        $data .= "--" . $delimiter . "--".$eol;
        return $data;
    }


    /**
     * Makes the curl request to retrieve the cloned page from Clonable.
     * Return a custom Clonable response object, containing request data.
     * @param $url string the url of the page
     * @param $request_headers array all the request headers
     * @param $request_type string HTTP request method
     * @param $body string|null optional post fields
     * @return ClonableResponse
     */
    private function make_curl_request($url, $request_headers, $request_type, $body = null) {
        $curl_builder = new CurlBuilder($url);

        if ($body != null) {
            $curl_builder = $curl_builder->set_post_fields($body);
        }

        try {
            $max_timeout = get_option('clonable_max_proxy_timeout', 15);
            $site_url = str_replace(array('https://', 'http://'), '', Functions::get_root_domain());
            $curl = $curl_builder
                ->set_request_type($request_type)
                ->set_standard_headers($site_url, $max_timeout)
                ->set_header_response($headers)
                ->set_http_headers($request_headers)
                ->build();
        } catch (Exception $e) {
            curl_close($curl_builder->build());

            error_log("Clonable failed to forward the request: {$e->getMessage()}");
            $message = 'An error occurred. Please check the server logs.<br/>';
            if (Functions::can_log_sensitive()) {
                // The stacktrace/error can be shown, because the user has access to the sensitive data
                $message .=  '[ERROR]: ' . $e->getMessage();
                $message .= '<br/><br/>';
                $message .= "<pre>" . nl2br($e->getTraceAsString()) . "</pre>";
                // maybe the previous error helps with solving more complex problems.
                $message .= "<pre>" . esc_html(print_r($e->getPrevious(), true)) . "</pre>";
            }
            return new ClonableResponse($message, array(), 500, true);
        }

        // Semaphore
        if (function_exists("sem_get") && function_exists("sem_acquire") && function_exists("sem_release")) {
            $std_limit = max(2, intval(get_option("clonable_max_upstream_requests", 4)));
            $queue_limit = $std_limit + max(0, intval(get_option("clonable_max_upstream_queued", 2)));

            $semaphore = sem_get(self::SEMAPHORE_UPSTREAM, $std_limit);
            $semaphore_queue = sem_get(self::SEMAPHORE_UPSTREAM_QUEUE, $queue_limit);
            if ($semaphore !== false) {
                if (!sem_acquire($semaphore_queue, true)) {
                    return new ClonableResponse("Server is busy. Please try again soon.", ['retry-after' => ['5'], 'location' => [$url]], 307, false);
                }
                sem_acquire($semaphore);
            } else {
                $semaphore = null;
            }
        }


        // Send the request to Clonable
        $response = curl_exec($curl);
        if ($response === false) {
            $error = curl_error($curl);
            curl_close($curl); // make sure to close the request

            error_log("Clonable failed to retrieve the response: {$error}");
            $message = 'An error occurred. Please check the server logs.<br/>';
            if (Functions::can_log_sensitive()) {
                $message .= $error;
            }

            if (isset($semaphore) && isset($semaphore_queue)) {
                sem_release($semaphore);
                sem_release($semaphore_queue);
            }
            return new ClonableResponse($message, $headers ?? [], 504, str_contains($error, 'timed out'));
        }

        $status_code = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        curl_close($curl); // make sure to close the request
        $body = substr($response, $header_size);
        if (!empty($headers['content-encoding']) && $headers['content-encoding'][0] === 'gzip') {
            $body = gzdecode($body);
        }

        if (isset($semaphore) && isset($semaphore_queue)) {
            sem_release($semaphore);
            sem_release($semaphore_queue);
        }
        return new ClonableResponse($body, $headers, $status_code);
    }
}