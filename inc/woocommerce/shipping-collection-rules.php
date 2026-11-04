<?php
/**
 * Shipping and collection rules for transactional WooCommerce orders.
 *
 * Collection is hidden when fulfilment services are selected because services
 * such as installation, disconnection, disposal and packaging removal require
 * local delivery.
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'blaines_cart_has_selected_fulfilment_services' ) ) {
    /**
     * Check whether the current cart contains selected fulfilment services.
     *
     * @param WC_Cart|null $cart Cart object.
     * @return bool
     */
    function blaines_cart_has_selected_fulfilment_services( ?WC_Cart $cart = null ): bool {
        if ( ! function_exists( 'blaines_get_selected_cart_fulfilment_services' ) ) {
            return false;
        }

        $cart = $cart ?: WC()->cart;

        if ( ! $cart || $cart->is_empty() ) {
            return false;
        }

        foreach ( $cart->get_cart() as $cart_item ) {
            $selected_services = blaines_get_selected_cart_fulfilment_services( $cart_item );

            if ( ! empty( $selected_services ) ) {
                return true;
            }
        }

        return false;
    }
}

if ( ! function_exists( 'blaines_hide_collection_when_services_selected' ) ) {
    /**
     * Remove Local Pickup / Collection when basket services are selected.
     *
     * @param array<string, WC_Shipping_Rate> $rates Available shipping rates.
     * @param array<string, mixed>            $package Shipping package.
     * @return array<string, WC_Shipping_Rate>
     */
    function blaines_hide_collection_when_services_selected( array $rates, array $package ): array {
        if ( ! blaines_cart_has_selected_fulfilment_services() ) {
            return $rates;
        }

        foreach ( $rates as $rate_id => $rate ) {
            if ( ! $rate instanceof WC_Shipping_Rate ) {
                continue;
            }

            if ( 'local_pickup' === $rate->get_method_id() ) {
                unset( $rates[ $rate_id ] );
            }
        }

        return $rates;
    }

    add_filter( 'woocommerce_package_rates', 'blaines_hide_collection_when_services_selected', 20, 2 );
}

if ( ! function_exists( 'blaines_hide_paid_delivery_when_free_delivery_available' ) ) {
    /**
     * Hide paid Local delivery when Free local delivery is available.
     *
     * This keeps the checkout fulfilment choices clean:
     * - Under £400: Local delivery / Collection from Blaines
     * - £400 or more: Free local delivery / Collection from Blaines
     * - Services selected: Collection is handled by the existing services rule.
     *
     * @param array<string, WC_Shipping_Rate> $rates   Available shipping rates.
     * @param array<string, mixed>            $package Shipping package.
     * @return array<string, WC_Shipping_Rate>
     */
    function blaines_hide_paid_delivery_when_free_delivery_available( array $rates, array $package ): array {
        $free_delivery_available = false;

        foreach ( $rates as $rate ) {
            if ( 'free_shipping' === $rate->get_method_id() ) {
                $free_delivery_available = true;
                break;
            }
        }

        if ( ! $free_delivery_available ) {
            return $rates;
        }

        foreach ( $rates as $rate_id => $rate ) {
            if ( 'flat_rate' === $rate->get_method_id() ) {
                unset( $rates[ $rate_id ] );
            }
        }

        return $rates;
    }

    add_filter( 'woocommerce_package_rates', 'blaines_hide_paid_delivery_when_free_delivery_available', 30, 2 );
}

if ( ! function_exists( 'blaines_render_shipping_services_notice_row' ) ) {
    /**
     * Show a short shipping note in the cart/checkout totals table.
     *
     * @return void
     */
    function blaines_render_shipping_services_notice_row(): void {
        if ( ! blaines_cart_has_selected_fulfilment_services() ) {
            return;
        }

        ?>
        <tr class="blaines-shipping-services-note">
            <th>
                <?php esc_html_e( 'Collection unavailable', 'blainestwentysix' ); ?>
            </th>
            <td data-title="<?php esc_attr_e( 'Collection', 'blainestwentysix' ); ?>">
                <p class="text-sm font-light leading-relaxed text-neutral-600">
                    <?php esc_html_e( 'Selected services require local delivery.', 'blainestwentysix' ); ?>
                </p>
            </td>
        </tr>
        <?php
    }

    add_action( 'woocommerce_cart_totals_before_shipping', 'blaines_render_shipping_services_notice_row', 20 );
    add_action( 'woocommerce_review_order_before_shipping', 'blaines_render_shipping_services_notice_row', 20 );
}

if ( ! function_exists( 'blaines_cart_has_no_available_shipping_rates' ) ) {
    /**
     * Check whether the current checkout has no available shipping rates.
     *
     * @return bool
     */
    function blaines_cart_has_no_available_shipping_rates(): bool {
        if (
            ! function_exists( 'WC' )
            || ! WC()->cart
            || ! WC()->shipping()
        ) {
            return false;
        }

        if ( ! WC()->cart->needs_shipping() ) {
            return false;
        }

        if (
            WC()->customer
            && method_exists( WC()->customer, 'has_calculated_shipping' )
            && ! WC()->customer->has_calculated_shipping()
        ) {
            return false;
        }

        $packages = WC()->shipping()->get_packages();

        if ( empty( $packages ) ) {
            return false;
        }

        foreach ( $packages as $package ) {
            if ( ! empty( $package['rates'] ) ) {
                return false;
            }
        }

        return true;
    }
}

if ( ! function_exists( 'blaines_cart_has_service_delivery_block' ) ) {
    /**
     * Check whether selected services require local delivery but no delivery rate is available.
     *
     * @return bool
     */
    function blaines_cart_has_service_delivery_block(): bool {
        return (
            function_exists( 'blaines_cart_has_selected_fulfilment_services' )
            && blaines_cart_has_selected_fulfilment_services()
            && blaines_cart_has_no_available_shipping_rates()
        );
    }
}

if ( ! function_exists( 'blaines_no_shipping_available_message' ) ) {
    /**
     * Shorten WooCommerce's no-shipping message inside the order review table.
     *
     * @param string $html Default no-shipping message HTML.
     * @return string
     */
    function blaines_no_shipping_available_message( string $html ): string {
        if ( ! blaines_cart_has_service_delivery_block() ) {
            return $html;
        }

        return '<span class="blaines-no-shipping-short-message">Local delivery is not available for this postcode.</span>';
    }

    add_filter( 'woocommerce_no_shipping_available_html', 'blaines_no_shipping_available_message', 20 );
    add_filter( 'woocommerce_cart_no_shipping_available_html', 'blaines_no_shipping_available_message', 20 );
}

if ( ! function_exists( 'blaines_render_service_delivery_block_notice' ) ) {
    /**
     * Render a clear checkout notice when selected services cannot be fulfilled
     * because the postcode is outside the local delivery area.
     *
     * @return void
     */
    function blaines_render_service_delivery_block_notice(): void {
        if (
            ! function_exists( 'is_checkout' )
            || ! is_checkout()
            || is_order_received_page()
            || ! blaines_cart_has_service_delivery_block()
        ) {
            return;
        }

        $cart_url = function_exists( 'wc_get_cart_url' )
            ? wc_get_cart_url()
            : home_url( '/basket/' );
        ?>

        <div
                id="blaines-service-delivery-block-notice"
                class="my-6 border-l-4 border-red-700 bg-red-50 p-5"
        >
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-red-700">
                Selected services require local delivery
            </p>

            <p class="mt-2 text-sm font-light leading-relaxed text-neutral-700">
                Local delivery is not available for this postcode. Please return to your basket to remove selected services, or contact Web Sales if you need help.
            </p>

            <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center">
                <a
                        href="<?php echo esc_url( $cart_url ); ?>"
                        class="inline-flex justify-center border border-neutral-950 bg-neutral-950 px-4 py-3 text-xs font-semibold uppercase tracking-[0.14em] text-white transition hover:border-blue-700 hover:bg-blue-700"
                >
                    Return to basket
                </a>

                <a
                        href="tel:01760302111"
                        class="inline-flex text-sm font-semibold text-neutral-950 transition hover:text-blue-700"
                >
                    Call Web Sales: 01760 302111
                </a>
            </div>
        </div>

        <?php
    }

    add_action( 'woocommerce_review_order_before_payment', 'blaines_render_service_delivery_block_notice', 5 );
}

if ( ! function_exists( 'blaines_replace_no_shipping_checkout_error' ) ) {
    /**
     * Replace WooCommerce's generic no-shipping checkout error with a clearer Blaines message.
     *
     * @param string $message Error message.
     * @return string
     */
    function blaines_replace_no_shipping_checkout_error( string $message ): string {
        if ( ! blaines_cart_has_service_delivery_block() ) {
            return $message;
        }

        $plain_message = wp_strip_all_tags( $message );

        if (
            false !== stripos( $plain_message, 'No shipping method has been selected' )
            || false !== stripos( $plain_message, 'There are no shipping options available' )
            || false !== stripos( $plain_message, 'Selected services require local delivery' )
        ) {
            $cart_url = function_exists( 'wc_get_cart_url' )
                ? wc_get_cart_url()
                : home_url( '/basket/' );

            return sprintf(
                '<strong>%1$s</strong><br><span>%2$s</span><br><a href="%3$s" class="blaines-checkout-error-return-link">%4$s</a>',
                esc_html__( 'Selected services require local delivery.', 'blainestwentysix' ),
                esc_html__( 'Local delivery is not available for this postcode. Please return to your basket to remove selected services, or contact Web Sales if you need help.', 'blainestwentysix' ),
                esc_url( $cart_url ),
                esc_html__( 'Return to basket', 'blainestwentysix' )
            );
        }

        return $message;
    }

    add_filter( 'woocommerce_add_error', 'blaines_replace_no_shipping_checkout_error', 20 );
}

if ( ! function_exists( 'blaines_service_delivery_block_scroll_script' ) ) {
    /**
     * Scroll the customer to the clear service/delivery notice after checkout validation.
     *
     * @return void
     */
    function blaines_service_delivery_block_scroll_script(): void {
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
            function blainesFindServiceDeliveryNotice() {
              return document.getElementById('blaines-service-delivery-block-notice');
            }

            function blainesScrollToServiceDeliveryNotice() {
              var notice = blainesFindServiceDeliveryNotice();

              if (!notice) {
                return;
              }

              var top = notice.getBoundingClientRect().top + window.pageYOffset - 140;

              window.scrollTo({
                top: top,
                behavior: 'smooth'
              });
            }

            function blainesHideDuplicateNoShippingNotice() {
              var notice = blainesFindServiceDeliveryNotice();

              if (!notice) {
                return;
              }

              var noticeGroups = document.querySelectorAll(
                  '.woocommerce-NoticeGroup-checkout, .woocommerce-notices-wrapper'
              );

              noticeGroups.forEach(function (group) {
                var groupText = (group.textContent || '').replace(/\s+/g, ' ').trim();

                if (
                    groupText.indexOf('No shipping method has been selected') !== -1
                    || groupText.indexOf('Selected services require local delivery') !== -1
                ) {
                  group.remove();
                }
              });
            }

            function blainesServiceDeliveryErrorCleanup() {
              blainesHideDuplicateNoShippingNotice();
            }

            document.body.addEventListener('updated_checkout', function () {
              setTimeout(blainesServiceDeliveryErrorCleanup, 50);
            });

            document.body.addEventListener('checkout_error', function () {
              setTimeout(blainesServiceDeliveryErrorCleanup, 50);
              setTimeout(blainesScrollToServiceDeliveryNotice, 300);
              setTimeout(blainesScrollToServiceDeliveryNotice, 700);
            });

            blainesServiceDeliveryErrorCleanup();
          })();
        </script>

        <?php
    }

    add_action( 'wp_footer', 'blaines_service_delivery_block_scroll_script', 99 );
}

if ( ! function_exists( 'blaines_rename_shipping_package_label' ) ) {
    /**
     * Rename the shipping package label in basket/checkout totals.
     *
     * @param string $package_name Package name.
     * @param int    $index        Package index.
     * @param array  $package      Shipping package.
     * @return string
     */
    function blaines_rename_shipping_package_label( string $package_name, int $index, array $package ): string {
        return 'Fulfilment';
    }

    add_filter( 'woocommerce_shipping_package_name', 'blaines_rename_shipping_package_label', 20, 3 );
}

if ( ! function_exists( 'blaines_rename_checkout_order_notes' ) ) {
    /**
     * Rename checkout order notes label/placeholder.
     *
     * @param array<string, mixed> $fields Checkout fields.
     * @return array<string, mixed>
     */
    function blaines_rename_checkout_order_notes( array $fields ): array {
        if ( isset( $fields['order']['order_comments'] ) ) {
            $fields['order']['order_comments']['label']       = 'Delivery or collection notes';
            $fields['order']['order_comments']['placeholder'] = 'Add any useful notes for Web Sales, delivery, collection, access or installation.';
        }

        return $fields;
    }

    add_filter( 'woocommerce_checkout_fields', 'blaines_rename_checkout_order_notes', 20 );
}

if ( ! function_exists( 'blaines_get_preferred_shipping_rate_id' ) ) {
    /**
     * Find the best rate ID for the checked postcode.
     *
     * @param array<string, WC_Shipping_Rate> $rates           Calculated rates.
     * @param bool                            $prefer_delivery Whether delivery is preferred.
     * @return string
     */
    function blaines_get_preferred_shipping_rate_id(
        array $rates,
        bool $prefer_delivery
    ): string {
        if ( empty( $rates ) ) {
            return '';
        }

        $method_priorities = $prefer_delivery
            ? [ 'free_shipping', 'flat_rate', 'local_pickup' ]
            : [ 'local_pickup', 'free_shipping', 'flat_rate' ];

        foreach ( $method_priorities as $method_id ) {
            foreach ( $rates as $rate_id => $rate ) {
                if (
                    $rate instanceof WC_Shipping_Rate
                    && $method_id === $rate->get_method_id()
                ) {
                    return (string) $rate_id;
                }
            }
        }

        if ( $prefer_delivery ) {
            foreach ( $rates as $rate_id => $rate ) {
                if (
                    $rate instanceof WC_Shipping_Rate
                    && 'local_pickup' !== $rate->get_method_id()
                ) {
                    return (string) $rate_id;
                }
            }
        }

        return (string) array_key_first( $rates );
    }
}

if ( ! function_exists( 'blaines_sync_shipping_after_postcode_check' ) ) {
    /**
     * Recalculate shipping after a product-page postcode check.
     *
     * @return void
     */
    function blaines_sync_shipping_after_postcode_check(): void {
        if (
            ! function_exists( 'WC' )
            || ! WC()->session
            || ! WC()->customer
            || ! WC()->cart
            || ! WC()->shipping()
        ) {
            return;
        }

        $postcode = function_exists( 'blaines_get_checked_product_postcode' )
            ? blaines_get_checked_product_postcode()
            : '';

        if ( '' === $postcode ) {
            return;
        }

        $delivery_available = function_exists( 'blaines_checked_postcode_has_delivery' )
            ? blaines_checked_postcode_has_delivery()
            : null;

        WC()->customer->set_shipping_country( 'GB' );
        WC()->customer->set_shipping_postcode( $postcode );

        if ( method_exists( WC()->customer, 'set_calculated_shipping' ) ) {
            WC()->customer->set_calculated_shipping( true );
        }

        WC()->customer->save();

        $packages = WC()->cart->get_shipping_packages();

        if ( empty( $packages ) ) {
            return;
        }

        foreach ( array_keys( $packages ) as $package_key ) {
            WC()->session->set( 'shipping_for_package_' . $package_key, null );
        }

        WC()->session->set( 'shipping_method_counts', [] );
        WC()->shipping()->reset_shipping();

        $calculated_packages = WC()->shipping()->calculate_shipping( $packages );
        $chosen_methods      = [];
        $method_counts       = [];

        foreach ( $calculated_packages as $package_key => $package ) {
            $rates = isset( $package['rates'] ) && is_array( $package['rates'] )
                ? $package['rates']
                : [];

            $chosen_methods[ $package_key ] = blaines_get_preferred_shipping_rate_id(
                $rates,
                false !== $delivery_available
            );

            $method_counts[ $package_key ] = count( $rates );
        }

        WC()->session->set( 'chosen_shipping_methods', $chosen_methods );
        WC()->session->set( 'shipping_method_counts', $method_counts );
        WC()->session->set( 'blaines_postcode_shipping_refresh_pending', 'no' );

        WC()->cart->calculate_totals();
    }
}

if ( ! function_exists( 'blaines_complete_pending_postcode_shipping_refresh' ) ) {
    /**
     * Complete a pending postcode/shipping refresh on the next Basket or
     * Checkout page request.
     *
     * Running during woocommerce_add_to_cart is too early for a brand-new
     * basket because conditional shipping methods can inspect cart totals
     * before the first item's line totals are fully settled.
     *
     * @return void
     */
    function blaines_complete_pending_postcode_shipping_refresh(): void {
        if (
            ! function_exists( 'WC' )
            || ! WC()->session
            || ! WC()->cart
            || 'yes' !== WC()->session->get(
                'blaines_postcode_shipping_refresh_pending',
                'no'
            )
        ) {
            return;
        }

        $is_cart_request = (
            function_exists( 'is_cart' )
            && is_cart()
        );

        $is_checkout_request = (
            function_exists( 'is_checkout' )
            && is_checkout()
            && (
                ! function_exists( 'is_order_received_page' )
                || ! is_order_received_page()
            )
        );

        if ( ! $is_cart_request && ! $is_checkout_request ) {
            return;
        }

        /*
         * Prime product line totals and the displayed subtotal before
         * evaluating free/paid local-delivery availability.
         */
        WC()->cart->calculate_totals();

        blaines_sync_shipping_after_postcode_check();

        if ( method_exists( WC()->session, 'save_data' ) ) {
            WC()->session->save_data();
        }
    }

    add_action(
        'template_redirect',
        'blaines_complete_pending_postcode_shipping_refresh',
        5
    );
}

if ( ! function_exists( 'blaines_prefer_delivery_rates_before_collection' ) ) {
    /**
     * Prefer local delivery rates before collection in the available rates list.
     *
     * This helps WooCommerce select Local delivery / Free local delivery by default
     * when both delivery and collection are available, while still allowing the
     * customer to manually choose Collection from Blaines.
     *
     * @param array<string, WC_Shipping_Rate> $rates   Available rates.
     * @param array<string, mixed>            $package Shipping package.
     * @return array<string, WC_Shipping_Rate>
     */
    function blaines_prefer_delivery_rates_before_collection( array $rates, array $package ): array {
        if ( empty( $rates ) ) {
            return $rates;
        }

        uasort(
            $rates,
            static function ( $a, $b ): int {
                $a_method = $a instanceof WC_Shipping_Rate ? $a->get_method_id() : '';
                $b_method = $b instanceof WC_Shipping_Rate ? $b->get_method_id() : '';

                $priorities = [
                    'free_shipping' => 10,
                    'flat_rate'     => 20,
                    'local_pickup'  => 90,
                ];

                $a_priority = $priorities[ $a_method ] ?? 50;
                $b_priority = $priorities[ $b_method ] ?? 50;

                if ( $a_priority === $b_priority ) {
                    return 0;
                }

                return $a_priority <=> $b_priority;
            }
        );

        return $rates;
    }

    add_filter( 'woocommerce_package_rates', 'blaines_prefer_delivery_rates_before_collection', 50, 2 );
}

