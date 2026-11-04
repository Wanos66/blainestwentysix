<?php
/**
 * Shared content area for Blog, filtering, etc.
 * Uses REST query (blt_rest_query) when present.
 */

// If REST query exists, swap it into the global loop temporarily
$rest_query = get_query_var( 'blt_rest_query' );
global $wp_query;
$old_query = null;

if ( $rest_query instanceof WP_Query ) {
    $old_query = $wp_query;
    $wp_query  = $rest_query;
}
?>

<div id="js-content-area" class="w-full">

    <!-- FILTER DROPDOWN (lives inside AJAX area) -->
<!--    --><?php //get_template_part( 'template-parts/components/filter-dropdown' ); ?>

    <!-- TITLE -->
    <div id="js-title-block" class="mb-8">
        <?php
        $category_slug = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : '';
        $tag_slug      = isset($_GET['tag']) ? sanitize_text_field($_GET['tag']) : '';
        $search_term   = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

        if ( $category_slug ) {
            $term = get_term_by('slug', $category_slug, 'category');
            if ($term) {
                echo '<h1 class="text-5xl font-bold leading-tight mb-4">'
                     . esc_html($term->name)
                     . '</h1>';

                if ($term->description) {
                    echo '<div class="text-lg text-gray-600 max-w-2xl">'
                         . wp_kses_post($term->description)
                         . '</div>';
                }
            }
        } elseif ( $tag_slug ) {
            $term = get_term_by('slug', $tag_slug, 'post_tag');
            if ($term) {
                echo '<h1 class="text-5xl font-bold leading-tight mb-4">'
                     . esc_html($term->name)
                     . '</h1>';
            }
        } elseif ( $search_term ) {
            echo '<h1 class="text-xl font-bold leading-tight my-8 sm:text-center md:text-left">'
                 . 'Search results for “' . esc_html($search_term) . '”'
                 . '</h1>';
        }
        // IMPORTANT: no default "News" heading here.
        // The hero header in home.php already outputs it.
        ?>
    </div>

    <!-- ACTIVE FILTER CHIPS -->
    <?php get_template_part( 'template-parts/components/active-filters' ); ?>

    <!-- GRID (uses current global $wp_query, which we swapped if REST) -->
    <div id="js-post-grid-wrapper" data-grid="blog">
        <?php get_template_part( 'template-parts/loop/posts-grid' ); ?>
    </div>

</div>

<?php
// Restore original query if we swapped it
if ( $old_query instanceof WP_Query ) {
    $wp_query = $old_query;
    wp_reset_postdata();
}
?>
