<?php

namespace Clonable;

use Clonable\Traits\WooCommerceCheck;

class WooCommerce implements MiddlewareInterface {
    use WooCommerceCheck;

    public function handle() {
        return $this->woocommerce_is_installed();
    }
}