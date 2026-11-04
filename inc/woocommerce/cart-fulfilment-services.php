<?php
/**
 * Basket-selectable fulfilment services.
 *
 * Allows customers to select relevant fulfilment services in the basket.
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'blaines_get_valid_cart_fulfilment_services' ) ) {
    /**
     * Get valid services for a cart item/product, keyed by service code.
     *
     * @param WC_Product $product Product object.
     * @return array<string, array<string, mixed>>
     */
    function blaines_get_valid_cart_fulfilment_services( WC_Product $product ): array {
        if ( ! function_exists( 'blaines_get_product_fulfilment_services' ) ) {
            return [];
        }

        $services = blaines_get_product_fulfilment_services( $product );

        if ( empty( $services ) ) {
            return [];
        }

        $keyed = [];

        foreach ( $services as $service ) {
            if ( empty( $service['code'] ) ) {
                continue;
            }

            $keyed[ (string) $service['code'] ] = $service;
        }

        return $keyed;
    }
}

if ( ! function_exists( 'blaines_get_selected_cart_fulfilment_services' ) ) {
    /**
     * Get selected services from a cart item.
     *
     * @param array<string, mixed> $cart_item Cart item.
     * @return array<int, string>
     */
    function blaines_get_selected_cart_fulfilment_services( array $cart_item ): array {
        if (
            function_exists( 'blaines_fulfilment_services_blocked_by_checked_postcode' )
            && blaines_fulfilment_services_blocked_by_checked_postcode()
        ) {
            return [];
        }
        if ( empty( $cart_item['blaines_fulfilment_services'] ) || ! is_array( $cart_item['blaines_fulfilment_services'] ) ) {
            return [];
        }

        return array_values(
            array_filter(
                array_map(
                    'sanitize_text_field',
                    $cart_item['blaines_fulfilment_services']
                )
            )
        );
    }
}

if ( ! function_exists( 'blaines_update_cart_fulfilment_services' ) ) {
    /**
     * Store selected fulfilment services against cart items when the basket is updated.
     *
     * If the customer's checked postcode is outside the local delivery area,
     * remove any selected services so they cannot be charged or carried through
     * to checkout/order data.
     *
     * @param bool $cart_updated Whether the cart was updated.
     * @return bool
     */
    function blaines_update_cart_fulfilment_services( bool $cart_updated ): bool {
        if (
            ! function_exists( 'WC' )
            || ! WC()->cart
            || WC()->cart->is_empty()
        ) {
            return $cart_updated;
        }

        /*
         * If the product-page postcode checker has confirmed that delivery is
         * unavailable, optional fulfilment services must not remain selected.
         */
        if (
            function_exists( 'blaines_fulfilment_services_blocked_by_checked_postcode' )
            && blaines_fulfilment_services_blocked_by_checked_postcode()
        ) {
            foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                unset( WC()->cart->cart_contents[ $cart_item_key ]['blaines_fulfilment_services'] );
            }

            WC()->cart->set_session();

            return $cart_updated;
        }

        $posted_services = [];

        if (
            isset( $_POST['blaines_fulfilment_services'] )
            && is_array( $_POST['blaines_fulfilment_services'] )
        ) {
            $posted_services = wc_clean( wp_unslash( $_POST['blaines_fulfilment_services'] ) );
        }

        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
            if (
                empty( $cart_item['data'] )
                || ! $cart_item['data'] instanceof WC_Product
            ) {
                unset( WC()->cart->cart_contents[ $cart_item_key ]['blaines_fulfilment_services'] );
                continue;
            }

            $product        = $cart_item['data'];
            $valid_services = blaines_get_valid_cart_fulfilment_services( $product );

            if ( empty( $valid_services ) ) {
                unset( WC()->cart->cart_contents[ $cart_item_key ]['blaines_fulfilment_services'] );
                continue;
            }

            $selected_services = [];

            if (
                isset( $posted_services[ $cart_item_key ] )
                && is_array( $posted_services[ $cart_item_key ] )
            ) {
                foreach ( $posted_services[ $cart_item_key ] as $service_code ) {
                    $service_code = sanitize_text_field( (string) $service_code );

                    if ( isset( $valid_services[ $service_code ] ) ) {
                        $selected_services[] = $service_code;
                    }
                }
            }

            $selected_services = array_values( array_unique( $selected_services ) );

            if ( empty( $selected_services ) ) {
                unset( WC()->cart->cart_contents[ $cart_item_key ]['blaines_fulfilment_services'] );
                continue;
            }

            WC()->cart->cart_contents[ $cart_item_key ]['blaines_fulfilment_services'] = $selected_services;
        }

        WC()->cart->set_session();

        return $cart_updated;
    }

    add_filter( 'woocommerce_update_cart_action_cart_updated', 'blaines_update_cart_fulfilment_services', 20 );
}

if ( ! function_exists( 'blaines_add_cart_fulfilment_service_fees' ) ) {
    /**
     * Add selected fulfilment services as grouped fee lines.
     *
     * @param WC_Cart $cart Cart object.
     * @return void
     */
    function blaines_add_cart_fulfilment_service_fees( WC_Cart $cart ): void {
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
            return;
        }

        if ( $cart->is_empty() ) {
            return;
        }

        $group_labels = [
            'installation'  => 'Installation services',
            'disconnection' => 'Disconnection services',
            'disposal'      => 'Disposal services',
            'packaging'     => 'Packaging removal',
            'setup'         => 'Setup services',
        ];

        $fees = [];

        foreach ( $cart->get_cart() as $cart_item ) {
            if ( empty( $cart_item['data'] ) || ! $cart_item['data'] instanceof WC_Product ) {
                continue;
            }

            $product           = $cart_item['data'];
            $quantity          = max( 1, (int) ( $cart_item['quantity'] ?? 1 ) );
            $valid_services    = blaines_get_valid_cart_fulfilment_services( $product );
            $selected_services = blaines_get_selected_cart_fulfilment_services( $cart_item );

            if ( empty( $valid_services ) || empty( $selected_services ) ) {
                continue;
            }

            foreach ( $selected_services as $service_code ) {
                if ( empty( $valid_services[ $service_code ] ) ) {
                    continue;
                }

                $service = $valid_services[ $service_code ];
                $group   = isset( $service['group'] ) ? (string) $service['group'] : 'other';
                $price   = isset( $service['price'] ) ? (float) $service['price'] : 0.00;

                if ( $price <= 0 ) {
                    continue;
                }

                $label = $group_labels[ $group ] ?? 'Additional services';

                if ( ! isset( $fees[ $label ] ) ) {
                    $fees[ $label ] = 0.00;
                }

                $fees[ $label ] += $price * $quantity;
            }
        }

        foreach ( $fees as $label => $amount ) {
            $cart->add_fee( $label, $amount, false );
        }
    }

    add_action( 'woocommerce_cart_calculate_fees', 'blaines_add_cart_fulfilment_service_fees', 20 );
}

if ( ! function_exists( 'blaines_show_selected_fulfilment_services_in_checkout' ) ) {
    /**
     * Show selected services under the product in checkout/order review.
     *
     * Prices are not repeated here because the service charges are already
     * shown as grouped fee lines in the checkout totals.
     *
     * @param array<int, array<string, mixed>> $item_data Item data.
     * @param array<string, mixed>            $cart_item Cart item.
     * @return array<int, array<string, mixed>>
     */
    function blaines_show_selected_fulfilment_services_in_checkout( array $item_data, array $cart_item ): array {
        if ( is_cart() ) {
            return $item_data;
        }

        if ( empty( $cart_item['data'] ) || ! $cart_item['data'] instanceof WC_Product ) {
            return $item_data;
        }

        $valid_services    = blaines_get_valid_cart_fulfilment_services( $cart_item['data'] );
        $selected_services = blaines_get_selected_cart_fulfilment_services( $cart_item );

        if ( empty( $valid_services ) || empty( $selected_services ) ) {
            return $item_data;
        }

        $service_labels = [];

        foreach ( $selected_services as $service_code ) {
            if ( empty( $valid_services[ $service_code ] ) ) {
                continue;
            }

            $service = $valid_services[ $service_code ];
            $label   = isset( $service['label'] ) ? (string) $service['label'] : $service_code;

            $service_labels[] = $label;
        }

        if ( empty( $service_labels ) ) {
            return $item_data;
        }

        $item_data[] = [
            'key'   => 'Selected services',
            'value' => implode( ', ', $service_labels ),
        ];

        return $item_data;
    }

    add_filter( 'woocommerce_get_item_data', 'blaines_show_selected_fulfilment_services_in_checkout', 20, 2 );
}

if ( ! function_exists( 'blaines_add_fulfilment_services_to_order_item' ) ) {
    /**
     * Store selected services against the order line item.
     *
     * @param WC_Order_Item_Product $item          Order line item.
     * @param string                $cart_item_key Cart item key.
     * @param array<string, mixed>  $values        Cart item values.
     * @param WC_Order              $order         Order object.
     * @return void
     */
    function blaines_add_fulfilment_services_to_order_item( WC_Order_Item_Product $item, string $cart_item_key, array $values, WC_Order $order ): void {
        if ( empty( $values['data'] ) || ! $values['data'] instanceof WC_Product ) {
            return;
        }

        $valid_services    = blaines_get_valid_cart_fulfilment_services( $values['data'] );
        $selected_services = blaines_get_selected_cart_fulfilment_services( $values );

        if ( empty( $valid_services ) || empty( $selected_services ) ) {
            return;
        }

        $service_labels = [];

        foreach ( $selected_services as $service_code ) {
            if ( empty( $valid_services[ $service_code ] ) ) {
                continue;
            }

            $service = $valid_services[ $service_code ];
            $label   = isset( $service['label'] ) ? (string) $service['label'] : $service_code;
            $price   = isset( $service['price'] ) ? (float) $service['price'] : 0.00;

            $service_labels[] = $label . ' — ' . wp_strip_all_tags( wc_price( $price ) );
        }

        if ( empty( $service_labels ) ) {
            return;
        }

        $item->add_meta_data( 'Selected fulfilment services', implode( ', ', $service_labels ), true );
    }

    add_action( 'woocommerce_checkout_create_order_line_item', 'blaines_add_fulfilment_services_to_order_item', 20, 4 );
}
