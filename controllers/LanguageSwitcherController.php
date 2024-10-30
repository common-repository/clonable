<?php

namespace Clonable\Controllers;

use Clonable\Helpers\Functions;
use Clonable\Helpers\Json;
use Clonable\Models\LanguageSwitcher;
use Clonable\Traits\Validation;
use Clonable\Views\LanguageSwitcherView;

class LanguageSwitcherController extends Controller {
    use Validation;
    //<editor-fold desc="Singleton pattern">
    private static $instance = null;

    public static function get_instance() {
        if (self::$instance == null) {
            self::$instance = new LanguageSwitcherController();
        }
        return self::$instance;
    }

    private function __construct() {
        // Keep constructor private
        $this->view = new LanguageSwitcherView();
    }
    //</editor-fold>

    protected $view;

    public function validate($request) {
        $this->validate_fields($request, LanguageSwitcher::$fields);
    }

    public function clonable_enable_language_switcher_validate($input) {
        return $this->validate_checkbox($input, "clonable_enable_language_switcher");
    }

    public function clonable_show_flag_validate($input) {
        return $this->validate_checkbox($input, "clonable_show_flag");
    }

    public function clonable_rounded_flag_validate($input) {
        return $this->validate_checkbox($input, "clonable_rounded_flag");
    }

    public function clonable_show_text_validate($input) {
        return $this->validate_checkbox($input, "clonable_show_text");
    }

    public function clonable_background_color_validate($input) {
        return $this->validate_color_input($input, "clonable_background_color");
    }

    public function clonable_hover_background_color_validate($input) {
        return $this->validate_color_input($input, "clonable_hover_background_color");
    }

    public function clonable_size_validate($input) {
        return $this->validate_select($input, array("sm", "md", "lg"), "clonable_size");
    }

    public function clonable_position_validate($input) {
        return $this->validate_select($input, array("bottom-left", "bottom-right"), "clonable_position");
    }

    public function clonable_language_switcher_items_validate($input) {
        if (!$input) {
            return null;
        }

        // str_replace needed because of incorrect inline character escaping of php json parsing
        // good explanation: https://stackoverflow.com/questions/32056940/how-to-deal-with-backslashes-in-json-strings-php/32057601#32057601
        $languages = Json::handle_input($input);
        if ($languages === null) {
            add_settings_error('clonable_langswitch_data', 'err_invalid_json', 'The supplied JSON string was invalid. Please check its syntax and try again.');
            return false;
        }

        if (!is_array($languages)) {
            add_settings_error('clonable_langswitch_data', 'err_invalid_languages_array', 'The languages list is invalid.');
            return false;
        }

        $failed = false;
        foreach ($languages as $language) {
            // @codingStandardsIgnoreStart
            if (!Functions::str_starts_with($language->clonableUrl, 'https://')) {
                add_settings_error('clonable_langswitch_data', 'err_invalid_domain_value', "The domain '$language->clonableUrl' should start with https://");
                $failed = true;
            }
            // @codingStandardIgnoreEnd
        }
        if ($failed) {
            return false;
        }

        return Json::handle_output($input);
    }
}