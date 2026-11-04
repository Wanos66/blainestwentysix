<?php
/**
 * Active Filters Bar
 */

$blog_id  = get_option('page_for_posts');
$blog_url = $blog_id ? get_permalink($blog_id) : home_url('/');

$current_category = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : '';
$current_tag      = isset($_GET['tag']) ? sanitize_text_field($_GET['tag']) : '';
$search_term      = get_search_query();
?>

<?php if ($current_category || $current_tag || $search_term) : ?>

    <div class="mb-6 p-4 bg-gray-100 border border-gray-300 rounded-xs">

        <div class="text-sm font-semibold text-gray-700 mb-2">
            Active filters:
        </div>

        <div class="flex flex-wrap gap-2">

            <!-- CATEGORY CHIP -->
            <?php if ($current_category) : ?>
                <?php $term = get_term_by('slug', $current_category, 'category'); ?>
                <a
                        href="<?php echo esc_url(remove_query_arg('category')); ?>"
                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xs bg-black text-white font-semibold transition"
                        data-filter-link
                        data-category=""
                        data-tag="<?php echo esc_attr($current_tag); ?>"
                >
                    <?php echo esc_html($term ? $term->name : $current_category); ?>
                    <span class="text-white text-sm">×</span>
                </a>
            <?php endif; ?>


            <!-- TAG CHIP -->
            <?php if ($current_tag) : ?>
                <a
                    href="<?php echo esc_url(remove_query_arg('tag')); ?>"
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xs bg-black text-white font-semibold transition"
                    data-filter-link
                    data-category="<?php echo esc_attr($current_category); ?>"
                    data-tag=""
                >
                    <?php echo esc_html($current_tag); ?>
                    <span class="text-white text-sm">×</span>
                </a>
            <?php endif; ?>

            <!-- SEARCH CHIP -->
            <?php if ($search_term) : ?>
                <a
                    href="<?php echo esc_url(remove_query_arg('s')); ?>"
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xs bg-black text-white font-semibold transition"
                    data-filter-link
                    data-category="<?php echo esc_attr($current_category); ?>"
                    data-tag="<?php echo esc_attr($current_tag); ?>"
                >
                    "<?php echo esc_html($search_term); ?>"
                    <span class="text-white text-sm">×</span>
                </a>
            <?php endif; ?>

            <!-- CLEAR ALL -->
            <a
                href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>"
                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xs border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 transition"
                data-filter-link
                data-category=""
                data-tag=""
            >
                Clear All
            </a>

        </div>
    </div>

<?php endif; ?>

