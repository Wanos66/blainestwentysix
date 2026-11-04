<?php
/**
 * Accessible Kitchens: Key Accessibility Features
 */

$section_title = get_field( 'key_accessibility_features_title' );
$section_intro = get_field( 'key_accessibility_features_intro' );
$features      = get_field( 'key_accessibility_features_items' );

$features = is_array( $features ) ? $features : [];

if ( ! $section_title && ! $section_intro && empty( $features ) ) {
    return;
}

$initial_visible = 4;
$feature_count   = count( $features );
?>

<section class="py-16 lg:py-20" data-accessibility-features-section aria-labelledby="accessible-features-heading">
    <div class="prose-site mx-auto max-w-7xl px-6 lg:px-8">

        <?php if ( $section_title || $section_intro ) : ?>
            <div class="max-w-3xl">
                <?php if ( $section_title ) : ?>
                    <h2 id="accessible-features-heading" class="text-3xl tracking-wide text-neutral-900 sm:text-4xl">
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

        <?php if ( ! empty( $features ) ) : ?>
        <div class="mt-10">
            <div class="overflow-hidden transition-[max-height] duration-500 ease-in-out" data-feature-grid-wrap>
                <div class="grid gap-x-6 gap-y-10 md:grid-cols-2 xl:grid-cols-4">
                    <?php foreach ( $features as $index => $feature ) : ?>
                        <?php
                        $image_id    = $feature['image'] ?? '';
                        $title       = $feature['title'] ?? '';
                        $description = $feature['description'] ?? '';

                        if ( ! $image_id && ! $title && ! $description ) {
                            continue;
                        }

                        $is_collapsed = $index >= $initial_visible;
                        ?>
                        <article class="feature-item">
                            <?php if ( $image_id ) : ?>
                                <div class="aspect-[4/3] overflow-hidden bg-neutral-100">
                                    <?php
                                    echo wp_get_attachment_image(
                                        $image_id,
                                        'large',
                                        false,
                                        [
                                            'class'   => 'h-full w-full object-cover',
                                            'loading' => $index < $initial_visible ? 'eager' : 'lazy',
                                        ]
                                    );
                                    ?>
                                </div>
                            <?php endif; ?>

                            <div class="mt-4">
                                <?php if ( $title ) : ?>
                                    <h3 class="text-lg tracking-wide font-medium text-neutral-900">
                                        <?php echo esc_html( $title ); ?>
                                    </h3>
                                <?php endif; ?>

                                <?php if ( $description ) : ?>
                                    <div class="mt-2 text-sm leading-6 text-neutral-700">
                                        <?php echo wp_kses_post( wpautop( $description ) ); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>

                <?php if ( $feature_count > $initial_visible ) : ?>
                    <div class="mt-10 text-center">
                        <button
                                type="button"
                                class="inline-flex items-center justify-center bg-[var(--freedom-accent)] px-6 py-3 text-sm font-body font-medium text-white transition
                                hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-[var(--freedom-accent)] focus:ring-offset-2"
                                data-feature-toggle
                                data-show-more-text="View All Features"
                                data-show-less-text="Show Less"
                                aria-expanded="false"
                        >
                            View All Features
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>
</section>