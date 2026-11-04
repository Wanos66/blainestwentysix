<?php get_header(); ?>

<?php $breadcrumbs = $args['breadcrumbs'] ?? true; ?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

    <div class="container-wide py-6">

        <!-- Breadcrumbs -->
        <?php if ( $breadcrumbs ) : ?>
            <div class="text-sm">
                <?php get_template_part( 'template-parts/components/breadcrumbs' ); ?>
            </div>
        <?php endif; ?>

        <!-- Main layout grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

            <!-- LEFT COLUMN (Gallery) -->
            <div class="lg:col-span-8">
                <?php get_template_part( 'template-parts/single-bedrooms/gallery' ); ?>
            </div>

            <!-- RIGHT COLUMN (Meta + content + swatches + showrooms) -->
            <div class="lg:col-span-4 space-y-8">
                <?php get_template_part( 'template-parts/single-bedrooms/meta' ); ?>

                <div>
                    <a
                            href="#"
                            class="block w-full rounded-md bg-slate-900 px-4 py-3 text-center text-sm font-medium text-white hover:bg-black focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-900 focus-visible:ring-offset-2"
                    >
                        Book an appointment
                    </a>
                </div>

                <?php get_template_part( 'template-parts/single-bedrooms/swatches' ); ?>
                <?php get_template_part( 'template-parts/single-bedrooms/showrooms' ); ?>
            </div>

        </div>

        <?php
        get_template_part('template-parts/components/cta-strip', null, [
            'title' => 'Ready to start planning your new bedroom?',
            'text'  => 'Visit one of our Norfolk showrooms or book a free design appointment with our team.',
            'button' => [
                'url'   => '/design-consultation/',
                'label' => 'Visit our showrooms',
            ],
            'tone' => 'light',
        ]);
        ?>

        <?php get_template_part( 'template-parts/single-bedrooms/related' ); ?>

    </div>

<?php endwhile; endif; ?>

<?php get_footer(); ?>
