<?php
/**
 * Template part for displaying post cards
 *
 * @package BlainesTwentySix
 */
?>

<article <?php post_class('card group'); ?>>
    <?php if ( has_post_thumbnail() ) : ?>
        <div class="relative overflow-hidden">
            <a href="<?php the_permalink(); ?>" class="block">
                <?php the_post_thumbnail('large', array(
                    'class' => 'w-full h-64 object-cover transform group-hover:scale-105 transition-transform duration-300',
                    'loading' => 'lazy'
                )); ?>
            </a>
        </div>
    <?php endif; ?>

    <div class="card-body">
        <?php
        // Categories
        $categories = get_the_category();
        if ( ! empty( $categories ) ) : ?>
            <div class="flex gap-2 mb-3">
                <?php foreach ( $categories as $category ) : ?>
                    <a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>"
                        class="inline-block text-xs font-semibold text-blue-600 hover:text-blue-800 uppercase tracking-wider">
                        <?php echo esc_html( $category->name ); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <h2 class="card-title">
            <a href="<?php the_permalink(); ?>" class="hover:text-blue-600 transition-colors">
                <?php the_title(); ?>
            </a>
        </h2>

        <div class="flex items-center gap-4 text-sm text-gray-500 mb-4">
            <time datetime="<?php echo get_the_date('c'); ?>">
                <?php echo get_the_date(); ?>
            </time>
            <span>•</span>
            <span><?php echo esc_html( get_the_author() ); ?></span>
        </div>

        <div class="card-text mb-4">
            <?php the_excerpt(); ?>
        </div>

        <a href="<?php the_permalink(); ?>" class="btn btn-outline btn-sm">
            Read More
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
</article>
