<?php

namespace Clonable\Services;

use Clonable\Helpers\Functions;
use Clonable\Helpers\Json;
use Clonable\Objects\ClonableConfig;
use Clonable\Services\Modules\DataPanelModule;
use Clonable\Services\Modules\ExclusionModule;
use Clonable\Services\Modules\ProductImporterModule;
use Clonable\Services\Modules\TaxonomyModule;
use Clonable\Traits\WooCommerceCheck;

defined( 'ABSPATH' ) || exit;

class ClonableWooCommerceService {
    use WooCommerceCheck;

    public function __construct() {
        $module_enabled = get_option('clonable_woocommerce_module_enabled', 'on') === 'on';
        if (!$this->woocommerce_is_installed() || !$module_enabled) {
            return;
        }

        $include_woocommerce_analytics = get_option('clonable_woocommerce_analytics_enabled', 'on') === 'on';
        if ($include_woocommerce_analytics) {
            // Add hidden field with the domain. Will get replaced by Clonable automagically to match actual domain.
            add_action('woocommerce_after_order_notes', array($this, 'add_origin_field'));
            add_action('woocommerce_checkout_update_order_meta', array($this, 'save_origin_field'));
            add_action('woocommerce_admin_order_data_after_shipping_address', array($this, 'display_order_origin'));

            // Implement redirecting back to clone instead of original website
            add_filter('woocommerce_get_checkout_order_received_url', array($this, 'filter_return_url'), 10, 2);

            // Also intercept checkout_url as it is also used by some gateways to handle cancellations.
            add_filter('woocommerce_get_checkout_payment_url', array($this, 'filter_return_url'), 10 , 2);

            // Add filter for Mollie's custom return url implementation
            add_filter('mollie-payments-for-woocommerce_return_url', array($this, 'filter_return_url'), 10, 2);
        }

        $include_product_exclusions = get_option('clonable_product_exclusions_enabled', 'on') === 'on';
        if ($include_product_exclusions) {
            $taxonomy_module = new TaxonomyModule();  // registers the exclusion taxonomy and terms.
            $exclusion_module = new ExclusionModule(); // handles the actual product exclusions (also adds checkboxes in admin view).
            $data_panel_module = new DataPanelModule(); // data panel on the admin product page.
            $product_importer_module = new ProductImporterModule(); // enables bulk edit via de WooCommerce product importer
        }
    }

    public function add_origin_field($checkout) {
        $http_data = wp_unslash($_SERVER);
        if (isset($http_data['HTTP_HOST'])) {
            $domain = rtrim(ClonableConfig::current_clonable_domain(), '/');
            if ($domain === ClonableConfig::ORIGINAL_SHOP) {
                $value = $http_data['HTTP_HOST'];
            } else {
                $value = $domain;
            }
        } else {
            $value = '';
        }
        echo "<input type=\"hidden\" name=\"clonable_origin\" value=\"" . esc_html($value) . "\" />";
    }

    public function save_origin_field($order_id) {
        // phpcs:disable WordPress.Security.NonceVerification.Missing
        $post_data = wp_unslash($_POST);
        if (!empty($post_data['clonable_origin'])) {
            if ($this->is_valid_origin($post_data['clonable_origin'])) {
                update_post_meta($order_id, 'clonable_origin', sanitize_text_field($post_data['clonable_origin']));
            }
        }
        // phpcs:enable WordPress.Security.NonceVerification.Missing
    }

    /**
     * Shows the order origin in the admin overview
     * @param WC_Order $order
     * @return void
     */
    public function display_order_origin($order) {
        echo '<p><strong>Clonable Origin:</strong> ' . esc_html(get_post_meta($order->get_id(), 'clonable_origin', true)) . '</p>';
    }


    /** Change the return url to point to the clone site */
    public function filter_return_url($return_url, $order) {
        // Check if order is set
        if (!$order) {
            return $return_url;
        }

        // Check if order origin was recorded. No validation is needed here, as it is done when saving the attribute.
        $order_origin = get_post_meta($order->get_id(), 'clonable_origin', true);

        // phpcs:disable WordPress.Security.NonceVerification.Missing
        $server_data = $_SERVER;
        // phpcs:enable WordPress.Security.NonceVerification.Missing
        $host = $server_data['HTTP_HOST'];

        if ($order_origin) {
            // Make sure both have www. or not
            if (strpos($host, 'www.') !== 0 && strpos($order_origin, 'www.') === 0) {
                // origin has www., host not
                $order_origin = substr($order_origin, 4);
            } else if (strpos($host, 'www.') === 0 && strpos($order_origin, 'www.') !== 0) {
                // host has www., origin not
                $order_origin = 'www.' . $order_origin;
            }

            if (Functions::str_starts_with($return_url, '/')) {
                // Starts with slash, prepend domain
                return 'https://' . $order_origin . $return_url;
            } else if (Functions::str_starts_with($return_url, 'http')) {
                // Probably already starts with http, just replace domain
                return str_replace($host, $order_origin, $return_url);
            } else {
                // Some weird scenario, do nothing
                return $return_url;
            }


        } else {
            // Setting not set
            return $return_url;
        }
    }

    public function is_valid_origin($origin) {
        $input = get_option('clonable_woocommerce_allowed_origins', '[]');
        $allowed = Json::handle_input($input) ?? [];

        if (in_array($origin, $allowed)) {
            return true;
        } else {
            // No direct hit, toggle www.
            if (strpos($origin, 'www.') === 0) {
                return in_array(substr($origin, 4), $allowed);
            } else {
                return in_array('www.' . $origin, $allowed);
            }
        }
    }
}