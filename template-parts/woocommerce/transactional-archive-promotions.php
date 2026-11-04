<?php
/**
 * Image-only transactional archive promotion panels.
 *
 * Expected args:
 * - items             array<int,array<string,mixed>>
 * - context_title     string
 * - show_placeholders bool
 *
 * Supported item keys:
 * - image_id        int|array
 * - mobile_image_id int|array
 *
 * @package Blaines
 */

defined( 'ABSPATH' ) || exit;

$raw_items = isset( $args['items'] ) && is_array( $args['items'] )
	? array_slice( array_values( $args['items'] ), 0, 3 )
	: [];

$show_placeholders = isset( $args['show_placeholders'] )
	? (bool) $args['show_placeholders']
	: false;

$context_title = isset( $args['context_title'] )
	? sanitize_text_field( $args['context_title'] )
	: 'Electrical products';

$resolve_image_id = static function ( $value ): int {
	if ( is_numeric( $value ) ) {
		return absint( $value );
	}

	if ( is_array( $value ) ) {
		if ( ! empty( $value['ID'] ) ) {
			return absint( $value['ID'] );
		}

		if ( ! empty( $value['id'] ) ) {
			return absint( $value['id'] );
		}
	}

	return 0;
};

$items = [];

foreach ( $raw_items as $item ) {
	if ( ! is_array( $item ) ) {
		continue;
	}

	$image_id = $resolve_image_id(
		$item['image_id'] ?? ( $item['image'] ?? 0 )
	);

	$mobile_image_id = $resolve_image_id(
		$item['mobile_image_id'] ?? ( $item['mobile_image'] ?? 0 )
	);

	if ( ! $image_id && ! $mobile_image_id ) {
		continue;
	}

	$items[] = [
		'image_id'        => $image_id,
		'mobile_image_id' => $mobile_image_id,
	];
}

if ( empty( $items ) && ! $show_placeholders ) {
	return;
}

$section_heading_id = function_exists( 'wp_unique_id' )
	? wp_unique_id( 'transactional-archive-promotions-' )
	: 'transactional-archive-promotions-heading';
?>

<section
	class="retail-archive-promotions mb-5"
	aria-labelledby="<?php echo esc_attr( $section_heading_id ); ?>"
>
	<h2 id="<?php echo esc_attr( $section_heading_id ); ?>" class="sr-only">
		<?php
		echo esc_html(
			sprintf(
				'Current %s promotions',
				$context_title
			)
		);
		?>
	</h2>

	<?php
	/*
	 * Always reserve three equal columns from the small breakpoint upwards.
	 * One or two supplied panels occupy only their own columns, leaving the
	 * remaining grid columns empty rather than stretching the supplied images.
	 */
	?>
	<div class="retail-archive-promotions__grid grid gap-3 sm:grid-cols-3 sm:gap-4">

		<?php if ( ! empty( $items ) ) : ?>

			<?php foreach ( $items as $index => $item ) : ?>
				<?php
				$image_id = $item['image_id'] ?: $item['mobile_image_id'];

				$image_attributes = [
					'class'    => 'retail-archive-promotions__image block h-auto w-full',
					'decoding' => 'async',
					'loading'  => 0 === $index ? 'eager' : 'lazy',
					'sizes'    => '(max-width: 639px) calc(100vw - 2rem), (min-width: 640px) 33vw',
				];

				if ( 0 === $index ) {
					$image_attributes['fetchpriority'] = 'high';
				}

				$mobile_srcset = $item['mobile_image_id']
					? wp_get_attachment_image_srcset( $item['mobile_image_id'], 'full' )
					: '';
				?>

				<div class="retail-archive-promotions__panel overflow-hidden border border-neutral-200 bg-neutral-100">
					<picture class="retail-archive-promotions__picture block w-full">
						<?php if ( $item['mobile_image_id'] ) : ?>
							<source
								media="(max-width: 639px)"
								srcset="<?php echo esc_attr( $mobile_srcset ?: wp_get_attachment_image_url( $item['mobile_image_id'], 'full' ) ); ?>"
							>
						<?php endif; ?>

						<?php
						echo wp_get_attachment_image(
							$image_id,
							'full',
							false,
							$image_attributes
						);
						?>
					</picture>
				</div>

			<?php endforeach; ?>

		<?php else : ?>

			<?php for ( $panel_number = 1; $panel_number <= 3; $panel_number++ ) : ?>
				<div class="retail-archive-promotions__placeholder flex aspect-[3/2] items-center justify-center border border-dashed border-neutral-300 bg-neutral-50 px-4 text-center">
					<div>
						<p class="text-xs font-semibold uppercase tracking-[0.12em] text-blue-700">
							Promotion panel <?php echo esc_html( $panel_number ); ?>
						</p>
						<p class="mt-2 text-sm leading-relaxed text-neutral-500">
							Add a 600 × 400 px image in the department page’s Electrical archive promotions fields.
						</p>
					</div>
				</div>
			<?php endfor; ?>

		<?php endif; ?>

	</div>
</section>
