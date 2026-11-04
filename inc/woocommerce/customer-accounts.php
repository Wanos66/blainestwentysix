<?php
/**
 * Customer account behaviour and presentation helpers.
 *
 * @package Blaines
 */

defined( 'ABSPATH' ) || exit;

/**
 * Return the customer account URL without silently falling back
 * to the site home page.
 */
function blaines_get_customer_account_url(): string {
    $page_id = absint(
        get_option( 'woocommerce_myaccount_page_id' )
    );

    if (
        $page_id > 0
        && 'publish' === get_post_status( $page_id )
        && $page_id !== (int) get_option( 'page_on_front' )
    ) {
        $account_url = get_permalink( $page_id );

        if (
            is_string( $account_url )
            && '' !== $account_url
        ) {
            return $account_url;
        }
    }

    return wp_login_url();
}

/**
 * Return the header account-link label.
 *
 * @return string
 */
function blaines_get_customer_account_label(): string {
    return is_user_logged_in()
        ? __( 'My account', 'blainestwentysix' )
        : __( 'Account', 'blainestwentysix' );
}

/**
 * Keep the account navigation focused on useful customer actions.
 *
 * Downloads are intentionally removed because the transactional catalogue
 * currently contains physical products only.
 *
 * @param array $items WooCommerce account menu items.
 * @return array
 */
function blaines_filter_customer_account_menu_items( array $items ): array {
    unset( $items['downloads'] );

    $preferred = [
        'dashboard'       => __( 'Overview', 'blainestwentysix' ),
        'orders'          => __( 'Orders', 'blainestwentysix' ),
        'edit-address'    => __( 'Addresses', 'blainestwentysix' ),
        'payment-methods' => __( 'Payment methods', 'blainestwentysix' ),
        'edit-account'    => __( 'Account details', 'blainestwentysix' ),
    ];

    $filtered = [];

    foreach ( $preferred as $endpoint => $label ) {
        if ( isset( $items[ $endpoint ] ) ) {
            $filtered[ $endpoint ] = $label;
        }
    }

    /*
     * Preserve any future extension endpoints before Log out.
     */
    foreach ( $items as $endpoint => $label ) {
        if (
            'customer-logout' === $endpoint
            || 'downloads' === $endpoint
            || isset( $filtered[ $endpoint ] )
        ) {
            continue;
        }

        $filtered[ $endpoint ] = $label;
    }

    if ( isset( $items['customer-logout'] ) ) {
        $filtered['customer-logout'] = __( 'Log out', 'blainestwentysix' );
    }

    return $filtered;
}
add_filter( 'woocommerce_account_menu_items', 'blaines_filter_customer_account_menu_items', 20 );

/**
 * Account creation should remain an explicit customer choice at checkout.
 *
 * @return bool
 */
function blaines_checkout_account_creation_default(): bool {
    return false;
}
add_filter( 'woocommerce_create_account_default_checked', 'blaines_checkout_account_creation_default' );

/**
 * Add a short explanation when the optional checkout account fields appear.
 */
function blaines_checkout_account_creation_intro(): void {
    ?>
    <div class="mb-5 border-l-4 border-blue-700 bg-blue-50 px-4 py-3 text-sm leading-relaxed text-neutral-700">
        <p class="font-semibold text-neutral-950">
            <?php esc_html_e( 'Create an optional Blaines account', 'blainestwentysix' ); ?>
        </p>
        <p class="mt-1">
            <?php esc_html_e( 'View your orders, save your address and make future checkouts quicker. Guest checkout remains available.', 'blainestwentysix' ); ?>
        </p>
    </div>
    <?php
}
add_action( 'woocommerce_before_checkout_registration_form', 'blaines_checkout_account_creation_intro' );

/**
 * Send a branded confirmation after a customer successfully resets a password.
 *
 * WooCommerce sends the reset-link email, but does not send the customer a
 * separate success confirmation after the new password is saved.
 *
 * @param WP_User $user     Customer whose password was reset.
 * @param string  $new_pass New password. Deliberately unused and never emailed.
 */
function blaines_send_password_reset_confirmation_email(
    WP_User $user,
    string $new_pass
): void {
    unset( $new_pass );

    if (
        ! function_exists( 'WC' )
        || ! WC()
        || ! is_email( $user->user_email )
    ) {
        return;
    }

    $mailer = WC()->mailer();

    if ( ! $mailer instanceof WC_Emails ) {
        return;
    }

    $site_name = wp_specialchars_decode(
        get_bloginfo( 'name' ),
        ENT_QUOTES
    );

    $display_name = trim( (string) $user->first_name );

    if ( '' === $display_name ) {
        $display_name = trim( (string) $user->display_name );
    }

    if ( '' === $display_name ) {
        $display_name = trim( (string) $user->user_login );
    }

    $account_url = function_exists( 'blaines_get_customer_account_url' )
        ? blaines_get_customer_account_url()
        : home_url( '/my-account/' );

    $subject = sprintf(
        /* translators: %s: Site name. */
        __( 'Your password was changed — %s', 'blainestwentysix' ),
        $site_name
    );

    $heading = __( 'Your password has been changed', 'blainestwentysix' );

    ob_start();
    ?>
    <p>
        <?php
        printf(
            /* translators: %s: Customer display name. */
            esc_html__( 'Hello %s,', 'blainestwentysix' ),
            esc_html( $display_name )
        );
        ?>
    </p>

    <p>
        <?php
        esc_html_e(
            'The password for your Blaines account was changed successfully.',
            'blainestwentysix'
        );
        ?>
    </p>

    <p>
        <?php
        esc_html_e(
            'If you made this change, no further action is required.',
            'blainestwentysix'
        );
        ?>
    </p>

    <p>
        <?php
        esc_html_e(
            'If you did not change your password, please contact Blaines Web Sales promptly on 01760 302111.',
            'blainestwentysix'
        );
        ?>
    </p>

    <p style="margin: 28px 0;">
        <a
            href="<?php echo esc_url( $account_url ); ?>"
            style="display:inline-block;background:#111827;color:#ffffff;text-decoration:none;font-weight:600;padding:13px 22px;"
        >
            <?php esc_html_e( 'Go to My account', 'blainestwentysix' ); ?>
        </a>
    </p>

    <p style="font-size:13px;color:#6b7280;">
        <?php
        esc_html_e(
            'For security, this email does not include your password.',
            'blainestwentysix'
        );
        ?>
    </p>
    <?php

    $message = $mailer->wrap_message(
        $heading,
        ob_get_clean()
    );

    $headers = [
        'Content-Type: text/html; charset=UTF-8',
    ];

    $mailer->send(
        $user->user_email,
        $subject,
        $message,
        $headers
    );
}
add_action(
    'after_password_reset',
    'blaines_send_password_reset_confirmation_email',
    20,
    2
);

