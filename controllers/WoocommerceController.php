<?php

namespace Clonable\Controllers;

use Clonable\Helpers\Functions;
use Clonable\Helpers\Json;
use Clonable\Models\ClonableWooCommerce;
use Clonable\Traits\Validation;
use Clonable\Views\WoocommerceView;

/**
 * Controller for actions in the analytics view.
 */
class WoocommerceController extends Controller {
    use Validation;
    //<editor-fold desc="Singleton pattern">
    private static $instance = null;

    private function __construct() {
        // Keep constructor private
        $this->view = new WoocommerceView();
    }

    public static function get_instance() {
        if (self::$instance == null) {
            self::$instance = new WoocommerceController();
        }
        return self::$instance;
    }
    //</editor-fold>

    public function validate($request) {
        $this->validate_fields($request, ClonableWooCommerce::$fields);
    }

    public function clonable_woocommerce_analytics_validate($input) {
        if (empty($input)) {
            return $input;
        }
        $old_input = get_option('clonable_woocommerce_allowed_origins');

        $json_input = Json::handle_input($input);
        foreach ($json_input as $location) {
            $split_location = explode('/', $location, 2);
            $domain = $split_location[0];

            // Validate each domain name
            if (!filter_var($domain, FILTER_VALIDATE_DOMAIN, ['flags' => FILTER_FLAG_HOSTNAME])) {
                add_settings_error('clonable_woocommerce_allowed_origins', 'err_invalid_domain', "The domain " . esc_html($domain) . " is invalid. Enter just the domain, without http://.");
                return $old_input;
            }

            // Make sure the location does not end in a /
            if (Functions::str_ends_with($location, '/')) {
                add_settings_error('clonable_woocommerce_allowed_origins', 'err_invalid_end', "The location " . esc_html($location) . "  should not end with a /");
                return $old_input;
            }
        }


        if (empty($json_input)) {
            return null;
        }
        return Json::handle_output(json_encode($json_input));
    }

	public function clonable_woocommerce_analytics_enabled_validate($input) {
		return $this->validate_checkbox($input, "clonable_woocommerce_analytics_enabled");
	}

    public function clonable_woocommerce_exclusions_enabled_validate($input) {
        return $this->validate_checkbox($input, "clonable_product_exclusions_enabled");
    }

    public function clonable_woocommerce_module_enabled_validate($input) {
        return $this->validate_checkbox($input, "clonable_woocommerce_module_enabled");
    }
}