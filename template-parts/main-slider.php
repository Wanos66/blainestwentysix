<?php
/**
 * Shared promotional slider.
 *
 * Optional arguments:
 * - aria_label: Accessible carousel label.
 * - source_id:  Post ID containing the ACF slider fields.
 * - context:    Optional display context: front_page or electrical.
 *
 * @package Blaines
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args(
    $args ?? [],
    [
        'aria_label' => 'Promotional slider',
        'source_id'  => get_queried_object_id(),
        'context'    => is_front_page() ? 'front_page' : '',
    ]
);

$source_id = (int) $args['source_id'];
$context   = sanitize_key( (string) $args['context'] );

$slides = function_exists( 'get_field' )
    ? get_field( 'slider', $source_id )
    : [];

if ( ! is_array( $slides ) || empty( $slides ) ) {
    return;
}

/*
 * A slide can optionally be assigned to the front page, the Electrical
 * landing page, or both through the ACF display_locations checkbox.
 *
 * Existing rows without the new field remain visible in every context so
 * introducing the field cannot unexpectedly remove live campaign banners.
 */
if ( in_array( $context, [ 'front_page', 'electrical' ], true ) ) {
    $slides = array_values(
        array_filter(
            $slides,
            static function ( $slide ) use ( $context ) {
                if ( ! is_array( $slide ) ) {
                    return false;
                }

                $locations = $slide['display_locations'] ?? [];

                if ( ! is_array( $locations ) ) {
                    $locations = [ $locations ];
                }

                $locations = array_values(
                    array_filter(
                        array_map(
                            'sanitize_key',
                            $locations
                        )
                    )
                );

                return empty( $locations )
                    || in_array( $context, $locations, true );
            }
        )
    );
}

if ( empty( $slides ) ) {
    return;
}

$slide_count = count( $slides );

$delay = (int) (
get_field( 'slider_autoplay_delay', $source_id )
    ?: 5000
);

$delay = max( 0, min( $delay, 60000 ) );

$transition = (int) (
get_field( 'slider_transition_duration', $source_id )
    ?: 700
);

$transition = max( 0, min( $transition, 5000 ) );
?>

<div
        id="acfSlider"
        class="retail-slider relative h-[300px] w-full overflow-hidden bg-zinc-900 md:h-[400px]"
        tabindex="0"
        data-autoplay-delay="<?php echo esc_attr( (string) $delay ); ?>"
        data-transition-duration="<?php echo esc_attr( (string) $transition ); ?>"
        data-slide-count="<?php echo esc_attr( (string) $slide_count ); ?>"
        aria-roledescription="carousel"
        aria-label="<?php echo esc_attr( $args['aria_label'] ); ?>"
>

    <div
            id="slider"
            class="retail-slider__track relative h-full w-full select-none"
    >

        <?php foreach ( $slides as $index => $slide ) : ?>

            <?php
            $image        = $slide['image'] ?? null;
            $mobile_image = $slide['mobile_image'] ?? null;
            $link         = $slide['link'] ?? '';

            if ( empty( $mobile_image ) && ! empty( $image ) ) {
                $mobile_image = $image;
            }

            $is_active = 0 === $index;

            $state_classes = $is_active
                ? 'opacity-100 pointer-events-auto'
                : 'opacity-0 pointer-events-none';
            ?>

            <div
                    class="retail-slider__slide absolute inset-0 h-full w-full transition-opacity duration-700 ease-in-out <?php echo esc_attr( $state_classes ); ?>"
                    data-index="<?php echo esc_attr( (string) $index ); ?>"
                    aria-hidden="<?php echo $is_active ? 'false' : 'true'; ?>"
            >

                <?php if ( $link ) : ?>
                <a
                        class="retail-slider__link block h-full w-full"
                        href="<?php echo esc_url( $link ); ?>"
                >
                    <?php endif; ?>

                    <?php if ( is_array( $image ) && ! empty( $image['url'] ) ) : ?>

                        <img
                                src="<?php echo esc_url( $image['url'] ); ?>"
                                alt="<?php echo esc_attr( $image['alt'] ?? '' ); ?>"
                                class="retail-slider__image hidden h-full w-full object-cover md:block"
                                loading="<?php echo 0 === $index ? 'eager' : 'lazy'; ?>"
                                fetchpriority="<?php echo 0 === $index ? 'high' : 'auto'; ?>"
                                decoding="async"
                        >

                    <?php endif; ?>

                    <?php if ( is_array( $mobile_image ) && ! empty( $mobile_image['url'] ) ) : ?>

                        <img
                                src="<?php echo esc_url( $mobile_image['url'] ); ?>"
                                alt="<?php echo esc_attr(
                                    $mobile_image['alt']
                                    ?? $image['alt']
                                       ?? ''
                                ); ?>"
                                class="retail-slider__image h-full w-full object-cover md:hidden"
                                loading="<?php echo 0 === $index ? 'eager' : 'lazy'; ?>"
                                fetchpriority="<?php echo 0 === $index ? 'high' : 'auto'; ?>"
                                decoding="async"
                        >

                    <?php endif; ?>

                    <?php if ( $link ) : ?>
                </a>
            <?php endif; ?>

            </div>

        <?php endforeach; ?>

    </div>

    <?php if ( $slide_count > 1 ) : ?>

        <button
                id="prevBtn"
                type="button"
                aria-label="Previous slide"
                class="retail-slider__control retail-slider__control--previous absolute left-4 top-1/2 z-10 inline-flex h-12 w-12 -translate-y-1/2 items-center justify-center bg-black/35 p-0 font-body text-white backdrop-blur transition hover:bg-black/45 focus:outline-none focus:ring-2 focus:ring-white/50"
        >
            <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    aria-hidden="true"
                    focusable="false"
            >
                <path d="m15 18-6-6 6-6"></path>
            </svg>
        </button>

        <button
                id="nextBtn"
                type="button"
                aria-label="Next slide"
                class="retail-slider__control retail-slider__control--next absolute right-4 top-1/2 z-10 inline-flex h-12 w-12 -translate-y-1/2 items-center justify-center bg-black/35 p-0 font-body text-white backdrop-blur transition hover:bg-black/45 focus:outline-none focus:ring-2 focus:ring-white/50"
        >
            <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    aria-hidden="true"
                    focusable="false"
            >
                <path d="m9 18 6-6-6-6"></path>
            </svg>
        </button>

        <div
                class="retail-slider__dots absolute bottom-4 left-1/2 z-10 flex -translate-x-1/2 gap-2"
                role="tablist"
                aria-label="Slide navigation"
        >

            <?php for ( $index = 0; $index < $slide_count; $index++ ) : ?>

                <button
                        type="button"
                        class="retail-slider__dot h-2.5 w-2.5 rounded-full <?php echo 0 === $index ? 'bg-white' : 'bg-white/70'; ?> hover:bg-white/80 focus:outline-none focus:ring-2 focus:ring-white/50"
                        data-dot="<?php echo esc_attr( (string) $index ); ?>"
                        aria-label="Go to slide <?php echo esc_attr( (string) ( $index + 1 ) ); ?>"
                        aria-selected="<?php echo 0 === $index ? 'true' : 'false'; ?>"
                        role="tab"
                ></button>

            <?php endfor; ?>

        </div>

    <?php endif; ?>

</div>