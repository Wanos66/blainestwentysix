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
 * Keep the applied destination for rate previews while a delivery address is being edited.
 */
function blaines_checkout_preserve_delivery_postcode( string $post_data ): void {
    parse_str( $post_data, $data );
    if (
        empty( $data['ship_to_different_address'] )
        || ! isset( $_POST['s_postcode'] )
        || ! is_string( $_POST['s_postcode'] )
        || ! WC()->customer
    ) {
        return;
    }

    $customer = WC()->customer;
    if ( '1' === ( $data['blaines_delivery_address_draft'] ?? '' ) ) {
        $_POST['s_country']   = wp_slash( $customer->get_shipping_country() );
        $_POST['s_state']     = wp_slash( $customer->get_shipping_state() );
        $_POST['s_postcode']  = wp_slash( $customer->get_shipping_postcode() );
        $_POST['s_city']      = wp_slash( $customer->get_shipping_city() );
        $_POST['s_address']   = wp_slash( $customer->get_shipping_address_1() );
        $_POST['s_address_2'] = wp_slash( $customer->get_shipping_address_2() );
        return;
    }

    if ( 'GB' !== ( $_POST['s_country'] ?? '' ) ) {
        return;
    }

    $postcode = wc_clean( wp_unslash( $_POST['s_postcode'] ) );
    if ( '' !== $postcode && WC_Validation::is_postcode( $postcode, 'GB' ) ) {
        return;
    }

    // WooCommerce reads this AJAX field after the hook. The checkout form and
    // the postcode submitted when placing the order retain the shopper's input.
    $previous = $customer->get_shipping_postcode();
    $_POST['s_postcode'] = 'GB' === $customer->get_shipping_country()
        && '' !== $previous && WC_Validation::is_postcode( $previous, 'GB' )
        ? wp_slash( $previous )
        : '';
}
add_action( 'woocommerce_checkout_update_order_review', 'blaines_checkout_preserve_delivery_postcode' );

/**
 * Ask the shopper to apply their edited address before placing a delivery order.
 */
function blaines_checkout_require_applied_delivery_address(): void {
    if (
        '1' === ( $_POST['blaines_delivery_address_draft'] ?? '' )
        && ! empty( $_POST['ship_to_different_address'] )
        && ! blaines_checkout_uses_collection()
    ) {
        wc_add_notice( __( 'Click Update delivery address to check delivery before placing your order.', 'blainestwentysix' ), 'error' );
    }
}
add_action( 'woocommerce_checkout_process', 'blaines_checkout_require_applied_delivery_address' );

/**
 * Render collection selector and calculated ready date in the order-review table.
 */
function blaines_checkout_render_collection_details(): void {
    // WooCommerce may have replaced the requested method while recalculating rates.
    $chosen_methods = WC()->session ? WC()->session->get( 'chosen_shipping_methods', [] ) : [];
    $method = (string) ( $chosen_methods[0] ?? '' );

    if ( ! blaines_checkout_shipping_method_is_collection( $method ) ) {
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
 * Retrieve the saved delivery address for a checked postcode on checkout entry.
 *
 * Keep a deliberate collection choice, and leave an address for a different
 * postcode for the customer to enter. Use the account address, since the
 * postcode checker can change the postcode on the session customer.
 */
function blaines_checkout_get_checked_saved_delivery_customer(): ?WC_Customer {
    static $resolved = false;
    static $customer = null;

    if ( $resolved ) {
        return $customer;
    }
    $resolved = true;

    if (
        'GET' !== ( $_SERVER['REQUEST_METHOD'] ?? '' )
        || ! is_checkout()
        || is_order_received_page()
        || ! is_user_logged_in()
        || ! function_exists( 'blaines_get_checked_product_postcode' )
        || ! function_exists( 'blaines_checked_postcode_has_delivery' )
        || true !== blaines_checked_postcode_has_delivery()
    ) {
        return null;
    }

    $chosen_method = explode( ':', blaines_checkout_get_chosen_shipping_method(), 2 )[0];
    if ( ! in_array( $chosen_method, [ 'flat_rate', 'free_shipping' ], true ) ) {
        return null;
    }

    $saved = new WC_Customer( get_current_user_id() );
    $checked_postcode = wc_format_postcode( blaines_get_checked_product_postcode(), 'GB' );
    $shipping_postcode = wc_format_postcode( $saved->get_shipping_postcode(), 'GB' );
    $billing_postcode = wc_format_postcode( $saved->get_billing_postcode(), 'GB' );

    if (
        '' === $checked_postcode
        || $checked_postcode !== $shipping_postcode
        || $shipping_postcode === $billing_postcode
        || 'GB' !== $saved->get_shipping_country()
        || '' === trim( $saved->get_shipping_address_1() )
        || '' === trim( $saved->get_shipping_city() )
    ) {
        return null;
    }

    $customer = $saved;
    return $customer;
}

/**
 * Keep the saved delivery destination active when checkout first calculates rates.
 */
function blaines_checkout_default_separate_delivery_address( bool $checked ): bool {
    if ( blaines_checkout_uses_collection() ) {
        return false;
    }

    return $checked || null !== blaines_checkout_get_checked_saved_delivery_customer();
}
add_filter( 'woocommerce_ship_to_different_address_checked', 'blaines_checkout_default_separate_delivery_address', 20 );

/**
 * Prefill the complete saved delivery address as a unit.
 */
function blaines_checkout_prefill_checked_saved_delivery_address( $value, string $field ) {
    $address_fields = [
        'shipping_first_name', 'shipping_last_name', 'shipping_company',
        'shipping_address_1', 'shipping_address_2', 'shipping_city',
        'shipping_state', 'shipping_postcode', 'shipping_country', 'shipping_phone',
    ];

    if ( null !== $value || ! in_array( $field, $address_fields, true ) ) {
        return $value;
    }

    $customer = blaines_checkout_get_checked_saved_delivery_customer();
    $getter   = 'get_' . $field;

    return $customer && is_callable( [ $customer, $getter ] )
        ? $customer->$getter()
        : $value;
}
add_filter( 'woocommerce_checkout_get_value', 'blaines_checkout_prefill_checked_saved_delivery_address', 20, 2 );

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
 * Give the classic checkout a fulfilment section before the billing fields.
 */
function blaines_checkout_render_fulfilment_section(): void {
    ?>
    <section class="blaines-checkout-fulfilment" aria-labelledby="blaines-checkout-fulfilment-title" data-blaines-checkout-fulfilment hidden>
        <h2 id="blaines-checkout-fulfilment-title"><?php esc_html_e( 'Delivery or collection', 'blainestwentysix' ); ?></h2>
        <div class="blaines-checkout-fulfilment__methods" role="group" aria-labelledby="blaines-checkout-fulfilment-title" data-blaines-fulfilment-methods></div>
        <input type="hidden" name="blaines_delivery_address_draft" value="1" data-blaines-delivery-address-draft disabled>
        <div class="blaines-delivery-address-summary" data-blaines-delivery-address-summary hidden>
            <div>
                <h3><?php esc_html_e( 'Delivery address', 'blainestwentysix' ); ?></h3>
                <p data-blaines-delivery-address-text></p>
            </div>
            <button type="button" class="blaines-delivery-address-edit" data-blaines-delivery-address-edit>
                <?php esc_html_e( 'Edit address', 'blainestwentysix' ); ?>
            </button>
        </div>
        <div class="blaines-delivery-address-summary" data-blaines-collection-address-result hidden>
            <div role="status">
                <h3><?php esc_html_e( 'Collection only for this address', 'blainestwentysix' ); ?></h3>
                <p><?php esc_html_e( 'Local delivery is unavailable for this postcode.', 'blainestwentysix' ); ?></p>
            </div>
            <button type="button" class="blaines-delivery-address-edit" data-blaines-delivery-address-edit>
                <?php esc_html_e( 'Try another address', 'blainestwentysix' ); ?>
            </button>
        </div>
        <div data-blaines-fulfilment-address></div>
        <div class="blaines-delivery-address-actions" data-blaines-delivery-address-actions hidden>
            <p data-blaines-delivery-address-error role="alert" hidden>
                <?php esc_html_e( 'Please complete or correct your delivery address.', 'blainestwentysix' ); ?>
            </p>
            <p data-blaines-delivery-postcode-error role="alert" hidden>
                <?php esc_html_e( 'Enter a full UK delivery postcode, for example NR19 1BX.', 'blainestwentysix' ); ?>
            </p>
            <button type="button" class="blaines-delivery-address-edit blaines-delivery-address-update" data-blaines-delivery-address-update>
                <?php esc_html_e( 'Update delivery address', 'blainestwentysix' ); ?>
            </button>
        </div>
        <div data-blaines-fulfilment-date></div>
        <div data-blaines-fulfilment-showroom></div>
    </section>
    <?php
}
add_action( 'woocommerce_checkout_billing', 'blaines_checkout_render_fulfilment_section', 2 );

/**
 * Checkout styles for full-width fulfilment choices and collection panel.
 */
function blaines_checkout_collection_styles(): void {
    if ( ! function_exists( 'is_checkout' ) || ! is_checkout() || is_order_received_page() ) {
        return;
    }
    ?>
    <style>
        .woocommerce-checkout .blaines-checkout-fulfilment {
            --fulfilment-gutter: clamp(1rem, 3vw, 2rem);
            margin: 0 0 1rem;
            padding: 0 var(--fulfilment-gutter) var(--fulfilment-gutter);
            border: 1px solid #aeb9cc;
            background: #fff;
            color: #0e295e;
        }

        .woocommerce-checkout .blaines-checkout-fulfilment h2 {
            margin: 0 calc(-1 * var(--fulfilment-gutter)) var(--fulfilment-gutter);
            padding: .85rem var(--fulfilment-gutter);
            background: #0e295e;
            color: #fff;
            font: 700 clamp(1.3rem, 2.5vw, 1.7rem)/1.2 var(--font-heading, sans-serif);
            letter-spacing: -.025em;
        }

        .woocommerce-checkout .blaines-checkout-fulfilment__methods {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(100%, 12rem), 1fr));
            gap: .625rem;
        }

        .woocommerce-checkout .blaines-checkout-fulfilment__option {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .625rem;
            min-height: 3.25rem;
            margin: 0;
            padding: .75rem .9rem;
            border: 1px solid #7c8ba5;
            border-radius: 0;
            background: #fff;
            color: #0e295e;
            font: inherit;
            font-weight: 700;
            line-height: 1.35;
            text-align: left;
        }

        .woocommerce-checkout button.blaines-checkout-fulfilment__option {
            cursor: pointer;
        }

        .woocommerce-checkout button.blaines-checkout-fulfilment__option[aria-pressed="false"]:not(:disabled):hover {
            border-color: #143981;
            background: #f3f0e9;
        }

        .woocommerce-checkout .blaines-checkout-fulfilment__option:is([aria-pressed="true"], p) {
            border-color: #215ed9;
            background: #215ed9;
            color: #fff;
        }

        .woocommerce-checkout .blaines-checkout-fulfilment__option[aria-pressed="true"]::after {
            flex: none;
            width: .45rem;
            height: .8rem;
            margin: 0 .25rem .18rem 0;
            border-right: 2px solid currentColor;
            border-bottom: 2px solid currentColor;
            content: "";
            transform: rotate(45deg);
        }

        .woocommerce-checkout .blaines-checkout-fulfilment__option:focus-visible {
            outline: 2px solid #215ed9;
            outline-offset: 2px;
        }

        .woocommerce-checkout .blaines-delivery-address-summary {
            display: flex;
            align-items: start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: .6rem 1rem;
            margin-top: 1.15rem;
            padding: .9rem 1rem;
            background: #f3f0e9;
        }

        .woocommerce-checkout .blaines-delivery-address-summary > div {
            flex: 1 1 12rem;
            min-width: 0;
        }

        .woocommerce-checkout .blaines-delivery-address-summary h3 {
            margin: 0 0 .3rem;
            color: #0e295e;
            font: 700 1rem/1.35 var(--font-heading, sans-serif);
        }

        .woocommerce-checkout .blaines-delivery-address-summary p {
            margin: 0;
            color: #303744;
            line-height: 1.5;
            overflow-wrap: anywhere;
        }

        .woocommerce-checkout .blaines-delivery-address-edit {
            min-height: 2.75rem;
            padding: .5rem .8rem;
            border: 1px solid #143981;
            border-radius: 0;
            background: #fff;
            color: #143981;
            font: inherit;
            font-weight: 650;
            cursor: pointer;
        }

        .woocommerce-checkout .blaines-delivery-address-edit:hover {
            background: #0e295e;
            color: #fff;
        }

        .woocommerce-checkout .blaines-delivery-address-edit:focus-visible {
            outline: 2px solid #215ed9;
            outline-offset: 2px;
        }

        .woocommerce-checkout .blaines-delivery-address-actions {
            margin-top: .75rem;
        }

        .woocommerce-checkout .blaines-delivery-address-actions p {
            margin: 0 0 .5rem;
            padding: .7rem .85rem;
            background: #8c2737;
            color: #fff;
            font-weight: 650;
        }

        .woocommerce-checkout .blaines-delivery-address-update {
            border-color: #143981;
            background: #143981;
            color: #fff;
        }

        .woocommerce-checkout .blaines-delivery-address-update:hover {
            background: #0e295e;
        }

        .woocommerce-checkout #customer_details .blaines-checkout-fulfilment:not([hidden]) ~ .woocommerce-billing-fields {
            margin: 0 0 1rem !important;
            padding: clamp(1.25rem, 3vw, 2rem) !important;
            border: 1px solid #aeb9cc !important;
            background: #fff;
        }

        .woocommerce-checkout #customer_details:has(.blaines-checkout-fulfilment:not([hidden])) .woocommerce-additional-fields {
            padding: clamp(1.25rem, 3vw, 2rem) !important;
            border: 1px solid #aeb9cc !important;
            background: #fff;
        }

        .woocommerce-checkout #customer_details:has(.blaines-checkout-fulfilment:not([hidden])) :is(.woocommerce-billing-fields, .woocommerce-additional-fields) > h3 {
            margin: 0 0 1rem !important;
            padding-bottom: .65rem;
            border-bottom: 2px solid #0e295e;
            color: #0e295e;
            font: 700 clamp(1.3rem, 2.5vw, 1.7rem)/1.25 var(--font-heading, sans-serif);
            letter-spacing: -.025em;
        }

        .woocommerce-checkout #customer_details:has(.blaines-checkout-fulfilment:not([hidden])) :is(.woocommerce-billing-fields, .woocommerce-additional-fields)::before,
        .woocommerce-checkout #customer_details:has(.blaines-checkout-fulfilment:not([hidden])) :is(.woocommerce-billing-fields, .woocommerce-additional-fields) > h3::after {
            display: none;
        }

        .woocommerce-checkout .blaines-checkout-fulfilment .woocommerce-shipping-fields[data-blaines-address-collapsed] {
            display: none !important;
        }

        .woocommerce-checkout .blaines-checkout-fulfilment .woocommerce-shipping-fields {
            padding: .65rem 0 0 !important;
            border: 0 !important;
        }

        .woocommerce-checkout .blaines-checkout-fulfilment .blaines-preferred-delivery-date-card,
        .woocommerce-checkout .blaines-checkout-fulfilment .blaines-collection-checkout-panel {
            margin: 1.15rem 0 0 !important;
            padding: .9rem 0 0 !important;
            border: 0 !important;
            border-top: 1px solid #aeb9cc !important;
            background: transparent !important;
            text-align: left;
        }

        .woocommerce-checkout .blaines-checkout-fulfilment .blaines-preferred-delivery-date-card h3::after {
            display: none;
        }

        .woocommerce-checkout .blaines-checkout-fulfilment .blaines-preferred-delivery-date-card h3 {
            font-size: 1.05rem;
        }

        .woocommerce-checkout #shipping_method[data-blaines-mirrored],
        .woocommerce-checkout .blaines-collection-details-row[data-blaines-moved],
        .woocommerce-checkout .blaines-checkout-fulfilment [hidden] {
            display: none !important;
        }

        .woocommerce-checkout [data-blaines-method-summary] {
            display: block;
            padding: .45rem 0;
            text-align: left;
        }

        /* Keep notices distinct from coupon, sign-in and gateway interfaces. */
        body.woocommerce-checkout .woocommerce
        :is(.woocommerce-error, .woocommerce-message, .woocommerce-info):not(
            .woocommerce-form-coupon-toggle *, .woocommerce-form-login-toggle *, .payment_box *
        ) {
            --checkout-notice-background: #0e295e;
            border: 0 !important;
            background: var(--checkout-notice-background) !important;
            color: #fff !important;
            font-size: .94rem;
            font-weight: 600;
            overflow-wrap: anywhere;
        }

        body.woocommerce-checkout .woocommerce .woocommerce-error:not(.payment_box *) {
            --checkout-notice-background: #8c2737;
        }

        body.woocommerce-checkout .woocommerce .woocommerce-message:not(.payment_box *) {
            --checkout-notice-background: #195845;
        }

        body.woocommerce-checkout .woocommerce
        :is(.woocommerce-error, .woocommerce-message, .woocommerce-info):not(
            .woocommerce-form-coupon-toggle *, .woocommerce-form-login-toggle *, .payment_box *
        )::before {
            color: #fff !important;
        }

        body.woocommerce-checkout .woocommerce
        :is(.woocommerce-error, .woocommerce-message, .woocommerce-info):not(
            .woocommerce-form-coupon-toggle *, .woocommerce-form-login-toggle *, .payment_box *
        ) a:not(.button) {
            color: #fff !important;
            text-decoration: underline;
            text-decoration-color: currentColor;
        }

        body.woocommerce-checkout .woocommerce
        :is(.woocommerce-error, .woocommerce-message, .woocommerce-info):not(
            .woocommerce-form-coupon-toggle *, .woocommerce-form-login-toggle *, .payment_box *
        ) .button {
            border-color: #fff !important;
            background: #fff !important;
            color: #0e295e !important;
        }

        body.woocommerce-checkout .woocommerce
        :is(.woocommerce-error, .woocommerce-message, .woocommerce-info):not(
            .woocommerce-form-coupon-toggle *, .woocommerce-form-login-toggle *, .payment_box *
        ) .button:hover {
            border-color: #f3f0e9 !important;
            background: #f3f0e9 !important;
        }

        body.woocommerce-checkout .woocommerce
        :is(.woocommerce-error, .woocommerce-message, .woocommerce-info):not(
            .woocommerce-form-coupon-toggle *, .woocommerce-form-login-toggle *, .payment_box *
        ) a:focus-visible {
            outline: 2px solid #fff !important;
            outline-offset: 2px;
        }

        body.woocommerce-checkout #payment .woocommerce-terms-and-conditions-wrapper .blaines-checkout-terms-error {
            border: 0 !important;
            background: #8c2737 !important;
            color: #fff !important;
        }

        .woocommerce-checkout #blaines-service-delivery-block-notice {
            border: 0;
            background: #8c2737;
            color: #fff;
        }

        .woocommerce-checkout #blaines-service-delivery-block-notice > p,
        .woocommerce-checkout #blaines-service-delivery-block-notice a {
            color: #fff;
        }

        .woocommerce-checkout #blaines-service-delivery-block-notice > p:first-child {
            font: 700 1rem/1.35 var(--font-heading, sans-serif);
            letter-spacing: 0;
            text-transform: none;
        }

        .woocommerce-checkout #blaines-service-delivery-block-notice a:first-child {
            border-color: #fff;
            background: #fff;
            color: #0e295e;
        }

        .woocommerce-checkout #blaines-service-delivery-block-notice a:first-child:hover {
            border-color: #f3f0e9;
            background: #f3f0e9;
        }

        .woocommerce-checkout #blaines-service-delivery-block-notice a:focus-visible {
            outline: 2px solid #fff;
            outline-offset: 2px;
        }

        @media (min-width: 1024px) {
            .woocommerce-checkout form.checkout > .woocommerce-NoticeGroup-checkout:not(:empty) {
                grid-column: 1 / -1;
                grid-row: 1;
            }

            .woocommerce-checkout form.checkout:has(> .woocommerce-NoticeGroup-checkout:not(:empty))
            > :is(#customer_details, #order_review_heading, #order_review) {
                grid-row: 2;
            }
        }

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
        var revealDeliveryOnError = false;
        var editingDeliveryAddress = false;
        var awaitingDeliveryRates = false;
        var collectionOnlyAddress = false;

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

        function hasIncompleteDeliveryPostcode(address) {
            var country = address.querySelector('#shipping_country');
            var postcode = address.querySelector('#shipping_postcode');
            if (!country || country.value !== 'GB' || !postcode) return false;

            var value = postcode.value.replace(/\s+/g, '').toUpperCase();
            var fullPostcode = /^[A-PR-UWYZ](?:[A-HK-Y]?\d{1,2}|\d[A-HJKPSTUW]|[A-HK-Y]\d[ABEHMNPRVWXY])\d[ABD-HJLNP-UW-Z]{2}$/;
            return !fullPostcode.test(value) && !/^(?:GIR0AA|BFPO\d{1,4})$/.test(value);
        }

        function hasLocalDeliveryOption() {
            return Array.from(document.querySelectorAll(
                'input[name^="shipping_method"], select[name^="shipping_method"] option'
            )).some(function (option) {
                return !option.disabled && /^(free_shipping|flat_rate)(:|$)/.test(option.value);
            });
        }

        function updateDeliveryAddressVisibility(collection) {
            var fields = document.querySelector('.woocommerce-shipping-fields');
            if (!fields) return;

            // The collection-only result provides its own way to reopen the editor.
            var hide = collection && (hasLocalDeliveryOption() || (collectionOnlyAddress && !editingDeliveryAddress))
                && !revealDeliveryOnError
                && !fields.querySelector(':invalid, .woocommerce-invalid');

            fields.style.display = hide ? 'none' : '';
        }

        function syncDeliveryAddressSummary(section, address, collection) {
            var summary = section.querySelector('[data-blaines-delivery-address-summary]');
            var collectionResult = section.querySelector('[data-blaines-collection-address-result]');
            var actions = section.querySelector('[data-blaines-delivery-address-actions]');
            if (!summary || !address) return;

            var checkbox = address.querySelector('#ship-to-different-address-checkbox');
            var keys = ['first_name', 'last_name', 'address_1', 'address_2', 'city', 'postcode'];
            var values = keys.map(function (key) {
                var field = address.querySelector('#shipping_' + key);
                return field ? field.value.trim() : '';
            });
            var complete = values[0] && values[1] && values[2] && values[4] && values[5];
            var collapse = !collection && /^(free_shipping|flat_rate)(:|$)/.test(getSelectedShippingMethod())
                && checkbox && checkbox.checked && complete && !editingDeliveryAddress
                && !revealDeliveryOnError && !hasIncompleteDeliveryPostcode(address)
                && !address.querySelector(':invalid, .woocommerce-invalid');

            summary.hidden = !collapse;
            collectionResult.hidden = !collectionOnlyAddress || !collection || hasLocalDeliveryOption()
                || editingDeliveryAddress || address.style.display !== 'none';
            address.toggleAttribute('data-blaines-address-collapsed', !!collapse);
            actions.hidden = !!collapse || !checkbox || !checkbox.checked || address.style.display === 'none';
            if (actions.hidden) {
                actions.querySelector('[data-blaines-delivery-address-error]').hidden = true;
                actions.querySelector('[data-blaines-delivery-postcode-error]').hidden = true;
            }

            if (collapse) {
                summary.querySelector('[data-blaines-delivery-address-text]').textContent =
                    [[values[0], values[1]].join(' '), values[2], values[3], values[4], values[5]]
                        .filter(Boolean).join(', ');
            }
        }

        function syncFulfilmentSection(collection, refreshed) {
            var section = document.querySelector('[data-blaines-checkout-fulfilment]');
            if (!section) return;

            var methods = section.querySelector('[data-blaines-fulfilment-methods]');
            var addressSlot = section.querySelector('[data-blaines-fulfilment-address]');
            var dateSlot = section.querySelector('[data-blaines-fulfilment-date]');
            var showroomSlot = section.querySelector('[data-blaines-fulfilment-showroom]');
            var address = document.querySelector('.woocommerce-shipping-fields');
            var date = document.querySelector('.blaines-preferred-delivery-date-card');
            var lists = document.querySelectorAll('.woocommerce-checkout-review-order-table #shipping_method');
            var list = lists.length === 1 ? lists[0] : null;
            var rates = list ? Array.from(list.querySelectorAll('input[name^="shipping_method"]')) : [];

            if (address && address.parentNode !== addressSlot) addressSlot.append(address);
            if (date && date.parentNode !== dateSlot) dateSlot.append(date);
            syncDeliveryAddressSummary(section, address, collection);

            // Keep the actual WooCommerce controls in the order-review fragment
            // so its update and payment handlers continue to read the same fields.
            var summary = list ? list.parentNode.querySelector('[data-blaines-method-summary]') : null;
            if (rates.length && !summary) {
                summary = document.createElement('span');
                summary.setAttribute('data-blaines-method-summary', '');
                list.after(summary);
            }

            var focused = methods.contains(document.activeElement)
                ? document.activeElement.getAttribute('data-blaines-rate')
                : null;
            methods.replaceChildren();

            rates.forEach(function (rate) {
                var label = rate.labels && rate.labels[0];
                if (!label && rate.type === 'hidden') {
                    var item = rate.closest('li');
                    label = item && item.querySelector('label');
                }
                if (!label) return;

                var selected = rate.type === 'hidden' || rate.checked;
                var choice = document.createElement(rate.type === 'radio' ? 'button' : 'p');
                choice.className = 'blaines-checkout-fulfilment__option';
                choice.textContent = label.textContent.trim().replace(/\s+/g, ' ');
                if (selected && summary) summary.textContent = choice.textContent;

                if (choice.tagName === 'BUTTON') {
                    choice.type = 'button';
                    choice.disabled = rate.disabled;
                    choice.setAttribute('data-blaines-rate', rate.value);
                    choice.setAttribute('aria-pressed', selected ? 'true' : 'false');
                    choice.addEventListener('click', function () {
                        if (!rate.checked) rate.click();
                    });
                }

                methods.append(choice);
                if (focused && focused === rate.value) choice.focus({ preventScroll: true });
            });

            if (list && methods.childElementCount === rates.length && rates.length) {
                list.setAttribute('data-blaines-mirrored', '');
            } else if (list) {
                list.removeAttribute('data-blaines-mirrored');
                if (summary) summary.remove();
                methods.replaceChildren();
            }

            // The showroom contents are generated with the WooCommerce order
            // review. Replace the previous contents with the refreshed panel.
            var incoming = document.querySelector('.blaines-collection-details-row .blaines-collection-checkout-panel');
            if (incoming) {
                var row = incoming.closest('.blaines-collection-details-row');
                var restoreFocus = showroomSlot.contains(document.activeElement);
                row.setAttribute('data-blaines-moved', '');
                showroomSlot.replaceChildren(incoming);
                if (restoreFocus) {
                    var replacement = incoming.querySelector('.blaines-collection-location-select');
                    if (replacement) replacement.focus({ preventScroll: true });
                }
            } else if (refreshed && !document.querySelector('.blaines-collection-details-row[data-blaines-moved]')) {
                showroomSlot.replaceChildren();
            }

            showroomSlot.hidden = !collection;
            var showroom = showroomSlot.querySelector('.blaines-collection-location-select');
            if (showroom) {
                showroom.disabled = !collection;
                showroom.required = collection;
            }

            section.hidden = false;
        }

        function updateShippingMethodCards() {
            document.querySelectorAll('#shipping_method li').forEach(function (item) {
                var input = item.querySelector('input[name^="shipping_method"]');
                item.classList.toggle('is-selected', !!(input && input.checked));
            });
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

        function updateCheckoutPresentation(refreshed) {
            updateShippingMethodCards();
            updateNotesCopy();

            var collection = isCollection();
            updateDeliveryAddressVisibility(collection);
            syncFulfilmentSection(collection, refreshed === true);
            document.querySelectorAll('.blaines-collection-details-row').forEach(function (row) {
                row.style.display = collection ? '' : 'none';

                var showroom = row.querySelector('.blaines-collection-location-select');
                if (showroom) {
                    showroom.disabled = !collection;
                    showroom.required = collection;
                }
            });
        }

        document.addEventListener('click', function (event) {
            var update = event.target.closest('[data-blaines-delivery-address-update]');
            if (update) {
                var section = update.closest('[data-blaines-checkout-fulfilment]');
                var address = section.querySelector('.woocommerce-shipping-fields');
                var draft = section.querySelector('[data-blaines-delivery-address-draft]');
                var error = section.querySelector('[data-blaines-delivery-address-error]');
                var postcodeError = section.querySelector('[data-blaines-delivery-postcode-error]');
                postcodeError.hidden = !hasIncompleteDeliveryPostcode(address);
                if (!postcodeError.hidden) {
                    error.hidden = true;
                    address.querySelector('#shipping_postcode').focus();
                    return;
                }

                var invalid = Array.from(address.querySelectorAll('.shipping_address input, .shipping_address select')).find(function (field) {
                    return !field.disabled && field.type !== 'hidden' && field.getClientRects().length > 0
                        && (!field.validity.valid || field.closest('.woocommerce-invalid')
                            || (field.closest('.validate-required') && !field.value.trim()));
                });

                error.hidden = !invalid;
                if (invalid) {
                    invalid.focus();
                    return;
                }

                draft.disabled = true;
                awaitingDeliveryRates = true;
                revealDeliveryOnError = false;
                if (window.jQuery) window.jQuery(document.body).trigger('update_checkout');
                return;
            }

            var edit = event.target.closest('[data-blaines-delivery-address-edit]');
            if (!edit) return;

            editingDeliveryAddress = true;
            updateCheckoutPresentation();
            var firstField = document.getElementById('shipping_first_name');
            if (firstField) firstField.focus();
        });

        document.addEventListener('invalid', function (event) {
            if (!event.target.closest('[data-blaines-address-collapsed]')) return;
            editingDeliveryAddress = true;
            updateCheckoutPresentation();
        }, true);

        function trackDeliveryAddressDraft(event) {
            if (!event.target.matches('.woocommerce-shipping-fields .shipping_address :is(input, select)')) return;

            var draft = document.querySelector('[data-blaines-delivery-address-draft]');
            var checkbox = document.getElementById('ship-to-different-address-checkbox');
            if (!draft || !checkbox || !checkbox.checked) return;

            draft.disabled = false;
            awaitingDeliveryRates = false;
            editingDeliveryAddress = true;
        }

        document.addEventListener('input', trackDeliveryAddressDraft, true);
        document.addEventListener('change', trackDeliveryAddressDraft, true);

        document.addEventListener('change', function (event) {
            if (event.target && event.target.matches('.blaines-collection-location-select')) {
                if (window.jQuery) {
                    window.jQuery(document.body).trigger('update_checkout');
                }
            }

            if (event.target && event.target.matches('input[name^="shipping_method"], select[name^="shipping_method"]')) {
                revealDeliveryOnError = false;
                window.setTimeout(updateCheckoutPresentation, 0);
            }

            if (event.target && event.target.matches('#ship-to-different-address-checkbox')) {
                var draft = document.querySelector('[data-blaines-delivery-address-draft]');
                if (draft && !event.target.checked) {
                    draft.disabled = true;
                    awaitingDeliveryRates = false;
                    editingDeliveryAddress = false;
                    collectionOnlyAddress = false;
                }
                window.setTimeout(updateCheckoutPresentation, 0);
            }
        });

        document.addEventListener('DOMContentLoaded', updateCheckoutPresentation);
        window.addEventListener('load', updateCheckoutPresentation);

        if (window.jQuery) {
            window.jQuery(document.body).on('updated_checkout', function () {
                var savedAddress = awaitingDeliveryRates;
                var restoreFocus = savedAddress && document.activeElement.matches('[data-blaines-delivery-address-update]');
                if (savedAddress) {
                    awaitingDeliveryRates = false;
                    editingDeliveryAddress = false;
                    collectionOnlyAddress = isCollection() && !hasLocalDeliveryOption();
                }
                updateCheckoutPresentation(true);
                if (restoreFocus) {
                    var edit = document.querySelector(
                        '[data-blaines-delivery-address-summary]:not([hidden]) button, '
                        + '[data-blaines-collection-address-result]:not([hidden]) button'
                    );
                    if (edit) edit.focus();
                }
            });
            window.jQuery(document.body).on('checkout_error', function () {
                awaitingDeliveryRates = false;
                revealDeliveryOnError = true;
                updateCheckoutPresentation();
            });
        }
    })();
    </script>
    <?php
}
add_action( 'wp_footer', 'blaines_checkout_collection_script', 100 );
