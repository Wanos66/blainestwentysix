<?php
/**
 * filter-cats.php
 *
 * @package BlainesTwentySix
 */

// Restore correct logic: read from GET, not get_query_var().
$current_category = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : '';
$current_tag      = isset($_GET['tag']) ? sanitize_text_field($_GET['tag']) : '';

$all_cat_url = remove_query_arg('category');
?>

<div id="js-category-list" class="flex flex-wrap gap-2">

    <!-- ALL categories -->
    <a
            href="<?php echo esc_url($all_cat_url); ?>"
            class="
            inline-flex items-center gap-2 px-3 py-1.5 rounded-xs transition
            <?php echo $current_category === ''
                    ? 'bg-black text-white font-semibold'
                    : 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-100'; ?>
        "
            data-filter-link
            data-category=""
            data-tag="<?php echo esc_attr($current_tag); ?>"
    >
        All
    </a>

    <!-- Individual categories -->
    <?php foreach (get_categories(['hide_empty' => false]) as $cat) : ?>
        <?php
        $is_active = ($current_category === $cat->slug);
        $cat_url   = add_query_arg([
                'category' => $cat->slug,
                'tag'      => $current_tag ?: null,
        ], get_permalink(get_option('page_for_posts')));
        ?>
        <a
                href="<?php echo esc_url($cat_url); ?>"
                class="
                inline-flex items-center gap-2 px-3 py-1.5 rounded-xs transition
                <?php echo $is_active
                        ? 'bg-black text-white font-semibold'
                        : 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-100'; ?>
            "
                data-filter-link
                data-category="<?php echo esc_attr($cat->slug); ?>"
                data-tag=""
        >
            <?php echo esc_html($cat->name); ?>
        </a>
    <?php endforeach; ?>

</div>
