<?php
/**
 * Template Name: Main Showrooms Page
 * Template Post Type: page
 */

get_header();

$hero = function_exists('get_field') ? get_field('showrooms_hero') : null;
$intro = function_exists('get_field') ? get_field('showrooms_intro') : '';
$logo = function_exists('get_field') ? get_field('showrooms_logo') : null;

get_template_part(
    'template-parts/components/page-header',
    null,
    [
        'hero'        => $hero,
        'title'       => get_the_title(),
        'intro'       => $intro,
        'breadcrumbs' => true,
        'align'       => 'center',
        'logo'        => $logo,
    ]
);
?>

<?php get_template_part('template-parts/components/showrooms-grid'); ?>

<?php get_footer(); ?>