<?php
/**
 * Accessible Kitchens: Transition Image
 */

$image_id = get_field( 'accessible_transition_image' );

if ( ! $image_id ) {
    return;
}
?>

<section class="pt-8 pb-4 lg:pt-10 lg:pb-6" aria-hidden="true">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="aspect-[21/7] overflow-hidden bg-neutral-100">
            <?php
            echo wp_get_attachment_image(
                $image_id,
                'full',
                false,
                [
                    'class'   => 'h-full w-full object-cover',
                    'loading' => 'lazy',
                ]
            );
            ?>
        </div>
    </div>
</section>
