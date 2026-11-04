<?php
// home.php
get_header(); ?>

<?php
$news_hero      = get_field( 'news_archive_hero_image', 'option' );
$news_intro_t   = get_field( 'news_archive_intro_title', 'option' );
$news_intro_txt = get_field( 'news_archive_intro_text', 'option' );
$news_title     = $news_intro_t ?: 'News';

get_template_part(
    'template-parts/components/page-header',
    null,
    [
        'hero'        => $news_hero,
        'title'       => $news_title,
        'intro'       => $news_intro_txt,
        'breadcrumbs' => true,
        'align'       => 'center',
    ]
);
?>

<div class="container-wide mx-auto w-full pb-20 px-4 relative">

    <div class="mt-10 min-w-0 lg:grid lg:grid-cols-[minmax(0,1fr)_360px] lg:gap-10 lg:items-start">

        <!-- Main column (AJAX replaces THIS only) -->
        <div>
            <div id="js-content-area-wrapper">
                <?php
                get_template_part(
                    'template-parts/loop/posts-grid',
                    null,
                    [ 'show_sidebar' => false ]
                );
                ?>
            </div>

            <div id="js-pagination" class="mt-10">
                <?php get_template_part( 'template-parts/components/pagination' ); ?>
            </div>
        </div>

        <!-- Sidebar (static, consistent with single.php) -->
        <?php
        get_template_part( 'template-parts/components/sidebar', 'news', [
            'sticky'         => true,
            'sticky_top'     => 'top-32',
            'trending_count' => 5,
            'cta' => [
                'title'       => 'Planning a Project?',
                'text'        => 'Visit one of our showrooms for expert advice on kitchens, appliances and home technology.',
                'button_text' => 'Book a Consultation',
                'button_url'  => '/design-consultation/',
            ],
            'showrooms' => [
                'title' => 'Visit a Showroom',
                'items' => [
                    [ 'label' => 'Swaffham', 'url' => '/showrooms/swaffham/' ],
                    [ 'label' => 'Dereham',  'url' => '/showrooms/dereham/' ],
                    [ 'label' => 'Watton',   'url' => '/showrooms/watton/' ],
                ],
            ],
        ] );
        ?>

    </div>

</div>

<?php get_footer(); ?>



