<?php
/**
 * Related posts (Style → Brand fallback)
 *
 * Args:
 * - post_type (string)   e.g. 'kitchen' | 'bathroom' | 'bedroom'
 * - style_tax (string)   e.g. 'kitchen_style'
 * - brand_tax (string)   e.g. 'kitchen_brand'
 * - heading (string)     e.g. 'Related Kitchens'
 * - limit (int)          default 3
 * - archive_link (string|null) optional override URL for "View all"
 * - show_brand (bool)    default true (shows first brand term label)
 */

$post_type    = $args['post_type'] ?? '';
$style_tax    = $args['style_tax'] ?? '';
$brand_tax    = $args['brand_tax'] ?? '';
$heading      = $args['heading'] ?? 'Related';
$limit        = isset($args['limit']) ? (int) $args['limit'] : 3;
$archive_link = $args['archive_link'] ?? null;
$show_brand   = array_key_exists('show_brand', $args) ? (bool) $args['show_brand'] : true;

if ( ! $post_type || ! $style_tax || ! $brand_tax || $limit < 1 ) {
    return;
}

$current_id = get_the_ID();

// Term IDs
$styles = wp_get_post_terms( $current_id, $style_tax, ['fields' => 'ids'] );
$brands = wp_get_post_terms( $current_id, $brand_tax, ['fields' => 'ids'] );

$base_args = [
    'post_type'           => $post_type,
    'post_status'         => 'publish',
    'posts_per_page'      => $limit,
    'post__not_in'        => [ $current_id ],
    'ignore_sticky_posts' => true,
    'no_found_rows'       => true,
];

$related_query = null;

// Style first
if ( ! empty( $styles ) ) {
    $related_query = new WP_Query( array_merge( $base_args, [
        'tax_query' => [[
            'taxonomy' => $style_tax,
            'field'    => 'term_id',
            'terms'    => $styles,
        ]],
    ] ) );
}

// Brand fallback
if ( ( ! $related_query || ! $related_query->have_posts() ) && ! empty( $brands ) ) {
    $related_query = new WP_Query( array_merge( $base_args, [
        'tax_query' => [[
            'taxonomy' => $brand_tax,
            'field'    => 'term_id',
            'terms'    => $brands,
        ]],
    ] ) );
}

// Bail if nothing found
if ( ! $related_query || ! $related_query->have_posts() ) {
    return;
}

if ( ! $archive_link ) {
    $archive_link = get_post_type_archive_link( $post_type );
}
?>

<section class="section">
    <div class="container-wide-nopad">

        <div class="flex items-end justify-between gap-4">
            <h2 class="font-heading text-3xl font-normal text-slate-900 sm:text-4xl">
<!--                --><?php //echo esc_html( $heading ); ?>
                Similar to <?php echo the_title(); ?>
            </h2>

            <?php if ( $archive_link ) : ?>
                <a href="<?php echo esc_url( $archive_link ); ?>"
                        class="text-sm font-body font-medium text-neutral-900 hover:underline">
                    View all
                </a>
            <?php endif; ?>
        </div>

        <div class="mt-6 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">

            <?php while ( $related_query->have_posts() ) : $related_query->the_post(); ?>

                <article class="group overflow-hidden border border-neutral-200 bg-white shadow-[0_8px_24px_rgba(0,0,0,0.06)] hover:shadow-[0_12px_32px_rgba(0,0,0,0.08)] transition">
                    <a href="<?php the_permalink(); ?>" class="block">

                        <div class="aspect-[16/10] overflow-hidden bg-neutral-100">
                            <?php if ( has_post_thumbnail() ) : ?>
                                <?php
                                echo get_the_post_thumbnail(
                                    get_the_ID(),
                                    'medium_large',
                                    [
                                        'class'    => 'w-full h-full object-cover transition-transform duration-300 group-hover:scale-105',
                                        'loading'  => 'lazy',
                                        'decoding' => 'async',
                                    ]
                                );
                                ?>
                            <?php endif; ?>
                        </div>

                        <div class="p-5">
                            <h3 class="font-heading text-2xl font-normal text-slate-900 sm:text-3xl">
                                <?php the_title(); ?>
                            </h3>

                            <?php if ( $show_brand ) :
                                $brand_terms = get_the_terms( get_the_ID(), $brand_tax );
                                if ( $brand_terms && ! is_wp_error( $brand_terms ) ) :
                                    ?>
                                    <p class="mt-2 text-sm font-body text-neutral-600">
                                        <?php echo esc_html( $brand_terms[0]->name ); ?>
                                    </p>
                                <?php endif;
                            endif; ?>
                        </div>

                    </a>
                </article>

            <?php endwhile; ?>

        </div>

    </div>
</section>

<?php wp_reset_postdata(); ?>
