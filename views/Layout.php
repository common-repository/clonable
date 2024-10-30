<?php

namespace Clonable\Views;

use Clonable\Controllers\Controller;
use Clonable\Helpers\Functions;
use Clonable\Helpers\Html;
use Clonable\Helpers\Session;
use Clonable\MiddlewareHandler;
use Clonable\Objects\Notification;
use Clonable\Routes\Router;
use Throwable;

class Layout {
    /* @var array $tabs */
    private $tabs;

    /* @var array $tabs */
    private $all_routes;

    /* @var string $menu_slug */
    private $menu_slug;

    /* @var string $default_route */
    private $default_route;

    /* @var MiddlewareHandler $middleware_handler */
    private $middleware_handler;

    /**
     * Constructs the custom page layout and the routes for the plugin.
     * @param string $menu_slug
     * @param Router $router
     */
    public function __construct($menu_slug, $router, $middleware_handler) {
        $this->menu_slug = $menu_slug;
        $this->tabs = $router->get_tabs();
        $this->all_routes = $router->get_routes();
        $this->default_route = $router->default_route();
        $this->middleware_handler = $middleware_handler;
    }

    /**
     * The general render method of the entire page, for example: the tabs.
     * Makes sure the correct render method gets invoked based on the current tab.
     * @return void
     */
    public function render() {
        // check user capabilities
        if (!current_user_can( 'manage_options' ) ) {
            return;
        }

        if (isset($_GET)) {
            $get_data = $_GET;
            if (!empty($get_data['tab']) && array_key_exists($get_data['tab'], $this->all_routes)) {
                if ($this->middleware_handler->check_route($this->all_routes[$get_data['tab']])) {
                    $current_tab = $get_data['tab'];
                } else {
                    $current_tab = $this->default_route;
                }
            } else {
                $current_tab = $this->default_route;
            }
        } else {
            $current_tab = $this->default_route;
        }

        $menu_slug = $this->menu_slug;
        $route = $this->all_routes[$current_tab];
        /* @var Controller $controller */
        $controller = $route["controller"];

        if (isset($route) && $route['type'] === Router::PAGE_TYPE_TAB) {
            ?>
            <div class="wrap">
                <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
                <?php $this->notices(); ?>
                <nav class="nav-tab-wrapper">
                    <?php
                    foreach ($this->tabs as $tab => $content) {
                        $class = (($tab === $current_tab) ? 'nav-tab-active' : '');
                        echo "<a href='?page=" . esc_attr($menu_slug) . "&tab=" . esc_attr($tab) . "' class='nav-tab " . esc_attr($class) . "'>" . esc_html($content['name']) . "</a>";
                    }
                    ?>
                </nav>

                <div class="tab-content">
                    <?php
                        Html::include_css('clonable-global.css');
                        $this->render_content($controller, $current_tab);
                    ?>
                </div>
            </div>
            <?php
        } else {
            // full page show don't show tabs
            $this->render_content($controller, $current_tab);
        }
    }

    /**
     * Handles the actual rendering of the view
     * @param $controller Controller which controllers should render the view
     * @param $current_route string just for showing a nicer error message
     * @return void invokes the controller view render method.
     */
    private function render_content($controller, $current_route) {
        try {
            /* @var ViewInterface $view */
            $view = $controller::get_instance()->get_view();
            if ($view == null) {
                echo "No view was configured for '" . esc_html($current_route) . "' tab<br/>";
            } else {
                $view->render();
                Session::clear_validation_data();
            }
        } catch (Throwable $throwable) {
            echo esc_html($throwable->getMessage());
        }
    }

    /**
     * Renders content into the WordPress admin footer
     * @return void
     */
    public function render_footer() {
        ?>
            <div class="tip">
                <p style="color: #ff541e">
                    Need any help with the plugin? Try out our
                    <a target="_blank" href="https://kb.clonable.net/introduction/setup/platforms/wordpress">knowledgebase</a>
                    or view our
                    <a target="_blank" href="https://www.youtube.com/@clonable">YouTube channel.</a>
                </p>
            </div>
        <?php
    }

    public function notices() {
        foreach (Notification::retrieve() as $notification) {
            $type = $notification['type'];
            echo "<div class=\"notice notice-$type is-dismissible\"><p>";
            echo $notification['content'];
            echo '</p></div>';
        }
    }
}