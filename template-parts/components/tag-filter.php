<?php
/**
 * Tag Filter Bar
 * Works on home.php, archive.php, search.php, etc.
 */

$current_tag = get_queried_object();
$active_tag_id = ( $current_tag && isset( $current_tag->term_id ) ) ? $current_tag->term_id : 0;

$tags = get_tags([
    'hide_empty' => true, // set to false to show all tags
]);

$all_active = ! is_tag();
?>

<div class="mb-12">
    <div class="flex flex-wrap gap-3 items-center">

        <!-- “All” Tags -->
        <a href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ); ?>"
            class="px-4 py-2 rounded-full text-sm font-medium transition
           <?php echo $all_active
                ? 'bg-purple-600 text-white'
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
            All Tags
        </a>

        <!-- Tag list -->
        <?php foreach ( $tags as $tag ) :
            $is_active = intval( $tag->term_id ) === intval( $active_tag_id );
            ?>
            <a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>"
                class="px-4 py-2 rounded-full text-sm font-medium transition
               <?php echo $is_active
                    ? 'bg-purple-600 text-white'
                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                <?php echo esc_html( $tag->name ); ?>
            </a>
        <?php endforeach; ?>

    </div>
</div>

