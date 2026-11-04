<?php
/**
 * Transactional WooCommerce product archive partial.
 *
 * Used for controlled Electrical category listing pages.
 *
 * Expected args:
 * - category_slug               string
 * - title                       string
 * - intro                       string
 * - promotion_source_id         int   Optional page ID containing ACF panels.
 * - promotion_items             array Optional explicit promotion-panel data.
 * - show_promotion_placeholders bool  Development-only placeholders.
 *
 * @package Blaines
 */

defined( 'ABSPATH' ) || exit;

$category_slug = isset( $args['category_slug'] )
	? sanitize_title( $args['category_slug'] )
	: '';

$title = isset( $args['title'] )
	? sanitize_text_field( $args['title'] )
	: 'Electrical products';

$intro = isset( $args['intro'] )
	? wp_kses_post( $args['intro'] )
	: '';

$promotion_items_were_supplied = array_key_exists( 'promotion_items', $args ?? [] )
	&& is_array( $args['promotion_items'] );

$promotion_items = $promotion_items_were_supplied
	? $args['promotion_items']
	: [];

$promotion_source_id = isset( $args['promotion_source_id'] )
	? absint( $args['promotion_source_id'] )
	: get_queried_object_id();

if (
	! $promotion_items_were_supplied
	&& function_exists( 'blaines_get_transactional_archive_promotions' )
) {
	$promotion_items = blaines_get_transactional_archive_promotions(
		$promotion_source_id
	);
}

$show_promotion_placeholders = isset( $args['show_promotion_placeholders'] )
	? (bool) $args['show_promotion_placeholders']
	: false;

if ( ! $category_slug ) {
	return;
}

if ( ! function_exists( 'blaines_get_transactional_archive_context' ) ) {
	?>
	<section class="container-wide py-8 sm:py-10 lg:py-12">
		<div class="border border-neutral-200 bg-neutral-50 p-6">
			<p class="text-neutral-700">
				Product filtering is currently unavailable.
			</p>
		</div>
	</section>
	<?php
	return;
}

$context = blaines_get_transactional_archive_context(
	$category_slug,
	$title,
	$intro
);

if ( is_wp_error( $context ) ) {
	?>
	<section class="container-wide py-8 sm:py-10 lg:py-12">
		<div class="border border-neutral-200 bg-neutral-50 p-6">
			<p class="text-neutral-700">
				<?php echo esc_html( $context->get_error_message() ); ?>
			</p>
		</div>
	</section>
	<?php
	return;
}

/*
 * The context keys deliberately match the variable names used by the
 * presentation below and by the extracted sidebar template part.
 */
extract( $context, EXTR_SKIP );

/**
 * Build the active-filter summary used above the product results.
 * Sorting is not treated as a filter and is preserved by Clear all.
 */
$active_filter_items = [];

$find_term_by_slug = static function ( array $terms, string $slug ): ?WP_Term {
	foreach ( $terms as $term ) {
		if ( $term instanceof WP_Term && $slug === $term->slug ) {
			return $term;
		}
	}

	return null;
};

if ( $type_filter ) {
	$active_type_display_term = $find_term_by_slug(
		$product_type_terms,
		$type_filter
	);

	if ( $active_type_display_term instanceof WP_Term ) {
		$active_filter_items[] = [
			'label'     => 'Product type',
			'value'     => $format_type_label( $active_type_display_term ),
			'clear_url' => remove_query_arg( [ 'type', 'pg' ] ),
		];
	}
}

foreach ( $filter_registry as $filter_key => $definition ) {
	$selected_value = isset( $taxonomy_filters[ $filter_key ] )
		? (string) $taxonomy_filters[ $filter_key ]
		: '';

	if ( ! $selected_value ) {
		continue;
	}

	$query_var = isset( $definition['query_var'] )
		? sanitize_key( $definition['query_var'] )
		: sanitize_key( $filter_key );

	$label = isset( $definition['label'] )
		? sanitize_text_field( $definition['label'] )
		: ucfirst( (string) $filter_key );

	$terms = isset( $taxonomy_facets[ $filter_key ]['terms'] )
		&& is_array( $taxonomy_facets[ $filter_key ]['terms'] )
		? $taxonomy_facets[ $filter_key ]['terms']
		: [];

	$selected_term = $find_term_by_slug( $terms, $selected_value );

	if ( ! $query_var || ! $selected_term instanceof WP_Term ) {
		continue;
	}

	$active_filter_items[] = [
		'label'     => $label,
		'value'     => $selected_term->name,
		'clear_url' => remove_query_arg( [ $query_var, 'pg' ] ),
	];
}

if ( 'in-stock' === $stock_filter ) {
	$active_filter_items[] = [
		'label'     => 'Stock status',
		'value'     => 'In stock',
		'clear_url' => remove_query_arg( [ 'stock', 'pg' ] ),
	];
}

if ( $price_filter ) {
	$price_bands = isset( $price_facet['bands'] )
		&& is_array( $price_facet['bands'] )
		? $price_facet['bands']
		: [];

	$price_label = isset( $price_bands[ $price_filter ]['label'] )
		? sanitize_text_field( $price_bands[ $price_filter ]['label'] )
		: '';

	if ( $price_label ) {
		$active_filter_items[] = [
			'label'     => 'Price',
			'value'     => $price_label,
			'clear_url' => remove_query_arg( [ 'price', 'pg' ] ),
		];
	}
}

$has_active_filters = ! empty( $active_filter_items );
$active_filter_count = count( $active_filter_items );
$clear_all_url       = get_permalink();

if ( 'default' !== $sort ) {
	$clear_all_url = add_query_arg(
		'sort',
		$sort,
		$clear_all_url
	);
}

$filter_shell_id   = 'transactional-filter-shell-' . sanitize_html_class( $category_slug );
$filter_panel_id   = 'transactional-filter-panel-' . sanitize_html_class( $category_slug );
$filter_button_id  = 'transactional-filter-button-' . sanitize_html_class( $category_slug );
$filter_heading_id = 'transactional-filter-heading-' . sanitize_html_class( $category_slug );
$archive_heading_id = 'transactional-archive-heading-' . sanitize_html_class( $category_slug );
?>

<div class="retail-archive-canvas">

<section
	id="<?php echo esc_attr( $filter_shell_id ); ?>"
	class="blaines-retail blaines-retail--archive retail-archive container-wide py-6 sm:py-8 lg:py-10"
	data-transactional-filter-shell
	aria-labelledby="<?php echo esc_attr( $archive_heading_id ); ?>"
>

	<div class="retail-archive__layout lg:grid lg:grid-cols-12 lg:gap-8 xl:gap-10">

		<div
			class="retail-filter-overlay fixed inset-0 z-[60] hidden bg-neutral-950/60 lg:hidden"
			data-transactional-filter-overlay
			aria-hidden="true"
		></div>

		<?php
		get_template_part(
			'template-parts/woocommerce/transactional-filter-sidebar',
			null,
			[
				'product_type_terms' => $product_type_terms,
				'filter_registry'    => $filter_registry,
				'taxonomy_filters'   => $taxonomy_filters,
				'taxonomy_facets'    => $taxonomy_facets,
				'type_filter'        => $type_filter,
				'stock_filter'       => $stock_filter,
				'price_filter'       => $price_filter,
				'price_facet'        => $price_facet,
				'sort'               => $sort,
				'format_type_label'  => $format_type_label,
				'has_active_filters' => $has_active_filters,
				'active_filter_count' => $active_filter_count,
				'clear_all_url'      => $clear_all_url,
				'panel_id'           => $filter_panel_id,
				'heading_id'         => $filter_heading_id,
			]
		);
		?>

		<div class="retail-archive__results min-w-0 lg:col-span-9">

            <h1
                    id="<?php echo esc_attr( $archive_heading_id ); ?>"
                    class="retail-archive__title"
            >
                <?php echo esc_html( $display_title ); ?>
            </h1>

			<div class="retail-filter-trigger-wrap mb-4 lg:hidden">
				<button
					id="<?php echo esc_attr( $filter_button_id ); ?>"
					type="button"
					class="retail-filter-trigger flex w-full items-center justify-between gap-4 border border-neutral-950 bg-neutral-950 px-4 py-3.5 text-left text-sm font-semibold text-white transition hover:bg-neutral-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-700 focus-visible:ring-offset-2"
					data-transactional-filter-open
					aria-controls="<?php echo esc_attr( $filter_panel_id ); ?>"
					aria-expanded="false"
				>
					<span class="flex items-center gap-3">
						<svg
							class="h-4 w-4 shrink-0"
							viewBox="0 0 24 24"
							fill="none"
							stroke="currentColor"
							stroke-width="1.8"
							aria-hidden="true"
						>
							<path d="M4 7h10"></path>
							<path d="M18 7h2"></path>
							<path d="M4 17h2"></path>
							<path d="M10 17h10"></path>
							<circle cx="16" cy="7" r="2"></circle>
							<circle cx="8" cy="17" r="2"></circle>
						</svg>
						<span>Filter products</span>
					</span>

					<?php if ( $active_filter_count > 0 ) : ?>
						<span class="retail-filter-trigger__count inline-flex h-6 min-w-6 items-center justify-center bg-white px-1.5 text-xs font-bold text-neutral-950">
							<?php echo esc_html( $active_filter_count ); ?>
						</span>
					<?php endif; ?>
				</button>
			</div>

			<?php
			get_template_part(
				'template-parts/woocommerce/transactional-archive-promotions',
				null,
				[
					'items'             => $promotion_items,
					'show_placeholders' => $show_promotion_placeholders,
					'context_title'     => $display_title,
				]
			);
			?>

			<?php
			if ( $has_active_filters ) {
				get_template_part(
					'template-parts/woocommerce/transactional-active-filters',
					null,
					[
						'items'         => $active_filter_items,
						'clear_all_url' => $clear_all_url,
					]
				);
			}
			?>

			<div class="retail-archive-toolbar mb-5 flex flex-col gap-3 border-y border-neutral-200 bg-neutral-50 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">

				<p class="retail-archive-toolbar__count text-sm font-medium text-neutral-700">
					<?php if ( $total_products > 0 ) : ?>
						Showing <?php echo esc_html( $start_result ); ?>–<?php echo esc_html( $end_result ); ?> of <?php echo esc_html( $total_products ); ?> products
					<?php else : ?>
						No products found
					<?php endif; ?>
				</p>

				<form method="get" class="retail-archive-sort flex w-full items-center justify-between gap-3 text-sm text-neutral-600 sm:w-auto sm:justify-end">

					<?php if ( $type_filter ) : ?>
						<input type="hidden" name="type" value="<?php echo esc_attr( $type_filter ); ?>">
					<?php endif; ?>

					<?php if ( 'in-stock' === $stock_filter ) : ?>
						<input type="hidden" name="stock" value="in-stock">
					<?php endif; ?>

					<?php if ( $price_filter ) : ?>
						<input type="hidden" name="price" value="<?php echo esc_attr( $price_filter ); ?>">
					<?php endif; ?>

					<?php if ( $brand_filter ) : ?>
						<input type="hidden" name="brand" value="<?php echo esc_attr( $brand_filter ); ?>">
					<?php endif; ?>

					<?php if ( $fitting_filter ) : ?>
						<input type="hidden" name="fitting" value="<?php echo esc_attr( $fitting_filter ); ?>">
					<?php endif; ?>

					<?php if ( $fuel_filter ) : ?>
						<input type="hidden" name="fuel" value="<?php echo esc_attr( $fuel_filter ); ?>">
					<?php endif; ?>

					<?php if ( $width_filter ) : ?>
						<input type="hidden" name="width" value="<?php echo esc_attr( $width_filter ); ?>">
					<?php endif; ?>

					<label for="transactional-sort" class="retail-archive-sort__label font-medium text-neutral-700">
						Sort by
					</label>

					<select
						id="transactional-sort"
						name="sort"
						class="retail-archive-sort__select min-w-48 border border-neutral-300 bg-white px-3 py-2.5 text-sm text-neutral-950 focus:border-blue-700 focus:outline-none focus:ring-1 focus:ring-blue-700"
						onchange="this.form.submit()"
					>
						<option value="default" <?php selected( $sort, 'default' ); ?>>Default</option>
						<option value="price-asc" <?php selected( $sort, 'price-asc' ); ?>>Price: low to high</option>
						<option value="price-desc" <?php selected( $sort, 'price-desc' ); ?>>Price: high to low</option>
						<option value="title-asc" <?php selected( $sort, 'title-asc' ); ?>>Name A–Z</option>
					</select>

				</form>

			</div>

			<?php if ( $products_query->have_posts() ) : ?>

				<div class="retail-product-results space-y-5">

					<?php
					while ( $products_query->have_posts() ) :
						$products_query->the_post();

						$product = wc_get_product( get_the_ID() );

						if ( ! $product ) {
							continue;
						}

						get_template_part(
							'template-parts/woocommerce/row-transactional-product',
							null,
							[
								'product' => $product,
							]
						);
					endwhile;

					wp_reset_postdata();
					?>

				</div>

				<?php if ( $total_pages > 1 ) : ?>

					<nav
						class="retail-pagination mt-8 flex flex-wrap items-center justify-center gap-2 border-t border-neutral-200 pt-8"
						aria-label="Product pagination"
					>

						<?php
						for ( $page = 1; $page <= $total_pages; $page++ ) :
							$url = add_query_arg(
								array_filter(
									[
										'pg'      => $page > 1 ? $page : null,
										'sort'    => 'default' !== $sort ? $sort : null,
										'stock'   => 'in-stock' === $stock_filter ? 'in-stock' : null,
										'price'   => $price_filter ?: null,
										'brand'   => $brand_filter ?: null,
										'type'    => $type_filter ?: null,
										'fitting' => $fitting_filter ?: null,
										'fuel'    => $fuel_filter ?: null,
										'width'   => $width_filter ?: null,
									]
								),
								get_permalink()
							);
							?>

							<?php if ( $page === $current_page ) : ?>

								<span class="retail-pagination__current inline-flex h-10 min-w-10 items-center justify-center bg-neutral-950 px-3 text-sm font-semibold text-white">
									<?php echo esc_html( $page ); ?>
								</span>

							<?php else : ?>

								<a
									href="<?php echo esc_url( $url ); ?>"
									class="retail-pagination__link inline-flex h-10 min-w-10 items-center justify-center border border-neutral-300 px-3 text-sm font-semibold text-neutral-700 transition hover:border-neutral-950 hover:text-neutral-950 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-700 focus-visible:ring-offset-2"
								>
									<?php echo esc_html( $page ); ?>
								</a>

							<?php endif; ?>

						<?php endfor; ?>

					</nav>

				<?php endif; ?>

			<?php else : ?>

				<div class="retail-empty-state border border-neutral-200 bg-neutral-50 px-5 py-8 text-center sm:px-8">
					<h2 class="retail-empty-state__title text-xl font-semibold text-neutral-950">
						<?php echo esc_html( $has_active_filters ? 'No products match your filters' : 'No products are currently available' ); ?>
					</h2>

					<p class="retail-empty-state__copy mx-auto mt-2 max-w-xl text-sm leading-relaxed text-neutral-600">
						<?php echo esc_html( $has_active_filters ? 'Try removing one or more filters to broaden the results.' : 'Please check back later or browse another electrical department.' ); ?>
					</p>

					<?php if ( $has_active_filters && $clear_all_url ) : ?>
						<a
							href="<?php echo esc_url( $clear_all_url ); ?>"
							class="retail-empty-state__action mt-5 inline-flex min-h-11 items-center justify-center border border-neutral-950 bg-neutral-950 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-neutral-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-700 focus-visible:ring-offset-2"
						>
							Clear all filters
						</a>
					<?php endif; ?>
				</div>

			<?php endif; ?>

		</div>

	</div>

</section>

</div>

<script>
(function () {
	'use strict';

	function initialiseTransactionalFilterShell(shell) {
		if (!shell || shell.dataset.transactionalFilterReady === 'true') {
			return;
		}

		var openButton = shell.querySelector('[data-transactional-filter-open]');
		var panel = shell.querySelector('[data-transactional-filter-panel]');
		var overlay = shell.querySelector('[data-transactional-filter-overlay]');
		var closeButtons = shell.querySelectorAll('[data-transactional-filter-close]');
		var desktopMedia = window.matchMedia('(min-width: 1024px)');
		var previouslyFocused = null;
		var documentOverflow = '';
		var bodyOverflow = '';

		if (!openButton || !panel || !overlay) {
			return;
		}

		shell.dataset.transactionalFilterReady = 'true';

		function getFocusableElements() {
			return Array.prototype.slice.call(
				panel.querySelectorAll(
					'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
				)
			).filter(function (element) {
				return element.offsetParent !== null;
			});
		}

		function lockPageScroll() {
			documentOverflow = document.documentElement.style.overflow;
			bodyOverflow = document.body.style.overflow;
			document.documentElement.style.overflow = 'hidden';
			document.body.style.overflow = 'hidden';
		}

		function unlockPageScroll() {
			document.documentElement.style.overflow = documentOverflow;
			document.body.style.overflow = bodyOverflow;
		}

		function openPanel() {
			if (desktopMedia.matches) {
				return;
			}

			previouslyFocused = document.activeElement;
			panel.classList.remove('hidden');
			overlay.classList.remove('hidden');
			openButton.setAttribute('aria-expanded', 'true');
			panel.setAttribute('role', 'dialog');
			panel.setAttribute('aria-modal', 'true');
			lockPageScroll();

			var closeButton = panel.querySelector('[data-transactional-filter-close]');
			var firstFocusable = getFocusableElements()[0];

			if (closeButton) {
				closeButton.focus();
			} else if (firstFocusable) {
				firstFocusable.focus();
			} else {
				panel.focus();
			}
		}

		function closePanel(restoreFocus) {
			if (!desktopMedia.matches) {
				panel.classList.add('hidden');
			}

			overlay.classList.add('hidden');
			openButton.setAttribute('aria-expanded', 'false');
			panel.removeAttribute('role');
			panel.removeAttribute('aria-modal');
			unlockPageScroll();

			if (restoreFocus && previouslyFocused && typeof previouslyFocused.focus === 'function') {
				previouslyFocused.focus();
			} else if (restoreFocus) {
				openButton.focus();
			}
		}

		function handlePanelKeydown(event) {
			if (desktopMedia.matches || panel.classList.contains('hidden')) {
				return;
			}

			if (event.key === 'Escape') {
				event.preventDefault();
				closePanel(true);
				return;
			}

			if (event.key !== 'Tab') {
				return;
			}

			var focusable = getFocusableElements();

			if (!focusable.length) {
				event.preventDefault();
				panel.focus();
				return;
			}

			var first = focusable[0];
			var last = focusable[focusable.length - 1];

			if (event.shiftKey && document.activeElement === first) {
				event.preventDefault();
				last.focus();
			} else if (!event.shiftKey && document.activeElement === last) {
				event.preventDefault();
				first.focus();
			}
		}

		function handleBreakpointChange(event) {
			if (event.matches) {
				panel.classList.remove('hidden');
				overlay.classList.add('hidden');
				openButton.setAttribute('aria-expanded', 'false');
				panel.removeAttribute('role');
				panel.removeAttribute('aria-modal');
				unlockPageScroll();
			} else {
				panel.classList.add('hidden');
			}
		}

		openButton.addEventListener('click', openPanel);
		overlay.addEventListener('click', function () {
			closePanel(true);
		});

		Array.prototype.forEach.call(closeButtons, function (button) {
			button.addEventListener('click', function () {
				closePanel(true);
			});
		});

		panel.addEventListener('keydown', handlePanelKeydown);

		if (typeof desktopMedia.addEventListener === 'function') {
			desktopMedia.addEventListener('change', handleBreakpointChange);
		} else if (typeof desktopMedia.addListener === 'function') {
			desktopMedia.addListener(handleBreakpointChange);
		}

		handleBreakpointChange(desktopMedia);
	}

	function initialiseAllTransactionalFilterShells() {
		document.querySelectorAll('[data-transactional-filter-shell]').forEach(
			initialiseTransactionalFilterShell
		);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initialiseAllTransactionalFilterShells);
	} else {
		initialiseAllTransactionalFilterShells();
	}
}());
</script>
