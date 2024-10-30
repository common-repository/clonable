<?php

namespace Clonable\Services;

use Clonable\Helpers\Functions;
use Clonable\Objects\ClonableConfig;
use Exception;
use Throwable;

class LanguageTagService {
    public function clonable_echo_language_tags() {
        if (get_option('clonable_language_tag_service_enabled', 'on') !== 'on') {
            return;
        }

        try {
            $tags = $this->clonable_get_all_language_tags();
        } catch (Throwable $exception) {
            error_log('Error while getting language tag data: ' . $exception->getMessage() . " || " . $exception->getTraceAsString());
            $tags = [];
        }

        try {
            foreach ($tags as $tag) {
                if (empty($tag['hreflang']) || empty($tag['href'])) {
                    continue;
                }
                echo "<link rel=\"alternate\" hreflang=\"" . esc_attr($tag['hreflang']) . "\" href=\"" . esc_url($tag['href']) . "\" />\n";
            }
        } catch (Throwable $exception) {
            error_log("[Clonable] Error while rendering language tags: {$exception->getMessage()}");
        }
    }

    private function clonable_get_all_language_tags() {
        $tags = [];

        $json = get_option('clonable_langtag_data');
        if ($json == null) {
            return $tags;
        }

        $decoded_json = json_decode($json);
        // check if the saved data is valid json
        if ($decoded_json == null || $decoded_json->data == null || $decoded_json->version == null) {
            return $tags;
        }

        $data = $decoded_json->data;

        $include_original = true;
        if ($decoded_json->version > 1) { // backwards compatibility with the old plugin
            $include_original = $data->original->include;
        }

        if (isset($data->original) && $include_original) {
            $href = $this->clonable_get_full_url($data->original);
            $tags[] = [
                'href' => $href,
                'hreflang' => 'x-default',
                'use_url_translation' => false,
            ];

            $tags[] = [
                'href' => $href,
                'hreflang' => $data->original->langcode,
                'use_url_translation' => false,
            ];
        }

        $translate_all_urls = get_option('clonable_langtag_switch', 'off') == 'on';
        if (isset($data->clones)) {
            foreach ($data->clones as $clone) {
                $translate_clone_url = ($clone->translate_urls ?? false);
                $href = $this->clonable_get_full_url($clone);
                $tags[] = [
                    'href' => $href,
                    'hreflang' => $clone->langcode,
                    'use_url_translation' => ($translate_all_urls && $translate_clone_url),
                ];
            }
        }

        // filter all the url that need to be translated
        $translatable_language_tags = [];
        foreach ($tags as $tag) {
            if ($tag['use_url_translation']) {
                $translatable_language_tags[] = [
                    'o' => $tag['href'],
                    'r' => $tag['href'],
                ];
            }
        }

        // If we have no urls to translate, return as-is
        if (count($translatable_language_tags) == 0) {
            return $tags;
        }

        // translate the actual urls
        $translation_output = $this->clonable_translate_hrefs($translatable_language_tags);

        // Update tags
        // This is O(n2), but that's not really an issue because of the small size of tags (typically <10)
        foreach ($translation_output as $translation) {
            foreach ($tags as $key => $tag) {
                if ($tag['href'] == $translation['o']) {
                    $tags[$key]['href'] = $translation['r'];
                    break;
                }
            }
        }

        return $tags;
    }

    private function clonable_get_full_url($site) {
        $domain = $site->domain;
        // Use fallback '/' for original site data, since there is no notion of subfolders there
        $original_subfolder = ($site->original_subfolder ?? '/');
        $clone_subfolder = ($site->clone_subfolder ?? '/');

        if (empty($_SERVER)) {
            return $domain;
        }
        $server_data = wp_unslash($_SERVER);
        if (!isset($server_data["REQUEST_URI"])) {
            return $domain;
        }

        // Swap subfolder if necessary
        $path = strtok($server_data["REQUEST_URI"], '?');
        if (Functions::str_starts_with($path, $original_subfolder)) {
            $path = substr($path, strlen($original_subfolder));
            $path = $clone_subfolder . $path;
        }

        return "https://$domain$path";
    }

    /**
     * Translate the href attributes of an array of language tags.
     * @param array $urls
     * @return array
     */
    private function clonable_translate_hrefs($urls) {
        try {
            $transient_key = "clonable_url_translations_" . substr(hash("sha256", json_encode($urls)), 0, 16);
            $cached_response = get_transient($transient_key);

            // check if cache is empty/expired, or random cache refresh chance hits,
            // if one of these conditions fulfills, the cache gets filled with new data
            if ($cached_response === false) {
                $debounce_counter = intval(get_option("clonable_debounce_counter", 0));
                // if debounceCounter is null or 0, then there were no error requests, and you shouldn't debounce
                if ($debounce_counter !== 0) {
                    $sample = 100; // default 9/100
                    $sample -= $debounce_counter; // subtract debounceCounter from request chance percentage
                    $should_debounce = rand(1, $sample);
                    if ($should_debounce < 9) { // default debounce rate of 9% with a sample=100 and a maximum of 90% with sample=10
                        return $urls;
                    }
                }
                $cached_response = $this->clonable_get_translated_urls($urls);
                if (empty($cached_response)) {
                    $this->clonable_handle_debounce_counter(true);
                    return $urls;
                } else {
                    $jitter = rand(1, 3600);
                    set_transient($transient_key, $cached_response, (86400 + $jitter));
                    $this->clonable_handle_debounce_counter(false);
                }
            }
            return $cached_response;
        } catch (Throwable $e) {
            // increase the debounceCounter when an error occurs during the url translation
            $this->clonable_handle_debounce_counter(true);
            return $urls;
        }
    }

    private function clonable_handle_debounce_counter($increment) {
        $debounce_counter = get_option('clonable_debounce_counter');
        if ($debounce_counter === false) {
            add_option('clonable_debounce_counter', 0);
            $debounce_counter = 0;
        } else {
            $debounce_counter = intval($debounce_counter);
        }

        if ($increment) {
            // maximum decrease amount of 90, so request are not completely impossible
            // this will result in a maximum debounce chance of 9/10
            if ($debounce_counter < 90) {
                // Add one (to prevent being stuck at 0) and multiply by 1.2, this reaches 90 in about 16 steps
                $debounce_counter = min(90, (int) round(($debounce_counter + 1) * 1.2));
                update_option("clonable_debounce_counter", $debounce_counter);
            }
        } else {
            if ($debounce_counter <= 0) {
                return;
            }
            if ($debounce_counter <= 1) { // if debounceCounter = 1, set it to 0 instead of dividing it by 2
                $debounce_counter = 0;
            } else {
                $debounce_counter = (int) round($debounce_counter / 2);
            }
            update_option("clonable_debounce_counter", $debounce_counter);
        }
    }

    /**
     * Retrieve the translations for the given language tags.
     * @param array $tags
     * @return mixed
     * @throws Exception
     */
    private function clonable_get_translated_urls($tags) {
        // make sure only the url are send to the url translation api
        $urls = [];
        foreach ($tags as $tag) {
            $urls[] = $tag['o'];
        }

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "http://" . ClonableConfig::UT_API_ENDPOINT . '/bulk',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($urls),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT_MS => 500,
            CURLOPT_CONNECTTIMEOUT_MS => 100,
            CURLOPT_USERAGENT => "Clonable Wordpress " . CLONABLE_VERSION . " (curl)",
        ]);
        curl_setopt(
            $curl,
            CURLOPT_HEADERFUNCTION,
            function ($curl, $header) use (&$headers) {
                $length = strlen($header);
                $header = explode(":", $header, 2);
                if (count($header) < 2) {
                    return $length;
                }

                $headers[strtolower(trim($header[0]))][] = trim($header[1]);

                return $length;
            }
        );

        // retrieve body from curl request
        $response = curl_exec($curl);
        if ($response !== false) {
            curl_close($curl); // make sure to close the request
            // you need to manually strip the header from the body response

            //retrieve and decode the x-signature header
            $signature = base64_decode($headers['x-signature'][0]);
            $public_key = $this->clonable_get_public_key();

            // Fix corrupt public key
            if (Functions::str_starts_with($public_key, 'HTTP/')) {
                $public_key = $this->clonable_get_public_key(true);
            }

            $ok = openssl_verify($response, $signature, $public_key, OPENSSL_ALGO_SHA256);
            if ($ok === 1) { // if verification is successful, return the result
                return json_decode($response, true);
            } else { // else return the original request
                throw new Exception("Failed to verify response from Clonable API while translating URLS.");
            }
        } else {
            $error = curl_error($curl);
            curl_close($curl);
            throw new Exception("Failed to fetch url translations from Clonable API: $error");
        }
    }

    /**
     * Gets the public openssl key for the url translation api
     * @throws Exception
     */
    public function clonable_get_public_key($reset = false) {
        $public_key = get_option("clonable_public_key");
        if ($public_key === false || $reset) { // check if the public key is set
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://" . ClonableConfig::UT_API_ENDPOINT . "/public-key",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => array("Accept: text/plain"),
                CURLOPT_TIMEOUT => 1,
                CURLOPT_USERAGENT => "Clonable Wordpress " . CLONABLE_VERSION . " (curl)",
            ]);
            // retrieve body from curl request
            $public_key = curl_exec($curl);
            if ($public_key === false) {
                $error = curl_error($curl);
                curl_close($curl);
                throw new Exception($error);
            }

            curl_close($curl);
            update_option("clonable_public_key", $public_key, true);
        }

        return $public_key;
    }
}