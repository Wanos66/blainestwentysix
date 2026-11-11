<?php
/**
 * CP2B.2 — Blaines customer refunded-order email (HTML).
 *
 * Theme override of WooCommerce's standard customer-refunded-order.php.
 * WooCommerce reference template: 10.4.0.
 *
 * Full refund: show the refunded amount, omit outdated fulfilment scheduling
 * rows and omit the generic order-meta section (which may repeat delivery
 * dates). Do not imply that a refund automatically cancels an unfulfilled
 * order. Partial refund: retain existing delivery / collection information.
 *
 * @package blainestwentysix
 */

use Automattic\WooCommerce\Utilities\FeaturesUtil;

defined( 'ABSPATH' ) || exit;

$email_improvements_enabled = class_exists( FeaturesUtil::class )
    && FeaturesUtil::feature_is_enabled( 'email_improvements' );

$is_partial_refund = ! empty( $partial_refund );
$is_full_refund    = ! $is_partial_refund;

// This is the total refunded to date for a full refund. For a partial refund,
// prefer the specific refund that triggered the email (if supplied).
$amount_refunded = (float) $order->get_total_refunded();

if ( $is_partial_refund && $refund instanceof WC_Order_Refund ) {
    $amount_refunded = (float) $refund->get_amount();
}

// Avoid a misleading zero amount in an email preview with no actual refund.
$has_refund_amount = $amount_refunded > 0;

/**
 * Restrict scheduling-row removal to this particular FULL refund email's
 * order-details rendering. Do not change order data or other email templates.
 */
$blaines_filter_refund_totals = static function ( $rows, $filtered_order, $tax_display = '' ) use ( $order ) {
    if ( ! $filtered_order instanceof WC_Order || $filtered_order->get_id() !== $order->get_id() ) {
        return $rows;
    }

    foreach ( $rows as $key => $row ) {
        $label = isset( $row['label'] )
            ? trim( wp_strip_all_tags( (string) $row['label'] ) )
            : '';

        $is_scheduling_row = in_array(
            (string) $key,
            [ 'shipping', 'blaines_collection_location', 'blaines_collection_date' ],
            true
        );

        if ( ! $is_scheduling_row ) {
            $is_scheduling_row = 1 === preg_match(
                '/^(?:shipping|fulfilment|preferred delivery date|delivery date|expected collection|collection showroom)\s*:?\s*$/iu',
                $label
            );
        }

        if ( $is_scheduling_row ) {
            unset( $rows[ $key ] );
        }
    }

    return $rows;
};

// The standard email header and footer preserve Blaine's email branding.
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

    <?php if ( $is_full_refund ) : ?>
        <p>
            Your Blaines order
            <strong>#<?php echo esc_html( $order->get_order_number() ); ?></strong>
            has been fully refunded.
        </p>
        <?php if ( $has_refund_amount ) : ?>
            <p>
                A refund of <strong><?php echo wp_kses_post( wc_price( $amount_refunded, [ 'currency' => $order->get_currency() ] ) ); ?></strong>
                has been issued to your original payment method. The time it
                takes to appear depends on your payment provider.
            </p>
        <?php endif; ?>
        <p>Here is a summary of your refunded order:</p>
    <?php else : ?>
        <p>
            A partial refund has been issued for your Blaines order
            <strong>#<?php echo esc_html( $order->get_order_number() ); ?></strong>.
        </p>
        <?php if ( $has_refund_amount ) : ?>
            <p>
                Amount refunded:
                <strong><?php echo wp_kses_post( wc_price( $amount_refunded, [ 'currency' => $order->get_currency() ] ) ); ?></strong>.
                The time it takes to appear depends on your payment provider.
            </p>
        <?php endif; ?>
        <p>
            This refund does not automatically change the remaining order or
            its delivery or collection arrangements. Web Sales will contact
            you if any changes are required.
        </p>
        <p>Here is a summary of your order:</p>
    <?php endif; ?>
<?php if ( $email_improvements_enabled ) : ?>
</div>
<?php endif; ?>

<?php
// The order-details template calls get_order_item_totals() to build rows.
// Add and remove our filter around that call, so nothing else is affected.
if ( $is_full_refund ) {
    add_filter( 'woocommerce_get_order_item_totals', $blaines_filter_refund_totals, 100, 3 );
}

do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

if ( $is_full_refund ) {
    remove_filter( 'woocommerce_get_order_item_totals', $blaines_filter_refund_totals, 100 );
}

// Full-refund emails deliberately omit the order-meta block. Blaines adds a
// preferred delivery / collection date through this hook, which would be
// misleading and duplicated in a fully refunded email.
// Partial refunds retain the normal order metadata.
if ( $is_partial_refund ) {
    do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );
}

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
