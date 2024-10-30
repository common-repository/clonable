<?php

namespace Clonable\Models;

class ApiKey {
    const PAGE = "api-key";

    public static $fields = [
        "clonable_api_key" => [
            "render" => "api_key_field",
            "name" => "Enter your api key",
            "setting" => "clonable_api_key",
        ],
    ];
}