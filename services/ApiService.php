<?php

namespace Clonable\Services;

use Clonable\Helpers\Locales;
use Clonable\Objects\ApiResponse;
use Clonable\Objects\ClonableConfig;
use Exception;

class ApiService {
    /**
     * @param string $api_key
     * @return false|mixed
     */
    public static function get_user($api_key = null) {
        $cached_user = get_transient('clonable_cached_user');
        if (!empty($cached_user['user'])) {
            return $cached_user;
        }

        try {
            $api_key = ($api_key ?? self::resolve_key());
            $response = self::make_request("/user", $api_key);
        } catch (Exception $e) {
            // if the api request was not 200, return false as a failure response.
            return false;
        }

        // set transient for 5 seconds, for some caching during validation and navigation
        set_transient('clonable_cached_user', $response, 5);
        return $response;
    }

    /**
     * Retrieves the Site model from the control panel.
     * @param $domain string the domain of the site.
     * @return ApiResponse
     */
    public static function get_site(string $domain): ApiResponse {
        try {
            $api_key = self::resolve_key();
            $response = self::make_request("/sites/$domain", $api_key);
        } catch (Exception $e) {
            $error_response = json_decode($e->getMessage(), true);
            return new ApiResponse(422, $error_response);
        }

        return new ApiResponse(200, $response["site"]);
    }

    /**
     * Calls the create clone endpoint of the api, returns a string when an
     * error occurs and returns a mixed clone object when the request was successful.
     * @param $request array the request parameters for the api request
     * @return ApiResponse
     */
    public static function create_site($request) {
        try {
            $api_key = self::resolve_key();
            $response = self::make_request("/sites", $api_key, $request);
        } catch (Exception $e) {
            $error_response = json_decode($e->getMessage(), true);
            return new ApiResponse(422, $error_response);
        }

        return new ApiResponse(200, $response["site"]);
    }

    /**
     * Make a post request to the create clone endpoint.
     * @param $request
     * @return ApiResponse
     */
    public static function create_clone($request) {
        try {
            $api_key = self::resolve_key();
            $response = self::make_request("/clones", $api_key, $request);
        } catch (Exception $e) {
            $error_response = json_decode($e->getMessage(), true);
            return new ApiResponse(422, $error_response);
        }

        return new ApiResponse(200, $response["clone"]);
    }

    /**
     * Gives a signal to the control panel that the clone in connected in the plugin.
     * @param $clone_id
     * @return ApiResponse
     */
    public static function connect_to_clone($clone_id): ApiResponse {
        try {
            $api_key = self::resolve_key();
            $response = self::make_request("/clones/connect-to-plugin/$clone_id", $api_key, ['clone_id' => $clone_id]);
        } catch (Exception $e) {
            $error_response = json_decode($e->getMessage(), true);
            return new ApiResponse(422, $error_response);
        }

        return new ApiResponse(200, $response["clone"]);
    }

    /**
     * Gets all available locales that Clonable support.
     * Retrieves this data from the API, the response gets cached every day.
     * @return array
     */
    public static function get_locales() {
        return Locales::get_all();
    }

    /**
     * General function for making api request to the control panel.
     * @param $path string request path of the api
     * @param $bearer_token string the authentication bearer token
     * @param $post_fields array possible post field for the request
     * @return mixed json response
     * @throws Exception throws an exception when the request fails or return a invalid response code
     */
    private static function make_request($path, $bearer_token = null, $post_fields = null) {
        $headers = array("accept: application/json");
        if ($bearer_token != null) {
            $headers[] = "authorization: Bearer $bearer_token";
        }
        if ($post_fields != null) {
            $headers[] = "content-type: application/json";
        }

        $curl = curl_init();
        $endpoint = "https://" . ClonableConfig::CP_API_ENDPOINT . $path;
        $curl_options = [
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 1,
            CURLOPT_FOLLOWLOCATION => 0,
            CURLOPT_USERAGENT => "Clonable Wordpress " . CLONABLE_VERSION . " (curl)",
        ];

        if ($post_fields != null) {
            $curl_options[CURLOPT_POST] = 1;
            $curl_options[CURLOPT_POSTFIELDS] = json_encode($post_fields);
        }

        curl_setopt_array($curl, $curl_options);

        // retrieve body from curl request
        $response = curl_exec($curl);
        if ($response === false) {
            throw new Exception(curl_error($curl));
        }

        $response_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($response_code >= 400) {
            throw new Exception($response);
        }

        curl_close($curl);
        return json_decode($response, true);
    }

    /**
     * Gets the API key from the WordPress options
     * @throws Exception
     */
    private static function resolve_key() {
        $api_key = get_option('clonable_api_key');
        if ($api_key === null) {
            throw new Exception("api key is null");
        }
        return $api_key;
    }
}