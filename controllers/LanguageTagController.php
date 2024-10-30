<?php

namespace Clonable\Controllers;

use Clonable\Helpers\Functions;
use Clonable\Helpers\Json;
use Clonable\Models\LanguageTag;
use Clonable\Traits\Validation;
use Clonable\Views\LanguageTagView;

class LanguageTagController extends Controller {
    use Validation;

    //<editor-fold desc="Singleton pattern">
    private static $instance = null;

    public static function get_instance() {
        if (self::$instance == null) {
            self::$instance = new LanguageTagController();
        }
        return self::$instance;
    }

    private function __construct() {
        // Keep constructor private
        $this->view = new LanguageTagView();
    }

    //</editor-fold>

    public function validate($request) {
        $this->validate_fields($request, LanguageTag::$fields);
    }

    public function clonable_langtag_switch_validate($input) {
        return $this->validate_checkbox($input, "clonable_langtag_switch");
    }

    public function clonable_langtag_data_validate($input) {
        if (!$input) {
            return null;
        }

        $json = Json::handle_input($input);
        if ($json === null) {
            add_settings_error('clonable_langtag_data', 'err_invalid_json', 'The supplied JSON string was invalid. Please check its syntax and try again.');
            return false;
        }

        // current versions allowed are 1 or 2
        if ($json->version < 1 || $json->version > 2) {
            add_settings_error('clonable_langtag_data', 'err_invalid_schema_version', 'The supplied JSON schema has the wrong version. Make sure the plug-in is up to date.');
            return false;
        }

        if (!isset($json->data)) {
            add_settings_error('clonable_langtag_data', 'err_no_data', 'The supplied JSON does not contain any data.');
            return false;
        } else {
            $data = $json->data;
        }

        // Validate original if present
        if (isset($data->original)) {
            if (!isset($data->original->domain) || !isset($data->original->langcode)) {
                add_settings_error('clonable_langtag_data', 'err_original_missing_data', 'The original is missing data. Please check if it has a domain name and language code field.');
                return false;
            }

            // Check if valid domain
            if (filter_var($data->original->domain, FILTER_VALIDATE_DOMAIN) === false) {
                add_settings_error('clonable_langtag_data', 'err_original_invalid_domain', 'The original domain is invalid.');
                return false;
            }

            // Check if valid langcode
            if (!preg_match('/^[a-z]{2}(-[a-z]{2})?$/', $data->original->langcode)) {
                add_settings_error('clonable_langtag_data', 'err_original_invalid_langcode', 'The original language code is invalid.');
                return false;
            }
        }

        // Validate clones
        if (isset($data->clones)) {
            if (!is_array($data->clones)) {
                add_settings_error('clonable_langtag_data', 'err_invalid_clone_array', 'The clones list is invalid.');
                return false;
            }

            foreach ($data->clones as $clone) {
                if (!isset($clone->domain) || !isset($clone->langcode)) {
                    add_settings_error('clonable_langtag_data', 'err_clone_missing_data', 'A clone in the list is missing data. Please check if all clones have a domain name and language code field.');
                    return false;
                }

                // Check if valid domain
                if (filter_var($clone->domain, FILTER_VALIDATE_DOMAIN) === false) {
                    add_settings_error('clonable_langtag_data', 'err_clone_invalid_domain', "The domain name of clone $clone->domain is invalid.");
                    return false;
                }

                // Check if valid langcode
                if (!preg_match('/^[a-z]{2}(-[a-z]{2})?$/', $clone->langcode)) {
                    add_settings_error('clonable_langtag_data', 'err_clone_invalid_langcode', "The language tag name of clone $clone->domain is invalid.");
                    return false;
                }

                // Check subfolder settings
                $original_subfolder = $clone->original_subfolder;
                $clone_subfolder = $clone->clone_subfolder;
                if (!(Functions::str_starts_with($original_subfolder, '/') && Functions::str_ends_with($original_subfolder, '/'))) {
                    add_settings_error('clonable_langtag_data', 'err_invalid_original_subfolder', "The original subfolder of $clone->domain is invalid.");
                    return false;
                }

                if (!(Functions::str_starts_with($clone_subfolder, '/') && Functions::str_ends_with($clone_subfolder, '/'))) {
                    add_settings_error('clonable_langtag_data', 'err_invalid_clone_subfolder', "The clone subfolder of $clone->domain is invalid.");
                    return false;
                }
            }
        }

        return Json::handle_output($input);
    }
}