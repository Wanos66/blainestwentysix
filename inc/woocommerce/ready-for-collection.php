<?php
/**
 * Blaines Ready for Collection order action.
 *
 * @package blainestwentysix
 */

defined( 'ABSPATH' ) || exit;

/**
 * Add a Ready for Collection action to eligible orders.
 */
add_filter(
    'woocommerce_order_actions',
    function ( $actions, $order = null ) {

        if ( ! $order instanceof WC_Order ) {
            return $actions;
        }

        // Only available for accepted orders.
        if ( ! $order->has_status( 'order-confirmed' ) ) {
            return $actions;
        }

        // Only available for showroom collection orders.
        $is_collection = false;

        foreach ( $order->get_items( 'shipping' ) as $shipping_item ) {
            if ( 'local_pickup' === $shipping_item->get_method_id() ) {
                $is_collection = true;
                break;
            }
        }

        if ( ! $is_collection ) {
            return $actions;
        }

        $actions['blaines_ready_for_collection'] =
            'Email customer — Ready for Collection';

        return $actions;
    },
    10,
    2
);

/**
 * Handle the manual Ready for Collection action.
 */
add_action(
    'woocommerce_order_action_blaines_ready_for_collection',
    function ( $order ) {

        if ( ! $order instanceof WC_Order ) {
            return;
        }

        $emails = WC()->mailer()->get_emails();

        $email = $emails['WC_Email_Blaines_Ready_For_Collection'] ?? null;

        if ( ! $email instanceof WC_Email_Blaines_Ready_For_Collection ) {
            return;
        }

        $email->trigger( $order->get_id() );
    }
);

/**
 * Display Ready for Collection notification status
 * in the WooCommerce order administration screen.
 */
add_action(
    'woocommerce_admin_order_data_after_order_details',
    function ( $order ) {

        if ( ! $order instanceof WC_Order ) {
            return;
        }

        // Only relevant for collection orders.
        $is_collection = false;

        foreach ( $order->get_items( 'shipping' ) as $shipping_item ) {
            if ( 'local_pickup' === $shipping_item->get_method_id() ) {
                $is_collection = true;
                break;
            }
        }

        if ( ! $is_collection ) {
            return;
        }

        $sent = 'yes' === $order->get_meta(
                '_blaines_ready_for_collection_email_sent',
                true
            );

        ?>
        <div class="blaines-collection-notification"
            style="clear:both;padding-top:14px;margin-top:14px;border-top:1px solid #ddd;">

            <p style="margin:0 0 5px;">
                <strong>Collection notification</strong>
            </p>

            <?php if ( $sent ) : ?>

                <p style="margin:0;color:#237a3b;">
                    &#10003; Ready for Collection email sent
                </p>

            <?php else : ?>

                <p style="margin:0;color:#666;">
                    Ready for Collection email not yet sent
                </p>

            <?php endif; ?>

        </div>
        <?php
    }
);


