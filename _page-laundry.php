<?php
/**
 * Laundry electrical products page.
 *
 * @package Blaines
 */

defined( 'ABSPATH' ) || exit;

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
        'category_slug' => 'laundry',
        'eyebrow'       => 'Electrical',
        'title'         => 'Laundry',
        'intro'         => 'Browse washing machines, tumble dryers and washer dryers available to buy online from Blaines, with local delivery or collection.',
    ]
);
?>

<?php
get_footer();
