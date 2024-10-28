<?php
/**
 * Avasize Widget Functions
 *
 * Widget related functions and widget registration.
 *
 * @package Avasize
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Include widget classes.
require_once dirname( __FILE__ ) . '/widgets/class-av-widget-avasize-library.php';

/**
 * Register Widgets.
 *
 */
function avsz_register_widgets() {
    register_widget( 'AVSZ_Widget_AvszBtn' );
}
add_action( 'widgets_init', 'avsz_register_widgets' );