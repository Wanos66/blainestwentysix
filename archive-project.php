<?php
/**
 * Projects Archive
 */

get_header();

$hero  = function_exists('get_field') ? get_field('projects_archive_hero_image', 'option') : null;
$title = function_exists('get_field') ? (get_field('projects_archive_hero_title', 'option') ?: 'Our Projects') : 'Our Projects';
$desc  = function_exists('get_field') ? get_field('projects_archive_hero_desc', 'option') : null;

get_template_part(
    'template-parts/components/page-header',
    null,
    [
        'hero'        => $hero,
        'title'       => $title,
        'intro'       => $desc,
        'breadcrumbs' => true,
        'align'       => 'center',
    ]
);
?>
    <section class="pb-10 lg:pb-12">
        <div class="container-wide pb-10 relative">

            <!-- FILTERS -->
            <?php
            get_template_part(
                'template-parts/components/filter-bar-project-type',
                null,
                [
                    'taxonomy'   => 'project_type',
                    'post_type'  => 'project',
                    'all_label'  => __('All', 'blainestwentysix'),
                    'aria_label' => __('Project filters', 'blainestwentysix'),
                ]
            );
            ?>

            <!-- RESULTS GRID -->
            <div class="grid gap-6 sm:gap-8 sm:grid-cols-2 lg:grid-cols-3">
                <?php if (have_posts()) : ?>
                    <?php while (have_posts()) : the_post(); ?>
                        <?php get_template_part('template-parts/projects/project', 'card'); ?>
                    <?php endwhile; ?>
                <?php else : ?>
                    <p class="font-body text-center text-slate-600">
                        <?php esc_html_e('No projects were found.', 'your-textdomain'); ?>
                    </p>
                <?php endif; ?>
            </div>

            <!-- PAGINATION -->
            <div id="js-pagination" class="mt-10">
                <?php get_template_part( 'template-parts/components/pagination' ); ?>
            </div>

        </div>
    </section>

<?php get_footer(); ?>