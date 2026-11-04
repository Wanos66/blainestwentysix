<?php
/**
 * Single Project Content Layout
 */

// Featured image
if ( has_post_thumbnail() ) :
    ?>
    <div class="mb-10">
        <?php the_post_thumbnail( 'full', ['class' => 'w-full h-auto'] ); ?>
    </div>
<?php endif; ?>

<div class="container mx-auto px-4 max-w-screen-xl">

    <?php get_template_part( 'template-parts/components/breadcrumbs' ); ?>

    <h1 class="text-4xl font-bold mb-4"><?php the_title(); ?></h1>

    <?php get_template_part( 'template-parts/project', 'meta' ); ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 mt-10">

        <!-- LEFT COLUMN: Article Content -->
        <div class="prose-project order-2 lg:order-1">
            <?php the_content(); ?>
        </div>

        <!-- RIGHT COLUMN: Gallery -->
        <div class="order-1 lg:order-2">
            <?php get_template_part( 'template-parts/project', 'gallery' ); ?>
        </div>

    </div>

</div>

