<?php
/**
 * Transactional product availability display helpers.
 *
 * 14P: one canonical customer-facing availability presentation for
 * archive cards, single product pages and the basket.
 *
 * @package blainestwentysix
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'blaines_get_transactional_availability_product' ) ) {
    /**
     * Resolve a usable WooCommerce product object.
     *
     * @param WC_Product|int|null $product Product object or product ID.
     * @return WC_Product|null
     */
    function blaines_get_transactional_availability_product( $product = null ): ?WC_Product {
        if ( $product instanceof WC_Product ) {
            return $product;
        }

        $product_id = $product ? absint( $product ) : get_the_ID();

        if ( ! $product_id ) {
            return null;
        }

        $resolved = wc_get_product( $product_id );

        return $resolved instanceof WC_Product ? $resolved : null;
    }
}

if ( ! function_exists( 'blaines_get_transactional_availability_meta_product_id' ) ) {
    /**
     * Return the product ID that owns operational availability meta.
     *
     * @param WC_Product $product Product object.
     * @return int
     */
    function blaines_get_transactional_availability_meta_product_id( WC_Product $product ): int {
        return $product->is_type( 'variation' ) && $product->get_parent_id()
            ? $product->get_parent_id()
            : $product->get_id();
    }
}

if ( ! function_exists( 'blaines_get_transactional_availability_source' ) ) {
    /**
     * Return the effective availability source.
     *
     * Local boxed stock always wins, even if older supplier meta remains.
     *
     * @param WC_Product|int|null $product Product object or product ID.
     * @return string
     */
    function blaines_get_transactional_availability_source( $product = null ): string {
        $product = blaines_get_transactional_availability_product( $product );

        if ( ! $product instanceof WC_Product ) {
            return '';
        }

        $product_id = blaines_get_transactional_availability_meta_product_id( $product );
        $source     = trim( (string) get_post_meta( $product_id, '_blaines_availability_source', true ) );
        $local_qty  = (int) get_post_meta( $product_id, '_blaines_local_free_stock', true );
        $local_flag = strtolower( trim( (string) get_post_meta( $product_id, '_blaines_local_stock_active', true ) ) );
        $local_on   = in_array( $local_flag, [ 'y', 'yes', 'a', 'active', '1', 'true' ], true );

        if ( 'local' === $source || ( $local_on && $local_qty > 0 ) ) {
            return 'local';
        }

        return $source;
    }
}

if ( ! function_exists( 'blaines_format_transactional_availability_date_object' ) ) {
    /**
     * Format an availability date for the chosen display context.
     *
     * @param DateTimeImmutable $date    Date.
     * @param string            $context loop, single or cart.
     * @return string
     */
    function blaines_format_transactional_availability_date_object( DateTimeImmutable $date, string $context = 'single' ): string {
        $format = 'loop' === $context ? 'D j M' : 'l j F';

        return wp_date( $format, $date->getTimestamp(), wp_timezone() );
    }
}

if ( ! function_exists( 'blaines_get_transactional_earliest_delivery_date' ) ) {
    /**
     * Return the same earliest local-delivery date used by checkout where possible.
     *
     * This keeps archive/product/cart messaging aligned with the preferred-date picker
     * and with Delivery Schedule date overrides.
     *
     * @param WC_Product $product Product object.
     * @return DateTimeImmutable|null
     */
    function blaines_get_transactional_earliest_delivery_date( WC_Product $product ): ?DateTimeImmutable {
        if ( function_exists( 'blaines_get_product_earliest_preferred_delivery_date' ) ) {
            $date = blaines_get_product_earliest_preferred_delivery_date( $product );

            if ( $date instanceof DateTimeImmutable ) {
                return $date;
            }
        }

        $product_id = blaines_get_transactional_availability_meta_product_id( $product );
        $stored     = trim( (string) get_post_meta( $product_id, '_blaines_customer_available_from_date', true ) );

        if ( '' !== $stored ) {
            $date = DateTimeImmutable::createFromFormat( '!Y-m-d', $stored, wp_timezone() );

            if ( $date instanceof DateTimeImmutable ) {
                return $date;
            }
        }

        return null;
    }
}

if ( ! function_exists( 'blaines_product_uses_manufacturer_order_availability_fallback' ) ) {
    /**
     * Determine whether a product is using the cautious BOS/NEF/SIE fallback.
     *
     * Tradeplace/Brand Stock is deliberately excluded because it has a real live
     * manufacturer availability signal and can therefore show a calculated date.
     *
     * @param WC_Product|int|null $product Product object or product ID.
     * @return bool
     */
    function blaines_product_uses_manufacturer_order_availability_fallback( $product = null ): bool {
        $product = blaines_get_transactional_availability_product( $product );

        if ( ! $product instanceof WC_Product ) {
            return false;
        }

        $product_id = blaines_get_transactional_availability_meta_product_id( $product );
        $source     = blaines_get_transactional_availability_source( $product );
        $flag       = strtolower( trim( (string) get_post_meta( $product_id, '_blaines_manufacturer_order_fallback', true ) ) );

        return 'manufacturer_order' === $source || 'yes' === $flag;
    }
}

if ( ! function_exists( 'blaines_get_transactional_availability_state' ) ) {
    /**
     * Return canonical availability state.
     *
     * @param WC_Product|int|null $product Product object or product ID.
     * @return string
     */
    function blaines_get_transactional_availability_state( $product = null ): string {
        $product = blaines_get_transactional_availability_product( $product );

        if ( ! $product instanceof WC_Product ) {
            return 'unknown';
        }

        $stock_status = (string) $product->get_stock_status();

        if ( 'outofstock' === $stock_status ) {
            return 'outofstock';
        }

        if ( blaines_product_uses_manufacturer_order_availability_fallback( $product ) ) {
            return 'backorder';
        }

        if ( 'instock' === $stock_status ) {
            return 'instock';
        }

        if ( 'onbackorder' === $stock_status ) {
            return 'backorder';
        }

        return 'unknown';
    }
}

if ( ! function_exists( 'blaines_get_transactional_availability_presentation' ) ) {
    /**
     * Return one canonical customer-facing availability presentation.
     *
     * @param WC_Product|int|null $product Product object or product ID.
     * @param string              $context loop, single or cart.
     * @return array<string, string>
     */
    function blaines_get_transactional_availability_presentation( $product = null, string $context = 'single' ): array {
        $product = blaines_get_transactional_availability_product( $product );

        $empty = [
            'state'           => 'unknown',
            'source'          => '',
            'title'           => '',
            'delivery_detail' => '',
            'collection_note' => '',
            'detail'          => '',
            'message'         => '',
        ];

        if ( ! $product instanceof WC_Product ) {
            return $empty;
        }

        $product_id = blaines_get_transactional_availability_meta_product_id( $product );

        if ( function_exists( 'blaines_is_transactional_product' ) && ! blaines_is_transactional_product( $product_id ) ) {
            return $empty;
        }

        $state    = blaines_get_transactional_availability_state( $product );
        $source   = blaines_get_transactional_availability_source( $product );
        $fallback = blaines_product_uses_manufacturer_order_availability_fallback( $product );

        $presentation           = $empty;
        $presentation['state']  = $state;
        $presentation['source'] = $source;

        if ( 'outofstock' === $state ) {
            $presentation['title']           = 'Out of stock';
            $presentation['delivery_detail'] = 'Please contact Web Sales for availability.';
            $presentation['detail']          = $presentation['delivery_detail'];
            $presentation['message']         = 'Out of stock';

            return $presentation;
        }

        if ( $fallback ) {
            $presentation['title']           = 'Available to order';
            $presentation['delivery_detail'] = 'Web Sales will confirm the earliest local delivery date.';
            $presentation['collection_note'] = 'Collection will also be available once stock is received.';

            if ( 'loop' === $context ) {
                $presentation['detail'] = 'Web Sales will confirm availability · Collection available';
            } else {
                $presentation['detail'] = $presentation['delivery_detail'];
            }

            $presentation['message'] = 'Available to order — ' . $presentation['delivery_detail'] . ' ' . $presentation['collection_note'];

            return $presentation;
        }

        $date           = blaines_get_transactional_earliest_delivery_date( $product );
        $formatted_date = $date instanceof DateTimeImmutable
            ? blaines_format_transactional_availability_date_object( $date, $context )
            : '';

        $is_brand_stock = 'brand_stock' === $source;
        $is_local       = 'local' === $source;
        $is_backorder   = 'backorder' === $state;

        $presentation['title'] = $is_backorder || $is_brand_stock
            ? 'Available to order'
            : 'In stock';

        if ( $is_brand_stock || $is_backorder ) {
            $presentation['delivery_detail'] = '' !== $formatted_date
                ? 'Expected local delivery from ' . $formatted_date . ( 'loop' === $context ? '' : '.' )
                : 'Available to order.';
        } else {
            $presentation['delivery_detail'] = '' !== $formatted_date
                ? 'Available for local delivery from ' . $formatted_date . ( 'loop' === $context ? '' : '.' )
                : 'Available for local delivery.';
        }

        $presentation['collection_note'] = 'Collection also available — choose your showroom at checkout.';

        if ( 'loop' === $context ) {
            if ( '' !== $formatted_date ) {
                $prefix = ( $is_brand_stock || $is_backorder )
                    ? 'Expected local delivery from '
                    : 'Local delivery from ';

                $presentation['detail'] = $prefix . $formatted_date . ' · Collection available';
            } else {
                $presentation['detail'] = 'Local delivery · Collection available';
            }
        } else {
            $presentation['detail'] = $presentation['delivery_detail'];
        }

        $presentation['message'] = $presentation['title'] . ' — ' . $presentation['delivery_detail'] . ' ' . $presentation['collection_note'];

        /*
         * Defensive fallback for unusual/legacy records with no meaningful source.
         */
        if ( '' === $source && ! $is_local ) {
            $stored_message = trim( (string) get_post_meta( $product_id, '_blaines_customer_availability_message', true ) );

            if ( '' !== $stored_message && 'availability currently unavailable' !== strtolower( $stored_message ) && ! $date instanceof DateTimeImmutable ) {
                $presentation['message'] = $stored_message;
            }
        }

        return $presentation;
    }
}

if ( ! function_exists( 'blaines_get_transactional_availability_message' ) ) {
    /**
     * Backwards-compatible availability message.
     *
     * The basket already consumes this helper, so returning the canonical 14P
     * message here automatically brings the cart into line without duplicating
     * another source-specific availability resolver in cart.php.
     *
     * @param WC_Product|int|null $product Product object or product ID.
     * @return string
     */
    function blaines_get_transactional_availability_message( $product = null ): string {
        $presentation = blaines_get_transactional_availability_presentation( $product, 'cart' );

        return (string) ( $presentation['message'] ?? '' );
    }
}

if ( ! function_exists( 'blaines_render_transactional_availability_message' ) ) {
    /**
     * Render availability for product archives and single-product pages.
     *
     * @param WC_Product|int|null $product Product object or product ID.
     * @param string              $context single or loop.
     * @return void
     */
    function blaines_render_transactional_availability_message( $product = null, string $context = 'single' ): void {
        $product = blaines_get_transactional_availability_product( $product );

        if ( ! $product instanceof WC_Product ) {
            return;
        }

        $presentation = blaines_get_transactional_availability_presentation( $product, $context );
        $state        = (string) ( $presentation['state'] ?? 'unknown' );
        $title        = trim( (string) ( $presentation['title'] ?? '' ) );
        $detail       = trim( (string) ( $presentation['detail'] ?? '' ) );
        $collection   = trim( (string) ( $presentation['collection_note'] ?? '' ) );

        if ( '' === $title ) {
            return;
        }

        $panel_class = 'mt-6 border border-neutral-200 border-l-4 border-l-neutral-400 bg-neutral-50 p-4 font-body';
        $title_class = 'text-sm font-semibold text-neutral-950';
        $meta_class  = 'mt-1 text-sm font-light leading-relaxed text-neutral-600';
        $icon_class  = 'mt-1 flex h-7 w-7 shrink-0 items-center justify-center border border-current text-xs font-semibold leading-none';

        if ( 'instock' === $state ) {
            $panel_class = 'mt-6 border border-green-200 border-l-4 border-l-green-700 bg-green-50 p-4 font-body';
            $title_class = 'text-sm font-semibold text-green-800';
        } elseif ( 'backorder' === $state ) {
            $panel_class = 'mt-6 border border-blue-200 border-l-4 border-l-blue-700 bg-blue-50 p-4 font-body';
            $title_class = 'text-sm font-semibold text-blue-900';
        } elseif ( 'outofstock' === $state ) {
            $panel_class = 'mt-6 border border-red-200 border-l-4 border-l-red-700 bg-red-50 p-4 font-body';
            $title_class = 'text-sm font-semibold text-red-800';
        }

        if ( 'loop' === $context ) {
            ?>
            <div
                class="mt-4 border-t border-neutral-200 pt-4 font-body"
                data-availability-state="<?php echo esc_attr( $state ); ?>"
            >
                <p class="<?php echo esc_attr( str_replace( 'text-sm', 'text-xs', $title_class ) ); ?>">
                    <?php echo esc_html( $title ); ?>
                </p>

                <?php if ( '' !== $detail ) : ?>
                    <p class="mt-1 text-xs font-light leading-snug text-neutral-600">
                        <?php echo esc_html( $detail ); ?>
                    </p>
                <?php endif; ?>
            </div>
            <?php

            return;
        }

        ?>
        <div
            class="<?php echo esc_attr( $panel_class ); ?>"
            data-availability-state="<?php echo esc_attr( $state ); ?>"
        >
            <div class="flex items-start gap-3">
                <div class="<?php echo esc_attr( $icon_class ); ?>">
                    <?php if ( 'instock' === $state ) : ?>
                        ✓
                    <?php elseif ( 'backorder' === $state ) : ?>
                        i
                    <?php elseif ( 'outofstock' === $state ) : ?>
                        !
                    <?php else : ?>
                        i
                    <?php endif; ?>
                </div>

                <div>
                    <p class="<?php echo esc_attr( $title_class ); ?>">
                        <?php echo esc_html( $title ); ?>
                    </p>

                    <?php if ( '' !== $detail ) : ?>
                        <p class="<?php echo esc_attr( $meta_class ); ?>">
                            <?php echo esc_html( $detail ); ?>
                        </p>
                    <?php endif; ?>

                    <?php if ( '' !== $collection && 'outofstock' !== $state ) : ?>
                        <p class="mt-2 text-xs font-light leading-relaxed text-neutral-500">
                            <?php echo esc_html( $collection ); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    }
}
