<?php
/**
 * Login and registration forms.
 *
 * @package WooCommerce\Templates
 * @version 9.9.0
 */

defined( 'ABSPATH' ) || exit;

$registration_enabled = 'yes' === get_option( 'woocommerce_enable_myaccount_registration' );

do_action( 'woocommerce_before_customer_login_form' );
?>

<section class="container-wide py-8 lg:py-12">

    <header class="mx-auto mb-8 max-w-3xl text-center">
        <h1 class="text-3xl font-semibold text-neutral-950 md:text-4xl">
            <?php esc_html_e( 'Your Blaines account', 'blainestwentysix' ); ?>
        </h1>

        <p class="mt-3 text-base leading-relaxed text-neutral-600">
            <?php esc_html_e( 'Sign in to view orders and manage your details, or create an optional account for future purchases.', 'blainestwentysix' ); ?>
        </p>
    </header>

    <div
        id="customer_login"
        class="<?php echo esc_attr( $registration_enabled ? 'grid gap-6 lg:grid-cols-2' : 'mx-auto max-w-xl' ); ?>"
    >

        <section class="border border-neutral-200 bg-white p-5 shadow-[0_10px_30px_rgba(0,0,0,0.06)] sm:p-7">
            <h2 class="text-2xl font-semibold text-neutral-950">
                <?php esc_html_e( 'Sign in', 'blainestwentysix' ); ?>
            </h2>

            <p class="mt-2 text-sm leading-relaxed text-neutral-600">
                <?php esc_html_e( 'Access your orders, addresses and account details.', 'blainestwentysix' ); ?>
            </p>

            <form class="woocommerce-form woocommerce-form-login login mt-6" method="post" novalidate>
                <?php do_action( 'woocommerce_login_form_start' ); ?>

                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <label class="mb-2 block text-sm font-medium text-neutral-800" for="username">
                        <?php esc_html_e( 'Email address', 'blainestwentysix' ); ?>
                        <span class="required" aria-hidden="true">*</span>
                        <span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span>
                    </label>

                    <input
                        type="text"
                        class="woocommerce-Input woocommerce-Input--text input-text min-h-12 w-full border border-neutral-300 bg-white px-4 py-2.5 text-neutral-950 outline-none focus:border-blue-700"
                        name="username"
                        id="username"
                        autocomplete="username"
                        value="<?php echo ( ! empty( $_POST['username'] ) && is_string( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>"
                        required
                        aria-required="true"
                    >
                </p>

                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide mt-4">
                    <label class="mb-2 block text-sm font-medium text-neutral-800" for="password">
                        <?php esc_html_e( 'Password', 'woocommerce' ); ?>
                        <span class="required" aria-hidden="true">*</span>
                        <span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span>
                    </label>

                    <input
                        class="woocommerce-Input woocommerce-Input--text input-text min-h-12 w-full border border-neutral-300 bg-white px-4 py-2.5 text-neutral-950 outline-none focus:border-blue-700"
                        type="password"
                        name="password"
                        id="password"
                        autocomplete="current-password"
                        required
                        aria-required="true"
                    >
                </p>

                <?php do_action( 'woocommerce_login_form' ); ?>

                <div class="mt-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme inline-flex items-center gap-2 text-sm text-neutral-700">
                        <input
                            class="woocommerce-form__input woocommerce-form__input-checkbox h-4 w-4"
                            name="rememberme"
                            type="checkbox"
                            id="rememberme"
                            value="forever"
                        >
                        <span><?php esc_html_e( 'Remember me', 'woocommerce' ); ?></span>
                    </label>

                    <a
                        class="text-sm font-medium text-blue-700 hover:text-neutral-950"
                        href="<?php echo esc_url( wp_lostpassword_url() ); ?>"
                    >
                        <?php esc_html_e( 'Forgotten your password?', 'blainestwentysix' ); ?>
                    </a>
                </div>

                <p class="mt-5">
                    <?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>

                    <button
                        type="submit"
                        class="woocommerce-button button woocommerce-form-login__submit inline-flex min-h-12 w-full items-center justify-center bg-neutral-950 px-6 py-3 text-sm font-semibold text-white transition hover:bg-blue-700"
                        name="login"
                        value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>"
                    >
                        <?php esc_html_e( 'Sign in', 'blainestwentysix' ); ?>
                    </button>
                </p>

                <?php do_action( 'woocommerce_login_form_end' ); ?>
            </form>
        </section>

        <?php if ( $registration_enabled ) : ?>
            <section class="border border-neutral-200 bg-neutral-50 p-5 sm:p-7">
                <h2 class="text-2xl font-semibold text-neutral-950">
                    <?php esc_html_e( 'Create an account', 'blainestwentysix' ); ?>
                </h2>

                <p class="mt-2 text-sm leading-relaxed text-neutral-600">
                    <?php esc_html_e( 'Registration is optional. You can still place orders using guest checkout.', 'blainestwentysix' ); ?>
                </p>

                <form
                    method="post"
                    class="woocommerce-form woocommerce-form-register register mt-6"
                    <?php do_action( 'woocommerce_register_form_tag' ); ?>
                >
                    <?php do_action( 'woocommerce_register_form_start' ); ?>

                    <?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>
                        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                            <label class="mb-2 block text-sm font-medium text-neutral-800" for="reg_username">
                                <?php esc_html_e( 'Username', 'woocommerce' ); ?>
                                <span class="required" aria-hidden="true">*</span>
                                <span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span>
                            </label>

                            <input
                                type="text"
                                class="woocommerce-Input woocommerce-Input--text input-text min-h-12 w-full border border-neutral-300 bg-white px-4 py-2.5 text-neutral-950 outline-none focus:border-blue-700"
                                name="username"
                                id="reg_username"
                                autocomplete="username"
                                value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>"
                                required
                                aria-required="true"
                            >
                        </p>
                    <?php endif; ?>

                    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide <?php echo 'no' === get_option( 'woocommerce_registration_generate_username' ) ? 'mt-4' : ''; ?>">
                        <label class="mb-2 block text-sm font-medium text-neutral-800" for="reg_email">
                            <?php esc_html_e( 'Email address', 'woocommerce' ); ?>
                            <span class="required" aria-hidden="true">*</span>
                            <span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span>
                        </label>

                        <input
                            type="email"
                            class="woocommerce-Input woocommerce-Input--text input-text min-h-12 w-full border border-neutral-300 bg-white px-4 py-2.5 text-neutral-950 outline-none focus:border-blue-700"
                            name="email"
                            id="reg_email"
                            autocomplete="email"
                            value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>"
                            required
                            aria-required="true"
                        >
                    </p>

                    <?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>
                        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide mt-4">
                            <label class="mb-2 block text-sm font-medium text-neutral-800" for="reg_password">
                                <?php esc_html_e( 'Password', 'woocommerce' ); ?>
                                <span class="required" aria-hidden="true">*</span>
                                <span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span>
                            </label>

                            <input
                                type="password"
                                class="woocommerce-Input woocommerce-Input--text input-text min-h-12 w-full border border-neutral-300 bg-white px-4 py-2.5 text-neutral-950 outline-none focus:border-blue-700"
                                name="password"
                                id="reg_password"
                                autocomplete="new-password"
                                required
                                aria-required="true"
                            >
                        </p>
                    <?php else : ?>
                        <p class="mt-4 border-l-4 border-blue-700 bg-blue-50 px-4 py-3 text-sm leading-relaxed text-neutral-700">
                            <?php esc_html_e( 'We will email you a secure link to set your password.', 'blainestwentysix' ); ?>
                        </p>
                    <?php endif; ?>

                    <?php do_action( 'woocommerce_register_form' ); ?>

                    <p class="mt-5">
                        <?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>

                        <button
                            type="submit"
                            class="woocommerce-Button woocommerce-button button woocommerce-form-register__submit inline-flex min-h-12 w-full items-center justify-center bg-blue-700 px-6 py-3 text-sm font-semibold text-white transition hover:bg-neutral-950"
                            name="register"
                            value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>"
                        >
                            <?php esc_html_e( 'Create account', 'blainestwentysix' ); ?>
                        </button>
                    </p>

                    <?php do_action( 'woocommerce_register_form_end' ); ?>
                </form>
            </section>
        <?php endif; ?>

    </div>
</section>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
