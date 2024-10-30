<?php

namespace Clonable\Helpers;

use Clonable\Services\ApiService;

class Locales {
    public static function get_display_name($locale) {
        return self::get_locale_attribute($locale, 'display_name');
    }

    public static function get_region($locale) {
        return self::get_locale_attribute($locale, 'region');
    }

    private static function get_locale_attribute($original_locale, $attribute) {
        $locales = ApiService::get_locales();
        $result = array_filter($locales, function ($locale) use ($original_locale) {
            return $locale["locale"] == $original_locale;
        });
        if (empty($result)) {
            return $original_locale;
        }
        $key = array_key_first($result);
        return $result[$key][$attribute];
    }

    public static function filter_regions() {
        // TODO nice to have: filter the most correct language, currently the en_ version is
        // most likely but this would be nicer is the correct language code was chosen.
        $mapped_locales = array();
        foreach (ApiService::get_locales() as $locale) {
            if (preg_match('~^\X* \((.*)\)$~', $locale['display_name'], $matches)) {
                $locale['display_country'] = $matches[1];
                $mapped_locales[] = $locale;
            }
        }
        return $mapped_locales;
    }

    /**
     * Return a list of hardcodes locales as the locales API would return.
     *
     * Was added for performance improvement.
     * @return array
     */
    public static function get_all() {
        return include 'clonable-locales-list.php';
    }
}