<?php
/**
 * Commerce page context helpers.
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'blaines_get_commerce_page_context' ) ) {
    /**
     * Build context data for Basket, Checkout, Order Confirmation and My Account pages.
     *
     * @return array<string, mixed>
     */
    function blaines_get_commerce_page_context(): array {
        $current_page_id = get_queried_object_id();
        $page_content    = $current_page_id
            ? (string) get_post_field( 'post_content', $current_page_id )
            : '';

        $is_order_received_page = (
            function_exists( 'is_order_received_page' )
            && is_order_received_page()
        );

        $is_cart_page = (
            (
                function_exists( 'is_cart' )
                && is_cart()
            )
            || (
                $current_page_id
                && has_shortcode( $page_content, 'woocommerce_cart' )
            )
            || is_page( 'cart' )
        );

        $is_checkout_page = (
            ! $is_order_received_page
            && (
                (
                    function_exists( 'is_checkout' )
                    && is_checkout()
                )
                || (
                    $current_page_id
                    && has_shortcode( $page_content, 'woocommerce_checkout' )
                )
                || is_page( 'checkout' )
            )
        );

        $is_account_page = (
            (
                function_exists( 'is_account_page' )
                && is_account_page()
            )
            || (
                $current_page_id
                && has_shortcode( $page_content, 'woocommerce_my_account' )
            )
            || is_page( 'my-account' )
        );

        $is_commerce_page = (
            $is_cart_page
            || $is_checkout_page
            || $is_order_received_page
            || $is_account_page
        );

        $context = [
            'is_commerce_page'       => $is_commerce_page,
            'is_cart_page'           => $is_cart_page,
            'is_checkout_page'       => $is_checkout_page,
            'is_order_received_page' => $is_order_received_page,
            'is_account_page'        => $is_account_page,
            'eyebrow'                => '',
            'title'                  => '',
            'intro'                  => '',
            'order_card_title'       => '',
            'order_card_text'        => '',
            'order_card_link'        => '',
            'order_card_link_label'  => '',
            'steps'                  => [],
        ];

        if ( ! $is_commerce_page ) {
            return $context;
        }

        if ( $is_order_received_page ) {
            $context['eyebrow']          = 'Order confirmation';
            $context['title']            = 'Thank you';
            $context['intro']            = 'Your order has been received. Web Sales will review your order and contact you if anything needs confirming.';
            $context['order_card_title'] = 'What happens next?';
            $context['order_card_text']  = 'We have received your order. Web Sales will review the details and contact you if anything needs confirming.';
        } elseif ( $is_checkout_page ) {
            $context['eyebrow']               = 'Secure checkout';
            $context['title']                 = 'Checkout';
            $context['intro']                 = 'Enter your details and choose your available delivery or collection options to complete your order.';
            $context['order_card_title']      = 'Ready to place your order?';
            $context['order_card_text']       = 'Check your details, fulfilment choice and order total before placing your order.';
            $context['order_card_link']       = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '';
            $context['order_card_link_label'] = 'Return to basket to change items or services';
        } elseif ( $is_cart_page ) {
            $context['eyebrow']          = 'Basket';
            $context['title']            = 'Your basket';
            $context['intro']            = 'Review your selected products before continuing to checkout.';
            $context['order_card_title'] = 'Your order';
            $context['order_card_text']  = 'Review your items, choose any services, then continue securely.';
        } elseif ( $is_account_page ) {
            $context['eyebrow'] = 'Customer account';
            $context['title']   = 'My account';
            $context['intro']   = 'View your orders, manage saved addresses and keep your account details up to date.';
        }

        if ( ! $is_account_page ) {
            $context['steps'] = [
                [
                    'label'    => 'Basket',
                    'active'   => $is_cart_page,
                    'complete' => $is_checkout_page || $is_order_received_page,
                ],
                [
                    'label'    => 'Checkout',
                    'active'   => $is_checkout_page,
                    'complete' => $is_order_received_page,
                ],
                [
                    'label'    => 'Confirmation',
                    'active'   => $is_order_received_page,
                    'complete' => false,
                ],
            ];
        }

        return $context;
    }
}
