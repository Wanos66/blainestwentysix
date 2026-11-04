<?php
/**
 * Transactional product archive filter sidebar.
 *
 * Expected args:
 * - product_type_terms
 * - filter_registry
 * - taxonomy_filters
 * - taxonomy_facets
 * - type_filter
 * - stock_filter
 * - price_filter
 * - price_facet
 * - sort
 * - format_type_label
 * - has_active_filters
 * - active_filter_count
 * - clear_all_url
 * - panel_id
 * - heading_id
 *
 * @package Blaines
 */

defined( 'ABSPATH' ) || exit;

$product_type_terms = isset( $args['product_type_terms'] )
	&& is_array( $args['product_type_terms'] )
	? $args['product_type_terms']
	: [];

$filter_registry = isset( $args['filter_registry'] )
	&& is_array( $args['filter_registry'] )
	? $args['filter_registry']
	: [];

$taxonomy_filters = isset( $args['taxonomy_filters'] )
	&& is_array( $args['taxonomy_filters'] )
	? $args['taxonomy_filters']
	: [];

$taxonomy_facets = isset( $args['taxonomy_facets'] )
	&& is_array( $args['taxonomy_facets'] )
	? $args['taxonomy_facets']
	: [];

$type_filter  = (string) ( $args['type_filter'] ?? '' );
$stock_filter = (string) ( $args['stock_filter'] ?? '' );
$price_filter = (string) ( $args['price_filter'] ?? '' );

$price_facet = isset( $args['price_facet'] )
	&& is_array( $args['price_facet'] )
	? $args['price_facet']
	: [];

$sort = (string) ( $args['sort'] ?? 'default' );

$has_active_filters = ! empty( $args['has_active_filters'] );
$active_filter_count = isset( $args['active_filter_count'] )
	? max( 0, absint( $args['active_filter_count'] ) )
	: 0;
$clear_all_url      = isset( $args['clear_all_url'] )
	? esc_url_raw( $args['clear_all_url'] )
	: '';

$panel_id = isset( $args['panel_id'] )
	? sanitize_html_class( $args['panel_id'] )
	: 'transactional-filter-panel';

$heading_id = isset( $args['heading_id'] )
	? sanitize_html_class( $args['heading_id'] )
	: 'transactional-filter-heading';

$format_type_label = $args['format_type_label'] ?? null;

if ( ! is_callable( $format_type_label ) ) {
	$format_type_label = static function ( WP_Term $term ): string {
		return $term->name;
	};
}

/**
 * Build the complete active query map used by every filter form.
 */
$active_query = [];

if ( $type_filter ) {
	$active_query['type'] = $type_filter;
}

foreach ( $filter_registry as $filter_key => $definition ) {
	$query_var = isset( $definition['query_var'] )
		? sanitize_key( $definition['query_var'] )
		: sanitize_key( $filter_key );

	$value = isset( $taxonomy_filters[ $filter_key ] )
		? (string) $taxonomy_filters[ $filter_key ]
		: '';

	if ( $query_var && $value ) {
		$active_query[ $query_var ] = $value;
	}
}

if ( 'in-stock' === $stock_filter ) {
	$active_query['stock'] = 'in-stock';
}

if ( $price_filter ) {
	$active_query['price'] = $price_filter;
}

if ( 'default' !== $sort ) {
	$active_query['sort'] = $sort;
}

/**
 * Return the active query values except the filter currently being changed.
 * Pagination is deliberately never preserved after a filter change.
 */
$get_preserved_query = static function ( string $excluded_query_var ) use ( $active_query ): array {
	$preserved = $active_query;

	unset(
		$preserved[ $excluded_query_var ],
		$preserved['pg']
	);

	return $preserved;
};
?>

<aside
	id="<?php echo esc_attr( $panel_id ); ?>"
        class="retail-filter-panel fixed inset-y-0 left-0 z-[70] hidden w-[min(90vw,22rem)] overflow-y-auto bg-white shadow-2xl focus:outline-none lg:static lg:z-auto lg:col-span-3 lg:block lg:w-auto lg:self-start lg:overflow-visible lg:bg-transparent lg:shadow-none"
	data-transactional-filter-panel
	aria-labelledby="<?php echo esc_attr( $heading_id ); ?>"
	tabindex="-1"
>
    <div class="retail-filter-panel__inner min-h-full border-r border-neutral-200 bg-white lg:min-h-0 lg:border lg:border-neutral-300">
		<div class="retail-filter-panel__header sticky top-0 z-10 border-b border-neutral-200 bg-white px-4 py-4 lg:static">
			<div class="flex items-center justify-between gap-4">
				<div class="flex min-w-0 items-center gap-2">
					<h2
						id="<?php echo esc_attr( $heading_id ); ?>"
						class="retail-filter-panel__title text-lg font-semibold text-neutral-950"
					>
						Filter products
					</h2>

					<?php if ( $active_filter_count > 0 ) : ?>
						<span class="retail-filter-panel__active-count inline-flex min-h-7 shrink-0 items-center bg-blue-50 px-2 text-xs font-semibold text-blue-800">
							<?php echo esc_html( $active_filter_count ); ?> active
						</span>
					<?php endif; ?>
				</div>

				<div class="flex shrink-0 items-center gap-3">
					<?php if ( $has_active_filters && $clear_all_url ) : ?>
						<a
							href="<?php echo esc_url( $clear_all_url ); ?>"
							class="retail-filter-panel__clear text-xs font-semibold text-blue-700 transition hover:text-blue-900 hover:underline hover:underline-offset-4"
						>
							Clear all
						</a>
					<?php endif; ?>

					<button
						type="button"
						class="retail-filter-panel__close inline-flex h-9 w-9 items-center justify-center border border-neutral-300 text-neutral-700 transition hover:border-neutral-950 hover:text-neutral-950 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-700 focus-visible:ring-offset-2 lg:hidden"
						data-transactional-filter-close
						aria-label="Close product filters"
					>
						<svg
							class="h-5 w-5"
							viewBox="0 0 24 24"
							fill="none"
							stroke="currentColor"
							stroke-width="1.8"
							aria-hidden="true"
						>
							<path d="M6 6l12 12"></path>
							<path d="M18 6L6 18"></path>
						</svg>
					</button>
				</div>
			</div>
		</div>

		<div class="retail-filter-panel__body divide-y divide-neutral-200 pb-6 text-sm lg:pb-0">

			<?php if ( ! empty( $product_type_terms ) ) : ?>
				<section class="retail-filter-group px-4 py-4">
					<div class="flex items-center justify-between gap-4">
						<h3 class="retail-filter-group__title font-semibold text-neutral-950">
							Product type
						</h3>

						<?php if ( $type_filter ) : ?>
							<a
								href="<?php echo esc_url( remove_query_arg( [ 'type', 'pg' ] ) ); ?>"
								class="retail-filter-group__clear text-xs font-semibold text-blue-700 transition hover:text-blue-900 hover:underline hover:underline-offset-4"
							>
								Clear
							</a>
						<?php endif; ?>
					</div>

					<form method="get" class="retail-filter-group__options mt-3 space-y-2 text-neutral-700">

						<?php foreach ( $get_preserved_query( 'type' ) as $name => $value ) : ?>
							<input
								type="hidden"
								name="<?php echo esc_attr( $name ); ?>"
								value="<?php echo esc_attr( $value ); ?>"
							>
						<?php endforeach; ?>

						<?php foreach ( $product_type_terms as $product_type_term ) : ?>
							<?php
							if ( ! $product_type_term instanceof WP_Term ) {
								continue;
							}

							$is_checked = $type_filter === $product_type_term->slug;
							?>

							<label class="<?php echo esc_attr( $is_checked ? 'retail-filter-option retail-filter-option--active border-neutral-950 bg-neutral-950 font-medium text-white' : 'retail-filter-option border-neutral-200 bg-white text-neutral-700 hover:border-neutral-400 hover:bg-neutral-50' ); ?> flex cursor-pointer items-center gap-3 border px-3 py-2.5 transition focus-within:ring-2 focus-within:ring-blue-700 focus-within:ring-offset-1">
								<input
									type="radio"
									name="type"
									value="<?php echo esc_attr( $product_type_term->slug ); ?>"
									<?php checked( $is_checked ); ?>
									onchange="this.form.submit()"
									class="sr-only"
								>

								<span class="<?php echo esc_attr( $is_checked ? 'retail-filter-option__control retail-filter-option__control--active border-white bg-white' : 'retail-filter-option__control border-neutral-300 bg-white' ); ?> flex h-3.5 w-3.5 shrink-0 items-center justify-center border">
									<?php if ( $is_checked ) : ?>
										<span class="retail-filter-option__mark h-1.5 w-1.5 bg-neutral-950"></span>
									<?php endif; ?>
								</span>

								<span class="retail-filter-option__label min-w-0 flex-1">
									<?php echo esc_html( $format_type_label( $product_type_term ) ); ?>
								</span>

								<span class="<?php echo esc_attr( $is_checked ? 'retail-filter-option__count retail-filter-option__count--active text-neutral-300' : 'retail-filter-option__count text-neutral-400' ); ?> text-xs tabular-nums">
									<?php echo esc_html( $product_type_term->count ); ?>
								</span>
							</label>
						<?php endforeach; ?>
					</form>
				</section>
			<?php endif; ?>

			<?php foreach ( $filter_registry as $filter_key => $definition ) : ?>
				<?php
				$query_var = isset( $definition['query_var'] )
					? sanitize_key( $definition['query_var'] )
					: sanitize_key( $filter_key );

				$label = isset( $definition['label'] )
					? (string) $definition['label']
					: ucfirst( (string) $filter_key );

				$terms = isset( $taxonomy_facets[ $filter_key ]['terms'] )
					&& is_array( $taxonomy_facets[ $filter_key ]['terms'] )
					? $taxonomy_facets[ $filter_key ]['terms']
					: [];

				$counts = isset( $taxonomy_facets[ $filter_key ]['counts'] )
					&& is_array( $taxonomy_facets[ $filter_key ]['counts'] )
					? $taxonomy_facets[ $filter_key ]['counts']
					: [];

				$selected = isset( $taxonomy_filters[ $filter_key ] )
					? (string) $taxonomy_filters[ $filter_key ]
					: '';

				if ( empty( $terms ) ) {
					continue;
				}

				get_template_part(
					'template-parts/woocommerce/transactional-filter-group',
					null,
					[
						'label'           => $label,
						'query_var'       => $query_var,
						'terms'           => $terms,
						'counts'          => $counts,
						'selected'        => $selected,
						'preserved_query' => $get_preserved_query( $query_var ),
					]
				);
				?>
			<?php endforeach; ?>

			<section class="retail-filter-group px-4 py-4">
				<div class="flex items-center justify-between gap-4">
					<h3 class="retail-filter-group__title font-semibold text-neutral-950">
						Stock status
					</h3>

					<?php if ( $stock_filter ) : ?>
						<a
							href="<?php echo esc_url( remove_query_arg( [ 'stock', 'pg' ] ) ); ?>"
							class="retail-filter-group__clear text-xs font-semibold text-blue-700 transition hover:text-blue-900 hover:underline hover:underline-offset-4"
						>
							Clear
						</a>
					<?php endif; ?>
				</div>

				<form method="get" class="retail-filter-group__options mt-3 space-y-2 text-neutral-700">

					<?php foreach ( $get_preserved_query( 'stock' ) as $name => $value ) : ?>
						<input
							type="hidden"
							name="<?php echo esc_attr( $name ); ?>"
							value="<?php echo esc_attr( $value ); ?>"
						>
					<?php endforeach; ?>

					<?php $stock_is_checked = 'in-stock' === $stock_filter; ?>

					<label class="<?php echo esc_attr( $stock_is_checked ? 'retail-filter-option retail-filter-option--active border-neutral-950 bg-neutral-950 font-medium text-white' : 'retail-filter-option border-neutral-200 bg-white text-neutral-700 hover:border-neutral-400 hover:bg-neutral-50' ); ?> flex cursor-pointer items-center gap-3 border px-3 py-2.5 transition focus-within:ring-2 focus-within:ring-blue-700 focus-within:ring-offset-1">
						<input
							type="checkbox"
							name="stock"
							value="in-stock"
							<?php checked( $stock_is_checked ); ?>
							onchange="this.form.submit()"
							class="sr-only"
						>

						<span class="<?php echo esc_attr( $stock_is_checked ? 'retail-filter-option__control retail-filter-option__control--active border-white bg-white' : 'retail-filter-option__control border-neutral-300 bg-white' ); ?> flex h-3.5 w-3.5 shrink-0 items-center justify-center border">
							<?php if ( $stock_is_checked ) : ?>
								<span class="retail-filter-option__mark h-1.5 w-1.5 bg-neutral-950"></span>
							<?php endif; ?>
						</span>

						<span class="retail-filter-option__label min-w-0 flex-1">
							In stock
						</span>
					</label>
				</form>
			</section>

			<?php
			$visible_price_bands = isset( $price_facet['bands'] )
				&& is_array( $price_facet['bands'] )
				? $price_facet['bands']
				: [];

			if ( ! empty( $visible_price_bands ) ) {
				get_template_part(
					'template-parts/woocommerce/transactional-price-filter-group',
					null,
					[
						'bands'           => $visible_price_bands,
						'selected'        => $price_filter,
						'preserved_query' => $get_preserved_query( 'price' ),
					]
				);
			}
			?>

		</div>
	</div>
</aside>
