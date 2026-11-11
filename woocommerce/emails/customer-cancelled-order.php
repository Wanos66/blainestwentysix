<?php
/**
 * CP2B.3 — Blaines customer cancelled order email (HTML).
 *
 * Theme override for WooCommerce emails/customer-cancelled-order.php.
 * Based on the WooCommerce 10.4.0 template interface.
 *
 * Keep the original order breakdown as a historical record, but omit
 * delivery/collection dates from the cancellation notice. Never imply
 * that cancelling an authorisation is the same as issuing a refund.
 *
 * @package blainestwentysix
 */

use Automattic\WooCommerce\Utilities\FeaturesUtil;

defined( 'ABSPATH' ) || exit;

$email_improvements_enabled = class_exists( FeaturesUtil::class )
    && FeaturesUtil::feature_is_enabled( 'email_improvements' );

/**
 * Scope the totals adjustment to this email's order-details rendering.
 * Preserve the original shipping charge and overall total so the original
 * order figures still reconcile; only rename shipping as historical.
 */
$blaines_cancelled_totals_filter = static function ( $rows, $filtered_order, $tax_display = '' ) use ( $order ) {
    if ( ! $filtered_order instanceof WC_Order || $filtered_order->get_id() !== $order->get_id() ) {
        return $rows;
    }

    foreach ( $rows as $key => $row ) {
        $label = isset( $row['label'] )
            ? trim( wp_strip_all_tags( (string) $row['label'] ) )
            : '';

        $is_date_or_location_row = in_array(
            (string) $key,
            [ 'blaines_collection_location', 'blaines_collection_date' ],
            true
        );

        if ( ! $is_date_or_location_row ) {
            $is_date_or_location_row = (bool) preg_match(
                '/^(?:preferred delivery date|delivery date|expected collection|collection showroom|collection date)\s*:?\s*$/iu',
                $label
            );
        }

        if ( $is_date_or_location_row ) {
            unset( $rows[ $key ] );
        }
    }

    if ( isset( $rows['shipping']['label'] ) ) {
        $rows['shipping']['label'] = 'Original fulfilment:';
    }

    return $rows;
};

do_action( 'woocommerce_email_header', $email_heading, $email );
?>
<?php if ( $email_improvements_enabled ) : ?>
<div class="email-introduction">
<?php endif; ?>
    <p>
        <?php if ( $order->get_billing_first_name() ) : ?>
            Hi <?php echo esc_html( $order->get_billing_first_name() ); ?>,
        <?php else : ?>
            Hi,
        <?php endif; ?>
    </p>
    <p>
        Your Blaines order
        <strong>#<?php echo esc_html( $order->get_order_number() ); ?></strong>
        has been cancelled.
    </p>
    <p>
        If your order has not yet been delivered or collected, no delivery or
        collection will take place for this cancelled order.
    </p>
    <p>
        If a payment was taken, Web Sales will confirm any refund separately.
        If your payment was only authorised, the time taken to release the
        authorisation depends on your payment provider.
    </p>
    <p>
        The summary below shows your original order for reference only.
        It does not represent a new payment request.
    </p>
<?php if ( $email_improvements_enabled ) : ?>
</div>
<?php endif; ?>
<?php
// Order items, service lines and original totals remain available for reference.
// Remove the filter immediately afterwards so other emails are unaffected.
add_filter( 'woocommerce_get_order_item_totals', $blaines_cancelled_totals_filter, 100, 3 );
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );
remove_filter( 'woocommerce_get_order_item_totals', $blaines_cancelled_totals_filter, 100 );

// Blaines' preferred delivery/collection details can also be repeated via
// this action. Do not show them in a cancelled-order email.
// do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

if ( ! empty( $additional_content ) ) {
    if ( $email_improvements_enabled ) {
        echo '<table border="0" cellpadding="0" cellspacing="0" width="100%" role="presentation"><tr><td class="email-additional-content">';
    }

    echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );

    if ( $email_improvements_enabled ) {
        echo '</td></tr></table>';
    }
}

do_action( 'woocommerce_email_footer', $email );
