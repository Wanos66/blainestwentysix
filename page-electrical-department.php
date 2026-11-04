<?php
/**
 * Shared Electrical transactional department page.
 *
 * Used for Laundry, Refrigeration, Dishwashers, Cooking,
 * Small Appliances, Floorcare and TV & Entertainment.
 *
 * @package Blaines
 */

defined( 'ABSPATH' ) || exit;

$page_id = get_queried_object_id();

if ( ! $page_id ) {
    return;
}

$category_slug = sanitize_title(
    (string) get_post_field( 'post_name', $page_id )
);

$title = sanitize_text_field(
    (string) get_the_title( $page_id )
);

get_header();
?>

    <div class="container-wide py-3 text-sm">
        <?php get_template_part( 'template-parts/components/breadcrumbs' ); ?>
    </div>

<?php
get_template_part(
    'template-parts/woocommerce/archive-transactional-products',
    null,
    [
        'category_slug' => $category_slug,
        'title'         => $title,
        'intro'         => '',
    ]
);
?>

<?php
get_footer();
