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
                <?php get_template_part( 'template-parts/single-bathrooms/gallery' ); ?>
            </div>

            <!-- RIGHT COLUMN (Meta + content + swatches + showrooms) -->
            <div class="lg:col-span-4 space-y-8">
                <?php get_template_part( 'template-parts/single-bathrooms/meta' ); ?>

                <div>
                    <a
                            href="#"
                            class="block w-full rounded-md bg-slate-900 px-4 py-3 text-center text-sm font-medium text-white hover:bg-black focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-900 focus-visible:ring-offset-2"
                    >
                        Book an appointment
                    </a>
                </div>

                <?php get_template_part( 'template-parts/single-bathrooms/swatches' ); ?>

            </div>
        </div>

        </div>

        <?php get_template_part( 'template-parts/single-bathrooms/showrooms' ); ?>

<div class="container-wide">

        <?php
        get_template_part('template-parts/components/cta-strip', null, [
            'title' => 'Need help choosing finishes and layouts?',
            'text'  => 'Visit one of our Norfolk showrooms or book a free design appointment with our team.',
            'button' => [
                'url'   => '/design-consultation/',
                'label' => 'Speak to our team',
            ],
            'tone' => 'light',
        ]);
        ?>

        <?php get_template_part( 'template-parts/single-bathrooms/related' ); ?>

</div>

<?php endwhile; endif; ?>

<?php get_footer(); ?>
