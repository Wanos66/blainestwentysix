<?php
/**
 * Swatches block for Single Bathroom
 */

$standard = get_field('standard_door_swatches');
?>

<?php if ( $standard ) : ?>

    <div class="space-y-8">

        <!-- STANDARD DOOR COLOURS -->
        <?php if ( $standard ) : ?>
            <div>
                <p class="font-body font-medium tracking-wide text-slate-900 mb-3">
                    Available in
                </p>

                <div class="flex flex-wrap gap-4">
                    <?php foreach ( $standard as $swatch ) : ?>
                        <div class="flex flex-col items-center text-center w-20">
                            <img
                                src="<?php echo esc_url( $swatch['sizes']['thumbnail'] ); ?>"
                                alt="<?php echo esc_attr( $swatch['alt'] ); ?>"
                                class="w-14 h-14 object-cover rounded-full"
                            >
                            <?php if ( ! empty( $swatch['caption'] ) ) : ?>
                                <span class="mt-1 text-xs font-body text-slate-600 leading-tight">
                                    <?php echo esc_html( $swatch['caption'] ); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

    </div>

<?php endif; ?>

