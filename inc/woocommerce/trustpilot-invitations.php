<?php
/**
 * Trustpilot Automatic Feedback Service integration.
 *
 * Adds a BCC recipient to WooCommerce's customer Completed Order email and
 * records a successful mail submission to avoid duplicate invitations.
 *
 * @package blainestwentysix
 */

defined( 'ABSPATH' ) || exit;

/**
 * Tracks BCC recipients actually passed to WooCommerce's mail callback.
 * Entries are scoped to this PHP request and the specific WC_Email instance.
 *
 * @var array<int, array{order_id: int, bcc: string}>
 */
$blaines_trustpilot_pending_submissions = array();

/**
 * Add the configured Trustpilot AFS BCC address to customer Completed emails.
 *
 * @param string $headers  Existing headers.
 * @param string $email_id WooCommerce email ID.
 * @param mixed  $order    Order object.
 * @return string
 */
add_filter(
    'woocommerce_email_headers',
    static function ( $headers, $email_id, $order ) {
        if ( 'customer_completed_order' !== $email_id || ! $order instanceof WC_Order ) {
            return $headers;
        }

        // Never queue another invitation after successful submission.
        if ( $order->get_meta( '_blaines_trustpilot_afs_sent', true ) ) {
            return $headers;
        }

        if ( ! defined( 'BLAINES_TRUSTPILOT_AFS_BCC' ) ) {
            return $headers;
        }

        $bcc = sanitize_email( BLAINES_TRUSTPILOT_AFS_BCC );

        if ( ! is_email( $bcc ) ) {
            return $headers;
        }

        // Other integrations may already have included this recipient.
        if ( false !== stripos( $headers, 'Bcc: ' . $bcc ) ) {
            return $headers;
        }

        return rtrim( $headers, "\r\n" ) . "\r\nBcc: " . $bcc . "\r\n";
    },
    10,
    3
);

/**
 * Inspect the actual headers passed to the mail transport.
 *
 * This runs after the headers have been assembled, unlike calling
 * $email->get_headers() later and reconstructing them from mutable state.
 */
add_filter(
    'woocommerce_mail_callback_params',
    static function ( $params, $email ) use ( &$blaines_trustpilot_pending_submissions ) {
        if ( ! $email instanceof WC_Email || 'customer_completed_order' !== $email->id ) {
            return $params;
        }

        $key = spl_object_id( $email );
        unset( $blaines_trustpilot_pending_submissions[ $key ] );

        $order = $email->object;
        if ( ! $order instanceof WC_Order || ! defined( 'BLAINES_TRUSTPILOT_AFS_BCC' ) ) {
            return $params;
        }

        if ( $order->get_meta( '_blaines_trustpilot_afs_sent', true ) ) {
            return $params;
        }

        $bcc = sanitize_email( BLAINES_TRUSTPILOT_AFS_BCC );
        if ( ! is_email( $bcc ) ) {
            return $params;
        }

        // The fourth callback parameter contains the outgoing mail headers.
        $headers = $params[3] ?? '';
        if ( is_array( $headers ) ) {
            $headers = implode( "\n", $headers );
        }

        if ( ! is_string( $headers ) || false === stripos( $headers, 'Bcc: ' . $bcc ) ) {
            return $params;
        }

        $blaines_trustpilot_pending_submissions[ $key ] = array(
            'order_id' => $order->get_id(),
            'bcc'      => $bcc,
        );

        return $params;
    },
    PHP_INT_MAX,
    2
);

/**
 * Record only successful submission of a Completed Order email that included
 * the configured BCC in the headers passed to the mail transport.
 *
 * @param bool     $sent     Whether the mail callback succeeded.
 * @param string   $email_id WooCommerce email ID.
 * @param WC_Email $email    Email instance.
 */
add_action(
    'woocommerce_email_sent',
    static function ( $sent, $email_id, $email ) use ( &$blaines_trustpilot_pending_submissions ) {
        if ( ! $email instanceof WC_Email || 'customer_completed_order' !== $email_id ) {
            return;
        }

        $key = spl_object_id( $email );
        $pending = $blaines_trustpilot_pending_submissions[ $key ] ?? null;
        unset( $blaines_trustpilot_pending_submissions[ $key ] );

        if ( ! $sent || ! $pending ) {
            return;
        }

        $order = $email->object;
        if ( ! $order instanceof WC_Order || $order->get_id() !== $pending['order_id'] ) {
            return;
        }

        // Guard against a second send in the same request.
        if ( $order->get_meta( '_blaines_trustpilot_afs_sent', true ) ) {
            return;
        }

        $order->update_meta_data( '_blaines_trustpilot_afs_sent', 'yes' );
        $order->save();
    },
    10,
    3
);
