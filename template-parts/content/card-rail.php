<?php
/**
 * Rail post card (compact, fixed-height).
 */
?>

<article class="group bg-white overflow-hidden border border-black/10">
    <a href="<?php the_permalink(); ?>" class="flex h-36 md:h-40">

        <!-- Thumbnail (fixed) -->
        <div class="w-40 md:w-44 shrink-0 overflow-hidden">
            <?php if ( has_post_thumbnail() ) : ?>
                <?php the_post_thumbnail( 'medium_large', [
                    'class' => 'w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.03]'
                ] ); ?>
            <?php else : ?>
                <div class="w-full h-full bg-gray-200"></div>
            <?php endif; ?>
        </div>

        <!-- Content (compact) -->
        <div class="flex-1 px-5 py-4 flex flex-col">
            <?php
            $cats = get_the_category();
            if ( ! empty( $cats ) ) :
                $primary = $cats[0];
                ?>
                <span class="inline-flex items-center bg-black/5 px-3 py-1 text-xs font-medium text-black/70 w-fit">
					<?php echo esc_html( $primary->name ); ?>
				</span>
            <?php endif; ?>

            <h2 class="mt-2 text-base md:text-lg font-semibold leading-snug tracking-tight line-clamp-2">
				<span class="group-hover:underline decoration-black/30 underline-offset-4">
					<?php the_title(); ?>
				</span>
            </h2>

            <div class="mt-auto text-sm text-black/60">
                <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                    <?php echo esc_html( get_the_date() ); ?>
                </time>
            </div>
        </div>

    </a>
</article>
