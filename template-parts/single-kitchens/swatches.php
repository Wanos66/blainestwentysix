<?php
/**
 * Swatches block for Single Kitchen
 */

$post_id          = $args['post_id'] ?? get_the_ID();
$is_freedom_mode  = ! empty( $args['is_freedom_mode'] );

$standard_swatches = get_field( 'standard_door_swatches', $post_id );
$freedom_swatches  = get_field( 'freedom_door_swatches', $post_id );

$standard_note = get_field( 'standard_door_swatches_note', $post_id );
$freedom_note  = get_field( 'freedom_door_swatches_note', $post_id );

/**
 * Freedom mode override:
 * - use Freedom swatches if present
 * - otherwise fall back to standard swatches
 */
$active_swatches = $standard_swatches;

if ( $is_freedom_mode && ! empty( $freedom_swatches ) ) {
    $active_swatches = $freedom_swatches;
}

/**
 * Freedom mode note override:
 * - use Freedom note if present
 * - otherwise fall back to standard note
 */
$active_note = $standard_note;

if ( $is_freedom_mode && ! empty( $freedom_note ) ) {
    $active_note = $freedom_note;
}

if ( empty( $active_swatches ) && empty( $active_note ) ) {
    return;
}
?>

<div class="space-y-5">

    <?php if ( ! empty( $active_swatches ) ) : ?>
        <div>
            <p class="mb-3 font-body text-sm font-medium tracking-wide text-slate-900">
                Available in
            </p>

            <div class="flex flex-wrap gap-4">
                <?php foreach ( $active_swatches as $swatch ) : ?>
                    <div class="flex w-20 flex-col items-center text-center">
                        <img
                                src="<?php echo esc_url( $swatch['sizes']['thumbnail'] ?? $swatch['url'] ?? '' ); ?>"
                                alt="<?php echo esc_attr( $swatch['alt'] ?? $swatch['caption'] ?? '' ); ?>"
                                class="h-14 w-14 rounded-full object-cover"
                        >

                        <?php if ( ! empty( $swatch['caption'] ) ) : ?>
                            <span class="mt-1 font-body text-xs leading-tight text-slate-600">
                                <?php echo esc_html( $swatch['caption'] ); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if ( ! empty( $active_note ) ) : ?>
        <div class="prose-site prose-news max-w-none">
            <?php echo wp_kses_post( $active_note ); ?>
        </div>
    <?php endif; ?>

</div>