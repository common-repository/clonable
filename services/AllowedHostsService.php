<?php

namespace Clonable\Services;

use Clonable\Objects\ClonableConfig;

class AllowedHostsService {
    public function __construct() {
        if (get_option('clonable_allowed_hosts_enabled', 'on') !== 'on') {
            return;
        }
        add_filter('allowed_redirect_hosts', [$this, 'add_allowed_hosts'], 10, 2);
    }

    /**
     * Fixes safe_redirect_method for WordPress
     * @param array $hosts
     * @param string $host
     * @return array
     */
    public function add_allowed_hosts(array $hosts, string $host) {
        try {
            $clones = ClonableConfig::get_clones();
            foreach ($clones as $clone) {
                if (empty($clone['domain'])) {
                    continue;
                }

                if (!in_array($clone['domain'], $hosts)) {
                    $hosts[] = $clone['domain'];
                    $hosts[] = 'www.' . $clone['domain'];
                }
            }
        } catch (\Exception $exception) {
            error_log("[Clonable] Error setting allowed hosts: {$exception->getMessage()}");
        }
        return $hosts;
    }
}