<?php

namespace Clonable\Models;

class LanguageSwitcher {
    const PAGE = "language_switcher";

    public static $fields = [
        "clonable_enable_language_switcher" => [
            "render" => "enable_language_switcher_field",
            "name" => "Enable language switcher",
            "setting" => "clonable_enable_language_switcher",
            "description" => "Turn the language switcher on or off.",
        ],
        "clonable_show_flag" => [
            "render" => "show_flag_field",
            "name" => "Show flag",
            "setting" => "clonable_show_flag",
            "description" => "Show the flag of the country.",
        ],
        "clonable_rounded_flag" => [
            "render" => "rounded_flag_field",
            "name" => "Rounded flags",
            "setting" => "clonable_rounded_flag",
            "description" => "Should the flags be circular or rectangular",
        ],
        "clonable_show_text" => [
            "render" => "show_text_field",
            "name" => "Show text",
            "setting" => "clonable_show_text",
            "description" => "Should the name of the language be show",
        ],
        "clonable_background_color" => [
            "render" => "background_color_field",
            "name" => "Choose background color",
            "setting" => "clonable_background_color",
        ],
        "clonable_hover_background_color" => [
            "render" => "background_hover_color_field",
            "name" => "Choose hover background color",
            "setting" => "clonable_hover_background_color",
        ],
        "clonable_size" => [
            "render" => "size_field",
            "name" => "Choose language switcher size",
            "setting" => "clonable_size",
            "description" => "Controls the size of the language switcher flag.",
        ],
        "clonable_position" => [
            "render" => "position_field",
            "name" => "Choose a position",
            "setting" => "clonable_position",
            "description" => "The position of the language switcher on the page.",
        ],
        "clonable_language_switcher_items" => [
            "render" => "languages_field",
            "name" => "Languages",
            "setting" => "clonable_langswitch_data",
        ],
    ];
}