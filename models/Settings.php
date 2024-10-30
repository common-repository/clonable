<?php

namespace Clonable\Models;

class Settings {
    const PAGE = "settings";

    public static $fields = [
        "clonable_max_proxy_timeout" => [
            "render" => "max_proxy_timeout_field",
            "name" => "Maximum connection time",
            "setting" => "clonable_max_proxy_timeout",
            "description" => "Sets the maximum timeout for the subfolder communication between WordPress and Clonable",
        ],
        "clonable_allowed_hosts_enabled" => [
            "render" => "allowed_hosts_enabled_field",
            "name" => "Enable allowed hosts service",
            "setting" => "clonable_allowed_hosts_enabled",
        ],
        "clonable_subfolder_service_enabled" => [
            "render" => "subfolder_service_enabled_field",
            "name" => "Enable subfolder service",
            "setting" => "clonable_subfolder_service_enabled",
        ],
        "clonable_locale_service_enabled" => [
            "render" => "locale_service_enabled_field",
            "name" => "Enable locale service",
            "setting" => "clonable_locale_service_enabled",
        ],
        "clonable_language_tag_service_enabled" => [
            "render" => "language_tag_service_enabled_field",
            "name" => "Enable language tag service",
            "setting" => "clonable_language_tag_service_enabled",
        ],
        "clonable_max_upstream_requests" => [
            "render" => "max_upstream_requests_field",
            "name" => "Maximum number of simultaneous upstream requests",
            "setting" => "clonable_max_upstream_requests",
            "description" => "This is the maximum number of requests that can be proxied to Clonable at the same time. This should be at most half of the maximum amount of php processes (pm.max_children).",
        ],
        "clonable_max_upstream_queued" => [
            "render" => "max_upstream_queued_field",
            "name" => "Maximum number of upstream requests that may be queued ",
            "setting" => "clonable_max_upstream_queued",
            "description" => "This is the maximum number of requests that can be queued to be proxied to Clonable at the same time when the setting above is exceeded. This should be at most half of the maximum amount of simultaneous upstream requests.",
        ],
    ];
}