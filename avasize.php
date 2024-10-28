<?php
/**
 * Plugin Name: Avasize
 * Plugin URI: https://avasize.com/
 * Description: This module allows shoppers to buy ready-to-wear garments for them or for their relatives "in the right size the first time, every time".
 * Version: 1.0.0
 * Author: Thomas Giordmaina
 * Text Domain: avasize
 * Domain Path: /i18n/languages/
 *
 * @package Avasize
 */

defined( 'ABSPATH' ) || exit;

// Define AVSZ_PLUGIN_FILE.
if ( ! defined( 'AVSZ_PLUGIN_FILE' ) ) {
	define( 'AVSZ_PLUGIN_FILE', __FILE__ );
}

// Include the main Avasize class.
if ( ! class_exists( 'Avasize' ) ) {
	include_once dirname( __FILE__ ) . '/includes/class-avasize.php';
}

/**
 * Returns the main instance of AVSZ.
 *
 * @since  2.1
 * @return Avasize
 */
function AVSZ() {
	return Avasize::instance();
}

// Global for backwards compatibility.
$GLOBALS['avasize'] = AVSZ();
