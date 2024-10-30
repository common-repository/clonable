<?php

namespace Clonable\Traits;

/**
 * Contains helper functions for dealing with WooCommerceView stuff
 */
trait WooCommerceCheck {
    /**
     * Checks if WooCommerce is installed.
     * @return bool true if WooCommerce is installed
     */
    public function woocommerce_is_installed() {
        // Test to see if WooCommerceView is active (including network activated).
        return $this->check_plugin_by_file('woocommerce/woocommerce.php');
    }

    /**
     * Check if the Buckaroo payment provider is installed
     * @return bool
     */
    public function buckaroo_installed() {
        return $this->check_plugin_by_file('wc-buckaroo-bpe-gateway/index.php');
    }

    private function check_plugin_by_file($plugin) {
        $plugin_path = trailingslashit(WP_PLUGIN_DIR) . $plugin;

        // A customer reported an error, so we built this failsafe
        $active_array = [];
        $network_array = [];
        if (function_exists('wp_get_active_and_valid_plugins')) {
            $active_array = wp_get_active_and_valid_plugins();
        }
        if (function_exists('wp_get_active_network_plugins')) {
            $network_array = wp_get_active_network_plugins();
        }

        return (in_array($plugin_path, $active_array) || in_array($plugin_path, $network_array));
    }
}