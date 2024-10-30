<?php

namespace Clonable\Services\Modules;

use Clonable\Helpers\Functions;
use Clonable\Helpers\Locales;
use Clonable\Objects\ClonableConfig;

class ProductImporterModule {
    public function __construct() {
        add_filter('woocommerce_csv_product_import_mapping_options', [$this, 'add_column_to_importer']);
        add_filter('woocommerce_csv_product_import_mapping_default_columns', [$this, 'add_column_to_mapping_screen']);
        add_filter('woocommerce_product_import_pre_insert_product_object', [$this, 'process_import'], 10, 2);

        add_filter('woocommerce_product_export_column_names', [$this, 'add_export_column']);
        add_filter('woocommerce_product_export_product_default_columns', [$this, 'add_export_column']);

        foreach (ClonableConfig::get_clones() as $clone) {
            $term = sprintf(ClonableConfig::WOOCOMMERCE_QUERY_ID, strtolower($clone['id']));
            add_filter(('woocommerce_product_export_product_column_' . $term), function ($value, $product) use ($term) {
                $excluded_ids = wp_get_post_terms($product->id, ClonableConfig::WOOCOMMERCE_TAXONOMY);
                $is_excluded = in_array($term, array_column($excluded_ids, 'slug'));
                return ($is_excluded) ? 'yes' : 'no';
            }, 10, 2);
        }
    }

    public function add_column_to_importer($options) {
        foreach (ClonableConfig::get_clones() as $clone) {
            $name = Locales::get_display_name($clone['lang_code']);
            $term = sprintf(ClonableConfig::WOOCOMMERCE_QUERY_ID, strtolower($clone['id']));
            $options[$term] = sprintf("Excluded from %s", $name);
        }
        return $options;
    }

    public function add_column_to_mapping_screen($columns) {
        foreach (ClonableConfig::get_clones() as $clone) {
            $term = sprintf(ClonableConfig::WOOCOMMERCE_QUERY_ID, strtolower($clone['id']));
            $name = Locales::get_display_name($clone['lang_code']);
            $columns[sprintf("Excluded from %s", $name)] = $term;
            $lower_name = strtolower($name);
            $columns[sprintf("excluded from %s", $lower_name)] = $term;
        }
        return $columns;
    }

    public function process_import($object, $data) {
        $excluded_terms = array_filter(array_keys($data), function ($key) use ($data) {
            // get all the term strings
            if (Functions::str_starts_with($key, 'clonable-excluded-')) {
                $value = ($data[$key] ?? false); // get the value of the excluded term
                // check for a truthy for if the product is excluded
                if ($value === 'yes' || $value === 'true' || $value === '1' || $value === 1 || $value == 'on') {
                    return true;
                }
            }
            return false;
        });

        $terms = implode(',', $excluded_terms);
        wp_set_post_terms($object->id, $terms, ClonableConfig::WOOCOMMERCE_TAXONOMY, false);
        return $object;
    }

    public function add_export_column($columns) {
        foreach (ClonableConfig::get_clones() as $clone) {
            $name = Locales::get_display_name($clone['lang_code']);
            $term = sprintf(ClonableConfig::WOOCOMMERCE_QUERY_ID, strtolower($clone['id']));
            $columns[$term] = sprintf("Excluded from %s", $name);
        }
        return $columns;
    }
}