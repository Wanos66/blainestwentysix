<?php
/**
 * Custom WooCommerce order statuses.
 *
 * @package blainestwentysix
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the Order Confirmed status.
 */
add_action( 'init', function () {
    if ( ! class_exists( 'WooCommerce' ) ) {
        return;
    }

    register_post_status( 'wc-order-confirmed', array(
        'label'                     => 'Order Confirmed',
        'public'                    => false,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop(
            'Order Confirmed <span class="count">(%s)</span>',
            'Order Confirmed <span class="count">(%s)</span>'
        ),
    ) );
}, 20 );

/**
 * Add Order Confirmed to WooCommerce's status list,
 * immediately after Processing.
 */
add_filter( 'wc_order_statuses', function ( $statuses ) {
    $updated = array();

    foreach ( $statuses as $status => $label ) {
        $updated[ $status ] = $label;

        if ( 'wc-processing' === $status ) {
            $updated['wc-order-confirmed'] = 'Order Confirmed';
        }
    }

    return $updated;
} );


/**
 * Treat Order Confirmed as a paid order status.
 *
 * WooCommerce expects statuses without the "wc-" prefix.
 */
add_filter( 'woocommerce_order_is_paid_statuses', function ( $statuses ) {
    $statuses[] = 'order-confirmed';

    return array_unique( $statuses );
} );

