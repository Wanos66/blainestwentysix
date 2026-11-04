<?php
/**
 * My Account dashboard.
 *
 * @package WooCommerce\Templates
 * @version 4.4.0
 */

defined( 'ABSPATH' ) || exit;

$current_user = wp_get_current_user();
$display_name = $current_user->first_name
    ? $current_user->first_name
    : $current_user->display_name;

$orders_url  = wc_get_endpoint_url( 'orders' );
$address_url = wc_get_endpoint_url( 'edit-address' );
$details_url = wc_get_endpoint_url( 'edit-account' );
?>

<header class="border-b border-neutral-200 pb-6">
    <h2 class="!mb-0">
        <?php
        printf(
            /* translators: %s: customer display name. */
            esc_html__( 'Welcome back, %s', 'blainestwentysix' ),
            esc_html( $display_name )
        );
        ?>
    </h2>

    <p class="mt-2">
        <?php esc_html_e( 'View your orders, manage your addresses and keep your account details up to date.', 'blainestwentysix' ); ?>
    </p>
</header>

<div class="mt-6 grid gap-4 sm:grid-cols-3">

    <a
        href="<?php echo esc_url( $orders_url ); ?>"
        class="group border border-neutral-200 p-5 !text-neutral-950 transition hover:border-blue-700 hover:bg-blue-50"
    >
        <svg class="h-7 w-7 text-blue-700" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M6 3h12v18l-3-2-3 2-3-2-3 2V3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M9 8h6M9 12h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>

        <h3 class="!mb-0 mt-4 !text-base">
            <?php esc_html_e( 'Orders', 'blainestwentysix' ); ?>
        </h3>

        <p class="mt-2 text-sm">
            <?php esc_html_e( 'Review previous and current orders.', 'blainestwentysix' ); ?>
        </p>
    </a>

    <a
        href="<?php echo esc_url( $address_url ); ?>"
        class="group border border-neutral-200 p-5 !text-neutral-950 transition hover:border-blue-700 hover:bg-blue-50"
    >
        <svg class="h-7 w-7 text-blue-700" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M12 21s6-5.1 6-11a6 6 0 1 0-12 0c0 5.9 6 11 6 11Z" stroke="currentColor" stroke-width="1.8"/>
            <circle cx="12" cy="10" r="2" stroke="currentColor" stroke-width="1.8"/>
        </svg>

        <h3 class="!mb-0 mt-4 !text-base">
            <?php esc_html_e( 'Addresses', 'blainestwentysix' ); ?>
        </h3>

        <p class="mt-2 text-sm">
            <?php esc_html_e( 'Update billing and delivery addresses.', 'blainestwentysix' ); ?>
        </p>
    </a>

    <a
        href="<?php echo esc_url( $details_url ); ?>"
        class="group border border-neutral-200 p-5 !text-neutral-950 transition hover:border-blue-700 hover:bg-blue-50"
    >
        <svg class="h-7 w-7 text-blue-700" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle cx="12" cy="8" r="3.5" stroke="currentColor" stroke-width="1.8"/>
            <path d="M5.5 20c.7-4 3-6 6.5-6s5.8 2 6.5 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>

        <h3 class="!mb-0 mt-4 !text-base">
            <?php esc_html_e( 'Account details', 'blainestwentysix' ); ?>
        </h3>

        <p class="mt-2 text-sm">
            <?php esc_html_e( 'Change your name, email or password.', 'blainestwentysix' ); ?>
        </p>
    </a>

</div>

<p class="mt-6 text-sm">
    <?php
    printf(
        /* translators: %s: logout URL. */
        wp_kses(
            __( 'Using a shared computer? <a href="%s">Log out securely</a>.', 'blainestwentysix' ),
            [
                'a' => [
                    'href' => [],
                ],
            ]
        ),
        esc_url( wc_logout_url() )
    );
    ?>
</p>

<?php
do_action( 'woocommerce_account_dashboard' );
do_action( 'woocommerce_before_my_account' );
do_action( 'woocommerce_after_my_account' );
