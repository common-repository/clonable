<?php

namespace Clonable;

use Clonable\Services\ApiService;

class Auth implements MiddlewareInterface {
    private $has_succeeded = false;

    public function handle() {
        if ($this->has_succeeded) {
            return true;
        }

        $api_key = get_option('clonable_api_key');
        if (!$api_key) {
            return false;
        }

        if (!preg_match("/^clonable_[a-z0-9]{8}_[a-z0-9]{24}$/", $api_key)) {
            return false;
        }

        $response = ApiService::get_user($api_key);
        if (empty($response['user'])) {
            return false;
        }

        $this->has_succeeded = true;
        return true;
    }
}