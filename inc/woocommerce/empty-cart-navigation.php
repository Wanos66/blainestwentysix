<?php
/**
 * Empty-basket return navigation.
 *
 * Remembers the last relevant transactional product so an empty basket does
 * not leave the customer at a dead end.
 *
 * @package Blaines
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'BLAINES_LAST_PRODUCT_COOKIE' ) ) {
    define(
        'BLAINES_LAST_PRODUCT_COOKIE',
        'blaines_last_transactional_product_id'
    );
}

/**
 * Validate a product for use as the empty-basket return destination.
 *
 * @param int $product_id Product ID.
 * @return WC_Product|null
 */
function blaines_get_valid_empty_cart_return_product(
    int $product_id
): ?WC_Product {
    if ( $product_id <= 0 || ! function_exists( 'wc_get_product' ) ) {
        return null;
    }

    $product = wc_get_product( $product_id );

    if (
        ! $product instanceof WC_Product
        || 'publish' !== get_post_status( $product_id )
        || ! $product->is_visible()
    ) {
        return null;
    }

    if (
        function_exists( 'blaines_is_transactional_product' )
        && ! blaines_is_transactional_product( $product_id )
    ) {
        return null;
    }

    return $product;
}

/**
 * Persist a product ID for the current browser session.
 *
 * @param int  $product_id Product ID.
 * @param bool $removed    Whether this was the latest removed basket product.
 * @return void
 */
function blaines_store_empty_cart_return_product(
    int $product_id,
    bool $removed = false
): void {
    if ( ! blaines_get_valid_empty_cart_return_product( $product_id ) ) {
        return;
    }

    if ( function_exists( 'WC' ) && WC()->session ) {
        WC()->session->set(
            'blaines_last_transactional_product_id',
            $product_id
        );

        if ( $removed ) {
            WC()->session->set(
                'blaines_last_removed_product_id',
                $product_id
            );
        }
    }

    /*
     * Use a session-only, HTTP-only functional cookie. It disappears when the
     * browser session ends and contains only a non-sensitive product ID.
     */
    if ( function_exists( 'wc_setcookie' ) ) {
        wc_setcookie(
            BLAINES_LAST_PRODUCT_COOKIE,
            (string) $product_id,
            0,
            is_ssl(),
            true
        );

        $_COOKIE[ BLAINES_LAST_PRODUCT_COOKIE ] = (string) $product_id;
    }
}

/**
 * Remember the current transactional product.
 *
 * @return void
 */
function blaines_remember_viewed_transactional_product(): void {
    if (
        ! function_exists( 'is_product' )
        || ! is_product()
    ) {
        return;
    }

    blaines_store_empty_cart_return_product(
        get_queried_object_id()
    );
}
add_action(
    'template_redirect',
    'blaines_remember_viewed_transactional_product',
    20
);

/**
 * Remember the product most recently removed from the basket.
 *
 * @param string  $cart_item_key Removed cart-item key.
 * @param WC_Cart $cart          Cart instance.
 * @return void
 */
function blaines_remember_removed_cart_product(
    string $cart_item_key,
    WC_Cart $cart
): void {
    $removed_items = $cart->get_removed_cart_contents();
    $removed_item  = $removed_items[ $cart_item_key ] ?? [];

    $product_id = isset( $removed_item['product_id'] )
        ? absint( $removed_item['product_id'] )
        : 0;

    blaines_store_empty_cart_return_product(
        $product_id,
        true
    );
}
add_action(
    'woocommerce_cart_item_removed',
    'blaines_remember_removed_cart_product',
    10,
    2
);

/**
 * Get the best product to return to from an empty basket.
 *
 * The most recently removed product takes priority, followed by the most
 * recently viewed transactional product.
 *
 * @return WC_Product|null
 */
function blaines_get_empty_cart_return_product(): ?WC_Product {
    $candidate_ids = [];

    if ( function_exists( 'WC' ) && WC()->session ) {
        $candidate_ids[] = absint(
            WC()->session->get(
                'blaines_last_removed_product_id',
                0
            )
        );

        $candidate_ids[] = absint(
            WC()->session->get(
                'blaines_last_transactional_product_id',
                0
            )
        );
    }

    if ( isset( $_COOKIE[ BLAINES_LAST_PRODUCT_COOKIE ] ) ) {
        $candidate_ids[] = absint(
            wc_clean(
                wp_unslash(
                    $_COOKIE[ BLAINES_LAST_PRODUCT_COOKIE ]
                )
            )
        );
    }

    foreach ( array_unique( array_filter( $candidate_ids ) ) as $product_id ) {
        $product = blaines_get_valid_empty_cart_return_product(
            (int) $product_id
        );

        if ( $product instanceof WC_Product ) {
            return $product;
        }
    }

    return null;
}
