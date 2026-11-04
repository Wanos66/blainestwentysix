<?php
/**
 * Preferred local delivery date.
 *
 * Adds a checkout-only preferred delivery date dropdown based on:
 * - basket item availability;
 * - Blaines valid local delivery days;
 * - selected fulfilment method.
 *
 * @package blainestwentysix
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'blaines_get_local_delivery_day_numbers' ) ) {
    /**
     * Return valid Blaines local delivery weekdays.
     *
     * @return int[]
     */
    function blaines_get_local_delivery_day_numbers(): array {
        if ( function_exists( 'blaines_delivery_schedule_get_customer_weekdays' ) ) {
            $days = blaines_delivery_schedule_get_customer_weekdays();

            if ( ! empty( $days ) ) {
                return $days;
            }
        }

        return [ 1, 2, 4, 5, 6 ];
    }
}

if ( ! function_exists( 'blaines_parse_delivery_date' ) ) {
    /**
     * Parse a stored delivery date safely.
     *
     * @param mixed $value Stored date value.
     * @return DateTimeImmutable|null
     */
    function blaines_parse_delivery_date( $value ): ?DateTimeImmutable {
        $value = trim( (string) $value );

        if ( '' === $value ) {
            return null;
        }

        $timezone = wp_timezone();

        $formats = [
            'Y-m-d',
            'd/m/Y',
            'j/n/Y',
            'd-m-Y',
            'j F Y',
            'j M Y',
            'D j M Y',
            'l j F Y',
        ];

        foreach ( $formats as $format ) {
            $date = DateTimeImmutable::createFromFormat( '!' . $format, $value, $timezone );

            if ( $date instanceof DateTimeImmutable ) {
                return $date;
            }
        }

        try {
            return new DateTimeImmutable( $value, $timezone );
        } catch ( Exception $exception ) {
            return null;
        }
    }
}

if ( ! function_exists( 'blaines_get_next_local_delivery_date' ) ) {
    /**
     * Move a date forward until it lands on a valid Blaines delivery day.
     *
     * @param DateTimeImmutable $date Start date.
     * @return DateTimeImmutable
     */
    function blaines_get_next_local_delivery_date( DateTimeImmutable $date ): DateTimeImmutable {
        if ( function_exists( 'blaines_delivery_schedule_get_next_customer_delivery_date' ) ) {
            return blaines_delivery_schedule_get_next_customer_delivery_date( $date );
        }

        $delivery_days = blaines_get_local_delivery_day_numbers();

        while ( ! in_array( (int) $date->format( 'N' ), $delivery_days, true ) ) {
            $date = $date->modify( '+1 day' );
        }

        return $date;
    }
}

if ( ! function_exists( 'blaines_get_minimum_local_delivery_start_date' ) ) {
    /**
     * Minimum delivery picker start date.
     *
     * We avoid same-day delivery promises by starting from tomorrow.
     *
     * @return DateTimeImmutable
     */
    function blaines_get_minimum_local_delivery_start_date(): DateTimeImmutable {
        return new DateTimeImmutable( '+1 day', wp_timezone() );
    }
}

if ( ! function_exists( 'blaines_get_local_stock_next_day_cutoff_time' ) ) {
    /**
     * Get the local-stock next-day delivery cut-off in HH:MM format.
     *
     * @return string
     */
    function blaines_get_local_stock_next_day_cutoff_time(): string {
        if ( function_exists( 'blaines_delivery_schedule_get_local_stock_next_day_cutoff' ) ) {
            $cutoff = trim( (string) blaines_delivery_schedule_get_local_stock_next_day_cutoff() );

            if ( preg_match( '/^(?:[01]\\d|2[0-3]):[0-5]\\d$/', $cutoff ) ) {
                return $cutoff;
            }
        }

        return '15:00';
    }
}

if ( ! function_exists( 'blaines_get_minimum_local_stock_delivery_start_date' ) ) {
    /**
     * Minimum delivery start date for Blaines local stock.
     *
     * Before the configured cut-off, tomorrow may be offered if it is an
     * available Blaines delivery date. At or after the cut-off, tomorrow is
     * skipped and the search begins from the following calendar day.
     *
     * @return DateTimeImmutable
     */
    function blaines_get_minimum_local_stock_delivery_start_date(): DateTimeImmutable {
        $timezone = wp_timezone();
        $now      = new DateTimeImmutable( 'now', $timezone );
        $tomorrow = ( new DateTimeImmutable( 'tomorrow', $timezone ) )->setTime( 0, 0, 0 );
        $cutoff   = blaines_get_local_stock_next_day_cutoff_time();

        [ $hour, $minute ] = array_map( 'intval', explode( ':', $cutoff ) );
        $cutoff_today      = $now->setTime( $hour, $minute, 0 );

        if ( $now >= $cutoff_today ) {
            return $tomorrow->modify( '+1 day' );
        }

        return $tomorrow;
    }
}

if ( ! function_exists( 'blaines_get_product_earliest_preferred_delivery_date' ) ) {
    /**
     * Get earliest preferred local delivery date for one product.
     *
     * @param WC_Product $product Product object.
     * @return DateTimeImmutable|null
     */
    function blaines_get_product_earliest_preferred_delivery_date( WC_Product $product ): ?DateTimeImmutable {
        $product_id = $product->get_id();

        if ( $product->is_type( 'variation' ) ) {
            $product_id = $product->get_parent_id();
        }

        $minimum_date = blaines_get_minimum_local_delivery_start_date();

        /*
         * If Blaines local stock is driving availability, use the next available
         * Blaines delivery day from the minimum date.
         */
        $availability_source = trim( (string) get_post_meta( $product_id, '_blaines_availability_source', true ) );
        $local_free_stock    = (float) get_post_meta( $product_id, '_blaines_local_free_stock', true );

        if ( 'local' === $availability_source || $local_free_stock > 0 ) {
            return blaines_get_next_local_delivery_date(
                blaines_get_minimum_local_stock_delivery_start_date()
            );
        }

        /*
         * Supplier/Euronics availability dates.
         *
         * The main date should be _blaines_customer_available_from_date.
         * The other keys are included as defensive fallbacks in case older local
         * data exists from earlier importer milestones.
         */
        $date_meta_keys = [
            '_blaines_customer_available_from_date',
            '_euronics_customer_available_from_date',
            '_blaines_expected_supplier_delivery_date',
            '_euronics_expected_supplier_delivery_date',
        ];

        foreach ( $date_meta_keys as $meta_key ) {
            $stored_date = blaines_parse_delivery_date( get_post_meta( $product_id, $meta_key, true ) );

            if ( $stored_date instanceof DateTimeImmutable ) {
                if ( $stored_date < $minimum_date ) {
                    $stored_date = $minimum_date;
                }

                return blaines_get_next_local_delivery_date( $stored_date );
            }
        }

        /*
         * If the product is in stock but does not have a calculated date, allow
         * the next valid local delivery day.
         */
        if ( $product->is_in_stock() ) {
            return blaines_get_next_local_delivery_date( $minimum_date );
        }

        /*
         * If we cannot determine availability, do not offer a preferred date.
         */
        return null;
    }
}

if ( ! function_exists( 'blaines_get_cart_earliest_preferred_delivery_date' ) ) {
    /**
     * Get the earliest possible basket-level preferred delivery date.
     *
     * For multiple products, use the latest item-level availability date.
     *
     * @return DateTimeImmutable|null
     */
    function blaines_get_cart_earliest_preferred_delivery_date(): ?DateTimeImmutable {
        if (
            ! function_exists( 'WC' )
            || ! WC()->cart
            || WC()->cart->is_empty()
        ) {
            return null;
        }

        $basket_date = null;

        foreach ( WC()->cart->get_cart() as $cart_item ) {
            if (
                empty( $cart_item['data'] )
                || ! $cart_item['data'] instanceof WC_Product
            ) {
                continue;
            }

            $product_date = blaines_get_product_earliest_preferred_delivery_date( $cart_item['data'] );

            if ( ! $product_date instanceof DateTimeImmutable ) {
                return null;
            }

            if ( ! $basket_date instanceof DateTimeImmutable || $product_date > $basket_date ) {
                $basket_date = $product_date;
            }
        }

        return $basket_date;
    }
}

if ( ! function_exists( 'blaines_get_preferred_delivery_date_options' ) ) {
    /**
     * Build preferred delivery date dropdown options.
     *
     * @param int $number_of_dates Number of valid delivery dates to offer.
     * @return array<string, string>
     */
    function blaines_get_preferred_delivery_date_options( int $number_of_dates = 14 ): array {
        $start_date = blaines_get_cart_earliest_preferred_delivery_date();

        if ( ! $start_date instanceof DateTimeImmutable ) {
            return [];
        }

        $date    = blaines_get_next_local_delivery_date( $start_date );
        $options = [];
        $guard   = 0;

        while ( count( $options ) < $number_of_dates && $guard < 90 ) {
            $is_available = function_exists( 'blaines_delivery_schedule_is_customer_delivery_date' )
                ? blaines_delivery_schedule_is_customer_delivery_date( $date )
                : in_array( (int) $date->format( 'N' ), blaines_get_local_delivery_day_numbers(), true );

            if ( $is_available ) {
                $value             = $date->format( 'Y-m-d' );
                $options[ $value ] = wp_date( 'l j F Y', $date->getTimestamp() );
            }

            $date = $date->modify( '+1 day' );
            $guard++;
        }

        return $options;
    }
}

if ( ! function_exists( 'blaines_shipping_rate_is_local_delivery' ) ) {
    /**
     * Determine whether a selected shipping rate is local delivery.
     *
     * @param string $rate_id Shipping rate id.
     * @return bool
     */
    function blaines_shipping_rate_is_local_delivery( string $rate_id ): bool {
        return (
            str_starts_with( $rate_id, 'flat_rate' )
            || str_starts_with( $rate_id, 'free_shipping' )
        );
    }
}

if ( ! function_exists( 'blaines_posted_checkout_uses_local_delivery' ) ) {
    /**
     * Determine whether posted checkout data uses local delivery.
     *
     * @return bool
     */
    function blaines_posted_checkout_uses_local_delivery(): bool {
        if (
            empty( $_POST['shipping_method'] )
            || ! is_array( $_POST['shipping_method'] )
        ) {
            return false;
        }

        $shipping_methods = wc_clean( wp_unslash( $_POST['shipping_method'] ) );
        $selected_method  = isset( $shipping_methods[0] ) ? (string) $shipping_methods[0] : '';

        return blaines_shipping_rate_is_local_delivery( $selected_method );
    }
}

if ( ! function_exists( 'blaines_current_checkout_uses_local_delivery' ) ) {
    /**
     * Determine whether the current checkout session is using local delivery.
     *
     * @return bool
     */
    function blaines_current_checkout_uses_local_delivery(): bool {
        if ( ! function_exists( 'WC' ) || ! WC()->session ) {
            return false;
        }

        $chosen_methods = WC()->session->get( 'chosen_shipping_methods', [] );

        if ( empty( $chosen_methods[0] ) ) {
            return true;
        }

        return blaines_shipping_rate_is_local_delivery( (string) $chosen_methods[0] );
    }
}

if ( ! function_exists( 'blaines_render_preferred_delivery_date_checkout_field' ) ) {
    /**
     * Render preferred delivery date field in checkout.
     *
     * @param WC_Checkout $checkout Checkout object.
     * @return void
     */
    function blaines_render_preferred_delivery_date_checkout_field( WC_Checkout $checkout ): void {
        $date_options = blaines_get_preferred_delivery_date_options();

        if ( empty( $date_options ) ) {
            return;
        }

        $selected_date = WC()->session
            ? (string) WC()->session->get( 'blaines_preferred_delivery_date', '' )
            : '';

        if ( '' === $selected_date ) {
            $selected_date = (string) $checkout->get_value( 'blaines_preferred_delivery_date' );
        }

        $field_class = [
            'form-row-wide',
            'blaines-preferred-delivery-date-field',
        ];

        $card_class = 'blaines-checkout-card blaines-preferred-delivery-date-card';

        if ( ! blaines_current_checkout_uses_local_delivery() ) {
            $card_class .= ' blaines-preferred-delivery-date-card--hidden';
        }

        echo '<div class="' . esc_attr( $card_class ) . '">';

        echo '<h3>' . esc_html__( 'Preferred local delivery date', 'blainestwentysix' ) . '</h3>';

        echo '<p class="blaines-checkout-card-intro">' . esc_html__( 'Choose your preferred local delivery date. Web Sales will review your order and contact you if anything needs confirming.', 'blainestwentysix' ) . '</p>';

        woocommerce_form_field(
            'blaines_preferred_delivery_date',
            [
                'type'        => 'select',
                'label'       => esc_html__( 'Preferred delivery date', 'blainestwentysix' ),
                'required'    => true,
                'class'       => $field_class,
                'input_class' => [ 'blaines-preferred-delivery-date-select' ],
                'options'     => array_merge(
                    [
                        '' => esc_html__( 'Select a preferred delivery date', 'blainestwentysix' ),
                    ],
                    $date_options
                ),
            ],
            $selected_date
        );

        echo '</div>';
    }

}

/**
 * Render preferred local delivery date before the Billing details panel.
 *
 * WooCommerce renders the billing form on `woocommerce_checkout_billing`
 * at priority 10, so priority 5 places this purchase-decision field directly
 * above the complete Billing details block.
 *
 * @return void
 */
if ( ! function_exists( 'blaines_render_preferred_delivery_date_before_billing' ) ) {
    function blaines_render_preferred_delivery_date_before_billing(): void {
        if ( ! function_exists( 'WC' ) ) {
            return;
        }

        $checkout = WC()->checkout();

        if ( ! $checkout instanceof WC_Checkout ) {
            return;
        }

        blaines_render_preferred_delivery_date_checkout_field( $checkout );
    }

    add_action( 'woocommerce_checkout_billing', 'blaines_render_preferred_delivery_date_before_billing', 5 );
}

/**
 * Keep the preferred delivery card in sync when the customer changes between
 * local delivery and showroom collection.
 *
 * @return void
 */
if ( ! function_exists( 'blaines_preferred_delivery_date_checkout_visibility_script' ) ) {
    function blaines_preferred_delivery_date_checkout_visibility_script(): void {
        if (
            ! function_exists( 'is_checkout' )
            || ! is_checkout()
            || is_order_received_page()
        ) {
            return;
        }
        ?>
        <script>
        (function () {
            function getSelectedShippingMethod() {
                var checked = document.querySelector(
                    'input[type="radio"][name^="shipping_method"]:checked'
                );

                if (checked) {
                    return checked.value || '';
                }

                var hidden = document.querySelector(
                    'input[type="hidden"][name^="shipping_method"]'
                );

                if (hidden) {
                    return hidden.value || '';
                }

                var select = document.querySelector(
                    'select[name^="shipping_method"]'
                );

                return select ? (select.value || '') : '';
            }

            function usesLocalDelivery() {
                var method = getSelectedShippingMethod();

                if (!method) {
                    return true;
                }

                return (
                    method.indexOf('flat_rate') === 0
                    || method.indexOf('free_shipping') === 0
                );
            }

            function updatePreferredDeliveryVisibility() {
                var card = document.querySelector(
                    '.blaines-preferred-delivery-date-card'
                );

                if (!card) {
                    return;
                }

                var show = usesLocalDelivery();
                var select = card.querySelector(
                    '[name="blaines_preferred_delivery_date"]'
                );

                card.classList.toggle(
                    'blaines-preferred-delivery-date-card--hidden',
                    !show
                );

                card.setAttribute('aria-hidden', show ? 'false' : 'true');

                if (select) {
                    select.disabled = !show;
                }
            }

            document.addEventListener('change', function (event) {
                if (
                    event.target
                    && event.target.matches(
                        'input[name^="shipping_method"], select[name^="shipping_method"]'
                    )
                ) {
                    window.setTimeout(
                        updatePreferredDeliveryVisibility,
                        0
                    );
                }
            });

            document.addEventListener(
                'DOMContentLoaded',
                updatePreferredDeliveryVisibility
            );

            window.addEventListener(
                'load',
                updatePreferredDeliveryVisibility
            );

            if (window.jQuery) {
                window.jQuery(document.body).on(
                    'updated_checkout',
                    updatePreferredDeliveryVisibility
                );
            }
        })();
        </script>
        <?php
    }

    add_action(
        'wp_footer',
        'blaines_preferred_delivery_date_checkout_visibility_script',
        101
    );
}

if ( ! function_exists( 'blaines_store_preferred_delivery_date_in_session' ) ) {
    /**
     * Store selected preferred delivery date during checkout Ajax refreshes.
     *
     * @param string $posted_data Posted checkout data string.
     * @return void
     */
    function blaines_store_preferred_delivery_date_in_session( string $posted_data ): void {
        if ( ! function_exists( 'WC' ) || ! WC()->session ) {
            return;
        }

        parse_str( $posted_data, $data );

        if ( isset( $data['blaines_preferred_delivery_date'] ) ) {
            WC()->session->set(
                'blaines_preferred_delivery_date',
                sanitize_text_field( wp_unslash( (string) $data['blaines_preferred_delivery_date'] ) )
            );
        }
    }

    add_action( 'woocommerce_checkout_update_order_review', 'blaines_store_preferred_delivery_date_in_session', 20 );
}

if ( ! function_exists( 'blaines_validate_preferred_delivery_date_checkout_field' ) ) {
    /**
     * Validate preferred delivery date at checkout.
     *
     * @return void
     */
    function blaines_validate_preferred_delivery_date_checkout_field(): void {
        if ( ! blaines_posted_checkout_uses_local_delivery() ) {
            return;
        }

        $selected_date = isset( $_POST['blaines_preferred_delivery_date'] )
            ? sanitize_text_field( wp_unslash( (string) $_POST['blaines_preferred_delivery_date'] ) )
            : '';

        if ( '' === $selected_date ) {
            wc_add_notice(
                esc_html__( 'Please choose your preferred local delivery date.', 'blainestwentysix' ),
                'error'
            );

            return;
        }

        $valid_options = blaines_get_preferred_delivery_date_options();

        if ( ! isset( $valid_options[ $selected_date ] ) ) {
            wc_add_notice(
                esc_html__( 'Please choose a valid preferred local delivery date.', 'blainestwentysix' ),
                'error'
            );
        }
    }

    add_action( 'woocommerce_checkout_process', 'blaines_validate_preferred_delivery_date_checkout_field', 20 );
}

if ( ! function_exists( 'blaines_save_preferred_delivery_date_to_order' ) ) {
    /**
     * Save preferred delivery date to the order.
     *
     * @param WC_Order $order Order object.
     * @return void
     */
    function blaines_save_preferred_delivery_date_to_order( WC_Order $order ): void {
        if ( ! blaines_posted_checkout_uses_local_delivery() ) {
            return;
        }

        $selected_date = isset( $_POST['blaines_preferred_delivery_date'] )
            ? sanitize_text_field( wp_unslash( (string) $_POST['blaines_preferred_delivery_date'] ) )
            : '';

        $valid_options = blaines_get_preferred_delivery_date_options();

        if ( '' === $selected_date || ! isset( $valid_options[ $selected_date ] ) ) {
            return;
        }

        $order->update_meta_data( '_blaines_preferred_delivery_date', $selected_date );
        $order->update_meta_data( '_blaines_preferred_delivery_date_label', $valid_options[ $selected_date ] );
    }

    add_action( 'woocommerce_checkout_create_order', 'blaines_save_preferred_delivery_date_to_order', 20 );
}

if ( ! function_exists( 'blaines_get_order_preferred_delivery_date_label' ) ) {
    /**
     * Get order preferred delivery date label.
     *
     * @param WC_Order $order Order object.
     * @return string
     */
    function blaines_get_order_preferred_delivery_date_label( WC_Order $order ): string {
        $label = trim( (string) $order->get_meta( '_blaines_preferred_delivery_date_label' ) );

        if ( '' !== $label ) {
            return $label;
        }

        $date = blaines_parse_delivery_date( $order->get_meta( '_blaines_preferred_delivery_date' ) );

        if ( $date instanceof DateTimeImmutable ) {
            return wp_date( 'l j F Y', $date->getTimestamp() );
        }

        return '';
    }
}

if ( ! function_exists( 'blaines_show_preferred_delivery_date_in_order_totals' ) ) {
    /**
     * Add preferred delivery date to order totals display.
     *
     * @param array<string, array<string, string>> $totals Order totals.
     * @param WC_Order                            $order  Order object.
     * @return array<string, array<string, string>>
     */
    function blaines_show_preferred_delivery_date_in_order_totals( array $totals, WC_Order $order ): array {
        $date_label = blaines_get_order_preferred_delivery_date_label( $order );

        if ( '' === $date_label ) {
            return $totals;
        }

        $new_totals = [];

        foreach ( $totals as $key => $total ) {
            $new_totals[ $key ] = $total;

            if ( 'shipping' === $key ) {
                $new_totals['blaines_preferred_delivery_date'] = [
                    'label' => esc_html__( 'Preferred delivery date:', 'blainestwentysix' ),
                    'value' => esc_html( $date_label ),
                ];
            }
        }

        if ( ! isset( $new_totals['blaines_preferred_delivery_date'] ) ) {
            $new_totals['blaines_preferred_delivery_date'] = [
                'label' => esc_html__( 'Preferred delivery date:', 'blainestwentysix' ),
                'value' => esc_html( $date_label ),
            ];
        }

        return $new_totals;
    }

    add_filter( 'woocommerce_get_order_item_totals', 'blaines_show_preferred_delivery_date_in_order_totals', 30, 2 );
}

if ( ! function_exists( 'blaines_show_preferred_delivery_date_in_admin_order' ) ) {
    /**
     * Display preferred delivery date in admin order screen.
     *
     * @param WC_Order $order Order object.
     * @return void
     */
    function blaines_show_preferred_delivery_date_in_admin_order( WC_Order $order ): void {
        $date_label = blaines_get_order_preferred_delivery_date_label( $order );

        if ( '' === $date_label ) {
            return;
        }

        echo '<p><strong>' . esc_html__( 'Preferred delivery date:', 'blainestwentysix' ) . '</strong><br>';
        echo esc_html( $date_label );
        echo '</p>';
    }

    add_action( 'woocommerce_admin_order_data_after_shipping_address', 'blaines_show_preferred_delivery_date_in_admin_order', 20 );
}

if ( ! function_exists( 'blaines_show_preferred_delivery_date_in_emails' ) ) {
    /**
     * Display preferred delivery date in WooCommerce emails.
     *
     * @param array<string, array<string, string>> $fields Email meta fields.
     * @param bool                                $sent_to_admin Sent to admin.
     * @param WC_Order                            $order Order object.
     * @return array<string, array<string, string>>
     */
    function blaines_show_preferred_delivery_date_in_emails( array $fields, bool $sent_to_admin, WC_Order $order ): array {
        $date_label = blaines_get_order_preferred_delivery_date_label( $order );

        if ( '' === $date_label ) {
            return $fields;
        }

        $fields['blaines_preferred_delivery_date'] = [
            'label' => esc_html__( 'Preferred delivery date', 'blainestwentysix' ),
            'value' => esc_html( $date_label ),
        ];

        return $fields;
    }

    add_filter( 'woocommerce_email_order_meta_fields', 'blaines_show_preferred_delivery_date_in_emails', 20, 3 );
}
