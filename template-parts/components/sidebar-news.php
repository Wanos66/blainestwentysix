<?php
/**
 * News sidebar (Trending + optional extras).
 *
 * Usage:
 * get_template_part( 'template-parts/components/sidebar', 'news', [
 *   'sticky'         => true,
 *   'sticky_top'     => 'top-32', // matches ~128px header
 *   'sidebar_width'  => '360px',  // purely informational; grid sets width
 *   'trending_title' => 'Trending',
 *   'trending_count' => 5,
 *   'show_banner'    => true,
 *   'banner_image'   => null,     // override ACF option if you want
 *   'cta'            => [ ... ],  // optional CTA config
 *   'showrooms'      => [ ... ],  // optional showroom links
 *   'categories'     => [ ... ],  // optional categories list
 * ] );
 */

$args = wp_parse_args( $args ?? [], [
    'sticky'         => true,
    'sticky_top'     => 'top-32',
    'trending_title' => 'Trending',
    'trending_count' => 5,
    'show_banner'    => true,

    // Allow overriding the banner image externally; otherwise use ACF option if present.
    'banner_image'   => null,

    // Optional sections (off by default unless passed).
    'cta'            => null, // ['title' => '', 'text' => '', 'button_text' => '', 'button_url' => '']
    'showrooms'      => null, // ['title' => '', 'items' => [ ['label'=>'','url'=>''], ... ]]
    'categories'     => null, // ['title' => '', 'items' => [ ['label'=>'','url'=>''], ... ]]

    'categories_limit' => 5,
    'show_categories'  => true,
]);

$post_id = get_the_ID();

$section_pad     = 'p-6';
$section_divider = 'border-t border-black/10';

/**
 * Banner image: ACF option fallback.
 * Expected format: ACF image array.
 */
$banner = $args['banner_image'];
if ( ! $banner && function_exists( 'get_field' ) ) {
    $banner = get_field( 'news_archive_hero_image', 'option' );
}

$banner_url = '';
if ( is_array( $banner ) ) {
    // Prefer large; fall back to direct URL.
    $banner_url = $banner['sizes']['large'] ?? ( $banner['url'] ?? '' );
}

/**
 * Trending query: latest posts excluding current.
 */
$limit = (int) $args['trending_count'];
$cache_key = 'blt_sidebar_trending_' . $limit;

$cached_ids = get_transient( $cache_key );

if ( false === $cached_ids ) {

    $query = new WP_Query([
        'post_type'           => 'post',
        'posts_per_page'      => $limit + 1,
        'ignore_sticky_posts' => true,
        'no_found_rows'       => true,
        'fields'              => 'ids',
    ]);

    $cached_ids = $query->posts;

    set_transient( $cache_key, $cached_ids, 6 * HOUR_IN_SECONDS );
}

// Exclude current post dynamically
$trending_ids = array_values(
    array_diff( $cached_ids, $post_id ? [ (int) $post_id ] : [] )
);

// Trim to limit after exclusion
$trending_ids = array_slice( $trending_ids, 0, $limit );

// Build final query only if we have posts
$trending = null;

if ( ! empty( $trending_ids ) ) {
    $trending = new WP_Query([
        'post_type'      => 'post',
        'post__in'       => $trending_ids,
        'orderby'        => 'post__in',
        'no_found_rows'  => true,
    ]);
}
?>

<aside class="mt-12 lg:mt-0 lg:self-start <?php echo $args['sticky'] ? 'lg:sticky lg:top-32' : ''; ?>">
    <div class="flex flex-col overflow-hidden border border-black/10 bg-white shadow-sm">

        <div class="px-6 pt-6 pb-4">
            <div class="flex items-center justify-between">
                <h2 class="font-body text-lg font-medium tracking-wide text-black">
                    <?php echo esc_html( $args['trending_title'] ); ?>
                </h2>

                <span class="font-body text-xs uppercase tracking-wide text-black/40">
                    Latest
                </span>
            </div>
        </div>

        <?php if ( $trending->have_posts() ) : ?>
            <div class="divide-y divide-black/10">
                <?php while ( $trending->have_posts() ) : $trending->the_post(); ?>
                    <?php get_template_part( 'template-parts/content/card', 'trending-item' ); ?>
                <?php endwhile; ?>
            </div>
            <?php wp_reset_postdata(); ?>
        <?php else : ?>
            <div class="p-6 font-body text-sm text-black/60">
                No trending posts right now.
            </div>
        <?php endif; ?>

        <?php
        if ( is_array( $args['cta'] ) ) :
            $cta = wp_parse_args( $args['cta'], [
                'title'       => 'Planning a Project?',
                'text'        => '',
                'button_text' => 'Get in touch',
                'button_url'  => '#',
            ]);
            ?>
            <div class="<?php echo $section_pad . ' ' . $section_divider; ?>">
                <h3 class="mb-3 font-body text-lg font-medium tracking-wide text-black">
                    <?php echo esc_html( $cta['title'] ); ?>
                </h3>

                <?php if ( $cta['text'] ) : ?>
                    <p class="mb-4 font-body text-sm text-black/70">
                        <?php echo esc_html( $cta['text'] ); ?>
                    </p>
                <?php endif; ?>

                <a href="<?php echo esc_url( $cta['button_url'] ); ?>"
                        class="inline-flex w-full items-center justify-center bg-black px-4 py-3 font-body text-sm font-medium text-white transition hover:bg-black/90">
                    <?php echo esc_html( $cta['button_text'] ); ?>
                </a>
            </div>
        <?php endif; ?>

        <?php
        if ( is_array( $args['showrooms'] ) && ! empty( $args['showrooms']['items'] ) ) :
            $showrooms = wp_parse_args( $args['showrooms'], [
                'title' => 'Visit a Showroom',
                'items' => [],
            ]);
            ?>
            <div class="<?php echo $section_pad . ' ' . $section_divider; ?>">
                <h3 class="mb-4 font-body text-lg font-medium tracking-wide text-black">
                    <?php echo esc_html( $showrooms['title'] ); ?>
                </h3>

                <ul class="space-y-2 font-body text-sm">
                    <?php foreach ( $showrooms['items'] as $item ) :
                        $label = $item['label'] ?? '';
                        $url   = $item['url'] ?? '';
                        if ( ! $label || ! $url ) continue;
                        ?>
                        <li>
                            <a class="text-black/80 underline-offset-4 hover:text-black hover:underline"
                                    href="<?php echo esc_url( $url ); ?>">
                                <?php echo esc_html( $label ); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php
        if ( $args['show_categories'] ) :

            $limit = (int) $args['categories_limit'];

            $cache_key = 'blt_sidebar_categories_' . $limit;

            $top_categories = get_transient( $cache_key );

            if ( false === $top_categories ) {

                $raw_categories = get_categories([
                    'taxonomy'   => 'category',
                    'orderby'    => 'count',
                    'order'      => 'DESC',
                    'hide_empty' => true,
                    'number'     => 20,
                ]);

                $filtered = array_filter( $raw_categories, function( $cat ) {
                    return strtolower( $cat->slug ) !== 'uncategorized';
                });

                $top_categories = array_slice( $filtered, 0, $limit );

                set_transient( $cache_key, $top_categories, 12 * HOUR_IN_SECONDS );
            }

            $current_cat_ids = wp_get_post_categories( get_the_ID() );

            $display_categories = array_filter( $top_categories, function( $cat ) use ( $current_cat_ids ) {
                return ! in_array( $cat->term_id, $current_cat_ids, true );
            });

            if ( ! empty( $display_categories ) ) :
                ?>
                <div class="<?php echo $section_pad . ' ' . $section_divider; ?>">
                    <h3 class="mb-3 font-body text-lg font-medium tracking-wide text-black">
                        Categories
                    </h3>

                    <ul class="space-y-3 font-body text-sm">
                        <?php foreach ( $display_categories as $cat ) : ?>
                            <li class="flex items-center justify-between">
                                <a class="text-black/80 transition-colors hover:text-black"
                                        href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>">
                                    <?php echo esc_html( $cat->name ); ?>
                                </a>
                                <span class="text-xs text-black/40 tabular-nums">
                                    (<?php echo (int) $cat->count; ?>)
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php
            endif;

        endif;
        ?>

    </div>
</aside>