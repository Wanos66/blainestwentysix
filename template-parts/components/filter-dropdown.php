<?php
/**
 * Filter Dropdown (Categories + Tags)
 * Works with REST/AJAX filters.
 */

$blog_id  = get_option( 'page_for_posts' );
$blog_url = $blog_id ? get_permalink( $blog_id ) : home_url( '/' );

$current_category = isset( $_GET['category'] ) ? sanitize_text_field( $_GET['category'] ) : '';
$current_tag      = isset( $_GET['tag'] ) ? sanitize_text_field( $_GET['tag'] ) : '';

$categories = get_categories([
        'hide_empty' => false,
]);

if ( function_exists( 'blt_get_tags_for_category' ) && $current_category ) {
    $tags = blt_get_tags_for_category( $current_category );
} else {
    $tags = get_tags([
            'hide_empty' => false,
    ]);
}
?>

<div
        x-data="{ open: false }"
        data-filter-container
        class="
        w-full max-w-sm mx-auto
    md:mx-0
    border border-gray-300 rounded-xs bg-white shadow-md px-6 py-2 z-40
    "
>


    <!-- Trigger -->
    <button
            type="button"
            @click="open = !open"
            class="flex items-center justify-between w-full px-4 py-3 text-sm font-semibold text-gray-800"
    >
        <span>Filter posts</span>
        <svg
                :class="open ? 'rotate-180' : ''"
                class="w-4 h-4 transition-transform duration-200 text-gray-500"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                viewBox="0 0 24 24"
        >
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <!-- Panel -->
    <div x-show="open" x-collapse class="border-t border-gray-200 px-4 py-4 space-y-6">

        <!-- CATEGORIES -->
        <div class="space-y-2">
            <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500">Categories</h3>

            <div id="js-category-list" class="flex flex-wrap gap-2">
                <?php
                $all_cat_url = add_query_arg([
                        'category' => false,
                        'tag'      => $current_tag ?: false,
                        'page'     => false,
                ], $blog_url);
                ?>

                <!-- ALL Categories -->
                <a
                        href="<?php echo esc_url( $all_cat_url ); ?>"
                        class="
                        inline-flex items-center gap-2 px-3 py-1.5 rounded-xs transition
                        <?php echo $current_category === ''
                                ? 'bg-black text-white font-semibold'
                                : 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-100'; ?>
                    "
                        data-filter-link
                        data-category=""
                        data-tag="<?php echo esc_attr( $current_tag ); ?>"
                >
                    All
                </a>

                <?php foreach ( $categories as $cat ) : ?>
                    <?php
                    $is_active = ( $current_category === $cat->slug );
                    $cat_url = add_query_arg([
                            'category' => $cat->slug,
                            'tag'      => $current_tag ?: false,
                            'page'     => false,
                    ], $blog_url);
                    ?>
                    <a
                            href="<?php echo esc_url( $cat_url ); ?>"
                            class="
                            inline-flex items-center gap-2 px-3 py-1.5 rounded-xs text-xs font-medium transition
                            <?php echo $is_active
                                    ? 'bg-black text-white font-semibold'
                                    : 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-100'; ?>
                        "
                            data-filter-link
                            data-category="<?php echo esc_attr( $cat->slug ); ?>"
                            data-tag="<?php echo esc_attr( $current_tag ); ?>"
                    >
                        <?php echo esc_html( $cat->name ); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- TAGS -->
        <div class="space-y-2">
            <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500">Tags</h3>

            <div id="js-tag-list" class="flex flex-wrap gap-2">
                <?php
                $all_tag_url = add_query_arg([
                        'category' => $current_category ?: false,
                        'tag'      => false,
                        'page'     => false,
                ], $blog_url);
                ?>

                <!-- ALL Tags -->
                <a
                        href="<?php echo esc_url( $all_tag_url ); ?>"
                        class="
                        inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium transition
                        <?php echo $current_tag === ''
                                ? 'bg-black text-white font-semibold'
                                : 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-100'; ?>
                    "
                        data-filter-link
                        data-category="<?php echo esc_attr( $current_category ); ?>"
                        data-tag=""
                >
                    All
                </a>

                <?php if ( !empty( $tags ) && !is_wp_error( $tags ) ) : ?>
                    <?php foreach ( $tags as $tag ) : ?>
                        <?php
                        $is_active_tag = ( $current_tag === $tag->slug );
                        $tag_url = add_query_arg([
                                'category' => $current_category ?: false,
                                'tag'      => $tag->slug,
                                'page'     => false,
                        ], $blog_url);
                        ?>
                        <a
                                href="<?php echo esc_url( $tag_url ); ?>"
                                class="
                                inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium transition
                                <?php echo $is_active_tag
                                        ? 'bg-black text-white font-semibold'
                                        : 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-100'; ?>
                            "
                                data-filter-link
                                data-category="<?php echo esc_attr( $current_category ); ?>"
                                data-tag="<?php echo esc_attr( $tag->slug ); ?>"
                        >
                            <?php echo esc_html( $tag->name ); ?>
                        </a>
                    <?php endforeach; ?>
                <?php else : ?>
                    <span class="text-xs text-gray-500">No tags available.</span>
                <?php endif; ?>
            </div>
        </div>

        <!-- SEARCH -->
        <div class="space-y-2">
            <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500">Search</h3>

            <div class="relative">
                <!-- Search icon (left) -->
                <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"
                        fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>

                <!-- Input -->
                <input
                        type="text"
                        id="js-filter-search"
                        name="s"
                        placeholder="Search posts…"
                        value="<?php echo esc_attr( isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '' ); ?>"
                        class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-xs focus:ring-2 focus:ring-black focus:outline-none"
                >

                <!-- Clear button (right) -->
                <button
                        id="js-filter-search-clear"
                        type="button"
                        class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                >
                    ×
                </button>
            </div>
        </div>

        <!-- CLEAR ALL -->
        <div class="pt-4 border-t border-gray-200">
            <a
                    href="<?php echo esc_url( $blog_url ); ?>"
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xs
                       border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 transition"
                    data-filter-link
                    data-category=""
                    data-tag=""
            >
                Clear All
            </a>
        </div>

    </div>
</div>
