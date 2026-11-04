<?php
/**
 * Lost password confirmation.
 *
 * @package WooCommerce\Templates
 */

defined( 'ABSPATH' ) || exit;
?>

<section class="container-wide py-8 lg:py-12">
    <div class="mx-auto max-w-xl border border-green-200 bg-green-50 p-5 sm:p-7">
        <h1 class="text-2xl font-semibold text-neutral-950">
            <?php esc_html_e( 'Check your email', 'blainestwentysix' ); ?>
        </h1>

        <p class="mt-3 leading-relaxed text-neutral-700">
            <?php
            echo apply_filters(
                'woocommerce_lost_password_confirmation_message',
                esc_html__( 'A password reset email has been sent. Please follow the secure link in that message to choose a new password.', 'blainestwentysix' )
            );
            ?>
        </p>
    </div>
</section>
