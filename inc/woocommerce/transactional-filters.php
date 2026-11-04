<?php
/**
 * Transactional product archive filter and query context.
 *
 * Builds the complete data context used by the controlled Electrical
 * department archive template. Includes request-local facet-query
 * memoisation, term-cache priming and contextual Price faceting for the
 * growing product catalogue.
 *
 * @package Blaines
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'blaines_get_transactional_filter_registry' ) ) {
	/**
	 * Return the current taxonomy-filter definitions.
	 *
	 * F2D keeps the explicit department applicability introduced in F2C.
	 * Facet counts use the same context for every filter: each taxonomy
	 * facet is calculated from the current department, active Product Type,
	 * Stock Status and every active taxonomy filter except itself. Product
	 * Type is also calculated contextually from every active taxonomy filter.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	function blaines_get_transactional_filter_registry(): array {
		$attribute_taxonomy = static function ( string $attribute ): string {
			return function_exists( 'wc_attribute_taxonomy_name' )
				? wc_attribute_taxonomy_name( $attribute )
				: 'pa_' . sanitize_title( $attribute );
		};

		return [
			'brand' => [
				'label'              => 'Brand',
				'query_var'          => 'brand',
				'taxonomy'           => 'product_brand',
				'sanitize_callback'  => 'sanitize_key',
				'exclude_terms'      => [],
				'departments'        => [ '*' ],
			],
			'fitting' => [
				'label'              => 'Installation type',
				'query_var'          => 'fitting',
				'taxonomy'           => $attribute_taxonomy( 'installation-type' ),
				'sanitize_callback'  => 'sanitize_title',
				'exclude_terms'      => [ 'not-applicable' ],
				'departments'        => [
					'laundry',
					'refrigeration',
					'dishwashers',
					'cooking',
				],
			],
			'fuel' => [
				'label'              => 'Fuel type',
				'query_var'          => 'fuel',
				'taxonomy'           => $attribute_taxonomy( 'fuel-type' ),
				'sanitize_callback'  => 'sanitize_title',
				'exclude_terms'      => [ 'not-applicable' ],
				'departments'        => [ 'cooking' ],
			],
			'width' => [
				'label'              => 'Appliance width',
				'query_var'          => 'width',
				'taxonomy'           => $attribute_taxonomy( 'appliance-width' ),
				'sanitize_callback'  => 'sanitize_title',
				'exclude_terms'      => [ 'not-applicable' ],
				'departments'        => [
					'refrigeration',
					'dishwashers',
					'cooking',
				],
			],
			'washing_capacity' => [
				'label'              => 'Washing Capacity (kg)',
				'query_var'          => 'washing_capacity',
				'taxonomy'           => $attribute_taxonomy( 'washing-capacity' ),
				'sanitize_callback'  => 'sanitize_title',
				'exclude_terms'      => [ 'not-applicable' ],
				'departments'        => [ 'laundry' ],
				'product_types'      => [
					'laundry-washing-machines',
					'laundry-washer-dryers',
				],
			],
			'drying_capacity' => [
				'label'              => 'Drying Capacity (kg)',
				'query_var'          => 'drying_capacity',
				'taxonomy'           => $attribute_taxonomy( 'drying-capacity' ),
				'sanitize_callback'  => 'sanitize_title',
				'exclude_terms'      => [ 'not-applicable' ],
				'departments'        => [ 'laundry' ],
				'product_types'      => [
					'laundry-tumble-dryers',
					'laundry-washer-dryers',
				],
			],
			'colour' => [
				'label'              => 'Colour',
				'query_var'          => 'colour',
				'taxonomy'           => $attribute_taxonomy( 'colour' ),
				'sanitize_callback'  => 'sanitize_title',
				'exclude_terms'      => [ 'not-applicable' ],
                'departments'        => [
                    'laundry',
                    'refrigeration',
                    'dishwashers',
                    'cooking',
                ],
			],
			'drying_technology' => [
				'label'              => 'Drying Technology',
				'query_var'          => 'drying_technology',
				'taxonomy'           => $attribute_taxonomy( 'drying-technology' ),
				'sanitize_callback'  => 'sanitize_title',
				'exclude_terms'      => [ 'not-applicable' ],
				'departments'        => [ 'laundry' ],
				'product_types'      => [ 'laundry-tumble-dryers' ],
			],
			'split' => [
				'label'              => 'Split',
				'query_var'          => 'split',
				'taxonomy'           => $attribute_taxonomy( 'split' ),
				'sanitize_callback'  => 'sanitize_title',
				'exclude_terms'      => [ 'not-applicable' ],
				'departments'        => [ 'refrigeration' ],
			],
			'hinge' => [
				'label'              => 'Hinge Type',
				'query_var'          => 'hinge',
				'taxonomy'           => $attribute_taxonomy( 'hinge-type' ),
				'sanitize_callback'  => 'sanitize_title',
				'exclude_terms'      => [ 'not-applicable' ],
				'departments'        => [ 'refrigeration' ],
				'requires_filters'   => [ 'fitting' => [ 'integrated' ] ],
			],
			'plumbed' => [
				'label'              => 'Plumbed',
				'query_var'          => 'plumbed',
				'taxonomy'           => $attribute_taxonomy( 'plumbed' ),
				'sanitize_callback'  => 'sanitize_title',
				'exclude_terms'      => [ 'not-applicable' ],
				'departments'        => [ 'refrigeration' ],
			],
			'water_dispenser' => [
				'label'              => 'Water Dispenser',
				'query_var'          => 'water_dispenser',
				'taxonomy'           => $attribute_taxonomy( 'water-dispenser' ),
				'sanitize_callback'  => 'sanitize_title',
				'exclude_terms'      => [ 'not-applicable' ],
				'departments'        => [ 'refrigeration' ],
			],
			'microwave_type' => [
				'label'              => 'Microwave Type',
				'query_var'          => 'microwave_type',
				'taxonomy'           => $attribute_taxonomy( 'microwave-type' ),
				'sanitize_callback'  => 'sanitize_title',
				'exclude_terms'      => [ 'not-applicable' ],
				'departments'        => [ 'cooking' ],
				'product_types'      => [ 'cooking-microwaves' ],
			],
			'microwave_capacity' => [
				'label'              => 'Microwave Capacity (L)',
				'query_var'          => 'microwave_capacity',
				'taxonomy'           => $attribute_taxonomy( 'microwave-capacity' ),
				'sanitize_callback'  => 'sanitize_title',
				'exclude_terms'      => [ 'not-applicable' ],
				'departments'        => [ 'cooking' ],
				'product_types'      => [ 'cooking-microwaves' ],
			],
			'screen_size' => [
				'label'              => 'Screen Size (")',
				'query_var'          => 'screen_size',
				'taxonomy'           => $attribute_taxonomy( 'screen-size' ),
				'sanitize_callback'  => 'sanitize_title',
				'exclude_terms'      => [ 'not-applicable' ],
				'departments'        => [ 'tv-entertainment' ],
				'product_types'      => [ 'tv-entertainment-televisions' ],
			],
			'screen_technology' => [
				'label'              => 'Screen Technology',
				'query_var'          => 'screen_technology',
				'taxonomy'           => $attribute_taxonomy( 'screen-technology' ),
				'sanitize_callback'  => 'sanitize_title',
				'exclude_terms'      => [ 'not-applicable' ],
				'departments'        => [ 'tv-entertainment' ],
				'product_types'      => [ 'tv-entertainment-televisions' ],
			],
		];
	}
}


if ( ! function_exists( 'blaines_get_transactional_price_bands' ) ) {
	/**
	 * Return the available transactional price bands.
	 *
	 * Band minimums are inclusive. Band maximums are exclusive, allowing
	 * adjacent bands to meet cleanly without overlapping at £300, £500,
	 * £750 or £1,000.
	 *
	 * @return array<string,array{label:string,min:float|null,max:float|null}>
	 */
	function blaines_get_transactional_price_bands(): array {
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

if ( ! function_exists( 'blaines_get_transactional_price_filter' ) ) {
	/**
	 * Read and validate the active price-band query parameter.
	 *
	 * @param array<string,array{label:string,min:float|null,max:float|null}> $price_bands Price bands.
	 *
	 * @return string
	 */
	function blaines_get_transactional_price_filter( array $price_bands ): string {
		$price_filter = isset( $_GET['price'] )
			? sanitize_key( wp_unslash( $_GET['price'] ) )
			: '';

		return isset( $price_bands[ $price_filter ] )
			? $price_filter
			: '';
	}
}

if ( ! function_exists( 'blaines_add_transactional_price_meta_query' ) ) {
	/**
	 * Add the active price band to a WooCommerce product meta query.
	 *
	 * WooCommerce stores the product's current effective price in `_price`,
	 * so sale prices are respected by the archive filter.
	 *
	 * @param array<int|string,mixed>                                                $meta_query   Meta query to update.
	 * @param string                                                                 $price_filter Active price-band slug.
	 * @param array<string,array{label:string,min:float|null,max:float|null}>         $price_bands  Price bands.
	 *
	 * @return array<int|string,mixed>
	 */
	function blaines_add_transactional_price_meta_query(
		array $meta_query,
		string $price_filter,
		array $price_bands
	): array {
		if ( ! $price_filter || ! isset( $price_bands[ $price_filter ] ) ) {
			return $meta_query;
		}

		$band = $price_bands[ $price_filter ];
		$min  = $band['min'];
		$max  = $band['max'];

		if ( null !== $min ) {
			$meta_query[] = [
				'key'     => '_price',
				'value'   => (float) $min,
				'compare' => (float) $min > 0 ? '>=' : '>',
				'type'    => 'NUMERIC',
			];
		}

		if ( null !== $max ) {
			$meta_query[] = [
				'key'     => '_price',
				'value'   => (float) $max,
				'compare' => '<',
				'type'    => 'NUMERIC',
			];
		}

		return $meta_query;
	}
}

if ( ! function_exists( 'blaines_transactional_filter_applies_to_department' ) ) {
	/**
	 * Determine whether a filter applies to the current department.
	 *
	 * Use an asterisk in the registry for filters that apply everywhere.
	 *
	 * @param array<string,mixed> $definition     Filter definition.
	 * @param string              $department_slug Department category slug.
	 *
	 * @return bool
	 */
	function blaines_transactional_filter_applies_to_department(
		array $definition,
		string $department_slug
	): bool {
		$departments = isset( $definition['departments'] )
			&& is_array( $definition['departments'] )
			? array_map(
				static function ( $department ): string {
					$department = (string) $department;

					return '*' === $department
						? '*'
						: sanitize_title( $department );
				},
				$definition['departments']
			)
			: [ '*' ];

		return in_array( '*', $departments, true )
			|| in_array( sanitize_title( $department_slug ), $departments, true );
	}
}

if ( ! function_exists( 'blaines_get_transactional_filters_for_department' ) ) {
	/**
	 * Return only the taxonomy filters relevant to one department.
	 *
	 * Irrelevant query parameters are therefore ignored by the main product
	 * query, omitted from facet queries and not preserved in filter forms,
	 * sorting or pagination.
	 *
	 * @param array<string,array<string,mixed>> $registry        Full registry.
	 * @param string                            $department_slug Department slug.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	function blaines_get_transactional_filters_for_department(
		array $registry,
		string $department_slug
	): array {
		return array_filter(
			$registry,
			static function ( array $definition ) use ( $department_slug ): bool {
				return blaines_transactional_filter_applies_to_department(
					$definition,
					$department_slug
				);
			}
		);
	}
}

if ( ! function_exists( 'blaines_get_transactional_filters_for_product_type' ) ) {
	/**
	 * Limit type-specific filters to the selected product category or its children.
	 *
	 * Apply this before reading query parameters so filters from a previous
	 * product type cannot constrain another type, its facets or pagination.
	 * Definitions without product_types retain their department-wide scope.
	 *
	 * @param array<string,array<string,mixed>> $registry Department registry.
	 * @param WP_Term|null $product_type Selected type, or the archive category.
	 * @return array<string,array<string,mixed>>
	 */
	function blaines_get_transactional_filters_for_product_type(
		array $registry,
		?WP_Term $product_type
	): array {
		return array_filter(
			$registry,
			static function ( array $definition ) use ( $product_type ): bool {
				if ( empty( $definition['product_types'] ) ) {
					return true;
				}

				if ( ! $product_type instanceof WP_Term ) {
					return false;
				}

				foreach ( (array) $definition['product_types'] as $type_slug ) {
					$type_slug = sanitize_title( (string) $type_slug );

					if ( $product_type->slug === $type_slug ) {
						return true;
					}

					$parent_type = get_term_by( 'slug', $type_slug, 'product_cat' );

					if (
						$parent_type instanceof WP_Term
						&& term_is_ancestor_of(
							$parent_type->term_id,
							$product_type->term_id,
							'product_cat'
						)
					) {
						return true;
					}
				}

				return false;
			}
		);
	}
}

if ( ! function_exists( 'blaines_get_transactional_filters_for_selection' ) ) {
	/**
	 * Keep conditional filters only when their required selections are active.
	 *
	 * Call with validated filter values, then discard values for removed
	 * definitions before building product queries, facets and form context.
	 * This prevents an old hinge selection from restricting freestanding items.
	 * Requirements refer to independent filters in the same registry.
	 *
	 * @param array<string,array<string,mixed>> $registry Scoped registry.
	 * @param array<string,string> $active_filters Validated filter selections.
	 * @return array<string,array<string,mixed>>
	 */
	function blaines_get_transactional_filters_for_selection(
		array $registry,
		array $active_filters
	): array {
		return array_filter(
			$registry,
			static function ( array $definition ) use ( $registry, $active_filters ): bool {
				foreach ( (array) ( $definition['requires_filters'] ?? [] ) as $required_key => $allowed_values ) {
					if (
						! isset( $registry[ $required_key ] )
						|| ! in_array( $active_filters[ $required_key ] ?? '', (array) $allowed_values, true )
					) {
						return false;
					}
				}

				return true;
			}
		);
	}
}

if ( ! function_exists( 'blaines_get_transactional_taxonomy_filters' ) ) {
	/**
	 * Read and sanitise the taxonomy-filter query parameters.
	 *
	 * @param array<string,array<string,mixed>> $registry Filter registry.
	 *
	 * @return array<string,string>
	 */
	function blaines_get_transactional_taxonomy_filters( array $registry ): array {
		$filters = [];

		foreach ( $registry as $filter_key => $definition ) {
			$query_var = isset( $definition['query_var'] )
				? (string) $definition['query_var']
				: (string) $filter_key;

			$raw_value = isset( $_GET[ $query_var ] )
				? wp_unslash( $_GET[ $query_var ] )
				: '';

			$sanitize_callback = isset( $definition['sanitize_callback'] )
				&& is_callable( $definition['sanitize_callback'] )
				? $definition['sanitize_callback']
				: 'sanitize_title';

			$filters[ $filter_key ] = '' !== $raw_value
				? (string) call_user_func( $sanitize_callback, $raw_value )
				: '';
		}

		return $filters;
	}
}

if ( ! function_exists( 'blaines_validate_transactional_taxonomy_filters' ) ) {
	/**
	 * Remove taxonomy values that cannot be valid in the active registry.
	 *
	 * This prevents stale, manually edited or excluded term slugs from
	 * forcing the main archive query to return no products.
	 *
	 * @param array<string,string>              $filters  Active values.
	 * @param array<string,array<string,mixed>> $registry Department registry.
	 *
	 * @return array<string,string>
	 */
	function blaines_validate_transactional_taxonomy_filters(
		array $filters,
		array $registry
	): array {
		foreach ( $filters as $filter_key => $value ) {
			if ( ! $value || ! isset( $registry[ $filter_key ] ) ) {
				$filters[ $filter_key ] = '';
				continue;
			}

			$definition = $registry[ $filter_key ];
			$taxonomy   = isset( $definition['taxonomy'] )
				? (string) $definition['taxonomy']
				: '';

			if ( ! $taxonomy || ! taxonomy_exists( $taxonomy ) ) {
				$filters[ $filter_key ] = '';
				continue;
			}

			$term = get_term_by( 'slug', $value, $taxonomy );

			if ( ! $term instanceof WP_Term ) {
				$filters[ $filter_key ] = '';
				continue;
			}

			$excluded_slugs = isset( $definition['exclude_terms'] )
				&& is_array( $definition['exclude_terms'] )
				? array_map( 'sanitize_title', $definition['exclude_terms'] )
				: [];

			if ( in_array( $term->slug, $excluded_slugs, true ) ) {
				$filters[ $filter_key ] = '';
			}
		}

		return $filters;
	}
}

if ( ! function_exists( 'blaines_get_transactional_matching_product_ids' ) ) {
	/**
	 * Return product IDs for a facet context, memoised for this request.
	 *
	 * Several facets can produce identical supporting queries, particularly
	 * before filters are selected. Request-local memoisation prevents those
	 * identical database queries from running more than once without creating
	 * persistent-cache invalidation concerns after product imports.
	 *
	 * @param array<int|string,mixed> $tax_query  Taxonomy query clauses.
	 * @param array<int|string,mixed> $meta_query Meta query clauses.
	 *
	 * @return array<int,int>
	 */
	function blaines_get_transactional_matching_product_ids(
		array $tax_query,
		array $meta_query = []
	): array {
		static $request_cache = [];
		static $primed_ids     = [];

		$cache_key = md5(
			(string) wp_json_encode(
				[
					'tax_query'  => $tax_query,
					'meta_query' => $meta_query,
				]
			)
		);

		if ( array_key_exists( $cache_key, $request_cache ) ) {
			return $request_cache[ $cache_key ];
		}

		$product_ids = get_posts(
			[
				'post_type'              => 'product',
				'post_status'            => 'publish',
				'posts_per_page'         => -1,
				'fields'                 => 'ids',
				'orderby'                => 'none',
				'no_found_rows'          => true,
				'ignore_sticky_posts'    => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'tax_query'              => $tax_query,
				'meta_query'             => $meta_query,
			]
		);

		$product_ids = array_values(
			array_unique(
				array_map( 'absint', $product_ids )
			)
		);

		/*
		 * The facet builders call get_the_terms() repeatedly. Prime each product's
		 * term relationships once per request to avoid an N+1 query pattern.
		 */
		$ids_to_prime = array_values(
			array_diff(
				$product_ids,
				array_keys( $primed_ids )
			)
		);

		if ( ! empty( $ids_to_prime ) ) {
			update_object_term_cache( $ids_to_prime, 'product' );

			foreach ( $ids_to_prime as $product_id ) {
				$primed_ids[ $product_id ] = true;
			}
		}

		$request_cache[ $cache_key ] = $product_ids;

		return $product_ids;
	}
}

if ( ! function_exists( 'blaines_build_transactional_taxonomy_facet' ) ) {
	/**
	 * Build the available terms and contextual counts for one taxonomy facet.
	 *
	 * Every facet applies the current department, active Product Type,
	 * Stock Status, the active Price band and every active taxonomy filter
	 * except the facet currently
	 * being calculated. This keeps alternative choices available while the
	 * customer switches a value within the same filter group.
	 *
	 * @param string                            $filter_key       Registry key.
	 * @param array<string,mixed>               $definition       Filter definition.
	 * @param array<string,array<string,mixed>> $registry         Department registry.
	 * @param WP_Term                           $product_category Current department.
	 * @param WP_Term|null                      $active_type_term Active product type.
	 * @param array<string,string>                                              $active_filters Active taxonomy filters.
	 * @param string                                                            $stock_filter   Active stock filter.
	 * @param string                                                            $price_filter   Active price band.
	 * @param array<string,array{label:string,min:float|null,max:float|null}>     $price_bands    Price bands.
	 *
	 * @return array{terms:array<int,WP_Term>,counts:array<int,int>}
	 */
	function blaines_build_transactional_taxonomy_facet(
		string $filter_key,
		array $definition,
		array $registry,
		WP_Term $product_category,
		?WP_Term $active_type_term,
		array $active_filters,
		string $stock_filter,
		string $price_filter,
		array $price_bands
	): array {
		$taxonomy = isset( $definition['taxonomy'] )
			? (string) $definition['taxonomy']
			: '';

		if ( ! $taxonomy || ! taxonomy_exists( $taxonomy ) ) {
			return [
				'terms'  => [],
				'counts' => [],
			];
		}

		$tax_query = [
			'relation' => 'AND',
			[
				'taxonomy'         => 'product_cat',
				'field'            => 'term_id',
				'terms'            => [ $product_category->term_id ],
				'include_children' => true,
			],
		];

		if ( $active_type_term instanceof WP_Term ) {
			$tax_query[] = [
				'taxonomy'         => 'product_cat',
				'field'            => 'term_id',
				'terms'            => [ $active_type_term->term_id ],
				'include_children' => true,
			];
		}

		foreach ( $registry as $other_filter_key => $other_definition ) {
			$other_filter_key = (string) $other_filter_key;

			if (
				$other_filter_key === $filter_key
				|| empty( $active_filters[ $other_filter_key ] )
			) {
				continue;
			}

			$other_taxonomy = isset( $other_definition['taxonomy'] )
				? (string) $other_definition['taxonomy']
				: '';

			if ( ! $other_taxonomy || ! taxonomy_exists( $other_taxonomy ) ) {
				continue;
			}

			$tax_query[] = [
				'taxonomy' => $other_taxonomy,
				'field'    => 'slug',
				'terms'    => [ $active_filters[ $other_filter_key ] ],
			];
		}

		$meta_query = [];

		if ( 'in-stock' === $stock_filter ) {
			$meta_query[] = [
				'key'   => '_stock_status',
				'value' => 'instock',
			];
		}

		$meta_query = blaines_add_transactional_price_meta_query(
			$meta_query,
			$price_filter,
			$price_bands
		);

		$product_ids = blaines_get_transactional_matching_product_ids(
			$tax_query,
			$meta_query
		);

		if ( empty( $product_ids ) ) {
			return [
				'terms'  => [],
				'counts' => [],
			];
		}

		$terms = get_terms(
			[
				'taxonomy'   => $taxonomy,
				'hide_empty' => true,
				'object_ids' => $product_ids,
				'orderby'    => 'name',
				'order'      => 'ASC',
			]
		);

		if ( is_wp_error( $terms ) ) {
			$terms = [];
		}

		$excluded_slugs = isset( $definition['exclude_terms'] )
			&& is_array( $definition['exclude_terms'] )
			? array_map( 'sanitize_title', $definition['exclude_terms'] )
			: [];

		if ( ! empty( $excluded_slugs ) ) {
			$terms = array_values(
				array_filter(
					$terms,
					static function ( WP_Term $term ) use ( $excluded_slugs ): bool {
						return ! in_array( $term->slug, $excluded_slugs, true );
					}
				)
			);
		} else {
			$terms = array_values( $terms );
		}

		$term_ids = array_map(
			'absint',
			wp_list_pluck( $terms, 'term_id' )
		);

		$term_id_lookup = array_fill_keys( $term_ids, true );
		$counts         = [];

		foreach ( $product_ids as $product_id ) {
			$product_terms = get_the_terms( $product_id, $taxonomy );

			if ( empty( $product_terms ) || is_wp_error( $product_terms ) ) {
				continue;
			}

			foreach ( $product_terms as $product_term ) {
				if ( in_array( $product_term->slug, $excluded_slugs, true ) ) {
					continue;
				}

				$term_id = (int) $product_term->term_id;

				if ( ! isset( $term_id_lookup[ $term_id ] ) ) {
					continue;
				}

				if ( ! isset( $counts[ $term_id ] ) ) {
					$counts[ $term_id ] = 0;
				}

				$counts[ $term_id ]++;
			}
		}

		return [
			'terms'  => $terms,
			'counts' => $counts,
		];
	}
}

if ( ! function_exists( 'blaines_build_transactional_product_type_facet' ) ) {
	/**
	 * Build contextual Product Type terms and counts.
	 *
	 * Product Type follows the same faceting rule as the taxonomy filters:
	 * apply the current department, Stock Status, the active Price band and
	 * every active taxonomy filter, but deliberately omit the active Product
	 * Type itself.
	 *
	 * The returned WP_Term objects are cloned before their count property is
	 * replaced, avoiding any mutation of globally cached term objects.
	 *
	 * @param WP_Term                           $product_category Current department.
	 * @param WP_Term|null                      $active_type_term Active Product Type.
	 * @param array<string,array<string,mixed>> $registry         Department registry.
	 * @param array<string,string>                                              $active_filters Active taxonomy filters.
	 * @param string                                                            $stock_filter   Active stock filter.
	 * @param string                                                            $price_filter   Active price band.
	 * @param array<string,array{label:string,min:float|null,max:float|null}>     $price_bands    Price bands.
	 *
	 * @return array<int,WP_Term>
	 */
	function blaines_build_transactional_product_type_facet(
		WP_Term $product_category,
		?WP_Term $active_type_term,
		array $registry,
		array $active_filters,
		string $stock_filter,
		string $price_filter,
		array $price_bands
	): array {
		$department_child_terms = get_terms(
			[
				'taxonomy'   => 'product_cat',
				'parent'     => $product_category->term_id,
				'hide_empty' => false,
				'orderby'    => 'name',
				'order'      => 'ASC',
			]
		);

		if ( is_wp_error( $department_child_terms ) ) {
			$department_child_terms = [];
		}

		$child_terms_by_id = [];

		foreach ( $department_child_terms as $child_term ) {
			if ( $child_term instanceof WP_Term ) {
				$child_terms_by_id[ (int) $child_term->term_id ] = $child_term;
			}
		}

		$tax_query = [
			'relation' => 'AND',
			[
				'taxonomy'         => 'product_cat',
				'field'            => 'term_id',
				'terms'            => [ $product_category->term_id ],
				'include_children' => true,
			],
		];

		foreach ( $registry as $filter_key => $definition ) {
			$filter_value = $active_filters[ $filter_key ] ?? '';
			$taxonomy     = isset( $definition['taxonomy'] )
				? (string) $definition['taxonomy']
				: '';

			if (
				! $filter_value
				|| ! $taxonomy
				|| ! taxonomy_exists( $taxonomy )
			) {
				continue;
			}

			$tax_query[] = [
				'taxonomy' => $taxonomy,
				'field'    => 'slug',
				'terms'    => [ $filter_value ],
			];
		}

		$meta_query = [];

		if ( 'in-stock' === $stock_filter ) {
			$meta_query[] = [
				'key'   => '_stock_status',
				'value' => 'instock',
			];
		}

		$meta_query = blaines_add_transactional_price_meta_query(
			$meta_query,
			$price_filter,
			$price_bands
		);

		$product_ids = blaines_get_transactional_matching_product_ids(
			$tax_query,
			$meta_query
		);

		$counts                   = [];
		$product_cat_to_child_id = [];

		foreach ( array_keys( $child_terms_by_id ) as $child_term_id ) {
			$counts[ $child_term_id ] = 0;
		}

		foreach ( $product_ids as $product_id ) {
			$product_category_terms = get_the_terms(
				$product_id,
				'product_cat'
			);

			if (
				empty( $product_category_terms )
				|| is_wp_error( $product_category_terms )
			) {
				continue;
			}

			$matched_child_ids = [];

			foreach ( $product_category_terms as $product_category_term ) {
				if ( ! $product_category_term instanceof WP_Term ) {
					continue;
				}

				$starting_term_id = (int) $product_category_term->term_id;

				if ( array_key_exists( $starting_term_id, $product_cat_to_child_id ) ) {
					$child_id = $product_cat_to_child_id[ $starting_term_id ];
				} else {
					$current_term    = $product_category_term;
					$child_id        = 0;
					$visited_term_ids = [];

					while ( $current_term instanceof WP_Term ) {
						$current_term_id   = (int) $current_term->term_id;
						$visited_term_ids[] = $current_term_id;

						if (
							(int) $current_term->parent
							=== (int) $product_category->term_id
						) {
							$child_id = $current_term_id;
							break;
						}

						if (
							(int) $current_term->parent <= 0
							|| $current_term_id
								=== (int) $product_category->term_id
						) {
							break;
						}

						$current_term = get_term(
							$current_term->parent,
							'product_cat'
						);

						if ( is_wp_error( $current_term ) ) {
							break;
						}
					}

					foreach ( $visited_term_ids as $visited_term_id ) {
						$product_cat_to_child_id[ $visited_term_id ] = $child_id;
					}
				}

				if (
					$child_id
					&& isset( $child_terms_by_id[ $child_id ] )
				) {
					$matched_child_ids[ $child_id ] = true;
				}
			}

			foreach ( array_keys( $matched_child_ids ) as $child_id ) {
				$counts[ $child_id ]++;
			}
		}

		$product_type_terms = [];
		$included_term_ids  = [];

		foreach ( $department_child_terms as $child_term ) {
			if ( ! $child_term instanceof WP_Term ) {
				continue;
			}

			$term_id = (int) $child_term->term_id;
			$count   = $counts[ $term_id ] ?? 0;

			if ( $count <= 0 ) {
				continue;
			}

			$display_term        = clone $child_term;
			$display_term->count = $count;

			$product_type_terms[]          = $display_term;
			$included_term_ids[ $term_id ] = true;
		}

		/*
		 * Keep an active direct-child Product Type visible even when the other
		 * filters currently reduce its contextual count to zero. This preserves
		 * the customer's ability to clear the selected Product Type.
		 */
		if (
			$active_type_term instanceof WP_Term
			&& (int) $active_type_term->parent
				=== (int) $product_category->term_id
			&& ! isset(
				$included_term_ids[ (int) $active_type_term->term_id ]
			)
		) {
			$display_active_term        = clone $active_type_term;
			$display_active_term->count = 0;
			$product_type_terms[]       = $display_active_term;
		}

		usort(
			$product_type_terms,
			static function ( WP_Term $first, WP_Term $second ): int {
				return strnatcasecmp( $first->name, $second->name );
			}
		);

		return $product_type_terms;
	}
}


if ( ! function_exists( 'blaines_get_transactional_product_price_map' ) ) {
	/**
	 * Return current `_price` values for the supplied product IDs.
	 *
	 * Only the required meta key is fetched, in chunks, rather than priming
	 * every post-meta row for every candidate product. Values are memoised for
	 * the remainder of the request because the same products can be used by
	 * several facet calculations.
	 *
	 * @param array<int,int> $product_ids Product IDs.
	 *
	 * @return array<int,float>
	 */
	function blaines_get_transactional_product_price_map(
		array $product_ids
	): array {
		global $wpdb;

		static $price_cache  = [];
		static $missing_ids  = [];

		$product_ids = array_values(
			array_unique(
				array_filter(
					array_map( 'absint', $product_ids )
				)
			)
		);

		$ids_to_fetch = [];

		foreach ( $product_ids as $product_id ) {
			if (
				! array_key_exists( $product_id, $price_cache )
				&& ! isset( $missing_ids[ $product_id ] )
			) {
				$ids_to_fetch[] = $product_id;
			}
		}

		foreach ( array_chunk( $ids_to_fetch, 500 ) as $product_id_chunk ) {
			$placeholders = implode(
				',',
				array_fill( 0, count( $product_id_chunk ), '%d' )
			);

			$query_args = array_merge(
				[ '_price' ],
				$product_id_chunk
			);

			$query = $wpdb->prepare(
				"SELECT post_id, meta_value
				FROM {$wpdb->postmeta}
				WHERE meta_key = %s
				AND post_id IN ({$placeholders})",
				$query_args
			);

			$rows = $wpdb->get_results( $query, ARRAY_A );
			$fetched_ids = [];

			foreach ( $rows as $row ) {
				$product_id = isset( $row['post_id'] )
					? absint( $row['post_id'] )
					: 0;

				$raw_price = $row['meta_value'] ?? '';

				if ( ! $product_id || ! is_numeric( $raw_price ) ) {
					continue;
				}

				$price_cache[ $product_id ] = (float) $raw_price;
				$fetched_ids[ $product_id ] = true;
			}

			foreach ( $product_id_chunk as $product_id ) {
				if ( ! isset( $fetched_ids[ $product_id ] ) ) {
					$missing_ids[ $product_id ] = true;
				}
			}
		}

		$prices = [];

		foreach ( $product_ids as $product_id ) {
			if ( array_key_exists( $product_id, $price_cache ) ) {
				$prices[ $product_id ] = $price_cache[ $product_id ];
			}
		}

		return $prices;
	}
}

if ( ! function_exists( 'blaines_build_transactional_price_facet' ) ) {
	/**
	 * Build contextual Price-band counts.
	 *
	 * The Price facet applies the department, active Product Type, Stock
	 * Status and every active taxonomy filter, but deliberately omits the
	 * active Price band itself. This keeps compatible alternative bands
	 * available while the customer switches Price.
	 *
	 * @param WP_Term                           $product_category Current department.
	 * @param WP_Term|null                      $active_type_term Active Product Type.
	 * @param array<string,array<string,mixed>> $registry         Department registry.
	 * @param array<string,string>              $active_filters   Active taxonomy filters.
	 * @param string                            $stock_filter     Active stock filter.
	 * @param string                            $price_filter     Active price band.
	 * @param array<string,array{label:string,min:float|null,max:float|null}> $price_bands Price bands.
	 *
	 * @return array{
	 *     bands:array<string,array{label:string,min:float|null,max:float|null,count:int}>,
	 *     counts:array<string,int>
	 * }
	 */
	function blaines_build_transactional_price_facet(
		WP_Term $product_category,
		?WP_Term $active_type_term,
		array $registry,
		array $active_filters,
		string $stock_filter,
		string $price_filter,
		array $price_bands
	): array {
		$tax_query = [
			'relation' => 'AND',
			[
				'taxonomy'         => 'product_cat',
				'field'            => 'term_id',
				'terms'            => [ $product_category->term_id ],
				'include_children' => true,
			],
		];

		if ( $active_type_term instanceof WP_Term ) {
			$tax_query[] = [
				'taxonomy'         => 'product_cat',
				'field'            => 'term_id',
				'terms'            => [ $active_type_term->term_id ],
				'include_children' => true,
			];
		}

		foreach ( $registry as $filter_key => $definition ) {
			$filter_value = $active_filters[ $filter_key ] ?? '';
			$taxonomy     = isset( $definition['taxonomy'] )
				? (string) $definition['taxonomy']
				: '';

			if (
				! $filter_value
				|| ! $taxonomy
				|| ! taxonomy_exists( $taxonomy )
			) {
				continue;
			}

			$tax_query[] = [
				'taxonomy' => $taxonomy,
				'field'    => 'slug',
				'terms'    => [ $filter_value ],
			];
		}

		$meta_query = [];

		if ( 'in-stock' === $stock_filter ) {
			$meta_query[] = [
				'key'   => '_stock_status',
				'value' => 'instock',
			];
		}

		$product_ids = blaines_get_transactional_matching_product_ids(
			$tax_query,
			$meta_query
		);

		$product_prices = blaines_get_transactional_product_price_map(
			$product_ids
		);

		$counts = array_fill_keys(
			array_keys( $price_bands ),
			0
		);

		foreach ( $product_prices as $price ) {
			if ( $price <= 0 ) {
				continue;
			}

			foreach ( $price_bands as $band_key => $band ) {
				$min = $band['min'];
				$max = $band['max'];

				$matches_min = null === $min
					|| ( (float) $min > 0
						? $price >= (float) $min
						: $price > (float) $min );

				$matches_max = null === $max
					|| $price < (float) $max;

				if ( $matches_min && $matches_max ) {
					$counts[ $band_key ]++;
					break;
				}
			}
		}

		$visible_bands = [];

		foreach ( $price_bands as $band_key => $band ) {
			$count = $counts[ $band_key ] ?? 0;

			if ( $count <= 0 && $band_key !== $price_filter ) {
				continue;
			}

			$visible_bands[ $band_key ] = [
				'label' => (string) $band['label'],
				'min'   => $band['min'],
				'max'   => $band['max'],
				'count' => $count,
			];
		}

		return [
			'bands'  => $visible_bands,
			'counts' => $counts,
		];
	}
}

if ( ! function_exists( 'blaines_get_transactional_archive_context' ) ) {
	/**
	 * Build the transactional archive context.
	 *
	 * @param string $category_slug Department product-category slug.
	 * @param string $title         Default archive title.
	 * @param string $intro         Default archive introduction.
	 *
	 * @return array|WP_Error
	 */
	function blaines_get_transactional_archive_context(
		$category_slug,
		$title,
		$intro = ''
	) {
		$category_slug = sanitize_title( $category_slug );
		$title         = sanitize_text_field( $title );
		$intro         = wp_kses_post( $intro );

		if ( ! $category_slug ) {
			return new WP_Error(
				'blaines_missing_transactional_category',
				'Product category not found.'
			);
		}

		$product_category = get_term_by( 'slug', $category_slug, 'product_cat' );

		if ( ! $product_category instanceof WP_Term ) {
			return new WP_Error(
				'blaines_invalid_transactional_category',
				'Product category not found.'
			);
		}

		$complete_filter_registry = blaines_get_transactional_filter_registry();

		$filter_registry = blaines_get_transactional_filters_for_department(
			$complete_filter_registry,
			$product_category->slug
		);

		$current_page = isset( $_GET['pg'] )
			? max( 1, absint( $_GET['pg'] ) )
			: 1;

		$sort = isset( $_GET['sort'] )
			? sanitize_key( wp_unslash( $_GET['sort'] ) )
			: 'default';

		$allowed_sorts = [
			'default',
			'price-asc',
			'price-desc',
			'title-asc',
		];

		if ( ! in_array( $sort, $allowed_sorts, true ) ) {
			$sort = 'default';
		}

		$stock_filter = isset( $_GET['stock'] )
			? sanitize_key( wp_unslash( $_GET['stock'] ) )
			: '';

		if ( 'in-stock' !== $stock_filter ) {
			$stock_filter = '';
		}

		$price_bands  = blaines_get_transactional_price_bands();
		$price_filter = blaines_get_transactional_price_filter( $price_bands );

		$type_filter = isset( $_GET['type'] )
			? sanitize_title( wp_unslash( $_GET['type'] ) )
			: '';

		$format_type_label = static function ( WP_Term $term ) use ( $product_category ): string {
			$label  = $term->name;
			$prefix = $product_category->name . ' ';

			if ( 0 === stripos( $label, $prefix ) ) {
				$label = trim( substr( $label, strlen( $prefix ) ) );
			}

			return $label;
		};

		$active_type_term     = null;
		$type_filter_is_valid = false;

		if ( $type_filter ) {
			$maybe_type_term = get_term_by( 'slug', $type_filter, 'product_cat' );

			if (
				$maybe_type_term instanceof WP_Term
				&& (
					(int) $maybe_type_term->parent === (int) $product_category->term_id
					|| term_is_ancestor_of(
						$product_category->term_id,
						$maybe_type_term->term_id,
						'product_cat'
					)
				)
			) {
				$active_type_term     = $maybe_type_term;
				$type_filter_is_valid = true;
			}
		}

		$filter_registry = blaines_get_transactional_filters_for_product_type(
			$filter_registry,
			$active_type_term instanceof WP_Term ? $active_type_term : $product_category
		);

		$taxonomy_filters = blaines_get_transactional_taxonomy_filters(
			$filter_registry
		);

		$taxonomy_filters = blaines_validate_transactional_taxonomy_filters(
			$taxonomy_filters,
			$filter_registry
		);

		$filter_registry = blaines_get_transactional_filters_for_selection(
			$filter_registry,
			$taxonomy_filters
		);

		$taxonomy_filters = array_intersect_key( $taxonomy_filters, $filter_registry );

		$brand_filter   = $taxonomy_filters['brand'] ?? '';
		$fitting_filter = $taxonomy_filters['fitting'] ?? '';
		$fuel_filter    = $taxonomy_filters['fuel'] ?? '';
		$width_filter   = $taxonomy_filters['width'] ?? '';

		$display_title = $title;
		$display_intro = $intro;

		if ( $active_type_term instanceof WP_Term ) {
			$display_title = $format_type_label( $active_type_term );

			if ( ! empty( $active_type_term->description ) ) {
				$display_intro = $active_type_term->description;
			} else {
				$display_intro = sprintf(
					'Browse %s available to buy online from Blaines, with local delivery or collection.',
					strtolower( $display_title )
				);
			}
		}

		$product_type_terms = blaines_build_transactional_product_type_facet(
			$product_category,
			$active_type_term,
			$filter_registry,
			$taxonomy_filters,
			$stock_filter,
			$price_filter,
			$price_bands
		);

		$products_per_page = 12;

		$query_args = [
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => $products_per_page,
			'paged'               => $current_page,
			'ignore_sticky_posts' => true,
			'tax_query'           => [
				[
					'taxonomy'         => 'product_cat',
					'field'            => 'term_id',
					'terms'            => [ $product_category->term_id ],
					'include_children' => true,
				],
			],
			'meta_query'     => [],
		];

		if ( 'in-stock' === $stock_filter ) {
			$query_args['meta_query'][] = [
				'key'   => '_stock_status',
				'value' => 'instock',
			];
		}

		$query_args['meta_query'] = blaines_add_transactional_price_meta_query(
			$query_args['meta_query'],
			$price_filter,
			$price_bands
		);

		if ( $type_filter ) {
			if ( $type_filter_is_valid && $active_type_term instanceof WP_Term ) {
				$query_args['tax_query'][] = [
					'taxonomy'         => 'product_cat',
					'field'            => 'term_id',
					'terms'            => [ $active_type_term->term_id ],
					'include_children' => true,
				];
			} else {
				$query_args['post__in'] = [ 0 ];
			}
		}

		foreach ( $filter_registry as $filter_key => $definition ) {
			$filter_value = $taxonomy_filters[ $filter_key ] ?? '';
			$taxonomy     = isset( $definition['taxonomy'] )
				? (string) $definition['taxonomy']
				: '';

			if (
				! $filter_value
				|| ! $taxonomy
				|| ! taxonomy_exists( $taxonomy )
			) {
				continue;
			}

			$query_args['tax_query'][] = [
				'taxonomy' => $taxonomy,
				'field'    => 'slug',
				'terms'    => [ $filter_value ],
			];
		}

		switch ( $sort ) {
			case 'price-asc':
				$query_args['meta_key'] = '_price';
				$query_args['orderby']  = 'meta_value_num';
				$query_args['order']    = 'ASC';
				break;

			case 'price-desc':
				$query_args['meta_key'] = '_price';
				$query_args['orderby']  = 'meta_value_num';
				$query_args['order']    = 'DESC';
				break;

			case 'title-asc':
				$query_args['orderby'] = 'title';
				$query_args['order']   = 'ASC';
				break;

			default:
				$query_args['orderby'] = [
					'menu_order' => 'ASC',
					'title'      => 'ASC',
				];
				break;
		}

		$products_query = new WP_Query( $query_args );

		$total_products = (int) $products_query->found_posts;
		$total_pages    = (int) $products_query->max_num_pages;

		$start_result = $total_products > 0
			? ( ( $current_page - 1 ) * $products_per_page ) + 1
			: 0;

		$end_result = min(
			$current_page * $products_per_page,
			$total_products
		);

		$taxonomy_facets = [];

		foreach ( $filter_registry as $filter_key => $definition ) {
			$taxonomy_facets[ $filter_key ] =
				blaines_build_transactional_taxonomy_facet(
					$filter_key,
					$definition,
					$filter_registry,
					$product_category,
					$active_type_term,
					$taxonomy_filters,
					$stock_filter,
					$price_filter,
					$price_bands
				);
		}

		$price_facet = blaines_build_transactional_price_facet(
			$product_category,
			$active_type_term,
			$filter_registry,
			$taxonomy_filters,
			$stock_filter,
			$price_filter,
			$price_bands
		);

		return [
			'display_title'      => $display_title,
			'display_intro'      => $display_intro,
			'products_query'     => $products_query,
			'total_products'     => $total_products,
			'total_pages'        => $total_pages,
			'start_result'       => $start_result,
			'end_result'         => $end_result,
			'current_page'       => $current_page,
			'sort'               => $sort,
			'type_filter'        => $type_filter,
			'brand_filter'       => $brand_filter,
			'fitting_filter'     => $fitting_filter,
			'fuel_filter'        => $fuel_filter,
			'width_filter'       => $width_filter,
			'stock_filter'       => $stock_filter,
			'price_filter'       => $price_filter,
			'price_bands'        => $price_bands,
			'price_facet'        => $price_facet,
			'product_type_terms' => $product_type_terms,
			'format_type_label'  => $format_type_label,
			'filter_registry'    => $filter_registry,
			'taxonomy_filters'   => $taxonomy_filters,
			'taxonomy_facets'    => $taxonomy_facets,
		];
	}
}
