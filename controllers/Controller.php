<?php

namespace Clonable\Controllers;

abstract class Controller {
    protected $view;

    public static abstract function get_instance();

    public function get_view() {
        return $this->view;
    }

    public abstract function validate($request);
}