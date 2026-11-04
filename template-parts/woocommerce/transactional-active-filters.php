<?php
/**
 * Active transactional archive filters.
 *
 * Expected args:
 * - items         array<int,array{label:string,value:string,clear_url:string}>
 * - clear_all_url string
 *
 * @package Blaines
 */

defined( 'ABSPATH' ) || exit;

$items = isset( $args['items'] ) && is_array( $args['items'] )
	? $args['items']
	: [];

$clear_all_url = isset( $args['clear_all_url'] )
	? esc_url_raw( $args['clear_all_url'] )
	: '';

if ( empty( $items ) ) {
	return;
}
?>

<section
	class="retail-active-filters mb-5 border border-neutral-200 bg-white px-4 py-4 sm:px-5"
	aria-labelledby="active-product-filters-heading"
>
	<div class="retail-active-filters__header flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
		<h2
			id="active-product-filters-heading"
			class="retail-active-filters__title text-sm font-semibold text-neutral-950"
		>
			Active filters
		</h2>

		<?php if ( $clear_all_url ) : ?>
			<a
				href="<?php echo esc_url( $clear_all_url ); ?>"
				class="retail-active-filters__clear text-xs font-semibold text-blue-700 transition hover:text-blue-900 hover:underline hover:underline-offset-4"
			>
				Clear all filters
			</a>
		<?php endif; ?>
	</div>

	<div class="retail-active-filters__chips mt-3 flex flex-wrap gap-2.5">
		<?php foreach ( $items as $item ) : ?>
			<?php
			$label = isset( $item['label'] )
				? sanitize_text_field( $item['label'] )
				: '';
			$value = isset( $item['value'] )
				? sanitize_text_field( $item['value'] )
				: '';
			$clear_url = isset( $item['clear_url'] )
				? esc_url_raw( $item['clear_url'] )
				: '';

			if ( ! $label || ! $value || ! $clear_url ) {
				continue;
			}
			?>

			<a
				href="<?php echo esc_url( $clear_url ); ?>"
				class="retail-active-filters__chip inline-flex min-h-9 items-center gap-2 border border-neutral-300 bg-neutral-50 px-3 py-2 text-xs text-neutral-700 transition hover:border-neutral-950 hover:bg-white hover:text-neutral-950 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-700 focus-visible:ring-offset-2"
				aria-label="<?php echo esc_attr( sprintf( 'Remove %s filter: %s', $label, $value ) ); ?>"
			>
				<span>
					<span class="retail-active-filters__chip-label font-semibold text-neutral-950">
						<?php echo esc_html( $label ); ?>:
					</span>
					<?php echo esc_html( $value ); ?>
				</span>

				<svg
					class="retail-active-filters__chip-icon h-3.5 w-3.5 shrink-0"
					viewBox="0 0 24 24"
					fill="none"
					stroke="currentColor"
					stroke-width="2"
					stroke-linecap="round"
					aria-hidden="true"
				>
					<path d="M6 6l12 12"></path>
					<path d="M18 6L6 18"></path>
				</svg>
			</a>
		<?php endforeach; ?>
	</div>
</section>
