<?php
/**
 * Lost password reset form.
 *
 * @package WooCommerce\Templates
 * @version 9.2.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_reset_password_form' );
?>

<section class="container-wide py-8 lg:py-12">
    <div class="mx-auto max-w-xl border border-neutral-200 bg-white p-5 shadow-[0_10px_30px_rgba(0,0,0,0.06)] sm:p-7">
        <h1 class="text-3xl font-semibold text-neutral-950">
            <?php esc_html_e( 'Choose a new password', 'blainestwentysix' ); ?>
        </h1>

        <form method="post" class="woocommerce-ResetPassword lost_reset_password mt-5">
            <p class="text-sm leading-relaxed text-neutral-600">
                <?php
                echo apply_filters(
                    'woocommerce_reset_password_message',
                    esc_html__( 'Enter your new password below.', 'blainestwentysix' )
                );
                ?>
            </p>

            <p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first mt-5">
                <label class="mb-2 block text-sm font-medium text-neutral-800" for="password_1">
                    <?php esc_html_e( 'New password', 'woocommerce' ); ?>
                    <span class="required" aria-hidden="true">*</span>
                    <span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span>
                </label>

                <input
                    type="password"
                    class="woocommerce-Input woocommerce-Input--text input-text min-h-12 w-full border border-neutral-300 bg-white px-4 py-2.5 text-neutral-950 outline-none focus:border-blue-700"
                    name="password_1"
                    id="password_1"
                    autocomplete="new-password"
                    required
                    aria-required="true"
                >
            </p>

            <p class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last mt-4">
                <label class="mb-2 block text-sm font-medium text-neutral-800" for="password_2">
                    <?php esc_html_e( 'Re-enter new password', 'woocommerce' ); ?>
                    <span class="required" aria-hidden="true">*</span>
                    <span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span>
                </label>

                <input
                    type="password"
                    class="woocommerce-Input woocommerce-Input--text input-text min-h-12 w-full border border-neutral-300 bg-white px-4 py-2.5 text-neutral-950 outline-none focus:border-blue-700"
                    name="password_2"
                    id="password_2"
                    autocomplete="new-password"
                    required
                    aria-required="true"
                >
            </p>

            <input type="hidden" name="reset_key" value="<?php echo esc_attr( $args['key'] ); ?>">
            <input type="hidden" name="reset_login" value="<?php echo esc_attr( $args['login'] ); ?>">

            <?php do_action( 'woocommerce_resetpassword_form' ); ?>

            <p class="woocommerce-form-row form-row mt-5">
                <input type="hidden" name="wc_reset_password" value="true">

                <button
                    type="submit"
                    class="woocommerce-Button button inline-flex min-h-12 w-full items-center justify-center bg-neutral-950 px-6 py-3 text-sm font-semibold text-white transition hover:bg-blue-700"
                    value="<?php esc_attr_e( 'Save', 'woocommerce' ); ?>"
                >
                    <?php esc_html_e( 'Save new password', 'blainestwentysix' ); ?>
                </button>
            </p>

            <?php wp_nonce_field( 'reset_password', 'woocommerce-reset-password-nonce' ); ?>
        </form>
    </div>
</section>

<?php do_action( 'woocommerce_after_reset_password_form' ); ?>
