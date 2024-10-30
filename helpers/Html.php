<?php

namespace Clonable\Helpers;

class Html {
    /**
     * Enqueues a custom CSS file from the /views/css directory
     * @param $file string the name of the file excluding the views/css directory, should not start with a /.
     * @return void does not return, enqueues the given css file
     */
    public static function include_css($file) {
        $full_path = (plugin_dir_url(__FILE__) . '../views/css/' . $file);
        wp_enqueue_style(
            $full_path,
            plugins_url(('/../views/css/' . $file), __FILE__),
            array(),
            "1.0.0"
        );
    }

    /**
     * Enqueues a custom JavaScript file from the /views/scripts directory
     * @param $file string the name of the file excluding the views/scripts directory, should not start with a /.
     * @return void does not return, enqueues the given script
     */
    public static function include_js($file) {
        wp_enqueue_script(
            $file,
            plugins_url(('/../views/scripts/' . $file), __FILE__),
            array(),
            "1.0.0"
        );
    }

    /**
     * Include a CDN link to a script or link tag in a WordPress save way.
     * @param $name string the visual name of the script (should be unique)
     * @param $link string the link to the cdn
     * @param $is_css string whether the cdn is css (if not javascript is the default)
     * @param $defer string if a script tag should be deferred
     * @return void does not return, enqueues the given CDN links
     */
    public static function include_cdn($name, $link, $is_css = false, $defer = false) {
        if ($is_css) {
            wp_register_style($name, $link, array(), "1.0.0");
            wp_enqueue_style($name);
        } else {
            wp_register_script($name, $link, array(), "1.0.0", (($defer) ? array('strategy' => "defer") : array()));
            wp_enqueue_script($name);
        }
    }

    /**
     * Enqueues a custom JavaScript file from the /views/scripts directory.
     * This function should be used for files that have Jquery dependencies.
     * @param $file string the name of the file excluding the views/scripts directory, should not start with a /.
     * @param $options array array of additional script loading strategies
     * @return void does not return, enqueues the given script
     */
    public static function include_jquery_script($file, $options = array()) {
        $options = array_merge(array('in_footer' => true), $options);
        wp_enqueue_script(
            $file,
            plugins_url(('/../views/scripts/' . $file), __FILE__),
            array('jquery'),
            "1.0.0",
            $options
        );
    }

    /**
     * Includes FreakFlags into the page.
     * @return void does not return, enqueues the freakflags.css file
     */
    public static function flags() {
        self::include_css("freakflags.css");
        $image = (plugin_dir_url(__FILE__) . '../images/flags.png');
        ?>
        <style>
            .fflag {
                background-image: url(<?php echo esc_attr($image); ?>);
            }
        </style>
        <?php
    }

    /**
     * Includes Fomantic dropdown into the page
     * @return void does not return, enqueues all the required fomantic css and script files
     */
    public static function include_fomantic_dropdown() {
        self::include_css("fomantic-dropdown.css");
        self::include_css("fomantic-transition.css");
        self::flags();
        self::include_jquery_script("fomantic-dropdown.js");
        self::include_jquery_script("fomantic-transition.js");
        self::include_jquery_script("onload.js", array('strategy' => 'defer'));
    }

    /**
     * Includes Alpine.js into the page
     * @return void does not return, enqueues all the required Alpine css and script files
     */
    public static function include_alpine() {
        self::include_jquery_script("alpine.js", array('in_footer' => false));
        self::include_cdn("Alpine-Tooltip.js", "https://cdn.jsdelivr.net/npm/@ryangjchandler/alpine-tooltip@1.x.x/dist/cdn.min.js", false, true);
        self::include_cdn("Tippy.css", "https://unpkg.com/tippy.js@6/dist/tippy.css", true);
        self::include_cdn("Alpine.js", "https://unpkg.com/alpinejs", false, true);
    }
}