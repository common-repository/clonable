<?php

namespace Clonable\Models;

use stdClass;

class LanguageTag {
    const PAGE = "language_tag";

    public static $fields = [
        "clonable_langtag_switch" => [
            "render" => "clonable_langtag_switch_field",
            "name" => "Translate URLs in language tags",
            "setting" => "clonable_langtag_switch",
        ],
        "clonable_langtag_data" => [
            "render" => "clonable_langtag_data_field",
            "name" => "Domain data",
            "setting" => "clonable_langtag_data",
        ],
    ];

    /**
     * Gets the default data for the language tag data.
     * @param Site $site the site settings
     * @return stdClass the data object
     */
    public static function get_default($site) {
        $language_tag_data = new stdClass();
        $language_tag_data->version = 2;
        $language_tag_data->data = new stdClass();
        $language_tag_data->data->original = new stdClass();
        $language_tag_data->data->original->domain = $site->get_domain();
        $href_lang = str_replace("_", "-", strtolower($site->get_locale()));
        $language_tag_data->data->original->langcode = $href_lang;
        $language_tag_data->data->original->original_subfolder = '/';
        $language_tag_data->data->original->clone_subfolder = '/';
        $language_tag_data->data->original->include = true;
        $language_tag_data->data->clones = array();
        return $language_tag_data;
    }
}