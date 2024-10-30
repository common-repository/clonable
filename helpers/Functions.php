<?php

namespace Clonable\Helpers;

class Functions {
    public static function str_starts_with($haystack, $needle) {
        if ($haystack === null || $needle === null) {
            return false;
        }

        if ($needle === '') {
            return true;
        }

        return strpos($haystack, $needle) === 0;
    }

    public static function str_ends_with($haystack, $needle) {
        if ($haystack === '' && $needle !== '') {
            return false;
        }
        $len = strlen($needle);

        return substr_compare($haystack, $needle, -$len, $len) === 0;
    }

    /**
     * Use to trim a specific part from the start of a string.
     * Also trim the whitespaces for the beginning and end.
     * @param $input string the input string.
     * @param $trim string the part the trim
     * @return string
     */
    public static function str_trim_start($input, $trim) {
        while (self::str_starts_with($input, $trim)) {
            $input = substr($input, strlen($trim));
        }
        return trim(($input ?? ''));
    }

    /**
     * Debug function for printing data in a nice format
     * Will stop the current process
     * @param $value mixed the data you want to show
     * @return void no return, this function prints
     */
    public static function dd($value) {
        self::dump($value);
        die();
    }

    public static function dump($value) {
        echo "<pre>" . esc_html(print_r($value, true)) . "</pre>";
    }

    public static function is_clonable_page() {
        if (isset($_GET)) {
            $get_data = wp_unslash($_GET);
            $current_page = ($get_data['page'] ?? null);
            return $current_page == 'clonable';
        }
        return false;
    }

    public static function can_log_sensitive() {
        if (current_user_can('administrator' )) {
            return true;
        }

        $api_key = get_option('clonable_api_key');
        if ($api_key === null || $api_key === false || $api_key === "") {
            return false;
        }

        $headers = getallheaders();
        $debug_header = array_filter($headers, function($header_value, $header_name) use ($api_key) {
            return strtolower($header_name) === 'x-clonable-debug' && $header_value === $api_key;
        }, ARRAY_FILTER_USE_BOTH);

        return count($debug_header) > 0;
    }

    /**
     * Get the scheme + domain of the website without the path.
     * get_site_url or get_home_url can return an url with a path.
     * this function makes sure this path is never included.
     *
     * @param string|null $url input url the get the domain from
     * @return string
     */
    public static function get_root_domain($url = null) {
        $full_url = $url ?? get_site_url();
        $url_parts = parse_url($full_url);

        // if the url parts can be parsed, create a new url
        if ($url_parts !== false) {
            return $url_parts['scheme'] . "://" . $url_parts['host'];
        }

        // if the input url is null, we can retry it with the home url
        if ($url === null) {
            return self::get_root_domain(get_home_url());
        }

        return $full_url; // use this as back-up if no url can be constructed
    }
}