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
require_once dirname( __FILE__ ) . '/actions/class-av-action-avasize-return.php';

/**
 * Handle actions.
 *
 */
function avsz_act_return ($ordId) {
    AVSZ_Action_Return::handleReturn($ordId);
}
function avsz_act_return_on_cancelled ($ordId) {
    AVSZ_Action_Return::handleReturn($ordId, true);
}
add_action( 'woocommerce_order_status_cancelled', 'avsz_act_return_on_cancelled' );
add_action( 'woocommerce_order_status_refunded', 'avsz_act_return_on_cancelled' );
add_action( 'woocommerce_order_status_failed', 'avsz_act_return_on_cancelled' );
add_action( 'woocommerce_order_refunded', 'avsz_act_return' );
