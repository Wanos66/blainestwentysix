<?php
/**
 * Category Filter Bar
 * Used in home.php, archive.php, search.php, etc.
 */

// Current category (if on a category archive)
$current_cat = get_queried_object();
$active_cat_id = ( $current_cat && isset( $current_cat->term_id ) ) ? $current_cat->term_id : 0;

// Get all categories
$categories = get_categories([
    'hide_empty' => true, // set to false if you want all categories
]);

// Determine if “All” should be highlighted
$all_active = is_home() || ( is_archive() && ! is_category() );
?>

<div class="mb-12">
    <div class="flex flex-wrap gap-3 items-center">

        <!-- “All” option -->
        <a href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ); ?>"
            class="px-4 py-2 rounded-full text-sm font-medium transition
                  <?php echo $all_active
                ? 'bg-blue-600 text-white'
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
            All
        </a>

        <!-- Category options -->
        <?php foreach ( $categories as $cat ) :
            $is_active = intval( $cat->term_id ) === intval( $active_cat_id );
            ?>
            <a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>"
                class="px-4 py-2 rounded-full text-sm font-medium transition
                      <?php echo $is_active
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                <?php echo esc_html( $cat->name ); ?>
            </a>
        <?php endforeach; ?>

    </div>
</div>

