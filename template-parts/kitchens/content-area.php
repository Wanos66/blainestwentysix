<?php
/**
 * Kitchens Content Area (REST-friendly, same structure as Blog)
 */

$rest_query = get_query_var('blt_rest_query');
global $wp_query;
$old_query = null;

if ($rest_query instanceof WP_Query) {
    $old_query = $wp_query;
    $wp_query  = $rest_query;
}

$search_term   = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
$current_style = isset($_GET['style']) ? sanitize_text_field($_GET['style']) : '';
$current_finish = isset($_GET['finish']) ? sanitize_text_field($_GET['finish']) : '';
$current_brand = isset($_GET['brand']) ? sanitize_text_field($_GET['brand']) : '';
?>

<div id="js-content-area" class="w-full">

    <!-- TITLE -->
    <div id="js-title-block" class="mb-12">

        <?php if ($search_term): ?>

            <h1 class="text-4xl font-serif font-semibold leading-tight mb-4">
                <?php printf(__('Search results for “%s”','blainestwentysix'), esc_html($search_term)); ?>
            </h1>

        <?php elseif ($current_style || $current_finish || $current_brand): ?>

            <h1 class="text-4xl font-serif font-semibold leading-tight mb-4">
                <?php esc_html_e('Filtered kitchens','blainestwentysix'); ?>
            </h1>

        <?php else: ?>

            <h1 class="text-4xl font-serif font-semibold leading-tight mb-4">
                <?php echo esc_html(get_the_archive_title()); ?>
            </h1>

            <?php if (term_description()): ?>
                <p class="text-lg text-slate-600 leading-relaxed max-w-2xl">
                    <?php echo wp_kses_post(term_description()); ?>
                </p>
            <?php endif; ?>

        <?php endif; ?>

    </div>

    <!-- ACTIVE FILTER CHIPS -->
    <div id="js-kitchen-active-filters"
            class="mb-6 flex flex-wrap gap-2 text-xs"
            aria-live="polite"></div>

    <!-- GRID -->
    <div id="js-kitchen-grid" class="grid gap-10 sm:grid-cols-2 lg:grid-cols-3" data-grid="kitchens">
        <?php if (have_posts()): ?>
            <?php while (have_posts()): the_post(); ?>
                <?php get_template_part('template-parts/kitchens/kitchen','card'); ?>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center text-slate-600">No kitchens were found.</p>
        <?php endif; ?>
    </div>

    <!-- PAGINATION -->
    <div id="js-kitchen-pagination" class="mt-10 flex justify-center">
        <?php the_posts_pagination(); ?>
    </div>

</div>

<?php
if ($old_query instanceof WP_Query) {
    $wp_query = $old_query;
    wp_reset_postdata();
}
?>
