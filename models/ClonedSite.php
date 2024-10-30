<?php

namespace Clonable\Models;

class ClonedSite {
    const PAGE = "clone";

    public static $fields = [
        "clonable_clone_subfolder" => [
            "render" => "clone_subfolder_field",
            "name" => "Subfolder",
            "setting" => "clonable_clone_subfolder",
        ],
        "clonable_clone_locale" => [
            "render" => "clone_locale_field",
            "name" => "Locale",
            "setting" => "clonable_clone_locale",
        ],
    ];
}