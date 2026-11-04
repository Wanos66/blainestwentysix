<?php
/**
 * Reusable campaign catalogue context for seasonal Electrical promotions.
 *
 * The caller supplies the promotion's ordered product IDs. This layer then:
 * - validates visible/purchasable transactional products;
 * - reads Product Type, Brand and effective WooCommerce price;
 * - builds contextual facet counts using "all active filters except self";
 * - applies sorting and pagination;
 * - exposes category-shortcut data for the campaign landing page.
 *
 * Query parameters: type, brand, price, sort, pg.
 *
 * @package Blaines
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'blaines_campaign_catalogue_normalize_product_ids' ) ) {
    function blaines_campaign_catalogue_normalize_product_ids( array $product_ids ): array {
        $valid_ids = [];
        $seen      = [];

        foreach ( $product_ids as $product_id ) {
            $product_id = absint( $product_id );

            if ( ! $product_id || isset( $seen[ $product_id ] ) ) {
                continue;
            }

            $seen[ $product_id ] = true;
            $product             = wc_get_product( $product_id );

            if ( ! $product instanceof WC_Product ) {
                continue;
            }

            if ( ! $product->is_visible() || ! $product->is_purchasable() ) {
                continue;
            }

            if (
                function_exists( 'blaines_is_transactional_product' )
                && ! blaines_is_transactional_product( $product )
            ) {
                continue;
            }

            $valid_ids[] = $product_id;
        }

        if ( ! empty( $valid_ids ) ) {
            update_meta_cache( 'post', $valid_ids );
            update_object_term_cache( $valid_ids, 'product' );
        }

        return $valid_ids;
    }
}

if ( ! function_exists( 'blaines_campaign_catalogue_get_price_bands' ) ) {
    function blaines_campaign_catalogue_get_price_bands(): array {
        if ( function_exists( 'blaines_get_transactional_price_bands' ) ) {
            return blaines_get_transactional_price_bands();
        }

        return [
            'under-300' => [
                'label' => 'Under £300',
                'min'   => 0.0,
                'max'   => 300.0,
            ],
            '300-499' => [
                'label' => '£300–£499.99',
                'min'   => 300.0,
                'max'   => 500.0,
            ],
            '500-749' => [
                'label' => '£500–£749.99',
                'min'   => 500.0,
                'max'   => 750.0,
            ],
            '750-999' => [
                'label' => '£750–£999.99',
                'min'   => 750.0,
                'max'   => 1000.0,
            ],
            '1000-plus' => [
                'label' => '£1,000 and over',
                'min'   => 1000.0,
                'max'   => null,
            ],
        ];
    }
}

if ( ! function_exists( 'blaines_campaign_catalogue_price_matches_band' ) ) {
    function blaines_campaign_catalogue_price_matches_band( float $price, array $band ): bool {
        if ( $price <= 0 ) {
            return false;
        }

        $min = $band['min'];
        $max = $band['max'];

        $matches_min = null === $min
            || ( (float) $min > 0 ? $price >= (float) $min : $price > (float) $min );

        $matches_max = null === $max || $price < (float) $max;

        return $matches_min && $matches_max;
    }
}

if ( ! function_exists( 'blaines_campaign_catalogue_get_electrical_structure' ) ) {
    function blaines_campaign_catalogue_get_electrical_structure(): array {
        static $structure = null;

        if ( is_array( $structure ) ) {
            return $structure;
        }

        $electrical = get_term_by( 'slug', 'electrical', 'product_cat' );

        if ( ! $electrical instanceof WP_Term ) {
            $structure = [
                'electrical'     => null,
                'departments'    => [],
                'department_ids' => [],
            ];

            return $structure;
        }

        $departments = get_terms(
            [
                'taxonomy'   => 'product_cat',
                'parent'     => $electrical->term_id,
                'hide_empty' => false,
                'orderby'    => 'name',
                'order'      => 'ASC',
            ]
        );

        if ( is_wp_error( $departments ) ) {
            $departments = [];
        }

        $departments = array_values(
            array_filter(
                $departments,
                static fn ( $term ): bool => $term instanceof WP_Term
            )
        );

        $structure = [
            'electrical'     => $electrical,
            'departments'    => $departments,
            'department_ids' => array_map(
                static fn ( WP_Term $term ): int => (int) $term->term_id,
                $departments
            ),
        ];

        return $structure;
    }
}


if ( ! function_exists( 'blaines_campaign_catalogue_get_department_for_term' ) ) {
    /**
     * Resolve a product category term to its top-level Electrical department.
     */
    function blaines_campaign_catalogue_get_department_for_term(
        WP_Term $term,
        int $electrical_term_id
    ): ?WP_Term {
        $current = $term;
        $guard   = 0;

        while ( $current instanceof WP_Term && $guard < 20 ) {
            if ( (int) $current->parent === $electrical_term_id ) {
                return $current;
            }

            if ( ! $current->parent ) {
                return null;
            }

            $parent = get_term(
                (int) $current->parent,
                'product_cat'
            );

            if (
                is_wp_error( $parent )
                || ! $parent instanceof WP_Term
            ) {
                return null;
            }

            $current = $parent;
            $guard++;
        }

        return null;
    }
}

if ( ! function_exists( 'blaines_campaign_catalogue_get_product_department_terms' ) ) {
    /**
     * Return unique top-level Electrical departments for a product.
     *
     * @return array<int,WP_Term>
     */
    function blaines_campaign_catalogue_get_product_department_terms(
        int $product_id
    ): array {
        $structure  = blaines_campaign_catalogue_get_electrical_structure();
        $electrical = $structure['electrical'] ?? null;

        if ( ! $electrical instanceof WP_Term ) {
            return [];
        }

        $terms = get_the_terms(
            $product_id,
            'product_cat'
        );

        if ( ! is_array( $terms ) ) {
            return [];
        }

        $departments = [];

        foreach ( $terms as $term ) {
            if ( ! $term instanceof WP_Term ) {
                continue;
            }

            $department = blaines_campaign_catalogue_get_department_for_term(
                $term,
                (int) $electrical->term_id
            );

            if ( ! $department instanceof WP_Term ) {
                continue;
            }

            $departments[ $department->term_id ] = $department;
        }

        return array_values( $departments );
    }
}

if ( ! function_exists( 'blaines_campaign_catalogue_find_acf_department_image' ) ) {
    /**
     * Return the existing Electrical landing-page ACF image for a department.
     *
     * The Electrical page stores the seven images inside the
     * `electrical_department_images` ACF group field.
     */
    function blaines_campaign_catalogue_find_acf_department_image(
        WP_Term $department
    ): int {
        if ( ! function_exists( 'get_field' ) ) {
            return 0;
        }

        /*
         * Resolve the real Electrical landing page rather than relying on
         * get_page_by_path() alone. The page template itself reads its ACF
         * images from get_queried_object_id(), so the campaign must target
         * that same page record.
         */
        $electrical_page_id = absint(
            url_to_postid(
                home_url( '/electrical/' )
            )
        );

        if ( ! $electrical_page_id ) {
            $electrical_page = get_page_by_path(
                'electrical',
                OBJECT,
                'page'
            );

            if ( $electrical_page instanceof WP_Post ) {
                $electrical_page_id = (int) $electrical_page->ID;
            }
        }

        if ( ! $electrical_page_id ) {
            $electrical_pages = get_posts(
                [
                    'post_type'      => 'page',
                    'post_status'    => 'publish',
                    'posts_per_page' => 1,
                    'fields'         => 'ids',
                    'meta_key'       => '_wp_page_template',
                    'meta_value'     => 'page-electrical.php',
                    'no_found_rows'  => true,
                ]
            );

            if ( ! empty( $electrical_pages ) ) {
                $electrical_page_id = absint(
                    $electrical_pages[0]
                );
            }
        }

        if ( ! $electrical_page_id ) {
            return 0;
        }

        $department_images = get_field(
            'electrical_department_images',
            $electrical_page_id
        );

        if ( ! is_array( $department_images ) ) {
            return 0;
        }

        $field_map = [
            'laundry'          => 'laundry',
            'refrigeration'    => 'refrigeration',
            'dishwashers'      => 'dishwashers',
            'cooking'          => 'cooking',
            'small-appliances' => 'small_appliances',
            'floorcare'        => 'floorcare',
            'tv-entertainment' => 'tv_entertainment',
        ];

        $field_name = $field_map[ $department->slug ] ?? '';

        if ( '' === $field_name ) {
            return 0;
        }

        $image_id = absint(
            $department_images[ $field_name ] ?? 0
        );

        if ( $image_id ) {
            return $image_id;
        }

        /*
         * ACF group subfields are stored using the parent field name as a
         * prefix. Read the underlying value as a defensive fallback in case
         * the grouped get_field() response does not expose a populated
         * subfield for the current request.
         */
        return absint(
            get_post_meta(
                $electrical_page_id,
                'electrical_department_images_' . $field_name,
                true
            )
        );
    }
}

if ( ! function_exists( 'blaines_campaign_catalogue_get_product_type_terms' ) ) {
    function blaines_campaign_catalogue_get_product_type_terms( int $product_id ): array {
        $structure      = blaines_campaign_catalogue_get_electrical_structure();
        $department_ids = $structure['department_ids'];

        if ( empty( $department_ids ) ) {
            return [];
        }

        $terms = get_the_terms( $product_id, 'product_cat' );

        if ( ! is_array( $terms ) ) {
            return [];
        }

        return array_values(
            array_filter(
                $terms,
                static function ( $term ) use ( $department_ids ): bool {
                    return $term instanceof WP_Term
                        && in_array( (int) $term->parent, $department_ids, true );
                }
            )
        );
    }
}

if ( ! function_exists( 'blaines_campaign_catalogue_format_type_label' ) ) {
    function blaines_campaign_catalogue_format_type_label( WP_Term $term ): string {
        $label = trim( (string) $term->name );

        if ( ! $term->parent ) {
            return $label;
        }

        $parent = get_term( (int) $term->parent, 'product_cat' );

        if ( ! $parent instanceof WP_Term ) {
            return $label;
        }

        $prefix = trim( (string) $parent->name ) . ' ';

        if ( 0 === stripos( $label, $prefix ) ) {
            $label = trim( substr( $label, strlen( $prefix ) ) );
        }

        return $label;
    }
}

if ( ! function_exists( 'blaines_campaign_catalogue_build_product_map' ) ) {
    function blaines_campaign_catalogue_build_product_map( array $product_ids ): array {
        $product_map = [];

        foreach ( array_values( $product_ids ) as $order => $product_id ) {
            $product = wc_get_product( $product_id );

            if ( ! $product instanceof WC_Product ) {
                continue;
            }

            $department_terms = blaines_campaign_catalogue_get_product_department_terms( $product_id );
            $type_terms       = blaines_campaign_catalogue_get_product_type_terms( $product_id );
            $brand_terms = taxonomy_exists( 'product_brand' )
                ? get_the_terms( $product_id, 'product_brand' )
                : [];

            if ( ! is_array( $brand_terms ) ) {
                $brand_terms = [];
            }

            $brand_terms = array_values(
                array_filter(
                    $brand_terms,
                    static fn ( $term ): bool => $term instanceof WP_Term
                )
            );

            $price         = (float) $product->get_price();
            $regular_price = (float) $product->get_regular_price();
            $saving        = 0.0;

            if (
                $product->is_on_sale()
                && $regular_price > 0
                && $price > 0
                && $regular_price > $price
            ) {
                $saving = max( 0, $regular_price - $price );
            }

            $product_map[ $product_id ] = [
                'product'     => $product,
                'title'       => trim( (string) $product->get_name() ),
                'price'       => $price,
                'saving'      => $saving,
                'department_slugs' => array_map(
                    static fn ( WP_Term $term ): string => (string) $term->slug,
                    $department_terms
                ),
                'department_terms' => $department_terms,
                'type_slugs'       => array_map(
                    static fn ( WP_Term $term ): string => (string) $term->slug,
                    $type_terms
                ),
                'type_terms'       => $type_terms,
                'brand_slugs' => array_map(
                    static fn ( WP_Term $term ): string => (string) $term->slug,
                    $brand_terms
                ),
                'brand_terms' => $brand_terms,
                'order'       => (int) $order,
            ];
        }

        return $product_map;
    }
}

if ( ! function_exists( 'blaines_campaign_catalogue_read_request' ) ) {
    function blaines_campaign_catalogue_read_request( array $price_bands ): array {
        $department = isset( $_GET['department'] )
            ? sanitize_title( wp_unslash( $_GET['department'] ) )
            : '';

        $type = isset( $_GET['type'] )
            ? sanitize_title( wp_unslash( $_GET['type'] ) )
            : '';

        $brand = isset( $_GET['brand'] )
            ? sanitize_key( wp_unslash( $_GET['brand'] ) )
            : '';

        $price = isset( $_GET['price'] )
            ? sanitize_key( wp_unslash( $_GET['price'] ) )
            : '';

        $sort = isset( $_GET['sort'] )
            ? sanitize_key( wp_unslash( $_GET['sort'] ) )
            : 'featured';

        $allowed_sorts = [
            'featured',
            'price-asc',
            'price-desc',
            'saving-desc',
            'name-asc',
        ];

        if ( ! in_array( $sort, $allowed_sorts, true ) ) {
            $sort = 'featured';
        }

        if ( ! isset( $price_bands[ $price ] ) ) {
            $price = '';
        }

        $page = isset( $_GET['pg'] ) ? max( 1, absint( $_GET['pg'] ) ) : 1;

        return [
            'department' => $department,
            'type'       => $type,
            'brand'      => $brand,
            'price' => $price,
            'sort'  => $sort,
            'page'  => $page,
        ];
    }
}

if ( ! function_exists( 'blaines_campaign_catalogue_product_matches' ) ) {
    function blaines_campaign_catalogue_product_matches(
        array $data,
        array $active,
        array $price_bands,
        array $omit = []
    ): bool {
        if (
            ! in_array( 'department', $omit, true )
            && '' !== (string) ( $active['department'] ?? '' )
            && ! in_array(
                (string) $active['department'],
                (array) ( $data['department_slugs'] ?? [] ),
                true
            )
        ) {
            return false;
        }

        if (
            ! in_array( 'type', $omit, true )
            && '' !== $active['type']
            && ! in_array( $active['type'], $data['type_slugs'], true )
        ) {
            return false;
        }

        if (
            ! in_array( 'brand', $omit, true )
            && '' !== $active['brand']
            && ! in_array( $active['brand'], $data['brand_slugs'], true )
        ) {
            return false;
        }

        if (
            ! in_array( 'price', $omit, true )
            && '' !== $active['price']
            && isset( $price_bands[ $active['price'] ] )
            && ! blaines_campaign_catalogue_price_matches_band(
                (float) $data['price'],
                $price_bands[ $active['price'] ]
            )
        ) {
            return false;
        }

        return true;
    }
}


if ( ! function_exists( 'blaines_campaign_catalogue_build_department_cards' ) ) {
    /**
     * Build campaign-wide top-level Electrical department cards.
     */
    function blaines_campaign_catalogue_build_department_cards(
        array $product_map
    ): array {
        $structure   = blaines_campaign_catalogue_get_electrical_structure();
        $departments = isset( $structure['departments'] )
            && is_array( $structure['departments'] )
            ? $structure['departments']
            : [];

        if ( empty( $departments ) ) {
            return [];
        }

        $counts = [];
        $first_product_image_ids = [];

        foreach ( $product_map as $data ) {
            $seen = [];

            foreach ( (array) ( $data['department_terms'] ?? [] ) as $department ) {
                if ( ! $department instanceof WP_Term ) {
                    continue;
                }

                $slug = (string) $department->slug;

                if ( isset( $seen[ $slug ] ) ) {
                    continue;
                }

                $seen[ $slug ] = true;

                if ( ! isset( $counts[ $slug ] ) ) {
                    $counts[ $slug ] = 0;
                }

                $counts[ $slug ]++;

                if ( empty( $first_product_image_ids[ $slug ] ) ) {
                    $product = $data['product'] ?? null;

                    if ( $product instanceof WC_Product ) {
                        $image_id = absint(
                            $product->get_image_id()
                        );

                        if ( $image_id ) {
                            $first_product_image_ids[ $slug ] = $image_id;
                        }
                    }
                }
            }
        }

        $preferred_order = [
            'laundry',
            'refrigeration',
            'dishwashers',
            'cooking',
            'small-appliances',
            'floorcare',
            'tv-entertainment',
        ];

        $by_slug = [];

        foreach ( $departments as $department ) {
            if ( ! $department instanceof WP_Term ) {
                continue;
            }

            $slug  = (string) $department->slug;
            $count = (int) ( $counts[ $slug ] ?? 0 );

            if ( $count <= 0 ) {
                continue;
            }

            $acf_image_id = blaines_campaign_catalogue_find_acf_department_image(
                $department
            );

            $image_id = $acf_image_id
                ?: absint(
                    $first_product_image_ids[ $slug ] ?? 0
                );

            $by_slug[ $slug ] = [
                'slug'         => $slug,
                'label'        => (string) $department->name,
                'count'        => $count,
                'term'         => $department,
                'image_id'     => $image_id,
                'image_source' => $acf_image_id
                    ? 'acf'
                    : ( $image_id ? 'product' : '' ),
            ];
        }

        $cards = [];

        foreach ( $preferred_order as $slug ) {
            if ( isset( $by_slug[ $slug ] ) ) {
                $cards[] = $by_slug[ $slug ];
                unset( $by_slug[ $slug ] );
            }
        }

        if ( ! empty( $by_slug ) ) {
            uasort(
                $by_slug,
                static fn ( array $a, array $b ): int =>
                    strcasecmp(
                        (string) $a['label'],
                        (string) $b['label']
                    )
            );

            foreach ( $by_slug as $card ) {
                $cards[] = $card;
            }
        }

        return $cards;
    }
}

if ( ! function_exists( 'blaines_campaign_catalogue_build_type_facet' ) ) {
    function blaines_campaign_catalogue_build_type_facet(
        array $product_map,
        array $active,
        array $price_bands
    ): array {
        $terms  = [];
        $counts = [];

        foreach ( $product_map as $data ) {
            if ( ! blaines_campaign_catalogue_product_matches( $data, $active, $price_bands, [ 'type' ] ) ) {
                continue;
            }

            foreach ( $data['type_terms'] as $term ) {
                if ( ! $term instanceof WP_Term ) {
                    continue;
                }

                $slug = (string) $term->slug;

                if ( ! isset( $terms[ $slug ] ) ) {
                    $terms[ $slug ]  = $term;
                    $counts[ $slug ] = 0;
                }

                $counts[ $slug ]++;
            }
        }

        $options = [];

        foreach ( $terms as $slug => $term ) {
            $count = $counts[ $slug ] ?? 0;

            if ( $count <= 0 && $slug !== $active['type'] ) {
                continue;
            }

            $options[] = [
                'slug'   => $slug,
                'label'  => blaines_campaign_catalogue_format_type_label( $term ),
                'count'  => $count,
                'active' => $slug === $active['type'],
                'term'   => $term,
            ];
        }

        usort(
            $options,
            static fn ( array $a, array $b ): int => strcasecmp( (string) $a['label'], (string) $b['label'] )
        );

        return $options;
    }
}

if ( ! function_exists( 'blaines_campaign_catalogue_build_brand_facet' ) ) {
    function blaines_campaign_catalogue_build_brand_facet(
        array $product_map,
        array $active,
        array $price_bands
    ): array {
        $terms  = [];
        $counts = [];

        foreach ( $product_map as $data ) {
            if ( ! blaines_campaign_catalogue_product_matches( $data, $active, $price_bands, [ 'brand' ] ) ) {
                continue;
            }

            foreach ( $data['brand_terms'] as $term ) {
                if ( ! $term instanceof WP_Term ) {
                    continue;
                }

                $slug = (string) $term->slug;

                if ( ! isset( $terms[ $slug ] ) ) {
                    $terms[ $slug ]  = $term;
                    $counts[ $slug ] = 0;
                }

                $counts[ $slug ]++;
            }
        }

        $options = [];

        foreach ( $terms as $slug => $term ) {
            $count = $counts[ $slug ] ?? 0;

            if ( $count <= 0 && $slug !== $active['brand'] ) {
                continue;
            }

            $options[] = [
                'slug'   => $slug,
                'label'  => trim( (string) $term->name ),
                'count'  => $count,
                'active' => $slug === $active['brand'],
                'term'   => $term,
            ];
        }

        usort(
            $options,
            static fn ( array $a, array $b ): int => strcasecmp( (string) $a['label'], (string) $b['label'] )
        );

        return $options;
    }
}

if ( ! function_exists( 'blaines_campaign_catalogue_build_price_facet' ) ) {
    function blaines_campaign_catalogue_build_price_facet(
        array $product_map,
        array $active,
        array $price_bands
    ): array {
        $counts = array_fill_keys( array_keys( $price_bands ), 0 );

        foreach ( $product_map as $data ) {
            if ( ! blaines_campaign_catalogue_product_matches( $data, $active, $price_bands, [ 'price' ] ) ) {
                continue;
            }

            foreach ( $price_bands as $slug => $band ) {
                if ( blaines_campaign_catalogue_price_matches_band( (float) $data['price'], $band ) ) {
                    $counts[ $slug ]++;
                    break;
                }
            }
        }

        $options = [];

        foreach ( $price_bands as $slug => $band ) {
            $count = $counts[ $slug ] ?? 0;

            if ( $count <= 0 && $slug !== $active['price'] ) {
                continue;
            }

            $options[] = [
                'slug'   => $slug,
                'label'  => (string) $band['label'],
                'count'  => $count,
                'active' => $slug === $active['price'],
            ];
        }

        return $options;
    }
}

if ( ! function_exists( 'blaines_campaign_catalogue_sort_ids' ) ) {
    function blaines_campaign_catalogue_sort_ids(
        array $product_ids,
        array $product_map,
        string $sort
    ): array {
        $product_ids = array_values( $product_ids );

        if ( 'featured' === $sort ) {
            return $product_ids;
        }

        usort(
            $product_ids,
            static function ( int $a, int $b ) use ( $product_map, $sort ): int {
                $a_data = $product_map[ $a ];
                $b_data = $product_map[ $b ];

                switch ( $sort ) {
                    case 'price-asc':
                        $comparison = (float) $a_data['price'] <=> (float) $b_data['price'];
                        break;

                    case 'price-desc':
                        $comparison = (float) $b_data['price'] <=> (float) $a_data['price'];
                        break;

                    case 'saving-desc':
                        $comparison = (float) $b_data['saving'] <=> (float) $a_data['saving'];
                        break;

                    case 'name-asc':
                        $comparison = strcasecmp( (string) $a_data['title'], (string) $b_data['title'] );
                        break;

                    default:
                        $comparison = (int) $a_data['order'] <=> (int) $b_data['order'];
                }

                if ( 0 !== $comparison ) {
                    return $comparison;
                }

                return (int) $a_data['order'] <=> (int) $b_data['order'];
            }
        );

        return $product_ids;
    }
}

if ( ! function_exists( 'blaines_get_campaign_catalogue_context' ) ) {
    function blaines_get_campaign_catalogue_context(
        array $product_ids,
        array $args = []
    ): array {
        $args = wp_parse_args(
            $args,
            [
                'per_page' => 12,
            ]
        );

        $product_ids = blaines_campaign_catalogue_normalize_product_ids( $product_ids );
        $price_bands = blaines_campaign_catalogue_get_price_bands();
        $active      = blaines_campaign_catalogue_read_request( $price_bands );
        $product_map = blaines_campaign_catalogue_build_product_map( $product_ids );

        if ( '' !== (string) ( $active['department'] ?? '' ) ) {
            $department_exists = false;

            foreach ( $product_map as $data ) {
                if (
                    in_array(
                        $active['department'],
                        (array) ( $data['department_slugs'] ?? [] ),
                        true
                    )
                ) {
                    $department_exists = true;
                    break;
                }
            }

            if ( ! $department_exists ) {
                $active['department'] = '';
            }
        }

        if ( '' !== $active['type'] ) {
            $type_exists = false;

            foreach ( $product_map as $data ) {
                if (
                    in_array(
                        $active['type'],
                        (array) $data['type_slugs'],
                        true
                    )
                    && (
                        '' === (string) ( $active['department'] ?? '' )
                        || in_array(
                            $active['department'],
                            (array) ( $data['department_slugs'] ?? [] ),
                            true
                        )
                    )
                ) {
                    $type_exists = true;
                    break;
                }
            }

            if ( ! $type_exists ) {
                $active['type'] = '';
            }
        }

        if ( '' !== $active['brand'] ) {
            $brand_exists = false;

            foreach ( $product_map as $data ) {
                if ( in_array( $active['brand'], $data['brand_slugs'], true ) ) {
                    $brand_exists = true;
                    break;
                }
            }

            if ( ! $brand_exists ) {
                $active['brand'] = '';
            }
        }

        $type_facet  = blaines_campaign_catalogue_build_type_facet( $product_map, $active, $price_bands );
        $brand_facet = blaines_campaign_catalogue_build_brand_facet( $product_map, $active, $price_bands );
        $price_facet = blaines_campaign_catalogue_build_price_facet( $product_map, $active, $price_bands );

        $department_cards = blaines_campaign_catalogue_build_department_cards(
            $product_map
        );

        $matching_ids = [];

        foreach ( $product_ids as $product_id ) {
            $data = $product_map[ $product_id ] ?? null;

            if (
                ! is_array( $data )
                || ! blaines_campaign_catalogue_product_matches( $data, $active, $price_bands )
            ) {
                continue;
            }

            $matching_ids[] = $product_id;
        }

        $matching_ids = blaines_campaign_catalogue_sort_ids( $matching_ids, $product_map, $active['sort'] );

        $per_page = max( 1, min( 48, absint( $args['per_page'] ) ) );
        $total       = count( $matching_ids );
        $total_pages = max( 1, (int) ceil( $total / $per_page ) );
        $page        = min( max( 1, (int) $active['page'] ), $total_pages );

        $active['page'] = $page;

        $paged_ids = array_slice(
            $matching_ids,
            ( $page - 1 ) * $per_page,
            $per_page
        );

        return [
            'candidate_ids'  => $product_ids,
            'product_map'    => $product_map,
            'active'         => $active,
            'facets'         => [
                'types'  => $type_facet,
                'brands' => $brand_facet,
                'prices' => $price_facet,
            ],
            'department_cards' => $department_cards,
            'matching_ids'   => $matching_ids,
            'product_ids'    => $paged_ids,
            'total'          => $total,
            'page'           => $page,
            'per_page'       => $per_page,
            'total_pages'    => $total_pages,
            'sort_options'   => [
                'featured'    => 'Featured',
                'price-asc'   => 'Price: low to high',
                'price-desc'  => 'Price: high to low',
                'saving-desc' => 'Biggest saving',
                'name-asc'    => 'Name: A–Z',
            ],
        ];
    }
}
