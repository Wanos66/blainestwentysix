<?php
/**
 * Shared posts layout
 * Page 1: Featured + Trending list (Riverside-style)
 * Page 2+: Standard grid
 *
 * Args:
 * - show_sidebar (bool): If true, page 1 shows the internal Trending panel column.
 *                        If false, page 1 renders Featured + standard grid (no internal sidebar).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$args = wp_parse_args( $args ?? [], [
    'show_sidebar' => true,
] );

$show_sidebar = (bool) $args['show_sidebar'];

$paged = max( 1, (int) get_query_var( 'paged' ) );
?>

<?php if ( have_posts() ) : ?>

    <?php if ( 1 === $paged ) : ?>

        <?php
        $featured = null;
        $trending = [];

        $i = 0;
        while ( have_posts() ) :
            the_post();

            if ( 0 === $i ) {
                $featured = get_post();
            } else {
                $trending[] = get_post();
            }

            $i++;
        endwhile;

        wp_reset_postdata();
        ?>

        <?php if ( $show_sidebar ) : ?>

            <section class="mb-12">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-stretch">

                    <!-- Featured (left) -->
                    <div class="lg:col-span-8 h-full">
                        <?php if ( $featured ) : ?>
                            <?php
                            $post = $featured;
                            setup_postdata( $post );
                            get_template_part( 'template-parts/content/card', 'featured' );
                            wp_reset_postdata();
                            ?>
                        <?php endif; ?>
                    </div>

                    <!-- Trending (right) -->
                    <div class="lg:col-span-4 h-full">

                        <div class="border border-black/10 bg-white h-full flex flex-col">
                            <!-- Panel header -->
                            <?php
                            $news_hero = get_field( 'news_archive_hero_image', 'option' );
                            $hero_url  = ( $news_hero && ! empty( $news_hero['sizes']['large'] ) ) ? $news_hero['sizes']['large'] : '';
                            ?>

                            <div class="relative h-28 overflow-hidden">

                                <?php if ( $hero_url ) : ?>
                                    <img
                                            src="<?php echo esc_url( $hero_url ); ?>"
                                            alt=""
                                            class="absolute inset-0 w-full h-full object-cover scale-110 blur-[1px]"
                                    >
                                <?php endif; ?>

                                <!-- Dark overlay -->
                                <div class="absolute inset-0 bg-black/20"></div>

                                <!-- Title -->
                                <div class="relative h-full flex items-center px-6">
                                    <h2 class="text-white text-lg font-semibold tracking-tight">Trending</h2>
                                </div>

                            </div>

                            <?php if ( ! empty( $trending ) ) : ?>
                                <div class="divide-y divide-black/10 flex-1">
                                    <?php foreach ( $trending as $p ) : ?>
                                        <?php
                                        $post = $p;
                                        setup_postdata( $post );
                                        get_template_part( 'template-parts/content/card', 'trending-item' );
                                        wp_reset_postdata();
                                        ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                        </div>

                    </div>

                </div>
            </section>

        <?php else : ?>

            <!-- Main-only mode: Featured + normal grid (no internal Trending column) -->
            <?php if ( $featured ) : ?>
                <section class="mb-12">
                    <?php
                    $post = $featured;
                    setup_postdata( $post );
                    get_template_part( 'template-parts/content/card', 'featured' );
                    wp_reset_postdata();
                    ?>
                </section>
            <?php endif; ?>

            <?php if ( ! empty( $trending ) ) : ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                    <?php foreach ( $trending as $p ) : ?>
                        <?php
                        $post = $p;
                        setup_postdata( $post );
                        get_template_part( 'template-parts/content/card', 'equal' );
                        wp_reset_postdata();
                        ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        <?php endif; ?>

    <?php else : ?>

        <!-- Page 2+: normal grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
            <?php while ( have_posts() ) : the_post(); ?>
                <?php get_template_part( 'template-parts/content/card', 'equal' ); ?>
            <?php endwhile; ?>
        </div>

    <?php endif; ?>

<?php else : ?>

    <?php get_template_part( 'template-parts/none' ); ?>

<?php endif; ?>