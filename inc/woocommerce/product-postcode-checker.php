<?php
/**
 * Product page postcode checker.
 *
 * Shows a postcode check modal before Add to Basket on transactional products.
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'blaines_should_render_product_postcode_checker' ) ) {
    /**
     * Determine whether the product postcode checker should render.
     *
     * @return bool
     */
    function blaines_should_render_product_postcode_checker(): bool {
        if (
            ! function_exists( 'is_product' )
            || ! is_product()
            || ! function_exists( 'wc_get_product' )
        ) {
            return false;
        }

        $product = wc_get_product( get_queried_object_id() );

        if ( ! $product instanceof WC_Product ) {
            return false;
        }

        if (
            function_exists( 'blaines_is_transactional_product' )
            && ! blaines_is_transactional_product( $product->get_id() )
        ) {
            return false;
        }

        return $product->is_purchasable();
    }
}

if ( ! function_exists( 'blaines_check_postcode_delivery_availability' ) ) {
    /**
     * Check delivery/collection availability for a postcode using WooCommerce shipping zones.
     *
     * @param string $postcode Raw postcode.
     * @return array<string, mixed>
     */
    function blaines_check_postcode_delivery_availability( string $postcode ): array {
        $postcode = wc_clean( $postcode );
        $postcode = wc_format_postcode( $postcode, 'GB' );

        if ( '' === $postcode ) {
            return [
                'success'              => false,
                'postcode'             => '',
                'delivery_available'   => false,
                'collection_available' => false,
                'status'               => 'invalid',
                'message'              => 'Please enter your full postcode so we can check delivery availability.',
            ];
        }

        if (
            class_exists( 'WC_Validation' )
            && ! WC_Validation::is_postcode( $postcode, 'GB' )
        ) {
            return [
                'success'              => false,
                'postcode'             => $postcode,
                'delivery_available'   => false,
                'collection_available' => false,
                'status'               => 'invalid',
                'message'              => 'Please enter a valid UK postcode.',
            ];
        }

        if (
            ! class_exists( 'WC_Shipping_Zones' )
            || ! function_exists( 'WC' )
        ) {
            return [
                'success'              => false,
                'postcode'             => $postcode,
                'delivery_available'   => false,
                'collection_available' => false,
                'status'               => 'unavailable',
                'message'              => 'We could not check delivery availability at this time. Please contact Web Sales if you need help.',
            ];
        }

        $package = [
            'contents'        => WC()->cart ? WC()->cart->get_cart() : [],
            'contents_cost'   => WC()->cart ? (float) WC()->cart->get_subtotal() : 0.00,
            'applied_coupons' => WC()->cart ? WC()->cart->get_applied_coupons() : [],
            'user'            => [
                'ID' => get_current_user_id(),
            ],
            'destination'     => [
                'country'   => 'GB',
                'state'     => '',
                'postcode'  => $postcode,
                'city'      => '',
                'address'   => '',
                'address_1' => '',
                'address_2' => '',
            ],
        ];

        $zone = WC_Shipping_Zones::get_zone_matching_package( $package );

        $delivery_available   = false;
        $collection_available = false;

        if ( $zone instanceof WC_Shipping_Zone ) {
            $methods = $zone->get_shipping_methods( true );

            foreach ( $methods as $method ) {
                $method_id = isset( $method->id ) ? (string) $method->id : '';

                if ( in_array( $method_id, [ 'flat_rate', 'free_shipping' ], true ) ) {
                    $delivery_available = true;
                }

                if ( 'local_pickup' === $method_id ) {
                    $collection_available = true;
                }
            }
        }

        if ( WC()->customer ) {
            WC()->customer->set_shipping_country( 'GB' );
            WC()->customer->set_shipping_postcode( $postcode );
            WC()->customer->save();
        }

        if ( WC()->session ) {
            /*
             * A logged-out visitor may reach this AJAX request before
             * WooCommerce has created a browser session cookie. Explicitly set
             * it now so the checked postcode and pending shipping refresh
             * survive into the subsequent Add to Basket request.
             */
            if (
                ! is_user_logged_in()
                && method_exists(
                    WC()->session,
                    'set_customer_session_cookie'
                )
            ) {
                WC()->session->set_customer_session_cookie( true );
            }

            WC()->session->set( 'blaines_checked_postcode', $postcode );
            WC()->session->set( 'blaines_checked_postcode_delivery_available', $delivery_available ? 'yes' : 'no' );
            WC()->session->set( 'blaines_checked_postcode_collection_available', $collection_available ? 'yes' : 'no' );
            WC()->session->set( 'blaines_postcode_shipping_refresh_pending', 'yes' );
        }

        if ( function_exists( 'blaines_sync_shipping_after_postcode_check' ) ) {
            blaines_sync_shipping_after_postcode_check();
        }

        /*
         * Persist the AJAX-created guest session immediately. WordPress will
         * also run the normal shutdown save, but saving here ensures the next
         * request can retrieve the postcode state even when it follows
         * straight after the JSON response.
         */
        if (
            WC()->session
            && method_exists( WC()->session, 'save_data' )
        ) {
            WC()->session->save_data();
        }

        if ( $delivery_available ) {
            return [
                'success'              => true,
                'postcode'             => $postcode,
                'delivery_available'   => true,
                'collection_available' => $collection_available,
                'status'               => 'delivery',
                'message'              => 'Great news — local delivery is available for your postcode. You can choose eligible delivery, installation, disposal and packaging services in your basket.',
            ];
        }

        if ( $collection_available ) {
            return [
                'success'              => true,
                'postcode'             => $postcode,
                'delivery_available'   => false,
                'collection_available' => true,
                'status'               => 'collection',
                'message'              => 'Local delivery and optional services are not available for this postcode. You can continue for collection from Blaines, or contact Web Sales if you need help.',
            ];
        }

        return [
            'success'              => true,
            'postcode'             => $postcode,
            'delivery_available'   => false,
            'collection_available' => false,
            'status'               => 'unavailable',
            'message'              => 'We cannot currently offer online delivery or collection for this postcode. Please contact Web Sales if you need help.',
        ];
    }
}

if ( ! function_exists( 'blaines_ajax_check_product_postcode' ) ) {
    /**
     * AJAX postcode checker callback.
     *
     * @return void
     */
    function blaines_ajax_check_product_postcode(): void {
        check_ajax_referer( 'blaines_product_postcode_checker', 'nonce' );

        $postcode = isset( $_POST['postcode'] )
            ? sanitize_text_field( wp_unslash( $_POST['postcode'] ) )
            : '';

        $result = blaines_check_postcode_delivery_availability( $postcode );

        wp_send_json( $result );
    }

    add_action( 'wp_ajax_blaines_check_product_postcode', 'blaines_ajax_check_product_postcode' );
    add_action( 'wp_ajax_nopriv_blaines_check_product_postcode', 'blaines_ajax_check_product_postcode' );
}

if ( ! function_exists( 'blaines_render_product_postcode_checker_modal' ) ) {
    /**
     * Render product postcode checker modal.
     *
     * @return void
     */
    function blaines_render_product_postcode_checker_modal(): void {
        if ( ! blaines_should_render_product_postcode_checker() ) {
            return;
        }

        $ajax_url = admin_url( 'admin-ajax.php' );
        $nonce    = wp_create_nonce( 'blaines_product_postcode_checker' );
        ?>

        <div
            id="blaines-product-postcode-checker"
            class="fixed inset-0 z-[9999] hidden"
            data-ajax-url="<?php echo esc_url( $ajax_url ); ?>"
            data-nonce="<?php echo esc_attr( $nonce ); ?>"
            aria-hidden="true"
        >
            <div class="absolute inset-0 bg-black/60" data-blaines-postcode-close></div>

            <div class="relative mx-auto mt-20 w-[calc(100%-2rem)] max-w-2xl border border-neutral-300 bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-neutral-200 px-6 py-4">
                    <h2 class="text-2xl font-semibold leading-tight text-neutral-950">
                        Delivery &amp; Installation
                    </h2>

                    <button
                        type="button"
                        class="text-3xl font-light leading-none text-neutral-500 transition hover:text-neutral-950"
                        data-blaines-postcode-close
                        aria-label="Close postcode checker"
                    >
                        &times;
                    </button>
                </div>

                <div class="p-6">
                    <p class="text-base font-light leading-relaxed text-neutral-700">
                        To help us show the correct delivery and service options, please enter your full delivery postcode.
                    </p>

                    <div class="mt-5">
                        <label
                            for="blaines-product-postcode-input"
                            class="block text-sm font-semibold text-neutral-950"
                        >
                            Please enter your full postcode
                        </label>

                        <div class="mt-2 flex flex-col gap-3 sm:flex-row">
                            <input
                                type="text"
                                id="blaines-product-postcode-input"
                                class="min-h-12 flex-1 border border-neutral-300 px-4 py-3 text-base font-light uppercase text-neutral-950"
                                autocomplete="postal-code"
                                placeholder="e.g. NR19 2AB"
                            >

                            <button
                                type="button"
                                class="inline-flex min-h-12 items-center justify-center border border-neutral-950 bg-neutral-950 px-5 py-3 text-sm font-semibold uppercase tracking-[0.12em] text-white transition hover:border-blue-700 hover:bg-blue-700"
                                data-blaines-postcode-check
                            >
                                Check postcode
                            </button>
                        </div>
                    </div>

                    <div
                        id="blaines-product-postcode-result"
                        class="mt-5 hidden border-l-4 p-4"
                        aria-live="polite"
                    ></div>

                    <div class="mt-5 hidden flex-wrap gap-3" data-blaines-postcode-actions>
                        <button
                            type="button"
                            class="hidden border border-neutral-950 bg-neutral-950 px-5 py-3 text-xs font-semibold uppercase tracking-[0.14em] text-white transition hover:border-blue-700 hover:bg-blue-700"
                            data-blaines-postcode-continue
                        >
                            Continue and add to basket
                        </button>

                        <a
                            href="tel:01760302111"
                            class="hidden items-center border border-neutral-300 bg-white px-5 py-3 text-xs font-semibold uppercase tracking-[0.14em] text-neutral-950 transition hover:border-blue-700 hover:text-blue-700"
                            data-blaines-postcode-call
                        >
                            Call Web Sales
                        </a>

                        <button
                            type="button"
                            class="border border-neutral-300 bg-white px-5 py-3 text-xs font-semibold uppercase tracking-[0.14em] text-neutral-950 transition hover:border-blue-700 hover:text-blue-700"
                            data-blaines-postcode-close
                        >
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <?php
    }

    add_action( 'wp_footer', 'blaines_render_product_postcode_checker_modal', 40 );
}

if ( ! function_exists( 'blaines_get_checked_product_postcode' ) ) {
    /**
     * Get the postcode checked by the product-page postcode checker.
     *
     * @return string
     */
    function blaines_get_checked_product_postcode(): string {
        if ( ! function_exists( 'WC' ) || ! WC()->session ) {
            return '';
        }

        return trim( (string) WC()->session->get( 'blaines_checked_postcode', '' ) );
    }

    /**
     * Carry the verified product/basket postcode into checkout.
     *
     * The product postcode checker stores the verified postcode in the
     * WooCommerce session. On a fresh guest checkout WooCommerce may otherwise
     * begin with an empty billing postcode, causing the first checkout AJAX
     * refresh to lose the local-delivery shipping zone.
     *
     * Existing checkout/customer postcode values always take priority.
     *
     * @param mixed $value Existing checkout postcode value.
     * @return mixed
     */
    if ( ! function_exists( 'blaines_default_checkout_postcode_from_checked_postcode' ) ) {
        function blaines_default_checkout_postcode_from_checked_postcode( $value ) {
            if (
                ! function_exists( 'is_checkout' )
                || ! is_checkout()
                || (
                    function_exists( 'is_order_received_page' )
                    && is_order_received_page()
                )
            ) {
                return $value;
            }

            /*
             * Never replace an address WooCommerce already knows.
             */
            if ( null !== $value && '' !== trim( (string) $value ) ) {
                return $value;
            }

            if ( ! function_exists( 'blaines_get_checked_product_postcode' ) ) {
                return $value;
            }

            $checked_postcode = blaines_get_checked_product_postcode();

            if ( '' === $checked_postcode ) {
                return $value;
            }

            return $checked_postcode;
        }

        add_filter(
            'default_checkout_billing_postcode',
            'blaines_default_checkout_postcode_from_checked_postcode',
            20
        );

        add_filter(
            'default_checkout_shipping_postcode',
            'blaines_default_checkout_postcode_from_checked_postcode',
            20
        );
    }
}

if ( ! function_exists( 'blaines_checked_postcode_has_delivery' ) ) {
    /**
     * Check whether the stored postcode check confirmed delivery availability.
     *
     * @return bool|null True = delivery available, false = no delivery, null = no postcode checked.
     */
    function blaines_checked_postcode_has_delivery(): ?bool {
        if ( ! function_exists( 'WC' ) || ! WC()->session ) {
            return null;
        }

        $postcode = blaines_get_checked_product_postcode();

        if ( '' === $postcode ) {
            return null;
        }

        $delivery_available = WC()->session->get( 'blaines_checked_postcode_delivery_available', '' );

        if ( 'yes' === $delivery_available ) {
            return true;
        }

        if ( 'no' === $delivery_available ) {
            return false;
        }

        return null;
    }
}

if ( ! function_exists( 'blaines_fulfilment_services_blocked_by_checked_postcode' ) ) {
    /**
     * Determine whether basket services should be blocked by the checked postcode.
     *
     * @return bool
     */
    function blaines_fulfilment_services_blocked_by_checked_postcode(): bool {
        return false === blaines_checked_postcode_has_delivery();
    }
}
