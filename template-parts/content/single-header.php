<?php if ( has_post_thumbnail() ) : ?>
    <div class="mb-8">
        <?php the_post_thumbnail( 'full', [
            'class' => 'w-full h-auto rounded-2xl'
        ] ); ?>
    </div>
<?php endif; ?>

<h1 class="text-5xl font-bold leading-tight mb-6">
    <?php the_title(); ?>
</h1>

<div class="text-sm text-gray-500 mb-10">
    Posted on <?php echo get_the_date(); ?>
    <?php if ( get_the_category_list() ) : ?>
        · In <?php the_category(', '); ?>
    <?php endif; ?>
</div>

