<?php
/**
 * Standard grid card (equal).
 */
?>

<article class="group flex flex-col bg-white overflow-hidden border border-black/10">

    <?php if ( has_post_thumbnail() ) : ?>
        <a href="<?php the_permalink(); ?>" class="block relative aspect-[16/10] overflow-hidden">
            <?php the_post_thumbnail( 'large', [
                'class' => 'w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.03]'
            ] ); ?>

            <?php
            $cats = get_the_category();
            if ( ! empty( $cats ) ) :
                $primary = $cats[0];
                ?>
                <span class="absolute left-4 top-4 inline-flex items-center bg-white/90 px-3 py-1 font-body text-xs font-medium text-black shadow-sm">
          <?php echo esc_html( $primary->name ); ?>
        </span>
            <?php endif; ?>

        </a>
    <?php else : ?>
        <div class="aspect-[16/10] bg-gray-200"></div>
    <?php endif; ?>

    <div class="flex flex-col p-6 flex-1">

        <h2 class="mb-3 font-heading text-xl font-medium leading-snug tracking-wide line-clamp-2 text-gray-900">
            <a href="<?php the_permalink(); ?>" class="hover:underline decoration-black/30 underline-offset-4">
                <?php the_title(); ?>
            </a>
        </h2>

        <div class="mb-4 font-body text-sm text-black/60">
            <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                <?php echo esc_html( get_the_date() ); ?>
            </time>
        </div>

        <div class="prose-site mb-6 font-body text-sm text-black/70 line-clamp-3">
            <?php the_excerpt(); ?>
        </div>

        <a href="<?php the_permalink(); ?>" class="mt-auto font-body underline-offset-4 hover:underline">
            Read more →
        </a>

    </div>

</article>
