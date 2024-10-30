<?php

namespace Clonable;

class MiddlewareHandler {
    /**
     * A key value pair array with middleware names mapped to classes.
     * @var MiddlewareInterface[]
     */
    private $concrete_classes;

    public function __construct() {
        $this->set_concrete_classes();
    }

    public function set_concrete_classes() {
        $this->concrete_classes = [
            'auth' => new Auth(),
            'cloned_site' => new ClonedSite(),
            'woocommerce' => new WooCommerce(),
        ];
    }

    /**
     * Applies the concrete middleware classes to the routes.
     * @param array $routes the application routes
     * @return array the routes with succeeded middlewares.
     * @deprecated
     * Applies the middleware to the given routes.
     */
    public function apply_middleware_to_routes($routes) {
        $valid_routes = [];
        foreach ($routes as $name => $route) {
            $middlewares = self::resolve_concrete_class(($route['middleware'] ?? null));
            $failed_middlewares = array_filter($middlewares, function ($middleware) {
                return !$middleware->handle();
            });
            if (empty($failed_middlewares)) {
                $valid_routes[$name] = $route;
            }
        }
        return $valid_routes;
    }

    /**
     * Checks the middleware of the given route. If at least one middleware fails, false will be returned.
     * @param array $route the application route to check
     * @return bool Whether the route is available using the given middleware
     */
    public function check_route($route): bool {
        $middlewares = self::resolve_concrete_class(($route['middleware'] ?? null));
        foreach ($middlewares as $middleware) {
            if (!$middleware->handle()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Turns the middleware names into concrete classes.
     * @param string[] $middlewares
     * @return MiddlewareInterface[]
     */
    private function resolve_concrete_class($middlewares) {
        if (empty($middlewares)) {
            return [];
        }

        $mapped_classes = array_map(function ($middleware_name) {
            return ($this->concrete_classes[$middleware_name] ?? null);
        }, $middlewares);
        return array_filter($mapped_classes, function ($mapped_class) {
            return ($mapped_class != null);
        });
    }
}