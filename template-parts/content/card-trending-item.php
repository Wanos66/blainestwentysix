<?php
/**
 * Trending list item (right panel).
 */
?>

<a href="<?php the_permalink(); ?>"
        class="group flex items-start gap-4 px-6 py-5 transition-colors hover:bg-black/[0.02]">

    <!-- Thumbnail -->
    <div class="w-24 h-16 shrink-0 overflow-hidden bg-black/5">
        <?php if ( has_post_thumbnail() ) : ?>
            <?php the_post_thumbnail( 'medium', [
                'class' => 'w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.03]'
            ] ); ?>
        <?php endif; ?>
    </div>

    <!-- Text block -->
    <div class="min-w-0 flex flex-col justify-between">

        <!-- Title -->
        <h3 class="min-h-[2.6em] font-body text-sm font-medium leading-snug tracking-wide line-clamp-2 text-gray-900">
            <span class="group-hover:underline decoration-black/30 underline-offset-4">
                <?php the_title(); ?>
            </span>
        </h3>

        <!-- Meta -->
        <div class="mt-2 font-body text-xs text-black/50">
            <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                <?php echo esc_html( get_the_date() ); ?>
            </time>

            <?php
            $cats = get_the_category();
            if ( ! empty( $cats ) ) :
                echo ' <span class="text-black/30 mx-1">·</span> ';
                echo esc_html( $cats[0]->name );
            endif;
            ?>
        </div>

    </div>
</a>