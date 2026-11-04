<?php
/**
 * My Account navigation.
 *
 * @package WooCommerce\Templates
 * @version 9.3.0
 */

defined( 'ABSPATH' ) || exit;

$current_user = wp_get_current_user();
$display_name = $current_user->first_name
    ? $current_user->first_name
    : $current_user->display_name;

do_action( 'woocommerce_before_account_navigation' );
?>

<nav
    class="woocommerce-MyAccount-navigation !float-none !w-full border border-neutral-200 bg-white shadow-[0_8px_24px_rgba(0,0,0,0.05)]"
    aria-label="<?php esc_attr_e( 'Account pages', 'woocommerce' ); ?>"
>
    <div class="border-b border-neutral-200 px-5 py-5">
        <p class="text-sm text-neutral-500">
            <?php esc_html_e( 'Signed in as', 'blainestwentysix' ); ?>
        </p>

        <p class="mt-1 font-semibold text-neutral-950">
            <?php echo esc_html( $display_name ); ?>
        </p>

        <?php if ( $current_user->user_email ) : ?>
            <p class="mt-1 break-all text-xs text-neutral-500">
                <?php echo esc_html( $current_user->user_email ); ?>
            </p>
        <?php endif; ?>
    </div>

    <ul class="m-0 list-none divide-y divide-neutral-200 p-0">
        <?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
            <?php $is_current = wc_is_current_account_menu_item( $endpoint ); ?>

            <li class="m-0 list-none p-0 <?php echo esc_attr( wc_get_account_menu_item_classes( $endpoint ) ); ?>">
                <a
                    href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"
                    class="<?php echo esc_attr(
                        $is_current
                            ? 'flex min-h-12 items-center justify-between border-l-4 border-blue-700 bg-blue-50 px-4 py-3 text-sm font-semibold text-neutral-950 no-underline'
                            : 'flex min-h-12 items-center justify-between border-l-4 border-transparent px-4 py-3 text-sm font-medium text-neutral-700 no-underline transition hover:border-neutral-300 hover:bg-neutral-50 hover:text-neutral-950'
                    ); ?>"
                    <?php echo $is_current ? 'aria-current="page"' : ''; ?>
                >
                    <span><?php echo esc_html( $label ); ?></span>

                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                        <path d="m7 4 6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
