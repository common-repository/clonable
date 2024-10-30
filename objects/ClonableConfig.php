<?php

namespace Clonable\Objects;

use Clonable\Helpers\Functions;
use Clonable\Models\Site;

defined( 'ABSPATH' ) || exit;

/**
 * Class with constants and function that are used throughout
 * the entire plugin.
 */
class ClonableConfig {
    const WOOCOMMERCE_QUERY_ID = "clonable-excluded-%s";
    const WOOCOMMERCE_TAXONOMY = 'clonable_excluded_products';
    const UT_API_ENDPOINT = 'ut.api.clonable.net';
    const SERVER_IP = '89.41.171.180';
    const CP_API_ENDPOINT = 'api.clonable.net';
    const MODULES_ENDPOINT = 'modules.clonable.net';
    const ORIGINAL_SHOP = 'original';

    /**
     * Return the Clonable clones from the WordPress option.
     * @return array
     */
    public static function get_clones() {
        $site = self::get_site();
        if (empty($site)) {
            return [];
        }
        return $site->get_clones();
    }

    /**
     * Retrieves the Clonable site from the WordPress option.
     * @return Site|null
     */
    public static function get_site() {
        $response = get_option("clonable_site");
        if (empty($response)) {
            return null;
        }
        return new Site($response);
    }

    /**
     * Gets the current clone domain including subfolder.
     * This value is unique per clone.
     * @return string returns the value of ClonableConfig::ORIGINAL_SHOP for the original site,
     * or return the domain + subfolder for the clone.
     */
    public static function current_clonable_domain() {
        // phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $server_data = $_SERVER;
        // phpcs:enable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

        $domain = ($server_data['HTTP_CLONABLE_CLONE_DOMAIN'] ?? null);
        if (empty($domain)) {
            // return a static string when it's the original shop.
            // allows for easier checks in different places.
            return self::ORIGINAL_SHOP;
        }
        $subfolder = ($server_data['HTTP_CLONABLE_CLONE_SUBFOLDER'] ?? '');
        // each clone is uniquely identified by their domain in combination with subfolder.
        return $domain . $subfolder;
    }
}