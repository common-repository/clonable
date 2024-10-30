<?php

if (!defined( 'WP_UNINSTALL_PLUGIN' )) {
    exit;
}

// phpcs:disable Squiz.PHP.GlobalKeyword.NotAllowed
global $wpdb;
// phpcs:enable Squiz.PHP.GlobalKeyword.NotAllowed

$plugin_options = $wpdb->get_results( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE 'clonable_%'" );
foreach($plugin_options as $option) {
    delete_option($option->option_name);
}

$delete_terms = $wpdb->get_results("DELETE FROM $wpdb->terms WHERE slug LIKE 'clonable-%'");
$delete_taxonomy = $wpdb->get_results("DELETE FROM $wpdb->term_taxonomy WHERE taxonomy LIKE 'clonable_%'");