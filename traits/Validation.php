<?php

namespace Clonable\Traits;

use Clonable\Helpers\Session;
use Clonable\Services\ApiService;

/**
 * Class with common validation function used in Controllers
 */
trait Validation {
    /**
     * Automatically runs validation methods for all given fields.
     * It will take the id of the field and will add an _validate suffix.
     *
     * For example: For field "clonable_description" the function clonable_description_validate($input) will be ran.
     * @param $request
     * @param array $fields
     * @return void
     */
    public function validate_fields($request, $fields) {
        foreach ($fields as $id => $field) {
            $validation_method = $id . '_validate';
            if (is_callable([$this, $validation_method])) {
                $option_name = $field['setting'];
                $validation_response = $this->$validation_method($request[$option_name] ?? null);
                if ($validation_response === false) {
                    Session::put_validation_data($option_name, $request[$option_name]);
                } else {
                    update_option($option_name, $validation_response, true);
                }
            }
        }
    }

    /**
     * Validation method for common checkbox validation
     * @param $input string the value of the checkbox
     * @param $setting string WordPress option api value
     * @return string|null
     */
    public function validate_checkbox($input, $setting) {
        if (!$input) {
            return null;
        }

        if (gettype($input) !== 'string' && !($input === 'on' || $input === 'off')) {
            add_settings_error($setting, 'err_invalid_value', 'The supplied value was not valid for a checkbox.');
            return null;
        }

        return $input;
    }

    /**
     * Validation method for common color input validation.
     * Validates the input as hexadecimal string.
     * @param $input string the value of the checkbox
     * @param $setting string WordPress option api value
     * @return string|null
     */
    public function validate_color_input($input, $setting) {
        if (!$input) {
            return null;
        }

        if (!preg_match("/^#[a-fA-F0-9]{6}$/", $input)) {
            add_settings_error($setting, 'err_invalid_value', 'The supplied color was not in the correct format, reverting back to default.');
            return '#ffffff';
        }

        return $input;
    }

    /**
     * Validation method for common select input validation.
     * @param $input string the value of the checkbox
     * @param $valid_options string[] all possible valid values
     * @param $setting string WordPress option api value
     * @return string|null return the first value of the array when an invalid value is passed.
     */
    public function validate_select($input, $valid_options, $setting) {
        if (!$input || !$valid_options) {
            return null;
        }

        if (!in_array($input, $valid_options)) {
            add_settings_error($setting, 'err_invalid_value', 'The supplied value was not one of the options.');
            return $valid_options[array_key_first($valid_options)];
        }

        return $input;
    }

    public function validate_api_key($input, $setting) {
        if (!$input) {
            return null;
        }

        if (!preg_match("/^clonable_[a-z0-9]{8}_[a-z0-9]{24}$/", $input)) {
            add_settings_error($setting, 'err_invalid_value', 'The api key has the incorrect format, make sure you copy the key correctly from Clonable.');
            return null;
        }

        $response = ApiService::get_user($input);
        if (empty($response['user'])) {
            add_settings_error($setting, 'err_invalid_value', 'You seem to have given a invalid api key, make sure you copy the key correctly from Clonable.');
            return null;
        }

        return $input;
    }
}