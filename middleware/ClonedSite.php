<?php

namespace Clonable;

class ClonedSite implements MiddlewareInterface {
    public function handle() {
        $cached_site = get_option("clonable_site");
        return !empty($cached_site) && (gettype($cached_site) == 'array');
    }
}