<?php
/**
 * page-header.php
 */

$hero               = $args['hero'] ?? null;
$title              = $args['title'] ?? '';
$intro              = $args['intro'] ?? '';
$breadcrumbs        = $args['breadcrumbs'] ?? true;
$align              = $args['align'] ?? 'center';
$logo               = $args['logo'] ?? null;
$hero_overlay_title = $args['hero_overlay_title'] ?? '';
$show_overlay       = $args['show_overlay'] ?? true;

$hero_style  = $args['hero_style'] ?? 'center'; // center | panel | editorial
$panel_side  = $args['panel_side'] ?? 'left';   // left | right
$hero_height = $args['hero_height'] ?? '';
$show_title  = $args['show_title'] ?? true;

$has_logo          = ! empty( $logo );
$has_overlay_title = ( $hero_overlay_title !== '' );
$is_panel          = $hero_style === 'panel';
$is_editorial      = $hero_style === 'editorial';

$rendered_title = '';

if ( is_string( $title ) && $title !== '' ) {
    $rendered_title = wp_kses_post( $title );

    // Remove a single outer <p> wrapper added by WYSIWYG.
    $rendered_title = preg_replace( '#^\s*<p>(.*?)</p>\s*$#is', '$1', $rendered_title );

    // Remove stray line breaks often inserted by WYSIWYG.
    $rendered_title = str_replace( [ '<br>', '<br />', '<br/>' ], '', $rendered_title );
}

$text_align_class = ( $is_panel || $is_editorial )
    ? 'text-left'
    : ( $align === 'left' ? 'text-left' : 'text-center' );

$content_justify_class = ( $is_panel || $is_editorial )
    ? ( $panel_side === 'right' ? 'justify-end' : 'justify-start' )
    : ( $align === 'left' ? 'justify-start' : 'justify-center' );

$mx_auto_class = ( $is_panel || $is_editorial )
    ? ''
    : ( $align === 'left' ? '' : 'mx-auto' );

$hero_height_class = $hero_height
    ? $hero_height
    : (
    $is_editorial
        ? 'h-[420px] md:h-[520px] lg:h-[620px]'
        : ( $is_panel ? 'h-[360px] md:h-[460px] lg:h-[540px]' : 'h-[320px] md:h-[420px]' )
    );

$title_classes = $is_editorial
    ? 'font-heading font-normal leading-[0.95] tracking-[-0.03em] text-white text-4xl md:text-6xl lg:text-[5rem]'
    : (
    $is_panel
        ? 'font-heading leading-[0.95] tracking-[-0.02em] text-white text-4xl md:text-5xl lg:text-[4.25rem]'
        : 'font-heading font-medium leading-[0.95] tracking-[-0.02em] text-white drop-shadow text-4xl md:text-5xl lg:text-[3.75rem]'
    );

$intro_size = $args['intro_size'] ?? '';

if ( $is_editorial ) {
    $intro_classes = 'mt-6 max-w-[32rem] font-body text-base md:text-lg font-light leading-7 md:leading-8 text-white/92 space-y-4 [&_p]:m-0 [&_p]:leading-relaxed';
} elseif ( $is_panel ) {
    $intro_classes = 'mt-5 max-w-[34rem] font-body text-base md:text-lg font-light leading-7 text-white/90 space-y-4 [&_p]:m-0 [&_p]:leading-relaxed';
} elseif ( $has_logo ) {
    $intro_classes = 'mt-4 max-w-[62ch] text-base md:text-lg text-white/90 font-body leading-relaxed space-y-4 [&_p]:m-0 [&_p]:leading-relaxed';
} else {
    $intro_classes = 'mt-5 max-w-[38rem] text-lg md:text-[1.375rem] text-white/95 font-body leading-relaxed space-y-4 [&_p]:m-0 [&_p]:leading-relaxed';
}

if ( $intro_size ) {
    if ( $is_editorial ) {
        $intro_classes = 'mt-6 max-w-[32rem] ' . $intro_size . ' text-white/92 font-body font-light leading-relaxed space-y-4 [&_p]:m-0 [&_p]:leading-relaxed';
    } elseif ( $is_panel ) {
        $intro_classes = 'mt-5 max-w-[34rem] ' . $intro_size . ' text-white/90 font-body font-light leading-relaxed space-y-4 [&_p]:m-0 [&_p]:leading-relaxed';
    } else {
        $intro_classes = 'mt-5 max-w-[38rem] ' . $intro_size . ' text-white/95 font-body leading-relaxed space-y-4 [&_p]:m-0 [&_p]:leading-relaxed';
    }
}

$panel_classes = 'max-w-xl bg-black/28 px-8 py-8 md:px-10 md:py-10 lg:px-12 lg:py-12 backdrop-blur-[2px]';

$editorial_content_classes = $panel_side === 'right'
    ? 'max-w-[34rem] pr-2 md:pr-4 lg:pr-8'
    : 'max-w-[34rem] pl-2 md:pl-4 lg:pl-8';
?>

<section class="w-full">

    <?php if ( $hero ) : ?>
        <div class="relative w-full overflow-hidden <?php echo esc_attr( $hero_height_class ); ?>">

            <?php
            $hero_id = $hero['ID'] ?? 0;

            if ( $hero_id ) {
                echo wp_get_attachment_image(
                    $hero_id,
                    'full',
                    false,
                    [
                        'class'    => 'absolute inset-0 h-full w-full object-cover object-center',
                        'loading'  => 'eager',
                        'decoding' => 'async',
                    ]
                );
            } else {
                ?>
                <img src="<?php echo esc_url( $hero['url'] ?? '' ); ?>"
                        alt="<?php echo esc_attr( $hero['alt'] ?? '' ); ?>"
                        class="absolute inset-0 h-full w-full object-cover object-center"
                        loading="eager"
                        decoding="async">
                <?php
            }
            ?>

            <?php if ( $show_overlay ) : ?>
                <?php if ( $is_editorial ) : ?>
                    <?php if ( $panel_side === 'right' ) : ?>
                        <div class="absolute inset-0 bg-gradient-to-l from-black/72 via-black/30 to-transparent"></div>
                    <?php else : ?>
                        <div class="absolute inset-0 bg-gradient-to-r from-black/72 via-black/30 to-transparent"></div>
                    <?php endif; ?>
                <?php elseif ( $is_panel ) : ?>
                    <div class="absolute inset-0 bg-black/18"></div>
                    <div class="absolute inset-0 bg-gradient-to-r from-black/45 via-black/18 to-black/18"></div>
                <?php else : ?>
                    <div class="absolute inset-0 bg-black/35"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/25 to-black/0"></div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="absolute inset-0 flex items-center">
                <div class="container-wide w-full">
                    <div class="flex <?php echo esc_attr( $content_justify_class ); ?>">

                        <?php if ( $is_editorial ) : ?>
                            <div class="<?php echo esc_attr( $editorial_content_classes ); ?>">
                                <?php if ( $has_logo ) : ?>
                                    <div class="mb-5 flex items-center">
                                        <?php
                                        $logo_id = $logo['ID'] ?? 0;

                                        if ( $logo_id ) {
                                            echo wp_get_attachment_image(
                                                $logo_id,
                                                'large',
                                                false,
                                                [
                                                    'class'    => 'h-14 w-auto object-contain md:h-16 lg:h-[4.5rem]',
                                                    'loading'  => 'eager',
                                                    'decoding' => 'async',
                                                ]
                                            );
                                        } else {
                                            ?>
                                            <img src="<?php echo esc_url( $logo['url'] ?? '' ); ?>"
                                                    alt="<?php echo esc_attr( $logo['alt'] ?? '' ); ?>"
                                                    class="h-14 w-auto object-contain md:h-16 lg:h-[4.5rem]"
                                                    loading="eager"
                                                    decoding="async">
                                            <?php
                                        }
                                        ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ( $has_overlay_title ) : ?>
                                    <div class="whitespace-pre-line <?php echo esc_attr( $title_classes ); ?>">
                                        <?php echo esc_html( $hero_overlay_title ); ?>
                                    </div>
                                <?php elseif ( $title && $show_title ) : ?>
                                    <h1 id="js-archive-title" data-default-title="<?php echo esc_attr( wp_strip_all_tags( $title ) ); ?>" class="<?php echo esc_attr( $title_classes ); ?>">
                                        <?php echo $rendered_title; ?>
                                    </h1>
                                <?php endif; ?>

                                <?php if ( $intro ) : ?>
                                    <div id="js-archive-intro" class="<?php echo esc_attr( $intro_classes ); ?>">
                                        <?php echo wp_kses_post( $intro ); ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                        <?php elseif ( $is_panel ) : ?>
                            <div class="<?php echo esc_attr( $panel_classes ); ?>">
                                <?php if ( $has_logo ) : ?>
                                    <div class="mb-4 flex items-center">
                                        <?php
                                        $logo_id = $logo['ID'] ?? 0;

                                        if ( $logo_id ) {
                                            echo wp_get_attachment_image(
                                                $logo_id,
                                                'large',
                                                false,
                                                [
                                                    'class'    => 'h-14 w-auto object-contain md:h-16 lg:h-[4.5rem]',
                                                    'loading'  => 'eager',
                                                    'decoding' => 'async',
                                                ]
                                            );
                                        } else {
                                            ?>
                                            <img src="<?php echo esc_url( $logo['url'] ?? '' ); ?>"
                                                    alt="<?php echo esc_attr( $logo['alt'] ?? '' ); ?>"
                                                    class="h-14 w-auto object-contain md:h-16 lg:h-[4.5rem]"
                                                    loading="eager"
                                                    decoding="async">
                                            <?php
                                        }
                                        ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ( $has_overlay_title ) : ?>
                                    <div class="whitespace-pre-line <?php echo esc_attr( $title_classes ); ?>">
                                        <?php echo esc_html( $hero_overlay_title ); ?>
                                    </div>
                                <?php elseif ( $title && $show_title ) : ?>
                                    <h1 id="js-archive-title" data-default-title="<?php echo esc_attr( wp_strip_all_tags( $title ) ); ?>" class="<?php echo esc_attr( $title_classes ); ?>">
                                        <?php echo $rendered_title; ?>
                                    </h1>
                                <?php endif; ?>

                                <?php if ( $intro ) : ?>
                                    <div id="js-archive-intro" class="<?php echo esc_attr( $intro_classes ); ?>">
                                        <?php echo wp_kses_post( $intro ); ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                        <?php else : ?>

                            <div class="container-medium-nopad <?php echo esc_attr( $text_align_class ); ?>">

                                <?php if ( $has_logo ) : ?>
                                    <div class="mb-4 flex items-center <?php echo esc_attr( $content_justify_class . ' ' . $mx_auto_class ); ?>">
                                        <?php
                                        $logo_id = $logo['ID'] ?? 0;

                                        if ( $logo_id ) {
                                            echo wp_get_attachment_image(
                                                $logo_id,
                                                'large',
                                                false,
                                                [
                                                    'class'    => 'h-14 w-auto object-contain md:h-16 lg:h-[4.5rem]',
                                                    'loading'  => 'eager',
                                                    'decoding' => 'async',
                                                ]
                                            );
                                        } else {
                                            ?>
                                            <img src="<?php echo esc_url( $logo['url'] ?? '' ); ?>"
                                                    alt="<?php echo esc_attr( $logo['alt'] ?? '' ); ?>"
                                                    class="h-14 w-auto object-contain md:h-16 lg:h-[4.5rem]"
                                                    loading="eager"
                                                    decoding="async">
                                            <?php
                                        }
                                        ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ( $has_overlay_title ) : ?>
                                    <div class="whitespace-pre-line <?php echo esc_attr( $title_classes ); ?>">
                                        <?php echo esc_html( $hero_overlay_title ); ?>
                                    </div>
                                <?php elseif ( $title && $show_overlay && $show_title ) : ?>
                                    <h1 id="js-archive-title" data-default-title="<?php echo esc_attr( wp_strip_all_tags( $title ) ); ?>" class="<?php echo esc_attr( $title_classes ); ?>">
                                        <?php echo $rendered_title; ?>
                                    </h1>
                                <?php endif; ?>

                                <?php if ( $intro ) : ?>
                                    <div id="js-archive-intro" class="<?php echo esc_attr( $intro_classes . ' ' . $mx_auto_class ); ?>">
                                        <?php echo wp_kses_post( $intro ); ?>
                                    </div>
                                <?php endif; ?>

                            </div>

                        <?php endif; ?>

                    </div>
                </div>
            </div>

        </div>
    <?php endif; ?>

    <?php if ( $breadcrumbs ) : ?>
        <div class="container-wide py-4 text-sm">
            <?php get_template_part( 'template-parts/components/breadcrumbs' ); ?>
        </div>
    <?php endif; ?>

</section>