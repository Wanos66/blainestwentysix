<?php
// archive.php
get_header(); ?>

<div class="container-wide w-full py-20 relative">

    <?php if ( function_exists('blainestwentysix_filters_enabled') && blainestwentysix_filters_enabled() ) : ?>
        <?php get_template_part( 'template-parts/components/filter-dropdown' ); ?>
    <?php endif; ?>

    <!-- This ALWAYS gets replaced by AJAX -->
    <div id="js-content-area-wrapper">
        <?php get_template_part( 'template-parts/loop/content-area' ); ?>

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
                            // Style active & inactive links
                            echo str_replace(
                                    ['page-numbers current', 'page-numbers'],
                                    [
                                        // active
                                            'page-numbers current inline-flex items-center justify-center px-4 py-2 rounded-full bg-black text-white font-semibold',
                                        // normal
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



