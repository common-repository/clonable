<?php

namespace Clonable\Models;

class ClonableWooCommerce {
    const PAGE = "woocommerce";

    public static $fields = [
        "clonable_woocommerce_module_enabled" => [
            "render" => "clonable_woocommerce_module_enabled_field",
            "name" => "Enable WooCommerce module",
            "setting" => "clonable_woocommerce_module_enabled",
            "description" => "This is the master setting of the WooCommerce module, turn this off if you wish to disable this module.",
        ],
        "clonable_woocommerce_exclusions_enabled" => [
            "render" => "clonable_woocommerce_exclusions_enabled_field",
            "name" => "Enable product exclusions",
            "setting" => "clonable_product_exclusions_enabled",
            "description" => "Allows you to exclude product for specific clones.",
        ],
	    "clonable_woocommerce_analytics_enabled" => [
		    "render" => "clonable_woocommerce_analytics_enabled_field",
		    "name" => "Enable conversion tracking",
		    "setting" => "clonable_woocommerce_analytics_enabled",
            "description" => "Changes the redirect url of the payment provider to the domain of the last visited clone.",
	    ],
        "clonable_woocommerce_analytics" => [
            "render" => "clonable_woocommerce_analytics_field",
            "name" => "Valid redirect locations",
            "setting" => "clonable_woocommerce_allowed_origins",
        ],
    ];
}