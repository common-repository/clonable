<?php

namespace Clonable\Helpers;

class Session {
    const CLONABLE_VALIDATION_KEY = "clonable-validation-data";

    public static function old($option_name, $default_value = false) {
        if (isset($_SESSION[self::CLONABLE_VALIDATION_KEY][$option_name])) {
            return $_SESSION[self::CLONABLE_VALIDATION_KEY][$option_name];
        }
        return get_option($option_name, $default_value);
    }

    public static function put_validation_data($key, $data) {
        $data = Json::handle_output(Json::handle_output($data));
        $_SESSION[self::CLONABLE_VALIDATION_KEY][$key] = $data;
    }

    public static function clear_validation_data() {
        unset($_SESSION[self::CLONABLE_VALIDATION_KEY]);
    }
}