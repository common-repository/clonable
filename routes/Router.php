<?php

namespace Clonable\Routes;

use Clonable\Controllers\SettingController;
use Clonable\Controllers\LandingPageController;
use Clonable\Controllers\LanguageSwitcherController;
use Clonable\Controllers\LanguageTagController;
use Clonable\Controllers\WooCommerceController;
use Clonable\Controllers\DashboardController;
use Clonable\MiddlewareHandler;
use Clonable\Traits\WooCommerceCheck;

/**
 * Class that contains all the available tabs/routes for the Clonable plugin
 */
class Router {
    use WooCommerceCheck;

    const PAGE_TYPE_TAB = "tab";
    const PAGE_TYPE_FULL_PAGE = "full-page";

    /** Handles the middlewares. @var MiddlewareHandler $middleware_handler */
    private $middleware_handler;

    /**
     * Keep this private, routes should not be modified by any other class.
     * @var array[] associative array with all the routes
     */
    private $routes;

    public function __construct($middleware_handler) {
        $this->middleware_handler = $middleware_handler;
        $this->setup();
    }

    /**
     * Initiates the routes of the application.
     * Can also be used the re-initiate the routes after some settings have been saved.
     * @return void sets internal parameters
     */
    public function setup($reset_middleware = false) {
        if ($reset_middleware) {
            // resets cached values in concrete middleware classes
            $this->middleware_handler->set_concrete_classes();
        }

        /**
         * Key is used as the identifier/slug of the tab
         * name is the name of the tab
         * controller is a Controller class name
         */
        $routes = [
            'setup' => [
                'name' => 'Clonable Landing Page',
                'controller' => LandingPageController::class,
                'type' => $this::PAGE_TYPE_FULL_PAGE,
            ],
        ];

        $routes['dashboard'] = [
            'name' => 'Dashboard',
            'controller' => DashboardController::class,
            'type' => $this::PAGE_TYPE_TAB,
            'middleware' => ['auth'],
        ];

        // these settings only are available when a site id is present.
        // this can either be achieved by creating or connecting a site.
        $routes['language-tag'] = [
            'name' => 'Language Tags',
            'controller' => LanguageTagController::class,
            'type' => $this::PAGE_TYPE_TAB,
            'middleware' => ['auth', 'cloned_site'],
        ];
        $routes['language-switcher'] = [
            'name' => 'Language Switcher',
            'controller' => LanguageSwitcherController::class,
            'type' => $this::PAGE_TYPE_TAB,
            'middleware' => ['auth', 'cloned_site'],
        ];

        // only add analytics route for WooCommerce clones
        $routes['woocommerce'] = [
            'name' => 'WooCommerce',
            'controller' => WooCommerceController::class,
            'type' => $this::PAGE_TYPE_TAB,
            'middleware' => ['auth', 'cloned_site', 'woocommerce'],
        ];

        $routes['settings'] = [
            'name' => 'Settings',
            'controller' => SettingController::class,
            'type' => $this::PAGE_TYPE_TAB,
            'middleware' => ['auth'],
        ];

        $this->routes = $routes;
    }

    public function default_route() {
        if (!$this->api_key_set()) {
            return "setup";
        } else {
            return "dashboard";
        }
    }

    /**
     * Getter for the routes
     * @return array[] all custom clonable tab routes
     */
    public function get_routes() {
        return $this->routes;
    }

    public function get_tabs() {
        return array_filter($this->routes, function ($route) {
            return $route['type'] === $this::PAGE_TYPE_TAB;
        });
    }

    private function api_key_set() {
        $api_key = get_option('clonable_api_key');
        if (!$api_key) {
            return false;
        }

        if (!preg_match("/^clonable_[a-z0-9]{8}_[a-z0-9]{24}$/", $api_key)) {
            return false;
        }

        return true;
    }
}