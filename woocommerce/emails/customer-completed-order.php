<?php
/**
 * Customer completed order email.
 *
 * Blaines Electrical — Aftercare message.
 *
 * @package blainestwentysix
 */

defined( 'ABSPATH' ) || exit;

/**
 * Standard WooCommerce email header.
 */
do_action(
    'woocommerce_email_header',
    $email_heading,
    $email
);

$first_name = $order->get_billing_first_name();

if ( empty( $first_name ) ) {
    $first_name = 'there';
}
?>

<p>
    <?php
    printf(
        esc_html__( 'Hi %s,', 'blainestwentysix' ),
        esc_html( $first_name )
    );
    ?>
</p>

<?php if ( $order instanceof WC_Order ) : ?>
    <p>
        Your Blaines order
        <strong>#<?php echo esc_html( $order->get_order_number() ); ?></strong>
        is now complete.
    </p>
<?php endif; ?>

<p>
    We hope you're delighted with your new purchase,
    and we'd like to thank you for choosing Blaines Electrical.
</p>

<h2>Don't forget your guarantee</h2>

<p>
    Some manufacturers require you to register your product
    to activate an extended guarantee or receive additional
    benefits.
</p>

<p>
    If applicable, please check the information supplied
    with your product and complete any necessary registration.
</p>

<p>
    Remember, your statutory consumer rights are unaffected
    by any manufacturer's guarantee.
</p>

<h2>We're always here to help</h2>

<p>
    If you have any questions about your purchase or need
    assistance in the future, our friendly team is always
    happy to help.
</p>

<p>
    And when you're next looking for a new appliance,
    television or something for your home, we'd love
    to welcome you back.
</p>

<h2>Stay in touch</h2>


    <p>
        Want to hear about our latest special offers,
        promotions and news?
    </p>

    <p>
        <a href="<?php echo esc_url( home_url( '/#newsletter' ) ); ?>">
            Sign up for our newsletter &rarr;
        </a>
    </p>


<p>
    Thank you again for supporting your local
    independent electrical retailer.
</p>

<p>
    The Blaines Team
</p>

<?php

/**
 * Standard WooCommerce email footer.
 */
do_action(
    'woocommerce_email_footer',
    $email
);

