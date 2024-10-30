<?php

namespace Clonable\Helpers;

class Json {
    public static function handle_input($input) {
        // str_replace needed because of incorrect inline character escaping of php json parsing
        // good explanation: https://stackoverflow.com/questions/32056940/how-to-deal-with-backslashes-in-json-strings-php/32057601#32057601
        return json_decode(str_replace('\\', "", $input));
    }

    public static function handle_output($input) {
        return str_replace("\\\"", "\"", $input);
    }
}