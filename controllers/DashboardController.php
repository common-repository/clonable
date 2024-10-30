<?php

namespace Clonable\Controllers;

use Clonable\Helpers\Functions;
use Clonable\Models\Site;
use Clonable\Services\ApiService;
use Clonable\Services\SyncService;
use Clonable\Traits\Validation;
use Clonable\Views\DashboardView;

class DashboardController extends Controller
{
    use Validation;
    //<editor-fold desc="Singleton pattern">
    private static $instance = null;

    public static function get_instance() {
        if (self::$instance == null) {
            self::$instance = new DashboardController();
        }
        return self::$instance;
    }

    private function __construct() {
        // Keep constructor private
        $this->view = new DashboardView();
    }
    //</editor-fold>

    public function validate($request) {
        if (array_key_exists("clonable_site_domain", $request)) {
            $this->create_site($request);
        } else if (array_key_exists('clonable_clone_subfolder', $request)) {
            $this->create_clone($request);
        } else if (array_key_exists('clonable_logout', $request)) {
            $this->use_other_api_key();
        } else if (array_key_exists('clonable-sync', $request)) {
            $sync_service = new SyncService();
            $sync_service->sync_site();
        }
    }

    private function create_site($request) {
        $domain = Functions::str_trim_start($request["clonable_site_domain"], "https://");
        $domain = Functions::str_trim_start($domain, "http://");
        $domain = Functions::str_trim_start($domain, "www.");

        $api_request = ApiService::create_site([
            "domain" => ($domain ?? ''),
            "locale" => $request["clonable_site_locale"],
            "platform" => "WordPress",
            "preferred_domain" => (array_key_exists("clonable_site_preferred_domain", $request) ? 'www' : 'non-www'),
            "origin" => $request["clonable_site_origin"],
        ]);
        // api error handling
        $response = $api_request->get_response();
        if ($api_request->get_code() == 200) {
            update_option('clonable_site', $response, true);
            ApiService::connect_to_clone($response['clone']['id']);
        } else if (isset($response['errors'])) {
            foreach ($response['errors'] as $field => $error) {
                add_settings_error("clonable_site_locale", 'err_invalid_value', $error[0]);
            }
        } else {
            add_settings_error('clonable_site_locale', 'err_unknown', 'An unknown error occurred during the site creation.');
        }
    }

    private function create_clone($request) {
        $site = new Site(get_option("clonable_site"));
        // make sure the subfolder field ends and starts with a slash.
        if (!Functions::str_starts_with($request["clonable_clone_subfolder"], "/")) {
            $request["clonable_clone_subfolder"] = str_pad($request["clonable_clone_subfolder"], (strlen($request["clonable_clone_subfolder"]) + 1), "/", STR_PAD_LEFT);
        }
        if (!Functions::str_ends_with($request["clonable_clone_subfolder"], "/")) {
            $request["clonable_clone_subfolder"] = str_pad($request["clonable_clone_subfolder"], (strlen($request["clonable_clone_subfolder"]) + 1), "/");
        }
        $api_response = ApiService::create_clone([
            "site_id" => $site->get_id(),
            "type" => "translate",
            "mode" => "SUBFOLDER",
            "domain" => $site->get_domain(),
            "locale" => $request["clonable_clone_locale"],
            "subfolder_clone" => $request["clonable_clone_subfolder"],
        ]);

        if ($api_response->get_code() != 200) {
            foreach ($api_response->get_response()['errors'] as $field => $error) {
                add_settings_error("clonable_clone_locale", 'err_invalid_value', $error[0]);
            }
            return;
        }

        $sync_service = new SyncService();
        $sync_service->sync_site();
    }

    public function use_other_api_key() {
        // phpcs:disable Squiz.PHP.GlobalKeyword.NotAllowed
        global $wpdb;
        // phpcs:enable Squiz.PHP.GlobalKeyword.NotAllowed
        $plugin_options = $wpdb->get_results("SELECT option_name FROM $wpdb->options WHERE option_name LIKE 'clonable_%'");

        foreach ($plugin_options as $option) {
            delete_option( $option->option_name );
        }
    }
}