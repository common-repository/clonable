<?php

use Clonable\Helpers\Functions;
use Clonable\Models\Site;

class LocaleService {
    /* @var string[] $locales */
    private $locales = [];

    public function __construct() {
        if (get_option('clonable_locale_service_enabled', 'on') !== 'on') {
            return;
        }

        $response = get_option("clonable_site");
        if (empty($response)) {
            return;
        }

        $site = new Site($response);
        $clones = $site->get_clones();
        foreach ($clones as $clone) {
            $this->locales[] = $clone['lang_code'];
        }

        add_filter('locale', array($this, 'set_locale'));
    }

    public function set_locale($locale) {
        $server_data = $_SERVER; // this fixes some linting rules
        if (empty($server_data['HTTP_CLONABLE_TARGET_LANGUAGE']) || empty($this->locales)) {
            return $locale;
        }

        $clone_locale = $server_data['HTTP_CLONABLE_TARGET_LANGUAGE'];
        $locales = array_filter(($this->locales), function ($locale) use ($clone_locale) {
            return Functions::str_starts_with($locale, $clone_locale);
        });

        if (empty($locales)) {
            return $locale;
        }

        // Return the first of the array, this may conflict if there are multiple clones with the same language,
        // Example: nl_NL and nl_BE.
        return $locales[array_key_first($locales)];
    }
}