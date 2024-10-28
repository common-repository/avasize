<?php
/**
 * WooCommerce Uninstall
 *
 * Uninstalling WooCommerce deletes user roles, pages, tables, and options.
 *
 * @package WooCommerce\Uninstaller
 * @version 2.3.0
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

global $wpdb;

// Delete options.
$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'widget\_avasz\_%';" );


// Clear any cached data that has been removed.
wp_cache_flush();
