<?php
/**
 * Active promotion titles for a transactional product archive row.
 *
 * Expected args:
 * - product WC_Product
 * - promotions array Optional preloaded active promotions.
 */

defined( 'ABSPATH' ) || exit;

$product = $args['product'] ?? null;

if ( ! $product instanceof WC_Product ) {
    $product = wc_get_product( get_the_ID() );
}

if ( ! $product ) {
    return;
}

$promotions = isset( $args['promotions'] ) && is_array( $args['promotions'] )
    ? $args['promotions']
    : ( function_exists( 'blaines_get_product_promotions' )
        ? blaines_get_product_promotions( $product->get_id(), true )
        : [] );

$promotions = array_values(
    array_filter(
        is_array( $promotions ) ? $promotions : [],
        static function ( $promotion ) {
            return is_array( $promotion )
                && ( ! array_key_exists( 'active', $promotion ) || ! empty( $promotion['active'] ) )
                && isset( $promotion['title'] )
                && '' !== trim( (string) $promotion['title'] );
        }
    )
);

if ( empty( $promotions ) ) {
    return;
}

/*
 * If the archive media column is already representing a promotion with a
 * roundel, do not repeat that same promotion as a text chip. Additional
 * promotions remain visible here.
 */
$roundel_promotion_skipped = false;

$promotions = array_values(
    array_filter(
        $promotions,
        static function ( array $promotion ) use ( &$roundel_promotion_skipped ): bool {
            if ( $roundel_promotion_skipped ) {
                return true;
            }

            $roundel = isset( $promotion['roundel'] ) && is_array( $promotion['roundel'] )
                ? $promotion['roundel']
                : [];

            $roundel_text = trim(
                (string) (
                    $roundel['display_text']
                    ?? $roundel['text']
                    ?? $promotion['title']
                    ?? ''
                )
            );

            if ( ! empty( $roundel['enabled'] ) && '' !== $roundel_text ) {
                $roundel_promotion_skipped = true;
                return false;
            }

            return true;
        }
    )
);

if ( empty( $promotions ) ) {
    return;
}

$visible_promotions = array_slice( $promotions, 0, 2 );
$remaining_count    = max( 0, count( $promotions ) - count( $visible_promotions ) );
$product_url        = get_permalink( $product->get_id() );

if ( ! $product_url ) {
    return;
}
?>

<div class="flex flex-wrap items-center gap-2" aria-label="Current promotions">
    <?php foreach ( $visible_promotions as $promotion ) : ?>
        <?php
        $classes = function_exists( 'blaines_get_transactional_promotion_classes' )
            ? blaines_get_transactional_promotion_classes( $promotion['style'] ?? 'blue' )
            : [
                'panel' => 'border-blue-200 bg-blue-50',
                'title' => 'text-blue-950',
            ];
        ?>

        <a
            href="<?php echo esc_url( $product_url . '#product-promotions' ); ?>"
            class="<?php echo esc_attr( $classes['panel'] . ' ' . $classes['title'] ); ?> inline-flex min-h-10 items-center border px-3 py-2 text-sm font-semibold leading-tight transition hover:brightness-95 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-700 focus-visible:ring-offset-2"
        >
            <?php echo esc_html( (string) $promotion['title'] ); ?>
        </a>
    <?php endforeach; ?>

    <?php if ( $remaining_count > 0 ) : ?>
        <a
            href="<?php echo esc_url( $product_url . '#product-promotions' ); ?>"
            class="inline-flex min-h-10 items-center border border-neutral-300 bg-neutral-50 px-3 py-2 text-xs font-semibold text-neutral-700 transition hover:border-neutral-950 hover:text-neutral-950 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-700 focus-visible:ring-offset-2"
        >
            +<?php echo esc_html( (string) $remaining_count ); ?> more
        </a>
    <?php endif; ?>
</div>
