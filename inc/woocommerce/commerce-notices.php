<?php
/**
 * Shared WooCommerce notice presentation.
 *
 * @package Blaines
 */

defined( 'ABSPATH' ) || exit;

/**
 * Determine whether the current request can display customer commerce notices.
 *
 * @return bool
 */
function blaines_is_commerce_notice_context(): bool {
    return (
        ( function_exists( 'is_woocommerce' ) && is_woocommerce() )
        || ( function_exists( 'is_cart' ) && is_cart() )
        || ( function_exists( 'is_checkout' ) && is_checkout() )
        || ( function_exists( 'is_account_page' ) && is_account_page() )
    );
}

/**
 * Load the shared notice stylesheet on WooCommerce customer-facing pages.
 */
function blaines_enqueue_commerce_notice_styles(): void {
    if ( is_admin() || ! blaines_is_commerce_notice_context() ) {
        return;
    }

    $relative_path = '/assets/css/woocommerce/commerce-notices.css';
    $file_path     = get_template_directory() . $relative_path;

    if ( ! is_readable( $file_path ) ) {
        return;
    }

    wp_enqueue_style(
        'blaines-commerce-notices',
        get_template_directory_uri() . $relative_path,
        [],
        (string) filemtime( $file_path )
    );
}
add_action(
    'wp_enqueue_scripts',
    'blaines_enqueue_commerce_notice_styles',
    30
);
