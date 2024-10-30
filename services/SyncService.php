<?php

namespace Clonable\Services;

use Clonable\Helpers\Functions;
use Clonable\Helpers\Json;
use Clonable\Helpers\Locales;
use Clonable\Models\LanguageTag;
use Clonable\Models\Site;
use Clonable\Objects\Notification;
use stdClass;

class SyncService {
    public function sync_site() {
        $url_parts = wp_parse_url(Functions::get_root_domain());
        $domain = str_replace('www.', '', $url_parts['host']);

        $site_response = ApiService::get_site($domain);
        if ($site_response->get_code() != 200) {
            // if the api request was invalid, don't set the clonable_site variable
            update_option("clonable_site", null, true);
            Notification::add_notification('Could not retrieve a website from Clonable, please verify that your site is created within the Clonable dashboard.', Notification::INFO);
        } else {
            update_option("clonable_site", $site_response->get_response(), true);
            $site = new Site($site_response->get_response());
            $this->sync_woocommerce($site->get_domain());

            $this->sync_language_switcher($site->get_locale(), Locales::get_display_name($site->get_locale()), $site->get_url());
            foreach ($site->get_clones() as $index => $clone) {
                $this->sync_clone($site, $clone, $index);
            }

            $last_synced = gmdate('d-m-Y H:i:s');
            update_option("clonable_last_sync", $last_synced, true);
        }
    }

    /**
     * Syncs the language switcher, language tag and woocommerce settings for a clone.
     * @param Site $site
     * @param array $clone
     * @return void
     */
    public function sync_clone($site, $clone, $index) {
        $this->sync_plugin_status_to_clonable($clone);
        $this->sync_language_switcher(
            $clone['lang_code'],
            Locales::get_display_name($clone['lang_code']),
            $site->get_clone_url($index)
        );

        $hreflang = str_replace('_', '-', strtolower($clone['lang_code']));
        $this->sync_language_tags(
            $hreflang,
            $clone['domain'],
            ($clone['subfolder_origin'] ?? '/'),
            ($clone['subfolder_clone'] ?? '/'),
            $site
        );

        $this->sync_woocommerce($clone['domain'], $clone['subfolder_clone']);
    }

    /**
     * Makes an api call too Clonable to correctly flag the clone as connected.
     * If the api fails, a notification will be shown to indicate what went wrong.
     * @param $clone
     * @return void
     */
    public function sync_plugin_status_to_clonable($clone) {
        $is_connected = ($clone['plugin_connected'] ?? false);
        if (!$is_connected) {
            $connection_response = ApiService::connect_to_clone($clone['id']);
            if ($connection_response->get_code() !== 200) {
                Notification::add_notification("Could not connect {$clone['domain']} to Clonable: " . $connection_response->get_response_message(), Notification::ERROR);
            } else {
                Notification::add_notification($clone['domain'] . ' has been connected to Clonable', Notification::SUCCESS);
            }
        } else {
            Notification::add_notification($clone['domain'] . ' has been connected to Clonable', Notification::SUCCESS);
        }
    }

    public function sync_language_tags($locale, $domain, $original_subfolder = '/', $clone_subfolder = '/', $site = null) {
        $enabled = get_option('clonable_langtag_switch');
        if (empty($enabled) && gettype($enabled) === 'string') {
            return; // the language tags have been turned off.
        }

        $language_tag_data_string = get_option('clonable_langtag_data');
        $language_tag_data = (Json::handle_input($language_tag_data_string) ?? LanguageTag::get_default($site));

        $language_tags = $language_tag_data->data->clones;
        if (empty($language_tags)) {
            $language_tags = array();
        }
        if (!in_array($locale, array_column($language_tags, 'langcode'))) {
            $language_tag = new stdClass();
            $language_tag->langcode = $locale;
            $language_tag->domain = $domain;
            $language_tag->original_subfolder = $original_subfolder;
            $language_tag->clone_subfolder = $clone_subfolder;
            $language_tag->translate_urls = true;
            $language_tags[] = $language_tag;
        }
        $language_tag_data->data->clones = $language_tags;
        $language_tag_data_string = Json::handle_output(json_encode($language_tag_data));
        update_option('clonable_langtag_data', $language_tag_data_string, true);
    }

    public function sync_language_switcher($locale, $display_name, $url) {
        $language_switcher_items = get_option('clonable_langswitch_data');
        if (empty($language_switcher_items)) {
            $language_switcher_items = "[]";
        }

        /* @var array $languages */
        $languages = Json::handle_input($language_switcher_items);
        // if value is not yet in array, then you can put it in.
        if (!in_array($url, array_column($languages, 'clonableUrl'))) {
            $languages[] = [
                "clonableLocaleCode" => $locale,
                "clonableDisplayLanguage" => $display_name,
                "clonableUrl" => $url,
            ];
        }

        $languages = Json::handle_output(json_encode($languages));
        update_option('clonable_langswitch_data', $languages, true);
    }

    public function sync_woocommerce($domain, $subfolder_clone = null) {
        $enabled = get_option('clonable_woocommerce_analytics_enabled');
        // check if the WooCommerce conversion tracking has explicitly been turned off.
        if (gettype($enabled) === 'string' && empty($enabled)) {
            return;
        }

        $allowed_origins = get_option('clonable_woocommerce_allowed_origins', null);
        if (empty($allowed_origins)) {
            $origins = array();
        } else {
            $origins = Json::handle_input($allowed_origins);
        }

        $new_origin = rtrim($domain . $subfolder_clone, "/");
        if (!in_array($new_origin, $origins)) {
            $origins[] = $new_origin;
        }

        $allowed_origins = Json::handle_output(json_encode($origins));
        update_option('clonable_woocommerce_allowed_origins', $allowed_origins, true);
    }
}