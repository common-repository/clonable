<?php

namespace Clonable\Controllers;

use Clonable\Helpers\Functions;
use Clonable\Models\Settings;
use Clonable\Traits\Validation;
use Clonable\Views\SettingsView;

class SettingController extends Controller {
    use Validation;
    //<editor-fold desc="Singleton pattern">
    private static $instance = null;

    public static function get_instance() {
        if (self::$instance == null) {
            self::$instance = new SettingController();
        }
        return self::$instance;
    }

    private function __construct() {
        // Keep constructor private
        $this->view = new SettingsView();
    }
    //</editor-fold>

    public function validate($request) {
        $this->validate_fields($request, Settings::$fields);
    }

    public function clonable_max_proxy_timeout_validate($input) {
        if (!$input) {
            return null;
        }

        if (intval($input) < 1) {
            add_settings_error('max_proxy_timeout', 'err_invalid_value', 'The value of the timeout has to be larger than 0.');
        }

        return $input;
    }

    public function clonable_allowed_hosts_enabled_validate($input) {
        return $this->validate_checkbox($input,'clonable_allowed_hosts_enabled');
    }

    public function clonable_subfolder_service_enabled_validate($input) {
        return $this->validate_checkbox($input,'clonable_subfolder_service_enabled');
    }

    public function clonable_locale_service_enabled_validate($input) {
        return $this->validate_checkbox($input,'clonable_locale_service_enabled');
    }

    public function clonable_language_tag_service_enabled_validate($input) {
        return $this->validate_checkbox($input,'clonable_language_tag_service_enabled');
    }

    public function clonable_max_upstream_requests_validate($input) {
        if (!$input) {
            return null;
        }

        if (intval($input) < 2) {
            add_settings_error('clonable_max_upstream_requests', 'err_invalid_value', 'The value has to be at least 2.');
        }

        if (intval($input) > 200) {
            add_settings_error('clonable_max_upstream_requests', 'err_invalid_value', 'The value has to be at most 200.');
        }

        return $input;
    }

    public function clonable_max_upstream_queued_validate($input) {
        if (!$input) {
            return null;
        }

        if (intval($input) < 0) {
            add_settings_error('clonable_max_upstream_queued', 'err_invalid_value', 'The value has to be at least 0.');
        }

        if (intval($input) > 200) {
            add_settings_error('clonable_max_upstream_queued', 'err_invalid_value', 'The value has to be at most 200.');
        }

        return $input;
    }
}