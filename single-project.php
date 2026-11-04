<?php
/**
 * Template for single Project
 */

$breadcrumbs = true;
if ( isset( $args ) && is_array( $args ) && array_key_exists( 'breadcrumbs', $args ) ) {
    $breadcrumbs = (bool) $args['breadcrumbs'];
}

get_header();

if ( have_posts() ) :
    while ( have_posts() ) :
        the_post();

        $hero = null;

        if ( has_post_thumbnail() ) {
            $hero_id  = get_post_thumbnail_id();
            $hero_alt = get_post_meta( $hero_id, '_wp_attachment_image_alt', true );

            $hero = [
                'ID'  => $hero_id,
                'url' => wp_get_attachment_image_url( $hero_id, 'full' ),
                'alt' => $hero_alt ?: get_the_title(),
            ];
        }

        get_template_part(
            'template-parts/components/page-header',
            null,
            [
                'hero'        => $hero,
                'title'       => get_the_title(),
                'breadcrumbs' => $breadcrumbs,
                'align'       => 'center',
                'show_overlay' => false,
            ]
        );
        ?>

        <div class="container-wide py-6">

            <div class="grid grid-cols-1 gap-10 lg:grid-cols-2 lg:items-start">
                <div class="min-w-0">
                    <?php get_template_part( 'template-parts/single-projects/content' ); ?>
                </div>

                <div class="min-w-0 self-start lg:sticky lg:top-32">
                    <?php get_template_part( 'template-parts/single-projects/gallery' ); ?>
                    <?php get_template_part( 'template-parts/single-projects/video' ); ?>
                    <?php get_template_part( 'template-parts/single-projects/facts' ); ?>
                    <?php get_template_part( 'template-parts/single-projects/cta' ); ?>
                </div>
            </div>

            <?php
            $prev = get_previous_post( true, '', 'project_type' );
            $next = get_next_post( true, '', 'project_type' );
            ?>

            <?php if ( $prev || $next ) : ?>
                <nav class="mt-14 border-t border-black/10 pt-8" aria-label="Project navigation">
                    <div class="grid grid-cols-1 gap-8 md:grid-cols-2">

                        <div>
                            <?php if ( $prev ) : ?>
                                <a href="<?php echo esc_url( get_permalink( $prev ) ); ?>" class="group flex gap-4">
                                    <div class="h-16 w-16 shrink-0 overflow-hidden border border-black/10 bg-black/5">
                                        <?php echo blt_nav_thumb_img( $prev, 'medium', [ 'sizes' => '64px' ] ); ?>
                                    </div>

                                    <div class="min-w-0">
                                        <div class="mb-2 font-body text-sm text-black/60">Previous</div>
                                        <div class="line-clamp-2 font-body font-medium leading-snug tracking-wide decoration-black/30 underline-offset-4
                                        group-hover:underline">
                                            <?php echo esc_html( get_the_title( $prev ) ); ?>
                                        </div>
                                    </div>
                                </a>
                            <?php endif; ?>
                        </div>

                        <div class="md:text-right">
                            <?php if ( $next ) : ?>
                                <a href="<?php echo esc_url( get_permalink( $next ) ); ?>" class="group flex gap-4 md:flex-row-reverse md:text-left">
                                    <div class="h-16 w-16 shrink-0 overflow-hidden border border-black/10 bg-black/5">
                                        <?php echo blt_nav_thumb_img( $next, 'medium', [ 'sizes' => '64px' ] ); ?>
                                    </div>

                                    <div class="min-w-0 md:text-right">
                                        <div class="mb-2 font-body text-sm text-black/60">Next</div>
                                        <div class="line-clamp-2 font-body font-medium leading-snug tracking-wide decoration-black/30 underline-offset-4
                                        group-hover:underline">
                                            <?php echo esc_html( get_the_title( $next ) ); ?>
                                        </div>
                                    </div>
                                </a>
                            <?php endif; ?>
                        </div>

                    </div>
                </nav>
            <?php endif; ?>

        </div>

    <?php
    endwhile;
endif;

get_footer();