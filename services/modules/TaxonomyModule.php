<?php

namespace Clonable\Services\Modules;

use Clonable\Helpers\Locales;
use Clonable\Objects\ClonableConfig;
use Clonable\Traits\WooCommerceCheck;

defined( 'ABSPATH' ) || exit;

class TaxonomyModule {
    use WooCommerceCheck;

    public function __construct() {
        // For now, we only add taxonomies when WooCommerce is installed.
        if(!$this->woocommerce_is_installed()) {
            return;
        }

        add_action('init', array($this, 'register_clonable_taxonomies'));
    }

    /**
     * Register the custom exclude_products taxonomy that Clonable uses for product exclusions on specific clones.
     * The terms of the taxonomy are automatically generated based on the information of the connected clones.
     * @return void
     */
    public function register_clonable_taxonomies() {
        $args = array(
            'labels'            => array(
                // these are the strings that are actually visible, there are a lot more, be you don't see them anyway.
                'name'              => __( 'Clonable excluded products', 'clonable'),
                'singular_name'     => __( 'Exclusions', 'clonable'),
                'search_items'      => __( 'Search product exclusions', 'clonable'),
                'popular_items'     => __( 'Popular product exclusions', 'clonable'),
                'all_items'         => __( 'All product exclusions', 'clonable'),
                'menu_name'         => __( 'Product exclusions', 'clonable'),
                'choose_from_most_used ' => __('Choose from the most used exclusions', 'clonable'),
            ),
            'capabilities' => array(
                'manage_terms' => 'manage_categories',
                'edit_terms' => '',     // empty string disables editing of taxonomy and terms
                'delete_terms' => '',   // empty string disables deletion of taxonomy and terms.
                'assign_terms' => 'edit_posts',
            ),
            'hierarchical'      => false,   // non-hierarchical because of issues with the exclusion query
            'show_ui'           => WP_DEBUG,    // shows the admin pages if debug is enabled
            'show_admin_column' => false,   // don't show in tables (we have custom function for that)
            'show_in_quick_edit ' => false, // don't show in quick/bulk edit panel
            'query_var'         => false,           // don't allow users to visit the frontend page for the taxonomy
            'publicly_queryable'         => false,  // don't allow users to visit the frontend page for the taxonomy
            'rewrite' => ['slug' => ClonableConfig::WOOCOMMERCE_TAXONOMY], // just looks a little nicer
        );

        // register the actual taxonomy
        $taxonomy = register_taxonomy(ClonableConfig::WOOCOMMERCE_TAXONOMY, ['product'], $args);
        foreach (ClonableConfig::get_clones() as $clone) {
            $name = Locales::get_display_name($clone['lang_code']);
            if (term_exists($name, ClonableConfig::WOOCOMMERCE_TAXONOMY)) {
                continue;
            }

            // create a term for each clone, terms are identified by their slug.
            wp_insert_term(sprintf("Excluded from %s", $name), $taxonomy->name, array(
                'description' => "This product is not available for $name.",
                'slug'        => sprintf(ClonableConfig::WOOCOMMERCE_QUERY_ID, $clone['id']),
            ));
        }

        // also create a term for the original site, enables user to have clone specific products.
        $site = ClonableConfig::get_site();
        if ($site != null) {
            $name = Locales::get_display_name($site->get_locale());
            if (!term_exists($name, ClonableConfig::WOOCOMMERCE_TAXONOMY)) {
                wp_insert_term(sprintf("Excluded from %s", $name), $taxonomy->name, array(
                    'description' => "This is the original shop, this product is not available for $name.",
                    'slug'        => sprintf(ClonableConfig::WOOCOMMERCE_QUERY_ID, $site->get_id()),
                ));
            }
        }
    }
}