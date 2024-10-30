<?php

namespace Clonable\Objects;

use Clonable\Helpers\Html;
use Clonable\Helpers\Locales;

/**
 * This object represents a product exclusion group (a.k.a. term).
 * This term is directly linked to a Clonable clone.
 * Each Clonable clone that has synced with WordPress will have such an object.
 */
class ExcludedProductTerm {
    /* @var $id string */
    private $id;

    /* @var $lang_code string */
    private $lang_code;

    /* @var $column string */
    private $column;

    /**
     * Construct an ExcludedProductTerm object.
     * @param $id string the id of the object.
     * @param $lang_code string the locale code.
     */
    public function __construct($id, $lang_code) {
        $this->id = $id;
        $this->lang_code = $lang_code;
        $this->column = "clone-{$id}";

        // adds the column
        add_filter('manage_edit-product_columns', array($this, 'add_column_to_table'), 20);
        // adds the checkbox for each row in the table
        add_action('manage_product_posts_custom_column', array($this, 'fill_table_column'));
        // load the jquery script
        add_action( 'admin_footer', array($this, 'load_jquery'));
        // handles the jquery save event
        add_action(('wp_ajax_clonable_save_product_inclusion_' . $id), array($this, "save"));
    }

    /**
     * The Jquery save method that's called when the checkbox state changes in the table.
     * @return void
     */
    public function save() {
        if (!current_user_can('manage_options')) {
            return;
        }

        // phpcs:disable WordPress.Security.NonceVerification.Missing
        $post_data = $_POST;
        // phpcs:enable WordPress.Security.NonceVerification.Missing

        check_ajax_referer('clonable-change-product', 'ajax_nonce');
        $term = sprintf(ClonableConfig::WOOCOMMERCE_QUERY_ID, $this->id);
        if ($post_data['value'] === 'no') {
            wp_set_post_terms($post_data['product_id'], $term, ClonableConfig::WOOCOMMERCE_TAXONOMY, true);
        } else {
            wp_remove_object_terms($post_data['product_id'], $term, ClonableConfig::WOOCOMMERCE_TAXONOMY);
        }
        echo "saved";
        die();
    }

    /**
     * Adds a new column with the flag of the country to the table.
     * @param $columns
     * @return mixed
     */
    public function add_column_to_table($columns) {
        $region = Locales::get_region($this->lang_code);
        $display_name = Locales::get_display_name($this->lang_code);

        $image = "<i title='Available in $display_name' class='fflag-$region fflag ff-sm' style='margin-top: 2px'></i>";
        $screen_reader = "<span class='screen-reader-text'>$display_name</span>";

        $columns[$this->column] = $image . $screen_reader;
        return $columns;
    }

    /**
     * Makes sure the checkbox state is correctly set for each problem.
     * @param $column
     * @return void
     */
    public function fill_table_column($column) {
        if ($column == $this->column) {
            $excluded_terms = wp_get_post_terms(get_the_ID(), ClonableConfig::WOOCOMMERCE_TAXONOMY);
            // due to the nature of slugs in WordPress, the Clonable id will automatically be lower case.
            // chance of a collision small enough that we can ignore this issue.
            $clone_id = strtolower($this->id);
            $is_excluded = in_array(sprintf(ClonableConfig::WOOCOMMERCE_QUERY_ID, $clone_id), array_column($excluded_terms, 'slug'));
            $checkbox_value = checked( 'yes', (($is_excluded) ? 'no' : 'yes'), false);
            $product_id = get_the_ID();
            $nonce_value = wp_create_nonce("clonable-change-product");
            ?>
                <input type='checkbox' class='clonable-language-checkbox'
                       data-nonce='<?php echo esc_attr($nonce_value); ?>'
                       data-clone='<?php echo esc_attr($this->id); ?>'
                       data-product-id='<?php echo esc_attr($product_id); ?>'
                    <?php echo esc_attr($checkbox_value); ?> />
            <?php
        }
    }

    /**
     * This loads the script that binds the on state change of the checkbox to the save method.
     * @return void
     */
    public function load_jquery() {
        Html::include_jquery_script('woocommerce-clone-field.js');
    }
}