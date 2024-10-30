<?php

namespace Clonable;

interface MiddlewareInterface {
    /**
     * Method for handling the actual middleware.
     * @return bool return true if the middleware succeeds
     */
    public function handle();
}