<?php
/**
 * Accessible Kitchens: Intro
 */

$section_title     = get_field( 'accessible_intro_title' );
$intro_lead        = get_field( 'accessible_intro_lead' );
$intro_supporting  = get_field( 'accessible_intro_supporting' );
$highlights        = get_field( 'accessible_intro_highlights' );

$highlights = is_array( $highlights ) ? $highlights : [];

if ( ! $section_title && ! $intro_lead && ! $intro_supporting && empty( $highlights ) ) {
    return;
}
?>

<section class="pt-16 pb-8 lg:pt-20 lg:pb-10" aria-labelledby="accessible-intro-heading">
    <div class="prose-site mx-auto max-w-7xl px-6 lg:px-8">

        <div class="grid gap-10 lg:grid-cols-12 lg:gap-12">
            <div class="lg:col-span-7">
                <?php if ( $section_title ) : ?>
                    <h2 id="accessible-intro-heading" class="max-w-4xl text-3xl tracking-wide text-neutral-900 sm:text-4xl">
                        <?php echo esc_html( $section_title ); ?>
                    </h2>
                <?php endif; ?>

                <?php if ( $intro_lead || $intro_supporting ) : ?>
                    <div class="mt-6 max-w-3xl space-y-5 text-base leading-7 text-neutral-700">
                        <?php if ( $intro_lead ) : ?>
                            <div class="text-lg tracking-wide leading-8 text-neutral-800">
                                <?php echo wp_kses_post( wpautop( $intro_lead ) ); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ( $intro_supporting ) : ?>
                            <div>
                                <?php echo wp_kses_post( wpautop( $intro_supporting ) ); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ( ! empty( $highlights ) ) : ?>
                <div class="lg:col-span-5">
                    <div class="grid gap-4 sm:grid-cols-3 lg:grid-cols-1">
                        <?php foreach ( $highlights as $highlight ) : ?>
                            <?php
                            $title = $highlight['title'] ?? '';
                            $text  = $highlight['text'] ?? '';

                            if ( ! $title && ! $text ) {
                                continue;
                            }
                            ?>
                            <article class="border border-neutral-200 bg-white px-5 py-5">
                                <div class="mb-4 h-1 w-12 bg-[var(--freedom-accent)]" aria-hidden="true"></div>

                                <?php if ( $title ) : ?>
                                    <h3 class="text-lg tracking-wide text-neutral-900">
                                        <?php echo esc_html( $title ); ?>
                                    </h3>
                                <?php endif; ?>

                                <?php if ( $text ) : ?>
                                    <div class="mt-2 text-sm leading-6 text-neutral-700">
                                        <?php echo wp_kses_post( wpautop( $text ) ); ?>
                                    </div>
                                <?php endif; ?>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </div>
</section>