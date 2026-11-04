<?php
/**
 * Generic transactional taxonomy filter group.
 *
 * Expected args:
 * - label           string
 * - query_var       string
 * - terms           WP_Term[]
 * - counts          array<int,int>
 * - selected        string
 * - preserved_query array<string,string>
 * - empty_message   string
 *
 * @package Blaines
 */

defined( 'ABSPATH' ) || exit;

$label = isset( $args['label'] )
	? sanitize_text_field( $args['label'] )
	: '';

$query_var = isset( $args['query_var'] )
	? sanitize_key( $args['query_var'] )
	: '';

$terms = isset( $args['terms'] ) && is_array( $args['terms'] )
	? $args['terms']
	: [];

$counts = isset( $args['counts'] ) && is_array( $args['counts'] )
	? $args['counts']
	: [];

$selected = isset( $args['selected'] )
	? (string) $args['selected']
	: '';

$preserved_query = isset( $args['preserved_query'] )
	&& is_array( $args['preserved_query'] )
	? $args['preserved_query']
	: [];

$empty_message = isset( $args['empty_message'] )
	? sanitize_text_field( $args['empty_message'] )
	: '';

if ( ! $label || ! $query_var ) {
	return;
}

if ( empty( $terms ) && ! $empty_message ) {
	return;
}
?>

<section class="retail-filter-group px-4 py-4">
	<div class="flex items-center justify-between gap-4">
		<h3 class="retail-filter-group__title font-semibold text-neutral-950">
			<?php echo esc_html( $label ); ?>
		</h3>

		<?php if ( $selected ) : ?>
			<a
				href="<?php echo esc_url( remove_query_arg( [ $query_var, 'pg' ] ) ); ?>"
				class="retail-filter-group__clear text-xs font-semibold text-blue-700 transition hover:text-blue-900 hover:underline hover:underline-offset-4"
			>
				Clear
			</a>
		<?php endif; ?>
	</div>

	<?php if ( ! empty( $terms ) ) : ?>
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

			<?php foreach ( $terms as $term ) : ?>
				<?php
				if ( ! $term instanceof WP_Term ) {
					continue;
				}

				$is_checked = $selected === $term->slug;
				?>

				<label class="<?php echo esc_attr( $is_checked ? 'retail-filter-option retail-filter-option--active border-neutral-950 bg-neutral-950 font-medium text-white' : 'retail-filter-option border-neutral-200 bg-white text-neutral-700 hover:border-neutral-400 hover:bg-neutral-50' ); ?> flex cursor-pointer items-center gap-3 border px-3 py-2.5 transition focus-within:ring-2 focus-within:ring-blue-700 focus-within:ring-offset-1">
					<input
						type="radio"
						name="<?php echo esc_attr( $query_var ); ?>"
						value="<?php echo esc_attr( $term->slug ); ?>"
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
						<?php echo esc_html( $term->name ); ?>
					</span>

					<span class="<?php echo esc_attr( $is_checked ? 'retail-filter-option__count retail-filter-option__count--active text-neutral-300' : 'retail-filter-option__count text-neutral-400' ); ?> text-xs tabular-nums">
						<?php echo esc_html( $counts[ $term->term_id ] ?? 0 ); ?>
					</span>
				</label>
			<?php endforeach; ?>
		</form>
	<?php else : ?>
		<p class="retail-filter-group__empty mt-3 border border-dashed border-neutral-200 bg-neutral-50 px-3 py-3 text-sm leading-relaxed text-neutral-500">
			<?php echo esc_html( $empty_message ); ?>
		</p>
	<?php endif; ?>
</section>
