<?php
/**
 * Blaines checkout collection experience.
 *
 * Adds showroom selection, collection-ready date calculation, collection-specific
 * checkout behaviour and full-width fulfilment method presentation.
 *
 * @package Blaines
 */

defined( 'ABSPATH' ) || exit;

/**
 * Return the currently chosen shipping method ID.
 */
function blaines_checkout_get_chosen_shipping_method(): string {
    if ( isset( $_POST['shipping_method'] ) ) {
        $posted = wc_clean( wp_unslash( $_POST['shipping_method'] ) );

        if ( is_array( $posted ) ) {
            $method = reset( $posted );

            if ( is_string( $method ) ) {
                return $method;
            }
        }
    }

    if ( function_exists( 'WC' ) && WC()->session ) {
        $chosen = WC()->session->get( 'chosen_shipping_methods', [] );

        if ( is_array( $chosen ) && ! empty( $chosen[0] ) ) {
            return (string) $chosen[0];
        }
    }

    return '';
}

/**
 * Determine whether a shipping method is Blaines collection/local pickup.
 */
function blaines_checkout_shipping_method_is_collection( string $method_id ): bool {
    return 0 === strpos( $method_id, 'local_pickup' );
}

/**
 * Determine whether checkout is currently using collection.
 */
function blaines_checkout_uses_collection(): bool {
    return blaines_checkout_shipping_method_is_collection( blaines_checkout_get_chosen_shipping_method() );
}

/**
 * Collection locations with safe fallbacks when the schedule plugin is unavailable.
 *
 * @return array<string,array{label:string,closed_days:int[]}>
 */
function blaines_checkout_get_collection_locations(): array {
    if ( function_exists( 'blaines_delivery_schedule_get_collection_locations' ) ) {
        $locations = blaines_delivery_schedule_get_collection_locations();

        if ( ! empty( $locations ) ) {
            return $locations;
        }
    }

    return [
        'swaffham' => [
            'label'       => 'Swaffham',
            'closed_days' => [ 7 ],
        ],
        'dereham' => [
            'label'       => 'Dereham',
            'closed_days' => [ 7 ],
        ],
        'watton' => [
            'label'       => 'Watton',
            'closed_days' => [ 3, 7 ],
        ],
    ];
}

/**
 * Collection ready time in HH:MM format.
 */
function blaines_checkout_get_collection_ready_time(): string {
    if ( function_exists( 'blaines_delivery_schedule_get_collection_ready_time' ) ) {
        return blaines_delivery_schedule_get_collection_ready_time();
    }

    return '11:00';
}

/**
 * Human-readable collection ready time.
 */
function blaines_checkout_get_collection_ready_time_label(): string {
    if ( function_exists( 'blaines_delivery_schedule_format_collection_ready_time' ) ) {
        return blaines_delivery_schedule_format_collection_ready_time();
    }

    return '11am';
}

/**
 * Parse an importer YYYY-MM-DD date into the WordPress timezone.
 */
function blaines_checkout_parse_meta_date( $value ): ?DateTimeImmutable {
    $value = trim( (string) $value );

    if ( '' === $value ) {
        return null;
    }

    $date = DateTimeImmutable::createFromFormat( '!Y-m-d', $value, wp_timezone() );

    if ( ! $date instanceof DateTimeImmutable ) {
        return null;
    }

    $errors = DateTimeImmutable::getLastErrors();

    if ( false !== $errors && ( $errors['warning_count'] > 0 || $errors['error_count'] > 0 ) ) {
        return null;
    }

    return $date;
}

/**
 * Determine the date a product is physically expected to be available at Blaines.
 *
 * Local boxed stock is available at Blaines today. Supplier/manufacturer products
 * use the importer-calculated inbound date into Blaines.
 *
 * @return array{date:?DateTimeImmutable,is_local:bool}
 */
function blaines_checkout_get_product_blaines_available_date( WC_Product $product ): array {
    $product_id = $product->get_id();
    $source     = trim( (string) get_post_meta( $product_id, '_blaines_availability_source', true ) );
    $local_qty  = (int) get_post_meta( $product_id, '_blaines_local_free_stock', true );
    $local_flag = strtolower( trim( (string) get_post_meta( $product_id, '_blaines_local_stock_active', true ) ) );
    $local_on   = in_array( $local_flag, [ 'y', 'yes', 'a', 'active', '1', 'true' ], true );

    if ( 'local' === $source || ( $local_on && $local_qty > 0 ) ) {
        return [
            'date'     => ( new DateTimeImmutable( 'today', wp_timezone() ) )->setTime( 0, 0, 0 ),
            'is_local' => true,
        ];
    }

    $date_keys = [
        '_blaines_expected_supplier_delivery_date',
        '_euronics_expected_supplier_delivery_date',
    ];

    foreach ( $date_keys as $meta_key ) {
        $date = blaines_checkout_parse_meta_date( get_post_meta( $product_id, $meta_key, true ) );

        if ( $date instanceof DateTimeImmutable ) {
            return [
                'date'     => $date,
                'is_local' => false,
            ];
        }
    }

    // Defensive fallback for genuine WooCommerce stock where importer meta is absent.
    if ( $product->is_in_stock() ) {
        return [
            'date'     => ( new DateTimeImmutable( 'today', wp_timezone() ) )->setTime( 0, 0, 0 ),
            'is_local' => false,
        ];
    }

    return [
        'date'     => null,
        'is_local' => false,
    ];
}

/**
 * Determine when the entire basket is physically available at Blaines.
 *
 * Uses the latest product date so the whole order can be collected together.
 *
 * @return array{date:?DateTimeImmutable,all_local:bool}
 */
function blaines_checkout_get_cart_blaines_available_date(): array {
    if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
        return [ 'date' => null, 'all_local' => false ];
    }

    $latest    = null;
    $all_local = true;
    $has_items = false;

    foreach ( WC()->cart->get_cart() as $cart_item ) {
        $product = isset( $cart_item['data'] ) && $cart_item['data'] instanceof WC_Product
            ? $cart_item['data']
            : null;

        if ( ! $product ) {
            continue;
        }

        $has_items = true;
        $resolved  = blaines_checkout_get_product_blaines_available_date( $product );

        if ( ! $resolved['is_local'] ) {
            $all_local = false;
        }

        if ( ! $resolved['date'] instanceof DateTimeImmutable ) {
            return [ 'date' => null, 'all_local' => false ];
        }

        if ( ! $latest || $resolved['date'] > $latest ) {
            $latest = $resolved['date'];
        }
    }

    return [
        'date'      => $latest,
        'all_local' => $has_items && $all_local,
    ];
}

/**
 * Calculate collection date for a showroom from basket stock availability.
 */
function blaines_checkout_get_collection_date( string $location_key ): ?DateTimeImmutable {
    $locations = blaines_checkout_get_collection_locations();

    if ( ! isset( $locations[ $location_key ] ) ) {
        return null;
    }

    $basket = blaines_checkout_get_cart_blaines_available_date();

    if ( ! $basket['date'] instanceof DateTimeImmutable ) {
        return null;
    }

    if ( function_exists( 'blaines_delivery_schedule_get_next_collection_date' ) ) {
        return blaines_delivery_schedule_get_next_collection_date( $location_key, $basket['date'] );
    }

    $candidate   = $basket['date']->modify( '+1 day' )->setTime( 0, 0, 0 );
    $closed_days = $locations[ $location_key ]['closed_days'] ?? [];

    for ( $i = 0; $i <= 21; $i++ ) {
        if ( ! in_array( (int) $candidate->format( 'N' ), $closed_days, true ) ) {
            return $candidate;
        }

        $candidate = $candidate->modify( '+1 day' );
    }

    return null;
}

/**
 * Get collection location stored in checkout session.
 */
function blaines_checkout_get_collection_location(): string {
    $location = '';

    if ( isset( $_POST['blaines_collection_location'] ) ) {
        $location = sanitize_key( wp_unslash( $_POST['blaines_collection_location'] ) );
    } elseif ( function_exists( 'WC' ) && WC()->session ) {
        $location = sanitize_key( (string) WC()->session->get( 'blaines_collection_location', '' ) );
    }

    return array_key_exists( $location, blaines_checkout_get_collection_locations() ) ? $location : '';
}

/**
 * Store collection location while WooCommerce refreshes checkout fragments.
 */
function blaines_checkout_store_collection_location( string $post_data ): void {
    if ( ! function_exists( 'WC' ) || ! WC()->session ) {
        return;
    }

    parse_str( $post_data, $data );

    $location = isset( $data['blaines_collection_location'] )
        ? sanitize_key( (string) $data['blaines_collection_location'] )
        : '';

    if ( array_key_exists( $location, blaines_checkout_get_collection_locations() ) ) {
        WC()->session->set( 'blaines_collection_location', $location );
    }
}
add_action( 'woocommerce_checkout_update_order_review', 'blaines_checkout_store_collection_location', 20 );

/**
 * Render collection selector and calculated ready date in the order-review table.
 */
function blaines_checkout_render_collection_details(): void {
    if ( ! blaines_checkout_uses_collection() ) {
        return;
    }

    $locations   = blaines_checkout_get_collection_locations();
    $selected    = blaines_checkout_get_collection_location();
    $basket      = blaines_checkout_get_cart_blaines_available_date();
    $ready_date  = $selected ? blaines_checkout_get_collection_date( $selected ) : null;
    $ready_time  = blaines_checkout_get_collection_ready_time_label();
    ?>
    <tr class="blaines-collection-details-row">
        <td colspan="2">
            <div class="blaines-collection-checkout-panel">
                <label for="blaines_collection_location" class="blaines-collection-checkout-panel__label">
                    Collection showroom <abbr class="required" title="required">*</abbr>
                </label>

                <select
                    id="blaines_collection_location"
                    name="blaines_collection_location"
                    class="blaines-collection-location-select"
                    required
                >
                    <option value="">Choose a showroom</option>
                    <?php foreach ( $locations as $key => $location ) : ?>
                        <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $selected, $key ); ?>>
                            <?php echo esc_html( $location['label'] ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <?php if ( $selected && $ready_date instanceof DateTimeImmutable ) : ?>
                    <div class="blaines-collection-ready-message">
                        <p class="blaines-collection-ready-message__title">
                            <?php echo esc_html( $basket['all_local'] ? 'Available to collect from' : 'Expected to be available to collect from' ); ?>
                        </p>
                        <p class="blaines-collection-ready-message__date">
                            <?php
                            echo esc_html(
                                wp_date( 'l j F Y', $ready_date->getTimestamp(), wp_timezone() )
                                . ' after '
                                . $ready_time
                            );
                            ?>
                        </p>
                        <p class="blaines-collection-ready-message__note">
                            Web Sales will confirm when your order is ready for collection.
                        </p>
                    </div>
                <?php elseif ( $selected ) : ?>
                    <p class="blaines-collection-ready-message__note">
                        Web Sales will confirm the earliest collection date for this order.
                    </p>
                <?php else : ?>
                    <p class="blaines-collection-ready-message__note">
                        Choose Swaffham, Dereham or Watton to see the expected collection date.
                    </p>
                <?php endif; ?>
            </div>
        </td>
    </tr>
    <?php
}
add_action( 'woocommerce_review_order_after_shipping', 'blaines_checkout_render_collection_details', 20 );

/**
 * Collection orders should not default to a separate shipping address.
 */
function blaines_checkout_disable_ship_to_different_address_for_collection( bool $checked ): bool {
    return blaines_checkout_uses_collection() ? false : $checked;
}
add_filter( 'woocommerce_ship_to_different_address_checked', 'blaines_checkout_disable_ship_to_different_address_for_collection', 20 );

/**
 * Validate collection showroom at place-order time.
 */
function blaines_checkout_validate_collection_location(): void {
    if ( ! blaines_checkout_uses_collection() ) {
        return;
    }

    $location = blaines_checkout_get_collection_location();

    if ( '' === $location ) {
        wc_add_notice( 'Please choose the Blaines showroom you would like to collect from.', 'error' );
    }
}
add_action( 'woocommerce_checkout_process', 'blaines_checkout_validate_collection_location' );

/**
 * Save collection details to the order.
 */
function blaines_checkout_save_collection_order_meta( WC_Order $order, array $data ): void {
    if ( ! blaines_checkout_uses_collection() ) {
        return;
    }

    $locations = blaines_checkout_get_collection_locations();
    $location  = blaines_checkout_get_collection_location();

    if ( '' === $location || ! isset( $locations[ $location ] ) ) {
        return;
    }

    $ready_date = blaines_checkout_get_collection_date( $location );
    $ready_time = blaines_checkout_get_collection_ready_time();

    $order->update_meta_data( '_blaines_collection_location', $location );
    $order->update_meta_data( '_blaines_collection_location_label', $locations[ $location ]['label'] );
    $order->update_meta_data( '_blaines_collection_ready_time', $ready_time );

    if ( $ready_date instanceof DateTimeImmutable ) {
        $order->update_meta_data( '_blaines_expected_collection_date', $ready_date->format( 'Y-m-d' ) );
        $order->update_meta_data(
            '_blaines_expected_collection_label',
            wp_date( 'l j F Y', $ready_date->getTimestamp(), wp_timezone() ) . ' after ' . blaines_checkout_get_collection_ready_time_label()
        );
    }

    // A collection order has no delivery address. Keep billing/contact details only.
    $order->set_shipping_first_name( '' );
    $order->set_shipping_last_name( '' );
    $order->set_shipping_company( '' );
    $order->set_shipping_address_1( '' );
    $order->set_shipping_address_2( '' );
    $order->set_shipping_city( '' );
    $order->set_shipping_state( '' );
    $order->set_shipping_postcode( '' );
    $order->set_shipping_country( '' );
}
add_action( 'woocommerce_checkout_create_order', 'blaines_checkout_save_collection_order_meta', 20, 2 );

/**
 * Add collection details to customer/admin order totals used by emails and order displays.
 *
 * @param array<string,array<string,mixed>> $total_rows Existing rows.
 */
function blaines_checkout_add_collection_order_totals( array $total_rows, WC_Order $order, string $tax_display = '' ): array {
    $location = trim( (string) $order->get_meta( '_blaines_collection_location_label', true ) );

    if ( '' === $location ) {
        return $total_rows;
    }

    $collection_rows = [
        'blaines_collection_location' => [
            'label' => 'Collection showroom:',
            'value' => esc_html( $location ),
        ],
    ];

    $expected = trim( (string) $order->get_meta( '_blaines_expected_collection_label', true ) );

    if ( '' !== $expected ) {
        $collection_rows['blaines_collection_date'] = [
            'label' => 'Expected collection:',
            'value' => esc_html( $expected ),
        ];
    }

    $rebuilt = [];

    foreach ( $total_rows as $key => $row ) {
        if ( 'order_total' === $key ) {
            $rebuilt = array_merge( $rebuilt, $collection_rows );
        }

        $rebuilt[ $key ] = $row;
    }

    if ( ! isset( $rebuilt['blaines_collection_location'] ) ) {
        $rebuilt = array_merge( $rebuilt, $collection_rows );
    }

    return $rebuilt;
}
add_filter( 'woocommerce_get_order_item_totals', 'blaines_checkout_add_collection_order_totals', 20, 3 );

/**
 * Show collection details prominently in WooCommerce admin.
 */
function blaines_checkout_admin_collection_details( WC_Order $order ): void {
    $location = trim( (string) $order->get_meta( '_blaines_collection_location_label', true ) );

    if ( '' === $location ) {
        return;
    }

    $expected = trim( (string) $order->get_meta( '_blaines_expected_collection_label', true ) );
    ?>
    <div class="blaines-admin-collection-details" style="margin-top:12px;padding-top:12px;border-top:1px solid #dcdcde;">
        <p><strong>Collection showroom:</strong> <?php echo esc_html( $location ); ?></p>
        <?php if ( $expected ) : ?>
            <p><strong>Expected collection:</strong> <?php echo esc_html( $expected ); ?></p>
        <?php endif; ?>
    </div>
    <?php
}
add_action( 'woocommerce_admin_order_data_after_shipping_address', 'blaines_checkout_admin_collection_details', 20 );

/**
 * Checkout styles for full-width fulfilment choices and collection panel.
 */
function blaines_checkout_collection_styles(): void {
    if ( ! function_exists( 'is_checkout' ) || ! is_checkout() || is_order_received_page() ) {
        return;
    }
    ?>
    <style>
        /* 14O.3 — checkout hierarchy refinement: stronger product identity + full-width fulfilment. */
        .woocommerce-checkout #shipping_method {
            display: grid !important;
            grid-template-columns: minmax(0, 1fr) !important;
            gap: 0 !important;
            width: 100% !important;
            margin: 2px 0 0 !important;
            padding: 0 !important;
        }

        .woocommerce-checkout #shipping_method li {
            display: flex !important;
            align-items: center !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 0 0 10px !important;
            border: 0 !important;
            border-bottom: 1px solid #edf0f4 !important;
            background: transparent !important;
            box-sizing: border-box !important;
        }

        .woocommerce-checkout #shipping_method li:last-child {
            border-bottom: 0 !important;
        }

        .woocommerce-checkout #shipping_method li input[type="radio"] {
            flex: 0 0 auto;
            margin: 0 11px 0 1px !important;
        }

        .woocommerce-checkout #shipping_method li label {
            display: block !important;
            flex: 1 1 auto !important;
            width: auto !important;
            min-width: 0 !important;
            margin: 0 !important;
            padding: 12px 0 !important;
            border: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
            line-height: 1.35;
            color: #303744;
            cursor: pointer;
        }

        .woocommerce-checkout #shipping_method li.is-selected {
            box-shadow: inset 3px 0 0 #215ED9 !important;
        }

        .woocommerce-checkout #shipping_method li.is-selected label {
            border: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
            color: #143981;
            font-weight: 600;
        }

        /* Let fulfilment read as one full-width section. Woo's table cells otherwise squeeze the methods into the right-hand column. */
        .woocommerce-checkout .woocommerce-checkout-review-order-table tr.woocommerce-shipping-totals.shipping {
            display: block !important;
            width: 100% !important;
            padding: 0 !important;
        }

        .woocommerce-checkout .woocommerce-checkout-review-order-table tr.woocommerce-shipping-totals.shipping > th,
        .woocommerce-checkout .woocommerce-checkout-review-order-table tr.woocommerce-shipping-totals.shipping > td {
            display: block !important;
            width: 100% !important;
            max-width: none !important;
            box-sizing: border-box !important;
            text-align: left !important;
            vertical-align: top !important;
        }

        .woocommerce-checkout .woocommerce-checkout-review-order-table tr.woocommerce-shipping-totals.shipping > th {
            padding: 17px 0 5px !important;
            border: 0 !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            color: #303744 !important;
        }

        .woocommerce-checkout .woocommerce-checkout-review-order-table tr.woocommerce-shipping-totals.shipping > td {
            padding: 0 0 16px !important;
            border: 0 !important;
        }

        .woocommerce-checkout .blaines-collection-details-row td {
            padding-top: 0 !important;
            padding-bottom: 18px !important;
            border-top: 0 !important;
        }

        .blaines-collection-checkout-panel {
            border: 0;
            padding: 2px 0 0;
        }

        .blaines-collection-checkout-panel__label {
            display: block;
            margin-bottom: 7px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .075em;
            color: #5f6773;
        }

        .blaines-collection-location-select {
            width: 100%;
            min-height: 42px;
            border: 1px solid #cfd6df;
            border-radius: 0;
            background: #fff;
            padding: 8px 10px;
            color: #111827;
        }

        /* Collection readiness is information, not another card. */
        .blaines-collection-ready-message {
            margin-top: 17px;
            border: 0;
            background: transparent;
            padding: 0;
        }

        .blaines-collection-ready-message__title {
            margin: 0;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .065em;
            color: #5f6773;
        }

        .blaines-collection-ready-message__date {
            margin: 5px 0 0;
            font-size: 15px;
            font-weight: 600;
            line-height: 1.4;
            color: #143981;
        }

        .blaines-collection-ready-message__note {
            margin: 7px 0 0;
            font-size: 12px;
            font-weight: 400;
            line-height: 1.5;
            color: #69717d;
        }

        /* Give the actual product and price enough prominence before fulfilment begins. */
        .woocommerce-checkout .woocommerce-checkout-review-order-table tbody tr.cart_item td.product-name {
            padding-top: 16px !important;
            padding-bottom: 18px !important;
            padding-right: 14px !important;
            font-size: 14px !important;
            font-weight: 600 !important;
            line-height: 1.45 !important;
            color: #111827 !important;
        }

        .woocommerce-checkout .woocommerce-checkout-review-order-table tbody tr.cart_item td.product-name .product-quantity {
            margin-left: 4px !important;
            font-weight: 400 !important;
            color: #6b7280 !important;
        }

        .woocommerce-checkout .woocommerce-checkout-review-order-table tbody tr.cart_item td.product-total {
            padding-top: 16px !important;
            padding-bottom: 18px !important;
            white-space: nowrap !important;
            text-align: right !important;
            font-size: 17px !important;
            font-weight: 700 !important;
            color: #143981 !important;
        }

        .woocommerce-checkout .woocommerce-checkout-review-order-table thead th {
            padding-bottom: 8px !important;
            border-bottom: 1px solid #e4e8ee !important;
        }

        /* Reduce table-rule noise in the order summary. */
        .woocommerce-checkout .woocommerce-checkout-review-order-table th,
        .woocommerce-checkout .woocommerce-checkout-review-order-table td {
            border-left: 0 !important;
            border-right: 0 !important;
        }

        .woocommerce-checkout .woocommerce-checkout-review-order-table tbody tr.cart_item td {
            border-bottom-color: #edf0f4 !important;
        }

        .woocommerce-checkout .woocommerce-checkout-review-order-table tfoot tr:not(.order-total) th,
        .woocommerce-checkout .woocommerce-checkout-review-order-table tfoot tr:not(.order-total) td {
            border-top-color: #edf0f4 !important;
        }

        /* Make the final order value the strongest information after the product price. */
        .woocommerce-checkout .woocommerce-checkout-review-order-table tfoot tr.order-total th,
        .woocommerce-checkout .woocommerce-checkout-review-order-table tfoot tr.order-total td {
            border-top: 1px solid #cfd6e1 !important;
            padding-top: 20px !important;
            padding-bottom: 19px !important;
            vertical-align: middle !important;
        }

        .woocommerce-checkout .woocommerce-checkout-review-order-table tfoot tr.order-total th {
            font-size: 17px !important;
            font-weight: 700 !important;
            line-height: 1.25 !important;
            color: #111827 !important;
        }

        .woocommerce-checkout .woocommerce-checkout-review-order-table tfoot tr.order-total td {
            text-align: right !important;
            font-size: 23px !important;
            font-weight: 700 !important;
            line-height: 1.1 !important;
            letter-spacing: -0.02em !important;
            color: #143981 !important;
        }

        .woocommerce-checkout .woocommerce-checkout-review-order-table tfoot tr.order-total td .woocommerce-Price-amount,
        .woocommerce-checkout .woocommerce-checkout-review-order-table tfoot tr.order-total td .amount {
            font-size: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
            color: inherit !important;
        }

        /* Payment method presentation now belongs to
         * src/css/woocommerce/checkout.css (CP1A).
         * Do not reintroduce inline !important resets for payment rows or
         * gateway payment boxes here; those suppress the checkout card styles.
         */

        .woocommerce-checkout #payment .place-order {
            margin-top: 18px !important;
            padding: 0 !important;
            border: 0 !important;
            background: transparent !important;
        }

        /* Quiet coupon treatment: useful link, not another full-width box. */
        .woocommerce-checkout .woocommerce-form-coupon-toggle .woocommerce-info {
            margin-bottom: 24px !important;
            padding: 0 !important;
            border: 0 !important;
            border-left: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
        }

        /* Suppress WooCommerce's residual info icon/pseudo-element so the
         * de-framed coupon prompt does not leave a stray square underneath. */
        .woocommerce-checkout .woocommerce-form-coupon-toggle .woocommerce-info::before {
            display: none !important;
            content: none !important;
        }

        /* The left column already has form controls; avoid extra section framing. */
        .woocommerce-checkout .woocommerce-billing-fields,
        .woocommerce-checkout .woocommerce-shipping-fields,
        .woocommerce-checkout .woocommerce-additional-fields {
            border-top-color: transparent !important;
        }

        @media (max-width: 767px) {
            .woocommerce-checkout #shipping_method li label {
                padding: 12px 0 !important;
            }
        }
    </style>
    <?php
}
add_action( 'wp_head', 'blaines_checkout_collection_styles', 99 );

/**
 * Checkout behaviour for collection mode.
 */
function blaines_checkout_collection_script(): void {
    if ( ! function_exists( 'is_checkout' ) || ! is_checkout() || is_order_received_page() ) {
        return;
    }
    ?>
    <script>
    (function () {
        function getSelectedShippingMethod() {
            var checked = document.querySelector('input[type="radio"][name^="shipping_method"]:checked');
            if (checked) return checked.value || '';

            var hidden = document.querySelector('input[type="hidden"][name^="shipping_method"]');
            if (hidden) return hidden.value || '';

            var select = document.querySelector('select[name^="shipping_method"]');
            if (select) return select.value || '';

            return '';
        }

        function isCollection() {
            return getSelectedShippingMethod().indexOf('local_pickup') === 0;
        }

        function updateShippingMethodCards() {
            document.querySelectorAll('#shipping_method li').forEach(function (item) {
                var input = item.querySelector('input[name^="shipping_method"]');
                item.classList.toggle('is-selected', !!(input && input.checked));
            });
        }

        function updateShippingAddressVisibility() {
            var shippingFields = document.querySelector('.woocommerce-shipping-fields');
            var checkbox = document.getElementById('ship-to-different-address-checkbox');
            var collection = isCollection();

            if (collection && checkbox && checkbox.checked) {
                checkbox.checked = false;
                checkbox.dispatchEvent(new Event('change', { bubbles: true }));
            }

            if (shippingFields) {
                shippingFields.style.display = collection ? 'none' : '';
            }
        }

        function updateNotesCopy() {
            var label = document.querySelector('label[for="order_comments"]');
            var textarea = document.getElementById('order_comments');

            if (!label || !textarea) return;

            if (isCollection()) {
                label.innerHTML = 'Collection notes <span class="optional">(optional)</span>';
                textarea.placeholder = 'Add any useful notes for Web Sales about your collection.';
            } else {
                label.innerHTML = 'Delivery or collection notes <span class="optional">(optional)</span>';
                textarea.placeholder = 'Add any useful notes for Web Sales, delivery, collection, access or installation.';
            }
        }

        function updateCheckoutPresentation() {
            updateShippingMethodCards();
            updateShippingAddressVisibility();
            updateNotesCopy();
        }

        document.addEventListener('change', function (event) {
            if (event.target && event.target.matches('.blaines-collection-location-select')) {
                if (window.jQuery) {
                    window.jQuery(document.body).trigger('update_checkout');
                }
            }

            if (event.target && event.target.matches('input[name^="shipping_method"], select[name^="shipping_method"]')) {
                window.setTimeout(updateCheckoutPresentation, 0);
            }
        });

        document.addEventListener('DOMContentLoaded', updateCheckoutPresentation);
        window.addEventListener('load', updateCheckoutPresentation);

        if (window.jQuery) {
            window.jQuery(document.body).on('updated_checkout', updateCheckoutPresentation);
        }
    })();
    </script>
    <?php
}
add_action( 'wp_footer', 'blaines_checkout_collection_script', 100 );
