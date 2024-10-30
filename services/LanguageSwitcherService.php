<?php

namespace Clonable\Services;

use Clonable\Objects\ClonableConfig;

/**
 * Handles the rendering of the language switcher on the page.
 * Uses the values set by the language switcher settings.
 */
class LanguageSwitcherService {
    /**
     * Prints out the language switcher script tag and the corresponding JavaScript settings.
     * @return void echos out the scripts.
     */
    public function print_language_switcher() {
        $render_language_switcher = get_option('clonable_enable_language_switcher', 'off') === 'on';
        if ($render_language_switcher) {
            $endpoint = ClonableConfig::MODULES_ENDPOINT;

            // phpcs:disable WordPress.WP.EnqueuedResources
            echo "<!-- Start Clonable Language Switcher -->\n";
            echo "<script>\n";
            $config = $this->create_clonable_config();
            echo ent2ncr($config);
            echo "</script>\n";
            echo "<script defer src=\"https://" . esc_attr($endpoint) . "/language-switcher/js/init.js?v=1.2.0\"></script>\n";
            echo "<!-- End Clonable Language Switcher -->\n";
            // phpcs:enable WordPress.WP.EnqueuedResources
        }
    }

    private function create_clonable_config() {
        // Display information
        $show_flag = (get_option('clonable_show_flag', 'on') === 'on') ? 1 : 0;
        $rounded_flag = (get_option('clonable_rounded_flag', 'on') === 'on') ? 1 : 0;
        $show_target_language = (get_option('clonable_show_text', 'on') === 'on') ? 1 : 0;
        // Colors
        $background_color = get_option('clonable_background_color', '#ffffff');
        $hover_background_color = get_option('clonable_hover_background_color', '#efefef');
        // Placement and size
        $size = get_option('clonable_size', 'md');
        $position = get_option('clonable_position', 'bottom-left');

        $languages = get_option('clonable_langswitch_data', []);
        $languages = str_replace('.', 'U+002E', $languages);

        return "
            const clonableLSC = {
                'backgroundColor': '$background_color',
                'backgroundHoverColor': '$hover_background_color',
                'textColor': '#000000',
                'menuTextColor': '#000000',
                'hasRoundedFlags': ($rounded_flag === 1),
                'showFlag': ($show_flag === 1),
                'showName': ($show_target_language === 1),
                'showCountry': false,
                'size': '$size',
                'position': '$position',
                'isStatic': true,
                'placement': 'before',
                'queryInput': '',
                'hasHoverTrigger': true,
                'hasClickTrigger': true,
                'languages': $languages
            }
        ";
    }
}