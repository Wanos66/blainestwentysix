<?php
/**
 * filter-tags.php
 *
 * @package BlainesTwentySix
 */

// Restore correct logic
$current_category = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : '';
$current_tag      = isset($_GET['tag']) ? sanitize_text_field($_GET['tag']) : '';

$all_tags_url = remove_query_arg('tag');

// Correctly restrict tags based on category again
$tags = blt_get_tags_for_category($current_category);
?>

<div id="js-tag-list" class="flex flex-wrap gap-2">

    <!-- ALL tags -->
    <a
            href="<?php echo esc_url($all_tags_url); ?>"
            class="
            inline-flex items-center gap-2 px-3 py-1.5 rounded-xs transition
            <?php echo $current_tag === ''
                    ? 'bg-black text-white font-semibold'
                    : 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-100'; ?>
        "
            data-filter-link
            data-category="<?php echo esc_attr($current_category); ?>"
            data-tag=""
    >
        All
    </a>

    <!-- Individual tags -->
    <?php foreach ($tags as $tag) : ?>
        <?php
        $is_active = ($current_tag === $tag->slug);
        $tag_url   = add_query_arg([
                'category' => $current_category ?: null,
                'tag'      => $tag->slug,
        ], get_permalink(get_option('page_for_posts')));
        ?>
        <a
                href="<?php echo esc_url($tag_url); ?>"
                class="
                inline-flex items-center gap-2 px-3 py-1.5 rounded-xs transition
                <?php echo $is_active
                        ? 'bg-black text-white font-semibold'
                        : 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-100'; ?>
            "
                data-filter-link
                data-category="<?php echo esc_attr($current_category); ?>"
                data-tag="<?php echo esc_attr($tag->slug); ?>"
        >
            <?php echo esc_html($tag->name); ?>
        </a>
    <?php endforeach; ?>

</div>
