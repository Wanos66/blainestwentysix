<!-- DEBUG: filter-bar -->

<?php
/**
 * Combined Category + Tag Filter Bar
 * with collapsible sections using Alpine.js
 */

// CATEGORY LOGIC
$current_cat   = is_category() ? get_queried_object() : null;
$active_cat_id = $current_cat ? intval($current_cat->term_id) : 0;

$categories = get_categories([
    'hide_empty' => true,
]);

$cat_all_active = ! is_category();

// TAG LOGIC with optional category filtering
$current_tag   = is_tag() ? get_queried_object() : null;
$active_tag_id = $current_tag ? intval($current_tag->term_id) : 0;

$tag_all_active = ! is_tag();

// If inside a category archive, filter tags by posts in that category
$tags = [];
if (is_category()) {

    $cat_id    = get_queried_object_id();
    $tag_query = new WP_Query([
        'cat'            => $cat_id,
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ]);

    $tag_ids = [];

    if ($tag_query->have_posts()) {
        foreach ($tag_query->posts as $post_id) {
            $post_tags = wp_get_post_tags($post_id);
            foreach ($post_tags as $tag) {
                $tag_ids[$tag->term_id] = $tag;
            }
        }
    }

    $tags = array_values($tag_ids);

} else {
    $tags = get_tags(['hide_empty' => true]);
}

// Blog index URL
$blog_url = esc_url(get_permalink(get_option('page_for_posts')));
?>

<div x-data="{ openCategories: true, openTags: true }" class="space-y-10 mb-12" data-filter-container>

    <!-- ======================== -->
    <!-- SEARCH INPUT             -->
    <!-- ======================== -->
    <div>
        <form method="get" action="<?php echo esc_url(home_url('/')); ?>" class="flex gap-3 items-center" data-filter-search>
            <input type="search"
                    name="s"
                    value="<?php echo esc_attr(get_search_query()); ?>"
                    placeholder="Search posts..."
                    class="px-4 py-2 w-full max-w-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"/>

            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Search
            </button>
        </form>
    </div>


    <!-- ======================== -->
    <!-- CATEGORY FILTER (COLLAPSIBLE) -->
    <!-- ======================== -->
    <div>
        <button
                @click="openCategories = !openCategories"
                class="flex items-center gap-2 text-lg font-semibold text-gray-800 mb-3 select-none">

            <span>Categories</span>

            <svg
                    x-bind:class="openCategories ? 'rotate-180' : ''"
                    class="w-5 h-5 transition-transform duration-200"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>

        </button>



        <div x-show="openCategories" x-transition class="flex flex-wrap gap-3 items-center">

            <!-- All Categories -->
            <a href="<?php echo $blog_url; ?>" data-filter-link class="px-4 py-2 rounded-full text-sm font-medium transition
               <?php echo $cat_all_active
                ? 'bg-blue-600 text-white'
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>"> All </a>

            <!-- Individual Categories -->
            <?php foreach ($categories as $cat) :
                $is_active = intval($cat->term_id) === $active_cat_id;
                ?>
                <a href="<?php echo esc_url(get_category_link($cat->term_id)); ?>" data-filter-link class="px-4 py-2 rounded-full text-sm font-medium transition
                   <?php echo $is_active
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                    <?php echo esc_html($cat->name); ?>
                </a>
            <?php endforeach; ?>

        </div>
    </div>


    <!-- ======================== -->
    <!-- TAG FILTER (COLLAPSIBLE) -->
    <!-- ======================== -->
    <div>
        <button
                @click="openTags = !openTags"
                class="flex items-center gap-2 text-lg font-semibold text-gray-800 mb-3 select-none">

            <span>Tags</span>

            <svg
                    x-bind:class="openTags ? 'rotate-180' : ''"
                    class="w-5 h-5 transition-transform duration-200"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>

        </button>


        <div x-show="openTags" x-transition class="flex flex-wrap gap-3 items-center">

            <!-- All Tags -->
            <a href="<?php echo $blog_url; ?>" data-filter-link class="px-4 py-2 rounded-full text-sm font-medium transition
               <?php echo $tag_all_active
                ? 'bg-purple-600 text-white'
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>"> All Tags </a>

            <!-- Individual Tags -->
            <?php foreach ($tags as $tag) :
                $is_active = intval($tag->term_id) === $active_tag_id;
                ?>
                <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" data-filter-link class="px-4 py-2 rounded-full text-sm font-medium transition
                   <?php echo $is_active
                    ? 'bg-purple-600 text-white'
                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                    <?php echo esc_html($tag->name); ?>
                </a>
            <?php endforeach; ?>

        </div>
    </div>

</div>
