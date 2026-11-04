<?php
/**
 * Trustpilot Automatic Feedback Service integration.
 *
 * Adds a BCC recipient to WooCommerce's customer
 * Completed Order email.
 *
 * @package blainestwentysix
 */

defined( 'ABSPATH' ) || exit;

/**
 * Add the configured Trustpilot AFS BCC address.
 *
 * @param string $headers  Existing email headers.
 * @param string $email_id WooCommerce email identifier.
 * @param mixed  $order    Order associated with the email.
 *
 * @return string
 */
add_filter(
    'woocommerce_email_headers',
    function ( $headers, $email_id, $order ) {

        // Only the customer Completed Order email.
        if ( 'customer_completed_order' !== $email_id ) {
            return $headers;
        }

        if ( ! $order instanceof WC_Order ) {
            return $headers;
        }

        /**
         * Do not send another Trustpilot BCC if one
         * has already been submitted for this order.
         */
        if ( $order->get_meta(
            '_blaines_trustpilot_afs_sent',
            true
        ) ) {
            return $headers;
        }


        // No BCC unless an address has been configured.
        if ( ! defined( 'BLAINES_TRUSTPILOT_AFS_BCC' ) ) {
            return $headers;
        }

        $bcc = sanitize_email(
            BLAINES_TRUSTPILOT_AFS_BCC
        );

        if ( ! is_email( $bcc ) ) {
            return $headers;
        }

        // Avoid adding the same BCC twice.
        if ( false !== stripos( $headers, 'Bcc: ' . $bcc ) ) {
            return $headers;
        }

        return $headers . 'Bcc: ' . $bcc . "\r\n";
    },
    10,
    3
);


/**
 * Record successful submission of a Completed Order
 * email containing the configured Trustpilot BCC.
 *
 * @param bool     $sent     Whether mail submission succeeded.
 * @param string   $email_id WooCommerce email ID.
 * @param WC_Email $email    Email instance.
 */
add_action(
    'woocommerce_email_sent',
    function ( $sent, $email_id, $email ) {

        if ( ! $sent || 'customer_completed_order' !== $email_id ) {
            return;
        }

        if ( ! $email instanceof WC_Email ) {
            return;
        }

        $order = $email->object;

        if ( ! $order instanceof WC_Order ) {
            return;
        }

        if ( ! defined( 'BLAINES_TRUSTPILOT_AFS_BCC' ) ) {
            return;
        }

        $bcc = sanitize_email(
            BLAINES_TRUSTPILOT_AFS_BCC
        );

        if ( ! is_email( $bcc ) ) {
            return;
        }

        // Only mark the order if the BCC was included.
        if ( false === stripos(
                $email->get_headers(),
                'Bcc: ' . $bcc
            ) ) {
            return;
        }

        $order->update_meta_data(
            '_blaines_trustpilot_afs_sent',
            'yes'
        );

        $order->save();
    },
    10,
    3
);


