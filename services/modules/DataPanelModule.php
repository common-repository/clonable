<?php

namespace Clonable\Services\Modules;

use Clonable\Helpers\Functions;
use Clonable\Helpers\Locales;
use Clonable\Objects\ClonableConfig;

defined( 'ABSPATH' ) || exit;

class DataPanelModule {
    public function __construct() {
        add_action('woocommerce_product_data_tabs', [$this, 'clonable_add_product_data_tab']);
        add_action('woocommerce_product_data_panels', [$this, 'clonable_add_product_data_panels']);

        add_action('woocommerce_process_product_meta', [$this, 'clonable_save_data_panels']);
    }

    /**
     * Add the product data panel tab, does not add the content.
     * @param $tabs
     * @return mixed
     */
    public function clonable_add_product_data_tab($tabs) {
        $tabs['clonable'] = [
            'label'    => __( 'Clonable', 'clonable' ),
            'target'   => 'clonable_product_data_panel',
            'priority' => 100,
        ];
        return $tabs;
    }

    /**
     * Create the content of the data panel.
     * @return void
     */
    public function clonable_add_product_data_panels() {
        $excluded_ids = get_the_terms(get_the_ID(), ClonableConfig::WOOCOMMERCE_TAXONOMY);
        echo "<div id='clonable_product_data_panel' class='panel woocommerce_options_panel hidden'>";

        // generate a checkbox for the original shop.
        $site = ClonableConfig::get_site();
        if ($site != null) {
            $term = sprintf(ClonableConfig::WOOCOMMERCE_QUERY_ID, strtolower($site->get_id()));
            $this->create_checkbox($term, $site->get_locale(), $excluded_ids);
        }

        // generate a checkbox for each clone.
        foreach (ClonableConfig::get_clones() as $clone) {
            $term = sprintf(ClonableConfig::WOOCOMMERCE_QUERY_ID, strtolower($clone['id']));
            $this->create_checkbox($term, $clone['lang_code'], $excluded_ids);
        }
        echo "</div>";
    }

    /**
     * Create the actual checkboxes for the terms.
     * @param $term string the slug of the term.
     * @param $locale string the locale of the term (used for the flag)
     * @param $excluded_ids array the excluded terms.
     * @return void
     */
    private function create_checkbox($term, $locale, $excluded_ids) {
        $region = Locales::get_region($locale);
        $image = "<i class='fflag-$region fflag ff-sm' style='margin-top: 2px'></i>";
        // some logic for if the checkbox should be checked or not.
        $value = !($excluded_ids === false) && in_array($term, array_column($excluded_ids, 'slug'));
        woocommerce_wp_checkbox([
            'id'          => "cb-$term",
            'value'       => (($value) ? 'yes' : 'no'),
            'label'       => $image,
            /* translators: %s - The display name of the locale */
            'description' => sprintf(__('Hide product on %s', 'clonable'), Locales::get_display_name($locale)),
        ]);
    }

    /**
     * Parse and save the edited data from the Clonable data panel.
     * @param $post_id int the id of the post
     * @return void
     */
    public function clonable_save_data_panels($post_id) {

        // phpcs:disable WordPress.Security.NonceVerification.Missing
        // get all the post value keys, and filter he clonable checkboxes.
        $submitted_values = array_filter(array_keys($_POST), function ($field) {
            return Functions::str_starts_with($field, 'cb-clonable-excluded');
        });
        // phpcs:enable WordPress.Security.NonceVerification.Missing

        // strip the cb- from the checkbox keys, then the remaining value is the same as the term.
        // then store it as a comma seperated string.
        $terms = implode(',', array_map(function ($value) {
            return str_replace('cb-', '', $value);
        }, $submitted_values));

        wp_set_post_terms($post_id, $terms, ClonableConfig::WOOCOMMERCE_TAXONOMY, false);
    }
}