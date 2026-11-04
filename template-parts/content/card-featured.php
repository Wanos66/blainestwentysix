<?php
/**
 * Featured post card (lead story) — editorial style.
 */
?>

<article class="group bg-white overflow-hidden border border-black/10 h-full flex flex-col">

    <?php if ( has_post_thumbnail() ) : ?>
        <a href="<?php the_permalink(); ?>" class="block relative h-80 lg:h-96 overflow-hidden">
        <?php the_post_thumbnail( 'large', [
                'class' => 'w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.02]'
            ] ); ?>
        </a>
    <?php else : ?>
        <div class="aspect-[16/10] bg-gray-200"></div>
    <?php endif; ?>

    <div class="p-7 md:p-8 flex-1 flex flex-col">

    <?php
        $cats = get_the_category();
        $cat_name = ! empty( $cats ) ? $cats[0]->name : '';
        ?>

        <div class="mb-4 flex flex-wrap items-center gap-x-3 gap-y-1 font-body text-sm text-black/60">
            <?php if ( $cat_name ) : ?>
                <span class="inline-flex items-center bg-black/5 px-3 py-1 font-body text-xs font-medium text-black/70">
					<?php echo esc_html( $cat_name ); ?>
				</span>
            <?php endif; ?>

            <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                <?php echo esc_html( get_the_date() ); ?>
            </time>
        </div>

        <h2 class="font-heading text-3xl font-medium leading-wide tracking-[-0.02em] text-gray-900 md:text-4xl">
            <a href="<?php the_permalink(); ?>" class="hover:underline decoration-black/30 underline-offset-4">
                <?php the_title(); ?>
            </a>
        </h2>

        <div class="prose-site mt-4 font-body text-base text-black/70 line-clamp-3">
            <?php the_content(); ?>
        </div>

    </div>

</article>
