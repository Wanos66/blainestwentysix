<?php
/**
 * Similar transactional product selection.
 *
 * @package Blaines
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'blaines_get_similar_transactional_products' ) ) {
    /**
     * Return credible alternatives for a transactional WooCommerce product.
     *
     * Selection order:
     * - deepest assigned product category;
     * - wider Electrical department fallback;
     * - products within roughly 30% of the current price are preferred;
     * - in-stock products are preferred ahead of back-orderable products.
     *
     * @param WC_Product $product Current product.
     * @param int        $limit   Maximum products to return.
     * @return WC_Product[]
     */
    function blaines_get_similar_transactional_products( WC_Product $product, $limit = 10 ) {
        static $request_cache = [];

        $limit     = max( 3, min( 12, absint( $limit ) ) );
        $product_id = $product->get_id();
        $cache_key  = $product_id . ':' . $limit;

        if ( isset( $request_cache[ $cache_key ] ) ) {
            return $request_cache[ $cache_key ];
        }

        $terms = get_the_terms( $product_id, 'product_cat' );

        if ( empty( $terms ) || is_wp_error( $terms ) ) {
            $request_cache[ $cache_key ] = [];
            return [];
        }

        $electrical_term = get_term_by( 'slug', 'electrical', 'product_cat' );
        $electrical_id   = $electrical_term instanceof WP_Term ? (int) $electrical_term->term_id : 0;
        $depths          = [];
        $valid_terms     = [];

        foreach ( $terms as $term ) {
            if ( ! $term instanceof WP_Term || 'electrical' === $term->slug ) {
                continue;
            }

            $ancestors = get_ancestors( $term->term_id, 'product_cat' );
            $depths[ $term->term_id ] = count( $ancestors );
            $valid_terms[]             = $term;
        }

        if ( empty( $valid_terms ) ) {
            $request_cache[ $cache_key ] = [];
            return [];
        }

        usort(
            $valid_terms,
            static function ( WP_Term $a, WP_Term $b ) use ( $depths ) {
                return ( $depths[ $b->term_id ] ?? 0 ) <=> ( $depths[ $a->term_id ] ?? 0 );
            }
        );

        $deepest_term = $valid_terms[0];
        $department   = null;
        $trail_ids    = array_reverse( get_ancestors( $deepest_term->term_id, 'product_cat' ) );
        $trail_ids[]  = $deepest_term->term_id;

        foreach ( $trail_ids as $term_id ) {
            $trail_term = get_term( $term_id, 'product_cat' );

            if ( ! $trail_term instanceof WP_Term || is_wp_error( $trail_term ) ) {
                continue;
            }

            if ( $electrical_id && (int) $trail_term->parent === $electrical_id ) {
                $department = $trail_term;
                break;
            }
        }

        if ( ! $department ) {
            $department = $valid_terms[ count( $valid_terms ) - 1 ] ?? $deepest_term;
        }

        $candidate_sources = [
            [
                'term' => $deepest_term,
                'rank' => 0,
            ],
        ];

        if ( $department instanceof WP_Term && $department->term_id !== $deepest_term->term_id ) {
            $candidate_sources[] = [
                'term' => $department,
                'rank' => 1,
            ];
        }

        $candidates    = [];
        $seen_ids      = [ $product_id => true ];
        $current_price = (float) $product->get_price();

        foreach ( $candidate_sources as $source ) {
            $source_products = wc_get_products(
                [
                    'status'     => 'publish',
                    'limit'      => 30,
                    'exclude'    => [ $product_id ],
                    'category'   => [ $source['term']->slug ],
                    'visibility' => 'catalog',
                    'return'     => 'objects',
                ]
            );

            foreach ( $source_products as $candidate ) {
                if ( ! $candidate instanceof WC_Product ) {
                    continue;
                }

                $candidate_id = $candidate->get_id();

                if ( isset( $seen_ids[ $candidate_id ] ) ) {
                    continue;
                }

                $seen_ids[ $candidate_id ] = true;

                if (
                    function_exists( 'blaines_is_transactional_product' )
                    && ! blaines_is_transactional_product( $candidate )
                ) {
                    continue;
                }

                if ( ! $candidate->is_visible() || ! $candidate->is_purchasable() ) {
                    continue;
                }

                if ( ! $candidate->is_in_stock() && ! $candidate->backorders_allowed() ) {
                    continue;
                }

                $candidate_price = (float) $candidate->get_price();
                $price_distance  = 999;

                if ( $current_price > 0 && $candidate_price > 0 ) {
                    $price_distance = abs( $candidate_price - $current_price ) / $current_price;
                }

                $stock_rank = 0;

                if ( ! $candidate->is_in_stock() && $candidate->backorders_allowed() ) {
                    $stock_rank = 1;
                }

                $candidates[] = [
                    'product'        => $candidate,
                    'source_rank'    => (int) $source['rank'],
                    'price_band_rank'=> $price_distance <= 0.30 ? 0 : 1,
                    'stock_rank'     => $stock_rank,
                    'price_distance' => $price_distance,
                ];
            }
        }

        usort(
            $candidates,
            static function ( array $a, array $b ) {
                foreach ( [ 'source_rank', 'price_band_rank', 'stock_rank', 'price_distance' ] as $key ) {
                    if ( $a[ $key ] === $b[ $key ] ) {
                        continue;
                    }

                    return $a[ $key ] <=> $b[ $key ];
                }

                return $a['product']->get_id() <=> $b['product']->get_id();
            }
        );

        $products = array_map(
            static function ( array $candidate ) {
                return $candidate['product'];
            },
            array_slice( $candidates, 0, $limit )
        );

        // A very small or weak section is less useful than no section.
        if ( count( $products ) < 3 ) {
            $products = [];
        }

        $request_cache[ $cache_key ] = $products;

        return $products;
    }
}
