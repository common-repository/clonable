<?php

namespace Clonable;

use Clonable\Helpers\Functions;
use Clonable\Models\Site;
use Clonable\Routes\Router;
use Throwable;
use Clonable\Views\Layout;

/**
 * Used as the starting point of the Clonable plugin.
 */
class Bootstrap {
    private $name;
    private $menu_slug = 'clonable';
    private $menu_position = 54;
    private $menu_image_path = "images/clonable.png";

    private $layout;
    /** Full router object. @var Router $router */
    private $router;

    /* @var MiddlewareHandler $middleware_handler */
    private $middleware_handler;

    public function __construct($name, $version) {
        $this->name = $name;

        if (is_admin()) {
            if (Functions::is_clonable_page()) {
                $this->middleware_handler = new MiddlewareHandler();

                // Register all the routes for the plugin
                $this->router = new Router($this->middleware_handler);

                // Create the main layout for the plugin
                $this->layout = new Layout($this->menu_slug, $this->router, $this->middleware_handler);
                add_action('in_admin_footer', array($this, 'render_footer'));
            }

            // always add the Clonable menu item
            add_action('admin_menu', array($this, 'init'));
        }
    }

    /**
     * Method used when the WordPress admin_menu hook is called.
     * Responsible for adding the actual admin page to WordPress.
     * @return void
     */
    public function init() {
        // Register a new page in WordPress
        $hook = add_menu_page(
            $this->name . ' Settings',
            $this->name,
            'manage_options',
            $this->menu_slug,
            array($this, 'render_layout'),
            plugin_dir_url(__FILE__) . $this->menu_image_path,
            $this->menu_position
        );

        add_action('in_admin_header', array($this, 'remove_notices'));
        add_action("load-$hook", array($this, "validate_settings"));
    }

    /**
     * Hide those ugly messages from other plugins, stay out of the Clonable page
     * @return void
     */
    public function remove_notices() {
        try {
            if (Functions::is_clonable_page()) {
                remove_all_actions( 'user_admin_notices' );
                remove_all_actions('admin_notices');
            }
        } catch (\Exception $exception) {
            error_log("[Clonable] Error removing notices: {$exception->getMessage()}");
        }
    }

    /**
     * Used as the custom page hook.
     * Make sure the correct Controllers validation method is called.
     * Also does request method and CSRF validation.
     * @return void
     */
    public function validate_settings() {
        if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] === "POST") {
            try {
                // phpcs:disable WordPress.Security.NonceVerification.Missing
                $post_data = $_POST;
                // phpcs:enable WordPress.Security.NonceVerification.Missing
                if (!isset($post_data['_wpnonce']) || !wp_verify_nonce($post_data['_wpnonce'], 'clonable_options-options')) {
                    return; //CSRF validation failed, so do not save that garbage
                }
                // check user capabilities
                if (!current_user_can('manage_options')) {
                    return;
                }

                $routes = $this->router->get_routes();
                $get_data = wp_unslash($_GET);
                if (isset($get_data['tab']) && array_key_exists($get_data['tab'], $routes) && $this->middleware_handler->check_route($routes[$get_data['tab']])) {
                    $current_route = $get_data['tab'];
                } else {
                    $current_route = $this->router->default_route();
                }
                $controller = $routes[$current_route]["controller"];
                if (!class_exists($controller)) {
                    echo "<span>Controller class could not be found, check if the controller contains the singleton pattern.</span>";
                    return;
                }

                $controller::get_instance()->validate($post_data);

                // make sure router and page get re-evaluated after validation methods ran.
                $this->router->setup(true);
                $this->layout = new Layout($this->menu_slug, $this->router, $this->middleware_handler);
            } catch (Throwable $throwable) {
                error_log('[Clonable] Error while processing POST request: ' . $throwable->getMessage());
            }
        }
    }

    /**
     * Simple function for calling the correct render method
     * @return void
     */
    public function render_layout() {
        $this->layout->render();
    }

    public function render_footer() {
        try {
            if (Functions::is_clonable_page()) {
                $this->layout->render_footer();
            }
        } catch (\Exception $exception) {
            error_log("[Clonable] Error while setting footer: {$exception->getMessage()}");
        }
    }

    public function verification() {
        $optional_site = get_option("clonable_site", null);
        if ($optional_site === null || gettype($optional_site) !== 'array') {
            return; // if the site does not exist, just return.
        }
        $site = new Site($optional_site);
        if (!$site->is_validated()) {
            echo '<meta name="clonable-verification" content="' . esc_attr($site->get_validation_hash()) . '">';
        }
    }
}