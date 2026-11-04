<?php
/**
 * Component: Taxonomy filter bar (combined archive links)
 *
 * Args:
 * - taxonomy (string)         required
 * - post_type (string)        required
 * - all_label (string)        default: 'All'
 * - aria_label (string)       default: 'Filters'
 * - current_style (string)    optional
 * - current_brand (string)    optional
 * - current_taxonomy (string) optional
 */

$args = wp_parse_args( $args ?? [], [
    'taxonomy'         => '',
    'post_type'        => '',
    'all_label'        => __( 'All', 'blainestwentysix' ),
    'aria_label'       => __( 'Filters', 'blainestwentysix' ),
    'current_style'    => '',
    'current_brand'    => '',
    'current_taxonomy' => '',
] );

$taxonomy         = (string) $args['taxonomy'];
$post_type        = (string) $args['post_type'];
$current_style    = (string) $args['current_style'];
$current_brand    = (string) $args['current_brand'];
$current_taxonomy = (string) $args['current_taxonomy'];

if ( ! $taxonomy || ! $post_type ) {
    return;
}

$terms = get_terms( [
    'taxonomy'   => $taxonomy,
    'hide_empty' => true,
    'orderby'    => 'name',
    'order'      => 'ASC',
] );

if ( is_wp_error( $terms ) || empty( $terms ) ) {
    return;
}

$archive_url = get_post_type_archive_link( $post_type );

/**
 * All button returns to the base archive for the current post type.
 */
$all_url    = $archive_url;
$all_active = is_post_type_archive( $post_type ) && ! $current_style && ! $current_brand && ! $current_taxonomy;

$pill_base   = 'inline-flex items-center justify-center whitespace-nowrap px-4 py-2 border text-sm font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-black/30 focus-visible:ring-offset-2';
$pill_idle   = 'bg-white text-slate-800 border-slate-200 hover:bg-slate-50 hover:border-slate-300';
$pill_active = 'bg-black text-white border-black';
?>

<nav class="mb-10" aria-label="<?php echo esc_attr( $args['aria_label'] ); ?>">
    <div class="-mx-4 overflow-x-auto px-4">
        <div class="flex w-max gap-3 sm:w-full sm:flex-wrap">

            <a
                    href="<?php echo esc_url( $all_url ); ?>"
                    class="<?php echo esc_attr( $pill_base . ' ' . ( $all_active ? $pill_active : $pill_idle ) ); ?>"
                <?php echo $all_active ? 'aria-current="page"' : ''; ?>
            >
                <?php echo esc_html( $args['all_label'] ); ?>
            </a>

            <?php foreach ( $terms as $term ) : ?>
                <?php
                $query_args = [];
                $is_active  = false;

                /**
                 * Kitchens: preserve the opposite filter dimension.
                 */
                if ( 'kitchen_brand' === $taxonomy ) {
                    if ( $current_style ) {
                        $query_args['kitchen_style'] = $current_style;
                    }
                    $query_args['kitchen_brand'] = $term->slug;
                    $is_active = ( $current_brand === $term->slug );

                } elseif ( 'kitchen_style' === $taxonomy ) {
                    if ( $current_brand ) {
                        $query_args['kitchen_brand'] = $current_brand;
                    }
                    $query_args['kitchen_style'] = $term->slug;
                    $is_active = ( $current_style === $term->slug );

                    /**
                     * Generic taxonomy archives: Bathrooms / Bedrooms / future reuse.
                     */
                } else {
                    $query_args[ $taxonomy ] = $term->slug;
                    $is_active = ( $current_taxonomy === $term->slug );
                }

                $url = add_query_arg( $query_args, $archive_url );
                ?>
                <a
                        href="<?php echo esc_url( $url ); ?>"
                        class="<?php echo esc_attr( $pill_base . ' ' . ( $is_active ? $pill_active : $pill_idle ) ); ?>"
                    <?php echo $is_active ? 'aria-current="page"' : ''; ?>
                >
                    <?php echo esc_html( $term->name ); ?>
                </a>
            <?php endforeach; ?>

        </div>
    </div>
</nav>