<article class="group flex flex-col">

    <?php if ( has_post_thumbnail() ) : ?>
        <a href="<?php the_permalink(); ?>" class="block overflow-hidden rounded-xl mb-4">
            <?php the_post_thumbnail( 'large', [
                'class' => 'w-full h-56 object-cover rounded-xl transition-transform duration-300 group-hover:scale-[1.03]'
            ] ); ?>
        </a>
    <?php endif; ?>

    <?php
    $cats = get_the_category();
    if ( $cats ) : ?>
        <div class="text-sm text-blue-600 font-medium uppercase tracking-wide mb-2">
            <?php echo esc_html( $cats[0]->name ); ?>
        </div>
    <?php endif; ?>

    <h2 class="text-2xl font-semibold leading-snug mb-3">
        <a href="<?php the_permalink(); ?>"
            class="hover:underline decoration-2 underline-offset-2">
            <?php the_title(); ?>
        </a>
    </h2>

    <div class="text-sm text-gray-500 mb-4">
        <?php echo get_the_date(); ?>
    </div>

    <div class="prose prose-sm max-w-none text-gray-700 mb-4 line-clamp-4">
        <?php the_excerpt(); ?>
    </div>

    <a href="<?php the_permalink(); ?>"
        class="text-blue-600 font-medium hover:underline mt-auto">
        Read more!! →
    </a>

</article>

