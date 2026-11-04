<?php
/**
 * Compact active-promotion highlight inside the unified purchase panel.
 */

defined( 'ABSPATH' ) || exit;

$promotion = isset( $args['promotion'] ) && is_array( $args['promotion'] )
    ? $args['promotion']
    : null;

if ( ! $promotion ) {
    return;
}

$title   = isset( $promotion['title'] ) ? trim( (string) $promotion['title'] ) : '';
$summary = isset( $promotion['summary'] ) ? trim( (string) $promotion['summary'] ) : '';

$is_black_friday =
    false !== stripos( $title, 'black friday' )
    || false !== stripos( $summary, 'black friday' );

if ( '' === $title && '' === $summary ) {
    return;
}

if ( $is_black_friday ) {
    $classes = [
        'panel' => 'relative overflow-hidden border-neutral-950 bg-neutral-950',
        'label' => 'text-amber-300',
        'title' => 'text-white',
        'text'  => 'text-neutral-200',
        'link'  => 'text-white',
    ];
} else {
    $classes = function_exists( 'blaines_get_transactional_promotion_classes' )
        ? blaines_get_transactional_promotion_classes(
            $promotion['style'] ?? 'blue'
        )
        : [
            'panel' => 'border-blue-200 bg-blue-50',
            'label' => 'text-blue-700',
            'title' => 'text-blue-950',
            'text'  => 'text-blue-900',
            'link'  => 'text-blue-950',
        ];
}

$period_label = function_exists( 'blaines_get_transactional_promotion_period_label' )
    ? blaines_get_transactional_promotion_period_label( $promotion )
    : '';
?>

<div class="border-t border-neutral-200 px-5 py-5 sm:px-6 lg:px-7">
    <div class="<?php echo esc_attr( $classes['panel'] ); ?> border px-4 py-4 <?php echo $is_black_friday ? 'pl-5 sm:px-5 sm:pl-6' : ''; ?>">
        <?php if ( $is_black_friday ) : ?>
            <span
                    class="absolute inset-y-0 left-0 w-1 bg-amber-300"
                    aria-hidden="true"
            ></span>
        <?php endif; ?>

        <p class="<?php echo esc_attr( $classes['label'] ); ?> text-xs font-semibold uppercase tracking-[0.14em]">
            <?php echo $is_black_friday ? 'Black Friday' : 'Promotion'; ?>
        </p>

        <?php if ( '' !== $title ) : ?>
            <p class="<?php echo esc_attr( $classes['title'] ); ?> mt-2 text-lg font-semibold leading-snug">
                <?php echo esc_html( $title ); ?>
            </p>
        <?php endif; ?>

        <?php if ( '' !== $summary ) : ?>
            <p class="<?php echo esc_attr( $classes['text'] ); ?> mt-1.5 text-sm font-light leading-relaxed">
                <?php echo esc_html( $summary ); ?>
            </p>
        <?php endif; ?>

        <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs">
            <?php if ( '' !== $period_label ) : ?>
                <span class="<?php echo esc_attr( $classes['text'] ); ?> font-medium">
                    <?php echo esc_html( $period_label ); ?>
                </span>
            <?php endif; ?>

            <a
                class="<?php echo esc_attr( $classes['link'] ); ?> font-semibold underline underline-offset-4 hover:no-underline"
                href="#product-promotions"
                data-open-product-promotions
            >Promotion details</a>
        </div>
    </div>
</div>
