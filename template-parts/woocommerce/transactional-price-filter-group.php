<?php
/**
 * Transactional Price filter group.
 *
 * Expected args:
 * - bands           array<string,array{label:string,min:float|null,max:float|null,count:int}>
 * - selected        string
 * - preserved_query array<string,string>
 *
 * @package Blaines
 */

defined( 'ABSPATH' ) || exit;

$bands = isset( $args['bands'] ) && is_array( $args['bands'] )
	? $args['bands']
	: [];

$selected = isset( $args['selected'] )
	? sanitize_key( $args['selected'] )
	: '';

$preserved_query = isset( $args['preserved_query'] )
	&& is_array( $args['preserved_query'] )
	? $args['preserved_query']
	: [];

if ( empty( $bands ) ) {
	return;
}
?>

<section class="retail-filter-group px-4 py-4">
	<div class="flex items-center justify-between gap-4">
		<h3 class="retail-filter-group__title font-semibold text-neutral-950">
			Price
		</h3>

		<?php if ( $selected ) : ?>
			<a
				href="<?php echo esc_url( remove_query_arg( [ 'price', 'pg' ] ) ); ?>"
				class="retail-filter-group__clear text-xs font-semibold text-blue-700 transition hover:text-blue-900 hover:underline hover:underline-offset-4"
			>
				Clear
			</a>
		<?php endif; ?>
	</div>

	<form method="get" class="retail-filter-group__options mt-3 space-y-2 text-neutral-700">

		<?php foreach ( $preserved_query as $name => $value ) : ?>
			<?php if ( '' !== (string) $value ) : ?>
				<input
					type="hidden"
					name="<?php echo esc_attr( $name ); ?>"
					value="<?php echo esc_attr( $value ); ?>"
				>
			<?php endif; ?>
		<?php endforeach; ?>

		<?php foreach ( $bands as $band_key => $band ) : ?>
			<?php
			$band_key = sanitize_key( $band_key );
			$label    = isset( $band['label'] )
				? sanitize_text_field( $band['label'] )
				: '';
			$count    = isset( $band['count'] )
				? max( 0, absint( $band['count'] ) )
				: 0;

			if ( ! $band_key || ! $label ) {
				continue;
			}

			$is_checked = $selected === $band_key;
			?>

			<label class="<?php echo esc_attr( $is_checked ? 'retail-filter-option retail-filter-option--active border-neutral-950 bg-neutral-950 font-medium text-white' : 'retail-filter-option border-neutral-200 bg-white text-neutral-700 hover:border-neutral-400 hover:bg-neutral-50' ); ?> flex cursor-pointer items-center gap-3 border px-3 py-2.5 transition focus-within:ring-2 focus-within:ring-blue-700 focus-within:ring-offset-1">
				<input
					type="radio"
					name="price"
					value="<?php echo esc_attr( $band_key ); ?>"
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
					<?php echo esc_html( $label ); ?>
				</span>

				<span class="<?php echo esc_attr( $is_checked ? 'retail-filter-option__count retail-filter-option__count--active text-neutral-300' : 'retail-filter-option__count text-neutral-400' ); ?> text-xs tabular-nums">
					<?php echo esc_html( $count ); ?>
				</span>
			</label>
		<?php endforeach; ?>

	</form>
</section>
