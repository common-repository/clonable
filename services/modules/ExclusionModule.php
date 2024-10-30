<?php

namespace Clonable\Services\Modules;

use Clonable\Helpers\Html;
use Clonable\Objects\ClonableConfig;
use Clonable\Objects\ExcludedProductTerm;
use Clonable\Traits\WooCommerceCheck;
use WP_Query;

defined( 'ABSPATH' ) || exit;

class ExclusionModule {
    use WooCommerceCheck;

    public function __construct() {
        if(!$this->woocommerce_is_installed()) {
            return;
        }

        add_action('init', [$this, 'init']);
    }

    public function init() {
        if (is_admin()) {
            // If it's an admin page, then render the checkboxes for the tables, and do not apply the filter.
            // If we did apply the filter, some of the items could be invisible for admins.
            $site = ClonableConfig::get_site();
            if ($site == null) {
                return;
            }
            add_action('admin_enqueue_scripts', [$this, 'queue_clonable_admin_scripts']);
            $site_term_ui = new ExcludedProductTerm($site->get_id(), $site->get_locale());
            foreach (ClonableConfig::get_clones() as $clone) {
                // Each clone corresponds to an excluded term.
                $clone_term_ui = new ExcludedProductTerm($clone['id'], $clone['lang_code']);
            }
        } else {
            // if it's a shop page, then apply the custom taxonomy filter.
            add_action('pre_get_posts', [$this, 'apply_exclusion_taxonomy_filter'], 16);
            add_action('wp', [$this, 'product_exclusion_redirect']);
            add_action('woocommerce_product_is_visible', [$this, 'product_visibility_filter'], 10 , 2);
        }
    }

    /**
     * Adds the css scripts that are used on the WooCommerce pages.
     * @return void
     */
    public function queue_clonable_admin_scripts() {
        Html::flags();
        Html::include_css('clonable-woocommerce.css');
    }

    private function get_current_id() {
        $current_domain = ClonableConfig::current_clonable_domain();
        $site = ClonableConfig::get_site();

        if ($current_domain === ClonableConfig::ORIGINAL_SHOP && !empty($site)) {
            // assume we are on the original site.
            return strtolower($site->get_id());
        }

        foreach (ClonableConfig::get_clones() as $clone) {
            $clone_domain = $clone['domain'] . ($clone['subfolder_clone'] ?? '');
            if ($clone_domain === $current_domain) {
                return strtolower($clone['id']);
            }
        }
        return null;
    }

    /**
     * Applies the exclusion taxonomy query on the post query.
     * @param $query WP_Query
     * @return void
     */
    public function apply_exclusion_taxonomy_filter($query) {
        $current_id = $this->get_current_id();

        // if the current id is null, then abandon the tax query.
        if ($current_id == null || $query->tax_query == null) {
            return;
        }

        // term is equal to the clone or site id with the taxonomy prefix
        $slug = sprintf(ClonableConfig::WOOCOMMERCE_QUERY_ID, $current_id);
        // apparently the tax_query can be null, so check it.
        $query->tax_query->queries[] = [
            "taxonomy" => ClonableConfig::WOOCOMMERCE_TAXONOMY,
            "field" => "slug",
            "operator" => "NOT IN",
            "terms" => $slug,
        ];
        $query->query_vars['tax_query'] = $query->tax_query->queries;
    }

    /**
     * Add redirect logic to product pages, this will ensure that excluded products are
     * not visited incidentally by a specific permalink or wierd filter.
     * @return void
     */
    public function product_exclusion_redirect() {
        // before actually visiting the product page, check if it's not excluded
        if (is_singular() && is_single() && get_post_type() === 'product') {
            $current_id = $this->get_current_id();
            $excluded_ids = get_the_terms(get_the_ID(), ClonableConfig::WOOCOMMERCE_TAXONOMY);
            $term = sprintf(ClonableConfig::WOOCOMMERCE_QUERY_ID, strtolower($current_id));
            $product_is_not_available = !($excluded_ids === false) && in_array($term, array_column($excluded_ids, 'slug'));
            // if the product is not available for a clone, then redirect to the home page;
            // if you're on a subfolder, then the redirect will send you to the base subfolder
            if ($product_is_not_available) {
                wp_safe_redirect('/', 302, 'WordPress - Clonable');
                exit();
            }
        }
    }

    public function product_visibility_filter($visible, $id) {
        $excluded_ids = get_the_terms($id, ClonableConfig::WOOCOMMERCE_TAXONOMY);
        $current_id = $this->get_current_id();
        $term = sprintf(ClonableConfig::WOOCOMMERCE_QUERY_ID, strtolower($current_id));
        $product_is_not_available = !($excluded_ids === false) && in_array($term, array_column($excluded_ids, 'slug'));
        if ($product_is_not_available) {
            return false;
        }
        return $visible;
    }
}