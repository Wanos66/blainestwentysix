<?php
// single.php
get_header();

$breadcrumbs = true;
if ( isset( $args ) && is_array( $args ) && array_key_exists( 'breadcrumbs', $args ) ) {
    $breadcrumbs = (bool) $args['breadcrumbs'];
}
?>

<?php if ( $breadcrumbs ) : ?>
    <div class="container-wide py-3 text-sm">
        <?php get_template_part( 'template-parts/components/breadcrumbs' ); ?>
    </div>
<?php endif; ?>

    <div class="container-wide pb-20">

        <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

            <?php
            // --- Sidebar: Trending (latest posts excluding current) ---
            $trending = new WP_Query([
                'post_type'           => 'post',
                'posts_per_page'      => 5,
                'post__not_in'        => [ get_the_ID() ],
                'ignore_sticky_posts' => true,
                'no_found_rows'       => true,
            ]);

            // --- Related: same primary category (fallback to latest) ---
            $cats = get_the_category();
            $primary_cat_id = ! empty( $cats ) ? (int) $cats[0]->term_id : 0;

            $related_args = [
                'post_type'           => 'post',
                'posts_per_page'      => 3,
                'post__not_in'        => [ get_the_ID() ],
                'ignore_sticky_posts' => true,
                'no_found_rows'       => true,
            ];

            if ( $primary_cat_id ) {
                $related_args['cat'] = $primary_cat_id;
            }

            $related = new WP_Query( $related_args );
            ?>

            <article class="min-w-0 lg:grid lg:grid-cols-[minmax(0,1fr)_360px] lg:gap-10 lg:items-start">

                <!-- Main -->
                <div>

                    <!-- Hero image -->
                    <?php if ( has_post_thumbnail() ) : ?>
                        <figure class="mb-8 border border-black/10 bg-white overflow-hidden">
                            <?php the_post_thumbnail( 'full', [ 'class' => 'w-full h-auto' ] ); ?>

                            <?php $thumb_cap = get_the_post_thumbnail_caption(); ?>
                            <?php if ( $thumb_cap ) : ?>
                                <figcaption class="px-4 py-3 text-sm text-black/60">
                                    <?php echo esc_html( $thumb_cap ); ?>
                                </figcaption>
                            <?php endif; ?>
                        </figure>
                    <?php endif; ?>

                    <h1 class="mt-5 mb-4 text-4xl font-heading font-medium leading-[1.08] tracking-[-0.02em] text-neutral-950 md:text-5xl">
                        <?php the_title(); ?>
                    </h1>

                    <!-- Kicker row -->
                    <?php
                    $cat = ! empty( $cats ) ? $cats[0] : null;
                    ?>
                    <div class="mb-5 flex flex-wrap items-center gap-x-3 gap-y-2 text-sm font-body leading-relaxed text-black/60">
                        <?php if ( $cat ) : ?>
                            <a class="inline-flex items-center bg-black/5 px-3 py-1 text-xs font-medium text-black/70 hover:bg-black/10"
                                    href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>">
                                <?php echo esc_html( $cat->name ); ?>
                            </a>
                        <?php endif; ?>

                        <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                            <?php echo esc_html( get_the_date() ); ?>
                        </time>
                    </div>



                    <?php if ( has_excerpt() ) : ?>
                        <p class="mt-5 max-w-none text-lg text-black/70">
                            <?php echo esc_html( get_the_excerpt() ); ?>
                        </p>
                    <?php endif; ?>

                    <div class="<?php echo has_excerpt() ? 'mt-8' : 'mt-10'; ?> prose-site prose-news max-w-none">
                        <?php the_content(); ?>
                    </div>

                    <!-- Prev / Next -->
                    <?php
                    $prev = get_previous_post();
                    $next = get_next_post();
                    ?>

                    <?php if ( $prev || $next ) : ?>
                        <nav class="mt-14 border-t border-black/10 pt-8" aria-label="Post navigation">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                                <!-- Previous -->
                                <div>
                                    <?php if ( $prev ) :
                                        $prev_url   = get_permalink( $prev );
                                        $prev_title = get_the_title( $prev );
                                        ?>
                                        <a href="<?php echo esc_url( $prev_url ); ?>" class="group flex gap-4">
                                            <div class="w-16 h-16 shrink-0 overflow-hidden bg-black/5 border border-black/10">
                                                <?php echo blt_nav_thumb_img( $prev, 'medium', [ 'sizes' => '64px' ] ); ?>
                                            </div>

                                            <div class="min-w-0">
                                                <div class="text-sm text-black/60 mb-2">Previous</div>
                                                <div class="font-semibold leading-snug line-clamp-2 group-hover:underline decoration-black/30 underline-offset-4">
                                                    <?php echo esc_html( $prev_title ); ?>
                                                </div>
                                            </div>
                                        </a>
                                    <?php endif; ?>
                                </div>

                                <!-- Next -->
                                <div class="md:text-right">
                                    <?php if ( $next ) :
                                        $next_url   = get_permalink( $next );
                                        $next_title = get_the_title( $next );
                                        ?>
                                        <a href="<?php echo esc_url( $next_url ); ?>" class="group flex gap-4 md:flex-row-reverse md:text-left">
                                            <div class="w-16 h-16 shrink-0 overflow-hidden bg-black/5 border border-black/10">
                                                <?php echo blt_nav_thumb_img( $next, 'medium', [ 'sizes' => '64px' ] ); ?>
                                            </div>

                                            <div class="min-w-0 md:text-right">
                                                <div class="text-sm text-black/60 mb-2">Next</div>
                                                <div class="font-semibold leading-snug line-clamp-2 group-hover:underline decoration-black/30 underline-offset-4">
                                                    <?php echo esc_html( $next_title ); ?>
                                                </div>
                                            </div>
                                        </a>
                                    <?php endif; ?>
                                </div>

                            </div>
                        </nav>
                    <?php endif; ?>

                    <!-- Related -->
                    <?php if ( $related->have_posts() ) : ?>
                        <div class="mt-14">
                            <div class="h-px w-full bg-black/10 mb-10"></div>
                            <h2 class="text-2xl font-semibold tracking-tight mb-8">Related</h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                                <?php while ( $related->have_posts() ) : $related->the_post(); ?>
                                    <?php get_template_part( 'template-parts/content/card', 'equal' ); ?>
                                <?php endwhile; wp_reset_postdata(); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>

                <!-- Sidebar -->
                <?php
                get_template_part( 'template-parts/components/sidebar', 'news', [
                    'sticky'     => true,
                    'sticky_top' => 'top-32',      // matches your ~127.6px sticky header
                    'trending_count' => 5,

                    // Optional enhancements (remove if not using):
                    'cta' => [
                        'title'       => 'Planning a Project?',
                        'text'        => 'Visit one of our showrooms for expert advice on kitchens, appliances and home technology.',
                        'button_text' => 'Book a Consultation',
                        'button_url'  => '/design-consultation/',
                    ],
                    'showrooms' => [
                        'title' => 'Visit a Showroom',
                        'items' => [
                            [ 'label' => 'Swaffham', 'url' => '/showrooms/swaffham/' ],
                            [ 'label' => 'Dereham',  'url' => '/showrooms/dereham/' ],
                            [ 'label' => 'Watton',   'url' => '/showrooms/watton/' ],
                        ],
                    ],
                ] );
                ?>

            </article>

        <?php endwhile; endif; ?>
    </div>

<?php get_footer(); ?>