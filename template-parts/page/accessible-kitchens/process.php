<?php
/**
 * Accessible Kitchens: Our Accessible Kitchen Process
 */

$section_title = get_field( 'accessible_process_title' );
$section_intro = get_field( 'accessible_process_intro' );
$steps         = get_field( 'accessible_process_steps' );

$steps = is_array( $steps ) ? $steps : [];

if ( ! $section_title && ! $section_intro && empty( $steps ) ) {
    return;
}

$step_count   = count( $steps );
$split_index  = (int) ceil( $step_count / 2 );
$left_steps   = array_slice( $steps, 0, $split_index );
$right_steps  = array_slice( $steps, $split_index );
?>

<section class="border-t border-neutral-200 py-16 lg:py-20" aria-labelledby="accessible-process-heading">
    <div class="container-wide">

        <?php if ( $section_title || $section_intro ) : ?>
            <div class="max-w-3xl">
                <?php if ( $section_title ) : ?>
                    <h2 id="accessible-process-heading" class="font-heading text-3xl font-medium tracking-wide text-neutral-900 sm:text-4xl">
                        <?php echo esc_html( $section_title ); ?>
                    </h2>
                <?php endif; ?>

                <?php if ( $section_intro ) : ?>
                    <div class="prose-site prose-news mt-4 max-w-none text-neutral-700">
                        <?php echo wp_kses_post( wpautop( $section_intro ) ); ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ( ! empty( $steps ) ) : ?>
            <div class="mt-10 grid grid-cols-1 gap-6 lg:mt-12 lg:grid-cols-2 lg:gap-8">

                <!-- Left column -->
                <div class="space-y-6">
                    <?php foreach ( $left_steps as $index => $step ) : ?>
                        <?php
                        $title       = $step['title'] ?? '';
                        $description = $step['description'] ?? '';

                        if ( ! $title && ! $description ) {
                            continue;
                        }

                        $step_number = $index + 1;
                        ?>
                        <article class="border border-neutral-200 bg-white px-6 py-6 lg:px-8 lg:py-8">
                            <div class="flex items-start gap-5 lg:gap-6">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center bg-[var(--freedom-accent)] font-body text-sm tracking-wide text-white">
                                    <?php echo esc_html( str_pad( (string) $step_number, 2, '0', STR_PAD_LEFT ) ); ?>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <?php if ( $title ) : ?>
                                        <h3 class="font-heading text-xl font-medium tracking-wide text-neutral-900">
                                            <?php echo esc_html( $title ); ?>
                                        </h3>
                                    <?php endif; ?>

                                    <?php if ( $description ) : ?>
                                        <div class="prose-site mt-3 max-w-none text-sm text-neutral-700 sm:text-base">
                                            <?php echo wp_kses_post( wpautop( $description ) ); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <!-- Right column -->
                <div class="space-y-6">
                    <?php foreach ( $right_steps as $index => $step ) : ?>
                        <?php
                        $title       = $step['title'] ?? '';
                        $description = $step['description'] ?? '';

                        if ( ! $title && ! $description ) {
                            continue;
                        }

                        $step_number = $split_index + $index + 1;
                        ?>
                        <article class="border border-neutral-200 bg-white px-6 py-6 lg:px-8 lg:py-8">
                            <div class="flex items-start gap-5 lg:gap-6">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center bg-[var(--freedom-accent)] font-body text-sm tracking-wide text-white">
                                    <?php echo esc_html( str_pad( (string) $step_number, 2, '0', STR_PAD_LEFT ) ); ?>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <?php if ( $title ) : ?>
                                        <h3 class="font-heading text-xl font-medium tracking-wide text-neutral-900">
                                            <?php echo esc_html( $title ); ?>
                                        </h3>
                                    <?php endif; ?>

                                    <?php if ( $description ) : ?>
                                        <div class="prose-site font-body mt-3 max-w-none text-sm text-neutral-700 sm:text-base">
                                            <?php echo wp_kses_post( wpautop( $description ) ); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

            </div>
        <?php endif; ?>

    </div>
</section>