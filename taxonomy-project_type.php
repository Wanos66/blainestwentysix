<?php
/**
 * Template for Project Type archive (Kitchen, Bathroom, etc.)
 */

get_header();
?>

<?php

$term = get_queried_object();
$hero_image = $term ? get_field('archive_hero_image', $term) : null;
$term_title  = single_term_title('', false);
$term_desc   = term_description();
?>

<?php if ($hero_image) : ?>
    <section
        class="relative h-[300px] md:h-[420px] w-full bg-cover bg-center flex items-center justify-center mb-6"
        style="background-image: url('<?php echo esc_url($hero_image); ?>');"
    >
        <!-- Dark overlay -->
        <div class="absolute inset-0 bg-black/40"></div>

        <!-- Overlay content -->
        <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-6">

            <h1 class="text-white text-4xl md:text-5xl font-bold drop-shadow-lg mb-4">
                <?php echo esc_html($term_title); ?> Projects
            </h1>

            <?php if ($term_desc) : ?>
                <div class="text-white text-lg opacity-90 leading-relaxed max-w-3xl mx-auto">
                    <?php echo wp_kses_post($term_desc); ?>
                </div>
            <?php endif; ?>

        </div>
    </section>
<?php endif; ?>


    <div class="container-wide py-6 relative">

        <?php get_template_part( 'template-parts/components/breadcrumbs' ); ?>

        <?php if (! $hero_image) : ?>
        <h1 class="text-4xl font-bold mb-6">
            <?php single_term_title(); ?> Projects
        </h1>
        <?php endif; ?>

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


        <!-- PROJECT GRID -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">

            <?php if ( have_posts() ) : ?>
                <?php while ( have_posts() ) : the_post(); ?>

                    <?php get_template_part( 'template-parts/projects/project', 'card' ); ?>

                <?php endwhile; ?>
            <?php else : ?>
                <p>No projects found.</p>
            <?php endif; ?>

        </div>

        <!-- Pagination -->
        <div id="js-pagination" class="mt-4">
            <?php
            $pagination = paginate_links([
                    'type'      => 'array',
                    'mid_size'  => 2,
                    'prev_text' => '«',
                    'next_text' => '»',
            ]);

            if ( $pagination ) : ?>
                <nav class="flex justify-center mt-12">
                    <ul class="flex items-center gap-2">
                        <?php foreach ( $pagination as $link ) : ?>
                            <li class="group">
                                <?php
                                echo str_replace(
                                        ['page-numbers current', 'page-numbers'],
                                        [
                                                'page-numbers current inline-flex items-center justify-center px-4 py-2 rounded-full bg-black text-white font-semibold',
                                                'page-numbers inline-flex items-center justify-center px-4 py-2 rounded-full border border-gray-300 text-gray-700 hover:bg-gray-100 transition'
                                        ],
                                        $link
                                );
                                ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>

    </div>

<?php get_footer(); ?>

