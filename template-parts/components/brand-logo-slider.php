<?php
/**
 * Brand Logo Slider (Options-driven)
 *
 * Args:
 * - variant (string): interiors | electrical | home
 * - heading (string|null)
 * - intro (string|null)
 * - items (array|null)
 * - autoplay (bool|null)
 * - speed (int|null)
 */

$variant = $args['variant'] ?? 'interiors';
$allowed = [ 'interiors', 'electrical', 'home' ];

if ( ! in_array( $variant, $allowed, true ) ) {
    $variant = 'interiors';
}


// -------------------------
// Load from Options
// -------------------------

$heading  = $args['heading']
            ?? get_field( "brand_slider_{$variant}_heading", 'option' );

$intro = $args['intro']
         ?? get_field( "brand_slider_{$variant}_intro", 'option' );

$items = $args['items']
         ?? get_field( "brand_slider_{$variant}_items", 'option' );

$autoplay_opt = get_field( "brand_slider_{$variant}_autoplay", 'option' );
$speed_opt    = get_field( "brand_slider_{$variant}_speed", 'option' );

// Defaults if fields missing or empty
$autoplay_default = ($autoplay_opt === null) ? true : (bool) $autoplay_opt;
$speed_default    = ($speed_opt === null || (int)$speed_opt <= 0) ? 40 : (int) $speed_opt;

$autoplay = array_key_exists('autoplay', $args)
    ? (bool) $args['autoplay']
    : $autoplay_default;

$speed = array_key_exists('speed', $args)
    ? (int) $args['speed']
    : $speed_default;


if ( empty( $items ) ) {
    return;
}

$uid = 'brand-slider-' . wp_unique_id();
?>


<section class="w-full bg-neutral-600 text-white py-4 md:py-6">

    <div class="container-wide mx-auto px-6">

        <?php if ( $heading || $intro ) : ?>

            <header class="max-w-3xl mx-auto text-center">

                <?php if ( $heading ) : ?>
                    <h2 class="text-2xl md:text-3xl font-semibold">
                        <?php echo esc_html( $heading ); ?>
                    </h2>
                <?php endif; ?>

                <?php if ( $intro ) : ?>
                    <div class="mt-3 text-white/80 leading-relaxed">
                        <?php echo wp_kses_post( $intro ); ?>
                    </div>
                <?php endif; ?>

            </header>

        <?php endif; ?>

    </div>


    <!-- FULL WIDTH SLIDER -->

    <div
            id="<?php echo esc_attr( $uid ); ?>"
            class="relative overflow-hidden"
            data-brand-marquee="1"
            data-speed="<?php echo esc_attr( $speed ); ?>"
            data-autoplay="<?php echo esc_attr( $autoplay ? '1' : '0' ); ?>"
    >

        <!-- LEFT FADE -->
        <div class="pointer-events-none absolute inset-y-0 left-0 w-24 bg-gradient-to-r from-neutral-600 to-transparent z-10"></div>

        <!-- RIGHT FADE -->
        <div class="pointer-events-none absolute inset-y-0 right-0 w-24 bg-gradient-to-l from-neutral-600 to-transparent z-10"></div>


        <div class="py-6">

            <div
                    class="flex gap-12 items-center will-change-transform px-6"
                    data-track
            >

                <?php

                $render = function( $row ) {

                    $logo = $row['logo'] ?? null;
                    if ( ! $logo ) return;

                    $url  = trim( (string) ( $row['url'] ?? '' ) );
                    $name = trim( (string) ( $row['name'] ?? '' ) );

                    $alt  = $logo['alt'] ?? $name ?? '';
                    $id   = $logo['ID'] ?? 0;


                    $img = $id
                        ? wp_get_attachment_image(
                            $id,
                            'medium',
                            false,
                            [
                                'class'    => 'h-10 md:h-12 w-auto object-contain opacity-80 hover:opacity-100 transition',
                                'loading'  => 'lazy',
                                'decoding' => 'async',
                                'alt'      => esc_attr( $alt ),
                            ]
                        )
                        : '<img
                                class="h-10 md:h-12 w-auto object-contain opacity-80 hover:opacity-100 transition"
                                src="' . esc_url( $logo['url'] ?? '' ) . '"
                                alt="' . esc_attr( $alt ) . '"
                                loading="lazy"
                                decoding="async"
                           >';


                    $inner =
                        '<div class="flex items-center justify-center min-w-[140px]">'
                        . $img .
                        '</div>';


                    if ( $url ) {

                        echo
                            '<a
                                class="block"
                                href="' . esc_url( $url ) . '"
                                target="_blank"
                                rel="noopener"
                                aria-label="' . esc_attr( $name ?: 'Brand' ) . '"
                            >'
                            . $inner .
                            '</a>';

                    } else {

                        echo $inner;

                    }

                };


                // duplicate for seamless loop

                foreach ( $items as $row ) $render( $row );
                foreach ( $items as $row ) $render( $row );

                ?>

            </div>

        </div>

    </div>

</section>