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
 * Add the order history heading and use the default status label in a badge.
 */
function blaines_render_account_orders_heading(): void {
    echo '<h2 class="blaines-orders-heading">' . esc_html__( 'Order history', 'blainestwentysix' ) . '</h2>';

    if ( ! has_action( 'woocommerce_my_account_my_orders_column_order-status' ) ) {
        add_action( 'woocommerce_my_account_my_orders_column_order-status', 'blaines_render_account_order_status' );
    }
}
add_action( 'woocommerce_before_account_orders', 'blaines_render_account_orders_heading', 5 );

function blaines_render_account_order_status( WC_Order $order ): void {
    printf(
        '<span class="blaines-order-status" data-status="%s">%s</span>',
        esc_attr( $order->get_status() ),
        esc_html( wc_get_order_status_name( $order->get_status() ) )
    );
}

/**
 * Give the default View action a more specific visible label.
 */
function blaines_label_account_order_view_action( array $actions ): array {
    if ( ( $actions['view']['name'] ?? '' ) === __( 'View', 'woocommerce' ) ) {
        $actions['view']['name'] = __( 'View order', 'blainestwentysix' );
    }

    return $actions;
}
add_filter( 'woocommerce_my_account_my_orders_actions', 'blaines_label_account_order_view_action' );

/**
 * Present the WooCommerce email prompt with its explanation before the supplied link.
 */
function blaines_format_account_orders_email_prompt( string $notice ): string {
    if ( ! is_account_page() || ! is_wc_endpoint_url( 'orders' ) ) {
        return $notice;
    }

    $message = esc_html__( 'Confirm your email address to check for past orders and link them to your account.', 'woocommerce' );
    $notice_content = trim( $notice );

    if ( ! str_ends_with( $notice_content, $message ) ) {
        return $notice;
    }

    $link = trim( substr( $notice_content, 0, -strlen( $message ) ) );
    if ( ! str_starts_with( $link, '<a ' ) || ! str_ends_with( $link, '</a>' ) ) {
        return $notice;
    }

    return '<span class="blaines-order-email-prompt"><strong>'
        . esc_html__( 'Looking for a past order?', 'blainestwentysix' )
        . '</strong><span>' . $message . '</span>' . $link . '</span>';
}
add_filter( 'woocommerce_add_notice', 'blaines_format_account_orders_email_prompt' );

/**
 * Apply the retail palette to the existing account forms and address templates.
 */
function blaines_style_customer_account(): void {
    if ( ! function_exists( 'is_account_page' ) || ! is_account_page() ) {
        return;
    }
    ?>
    <style>
        body.woocommerce-account .woocommerce {
            --account-navy: #0e295e;
            --account-cobalt: #215ed9;
            --account-stone: #f3f0e9;
            --account-rule: #aeb9cc;
            --account-gutter: clamp(1rem, 3vw, 1.75rem);
        }

        .woocommerce-account .woocommerce section:has(> #customer_login) {
            max-width: 72rem;
            margin-inline: auto;
            padding: 0;
        }

        .woocommerce-account .woocommerce section:has(> #customer_login > section:only-child) {
            max-width: 36rem;
        }

        .woocommerce-account .woocommerce section:has(> #customer_login) > header {
            max-width: none;
            margin: 0 0 1.25rem;
            padding: clamp(1.3rem, 4vw, 2.5rem);
            background: var(--account-navy);
            color: #fff;
            text-align: left;
        }

        .woocommerce-account .woocommerce section:has(> #customer_login) > header :is(h1, p) {
            color: #fff;
        }

        .woocommerce-account .woocommerce section:has(> #customer_login) > header p {
            max-width: 42rem;
        }

        .woocommerce-account .woocommerce #customer_login > section {
            min-width: 0;
            padding: var(--account-gutter);
            border: 1px solid var(--account-rule);
            box-shadow: none;
        }

        .woocommerce-account .woocommerce #customer_login > section:has(.woocommerce-form-register) {
            background: var(--account-stone);
        }

        .woocommerce-account .woocommerce #customer_login > section > h2 {
            color: var(--account-navy);
        }

        .woocommerce-account .woocommerce #customer_login :is(.woocommerce-form-login, .woocommerce-form-register) {
            margin: 1.25rem 0 0;
            padding: 0;
            border: 0;
            border-radius: 0;
            background: transparent;
        }

        .woocommerce-account .woocommerce #customer_login .woocommerce-form-register > p.bg-blue-50 {
            padding: .7rem .8rem;
            border: 1px solid var(--account-rule);
            background: #fff;
            color: var(--account-navy);
        }

        .woocommerce-account .woocommerce #customer_login :is(
            .woocommerce-form-login__submit, .woocommerce-form-register__submit
        ) {
            border: 1px solid var(--account-cobalt) !important;
            border-radius: 0;
            background: var(--account-cobalt) !important;
            color: #fff !important;
        }

        .woocommerce-account .woocommerce #customer_login .woocommerce-form-register__submit {
            border-color: var(--account-navy) !important;
            background: #fff !important;
            color: var(--account-navy) !important;
        }

        .woocommerce-account .woocommerce #customer_login :is(
            .woocommerce-form-login__submit, .woocommerce-form-register__submit
        ):hover {
            border-color: var(--account-navy) !important;
            background: var(--account-navy) !important;
            color: #fff !important;
        }

        .woocommerce-account .woocommerce .woocommerce-MyAccount-navigation,
        .woocommerce-account .woocommerce .woocommerce-MyAccount-content {
            border-color: var(--account-rule);
            box-shadow: none;
        }

        .woocommerce-account .woocommerce .woocommerce-MyAccount-navigation > div {
            background: var(--account-stone);
        }

        .woocommerce-account .woocommerce .woocommerce-MyAccount-navigation a[aria-current="page"] {
            border-left-color: var(--account-cobalt);
            background: var(--account-cobalt);
            color: #fff;
        }

        .woocommerce-account .woocommerce .woocommerce-MyAccount-content .woocommerce-Addresses {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(100%, 15rem), 1fr));
            gap: 1rem;
            margin-top: 1.1rem;
        }

        .woocommerce-account .woocommerce .woocommerce-MyAccount-content .woocommerce-Addresses::before,
        .woocommerce-account .woocommerce .woocommerce-MyAccount-content .woocommerce-Addresses::after,
        .woocommerce-account .woocommerce .woocommerce-Address-title::before,
        .woocommerce-account .woocommerce .woocommerce-Address-title::after {
            display: none;
        }

        .woocommerce-account .woocommerce .woocommerce-MyAccount-content .woocommerce-Address {
            float: none;
            width: auto;
            min-width: 0;
            margin: 0;
            border: 1px solid var(--account-rule);
            background: var(--account-stone);
            overflow-wrap: anywhere;
        }

        .woocommerce-account .woocommerce .woocommerce-MyAccount-content > .woocommerce-Address {
            max-width: 26rem;
            margin-top: 1.1rem;
        }

        .woocommerce-account .woocommerce .woocommerce-Address .woocommerce-Address-title {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: .55rem 1rem;
            padding: .75rem 1rem;
            background: var(--account-navy);
            color: #fff;
        }

        .woocommerce-account .woocommerce .woocommerce-Address .woocommerce-Address-title h2 {
            float: none;
            margin: 0;
            color: #fff;
            font-family: var(--font-heading, Montserrat, sans-serif);
            font-size: 1.06rem;
            line-height: 1.35;
        }

        .woocommerce-account .woocommerce .woocommerce-Address .woocommerce-Address-title a.edit {
            float: none;
            display: inline-flex;
            align-items: center;
            min-height: 2.75rem;
            color: #fff;
            font-size: .85rem;
            text-decoration: underline;
            text-underline-offset: .2em;
        }

        .woocommerce-account .woocommerce .woocommerce-Address .woocommerce-Address-title a.edit:focus-visible {
            outline-color: #fff;
        }

        .woocommerce-account .woocommerce .woocommerce-Address address {
            margin: 0;
            padding: 1rem;
            color: var(--account-navy);
        }

        .woocommerce-account .woocommerce .woocommerce-Address .woocommerce-Address-copy {
            margin: .8rem 1rem 0;
            font-size: .875rem;
        }

        .woocommerce-account .woocommerce .woocommerce-MyAccount-content form:has(> .woocommerce-address-fields) > h2 {
            padding-bottom: .65rem;
            border-bottom: 2px solid var(--account-navy);
            color: var(--account-navy);
        }

        .woocommerce-account .woocommerce .woocommerce-address-fields__field-wrapper {
            display: flow-root;
            max-width: 44rem;
        }

        .woocommerce-account .woocommerce .woocommerce-address-fields .button[name="save_address"] {
            min-height: 2.75rem;
            background: var(--account-cobalt) !important;
        }

        .woocommerce-account .woocommerce .woocommerce-address-fields .button[name="save_address"]:hover {
            background: var(--account-navy) !important;
        }

        .woocommerce-account .woocommerce .woocommerce-MyAccount-content .blaines-orders-heading {
            margin: 0 0 1.25rem;
            padding-bottom: .65rem;
            border-bottom: 2px solid var(--account-navy);
            color: var(--account-navy);
            font-family: var(--font-heading, Montserrat, sans-serif);
        }

        .woocommerce-account .woocommerce .woocommerce-info:has(.blaines-order-email-prompt) {
            padding: 1rem !important;
            border: 1px solid var(--account-rule) !important;
            background: var(--account-stone) !important;
            color: var(--account-navy) !important;
        }

        .woocommerce-account .woocommerce .woocommerce-info:has(.blaines-order-email-prompt)::before {
            display: none;
        }

        .woocommerce-account .woocommerce .blaines-order-email-prompt {
            display: grid;
            justify-items: start;
            gap: .4rem;
        }

        .woocommerce-account .woocommerce .blaines-order-email-prompt strong {
            font-size: 1rem;
        }

        .woocommerce-account .woocommerce .blaines-order-email-prompt .button {
            height: auto !important;
            min-height: 2.75rem;
            margin: .45rem 0 0 !important;
            border: 1px solid var(--account-navy) !important;
            background: #fff !important;
            color: var(--account-navy) !important;
            line-height: 1.35 !important;
            white-space: normal;
        }

        .woocommerce-account .woocommerce .blaines-order-email-prompt .button:hover {
            background: var(--account-navy) !important;
            color: #fff !important;
        }

        .woocommerce-account .woocommerce table.woocommerce-orders-table {
            margin: 0 0 1rem;
            border: 1px solid var(--account-rule);
            border-radius: 0;
        }

        .woocommerce-account .woocommerce table.woocommerce-orders-table thead th {
            padding: .85rem .75rem;
            border: 0;
            background: var(--account-navy);
            color: #fff;
        }

        .woocommerce-account .woocommerce table.woocommerce-orders-table tbody :is(th, td) {
            padding: .8rem .75rem;
            border-color: #dfe3ea;
            vertical-align: middle;
        }

        .woocommerce-account .woocommerce table.woocommerce-orders-table tbody tr:nth-child(even) > * {
            background: var(--account-stone);
        }

        .woocommerce-account .woocommerce .woocommerce-orders-table__cell-order-number a {
            color: var(--account-navy);
            font-weight: 700;
        }

        .woocommerce-account .woocommerce .blaines-order-status {
            display: inline-block;
            padding: .35rem .6rem;
            background: var(--account-navy);
            color: #fff;
            font-size: .8rem;
            font-weight: 700;
            line-height: 1.35;
        }

        .woocommerce-account .woocommerce .blaines-order-status[data-status="completed"] {
            background: #195845;
        }

        .woocommerce-account .woocommerce .blaines-order-status:is([data-status="failed"], [data-status="cancelled"]) {
            background: #8c2737;
        }

        .woocommerce-account .woocommerce .woocommerce-orders-table__cell-order-actions .button {
            height: auto !important;
            min-height: 2.75rem;
            margin: .15rem 0;
            line-height: 1.35 !important;
            white-space: normal;
        }

        .woocommerce-account .woocommerce .woocommerce-orders-table__cell-order-actions .button.view {
            background: var(--account-cobalt) !important;
        }

        .woocommerce-account .woocommerce .woocommerce-orders-table__cell-order-actions .button.view:hover {
            background: var(--account-navy) !important;
        }

        @media (max-width: 767px) {
            .woocommerce-account .woocommerce table.woocommerce-orders-table tbody .woocommerce-orders-table__cell-order-number {
                display: block;
                border-top: 2px solid var(--account-navy);
                background: var(--account-stone);
            }

            .woocommerce-account .woocommerce table.woocommerce-orders-table .woocommerce-orders-table__cell {
                overflow-wrap: anywhere;
            }
        }

        .woocommerce-account .woocommerce:has(.woocommerce-MyAccount-navigation-link--dashboard > a[aria-current="page"])
        .woocommerce-MyAccount-content > header:first-of-type {
            padding: clamp(1.15rem, 3vw, 2rem);
            border: 0;
            background: var(--account-navy);
        }

        .woocommerce-account .woocommerce:has(.woocommerce-MyAccount-navigation-link--dashboard > a[aria-current="page"])
        .woocommerce-MyAccount-content > header:first-of-type :is(h2, p) {
            color: #fff;
        }

        .woocommerce-account .woocommerce:has(.woocommerce-MyAccount-navigation-link--dashboard > a[aria-current="page"])
        .woocommerce-MyAccount-content > header:first-of-type h2 {
            font-family: var(--font-heading, Montserrat, sans-serif);
        }

        .woocommerce-account .woocommerce:has(.woocommerce-MyAccount-navigation-link--dashboard > a[aria-current="page"])
        .woocommerce-MyAccount-content > header:first-of-type + .grid {
            grid-template-columns: repeat(auto-fit, minmax(min(100%, 12rem), 1fr));
            margin-top: 1rem;
        }

        .woocommerce-account .woocommerce:has(.woocommerce-MyAccount-navigation-link--dashboard > a[aria-current="page"])
        .woocommerce-MyAccount-content > header:first-of-type + .grid > a {
            position: relative;
            min-width: 0;
            border-color: var(--account-rule);
            background: var(--account-stone);
            text-decoration: none;
        }

        .woocommerce-account .woocommerce:has(.woocommerce-MyAccount-navigation-link--dashboard > a[aria-current="page"])
        .woocommerce-MyAccount-content > header:first-of-type + .grid > a h3 {
            color: var(--account-navy);
            font-family: var(--font-heading, Montserrat, sans-serif);
        }

        .woocommerce-account .woocommerce:has(.woocommerce-MyAccount-navigation-link--dashboard > a[aria-current="page"])
        .woocommerce-MyAccount-content > header:first-of-type + .grid > a svg {
            color: var(--account-cobalt);
        }

        .woocommerce-account .woocommerce:has(.woocommerce-MyAccount-navigation-link--dashboard > a[aria-current="page"])
        .woocommerce-MyAccount-content > header:first-of-type + .grid > a::after {
            position: absolute;
            top: 1.65rem;
            right: 1.4rem;
            width: .45rem;
            height: .45rem;
            border-top: 2px solid var(--account-navy);
            border-right: 2px solid var(--account-navy);
            content: "";
            transform: rotate(45deg);
        }

        .woocommerce-account .woocommerce:has(.woocommerce-MyAccount-navigation-link--dashboard > a[aria-current="page"])
        .woocommerce-MyAccount-content > header:first-of-type + .grid > a:is(:hover, :focus-visible) {
            border-color: var(--account-cobalt);
            background: #fff;
        }

        .woocommerce-account .woocommerce:has(.woocommerce-MyAccount-navigation-link--dashboard > a[aria-current="page"])
        .woocommerce-MyAccount-content > header:first-of-type + .grid > a:is(:hover, :focus-visible) h3 {
            text-decoration: underline;
            text-underline-offset: .18em;
        }

        .woocommerce-account .woocommerce :where(
            #customer_login, .woocommerce-MyAccount-navigation, .woocommerce-MyAccount-content
        ) :is(a, button, input, select, textarea):focus-visible {
            outline: 2px solid var(--account-cobalt);
            outline-offset: 2px;
        }
    </style>
    <?php
}
add_action( 'wp_head', 'blaines_style_customer_account', 99 );

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

