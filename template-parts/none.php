<?php
/**
 * No Results Found template
 * Shows smart contextual message based on active filters.
 */

$blog_id  = get_option('page_for_posts');
$blog_url = $blog_id ? get_permalink($blog_id) : home_url('/');

$current_category = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : '';
$current_tag      = isset($_GET['tag']) ? sanitize_text_field($_GET['tag']) : '';
$search_term      = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

$message = "No results found";

// Build dynamic title text
if ($search_term) {
    $message = sprintf('No results found for “%s”', esc_html($search_term));
} elseif ($current_category && !$current_tag) {
    $term = get_term_by('slug', $current_category, 'category');
    if ($term) {
        $message = sprintf('No results found in category “%s”', esc_html($term->name));
    }
} elseif ($current_tag && !$current_category) {
    $term = get_term_by('slug', $current_tag, 'post_tag');
    if ($term) {
        $message = sprintf('No results found for tag “%s”', esc_html($term->name));
    }
} elseif ($current_category && $current_tag) {
    $cat = get_term_by('slug', $current_category, 'category');
    $tag = get_term_by('slug', $current_tag, 'post_tag');

    $cat_name = $cat ? $cat->name : $current_category;
    $tag_name = $tag ? $tag->name : $current_tag;

    $message = sprintf(
            'No results found for “%s” in category “%s”',
            esc_html($tag_name),
            esc_html($cat_name)
    );
}
?>

<div class="flex flex-col items-center justify-center py-20 text-center">

    <!-- Icon -->
    <div class="w-16 h-16 mb-6 rounded-full bg-gray-100 flex items-center justify-center">
        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
    </div>

    <!-- Dynamic Title -->
    <h2 class="text-2xl font-semibold text-gray-800 mb-3">
        <?php echo $message; ?>
    </h2>

    <!-- Message -->
    <p class="text-gray-600 max-w-md mb-8">
        Try adjusting your category, tags, or search term.
    </p>

    <!-- Clear All Button -->
    <a href="<?php echo esc_url($blog_url); ?>"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xs
              bg-black text-white font-semibold hover:bg-gray-900 transition"
            data-filter-link
            data-category=""
            data-tag=""
    >
        Clear All Filters
    </a>
</div>
