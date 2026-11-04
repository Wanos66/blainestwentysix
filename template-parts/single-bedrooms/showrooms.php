<?php
/**
 * Showrooms block for Single Bedroom
 */

$showroomDisplays = get_field('showroom_display');

if ( ! $showroomDisplays ) {
    return;
}
?>

<div class="mt-6">

    <p class="text-slate-900 mb-3 font-semibold">
        On display in these showrooms
    </p>

    <div class="flex flex-col gap-1 text-sm text-slate-700">

        <?php foreach ( $showroomDisplays as $display ) :
            // Slugify for URL
            $slug = sanitize_title( $display );
            $url  = site_url( '/showroom/' . $slug );
            ?>
            <a href="<?php echo esc_url( $url ); ?>" class="hover:underline capitalize">
                Blaines – <?php echo esc_html( $display ); ?>
            </a>
        <?php endforeach; ?>

    </div>

</div>

