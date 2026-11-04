<?php
/**
 * Accessible Kitchens: Key Design Considerations
 */

$section_title = get_field( 'accessible_considerations_title' );
$section_intro = get_field( 'accessible_considerations_intro' );
$items         = get_field( 'accessible_considerations_items' );

$items = is_array( $items ) ? $items : [];

if ( ! $section_title && ! $section_intro && empty( $items ) ) {
    return;
}
?>

<section class="border-t border-neutral-200 py-16 lg:py-20" aria-labelledby="accessible-considerations-heading">
    <div class="prose-site mx-auto max-w-7xl px-6 lg:px-8">

        <?php if ( $section_title || $section_intro ) : ?>
            <div class="max-w-2xl">
                <?php if ( $section_title ) : ?>
                    <h2 id="accessible-considerations-heading" class="text-3xl tracking-wide text-neutral-900 sm:text-4xl">
                        <?php echo esc_html( $section_title ); ?>
                    </h2>
                <?php endif; ?>

                <?php if ( $section_intro ) : ?>
                    <div class="mt-4 leading-7 text-neutral-700">
                        <?php echo wp_kses_post( wpautop( $section_intro ) ); ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ( ! empty( $items ) ) : ?>
            <div class="mt-10 grid gap-8 md:grid-cols-2 xl:grid-cols-3 lg:mt-12">
                <?php foreach ( $items as $item ) : ?>
                    <?php
                    $image_id    = $item['image'] ?? '';
                    $title       = $item['title'] ?? '';
                    $description = $item['description'] ?? '';

                    if ( ! $image_id && ! $title && ! $description ) {
                        continue;
                    }
                    ?>
                    <article class="flex h-full flex-col bg-white">
                        <?php if ( $image_id ) : ?>
                            <div class="aspect-[4/3] overflow-hidden bg-neutral-100">
                                <?php
                                echo wp_get_attachment_image(
                                    $image_id,
                                    'large',
                                    false,
                                    [
                                        'class'   => 'h-full w-full object-cover',
                                        'loading' => 'lazy',
                                    ]
                                );
                                ?>
                            </div>
                        <?php endif; ?>

                        <div class="pt-5">
                            <?php if ( $title ) : ?>
                                <h3 class="text-xl font-medium tracking-wide text-neutral-900">
                                    <?php echo esc_html( $title ); ?>
                                </h3>
                            <?php endif; ?>

                            <?php if ( $description ) : ?>
                                <div class="mt-4 text-sm leading-6 text-neutral-700 sm:text-base">
                                    <?php echo wp_kses_post( wpautop( $description ) ); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</section>