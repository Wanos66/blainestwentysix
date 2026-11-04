<?php
/**
 * Project card template
 *
 * @package blainestwentysix
 */

?>

<article class="group overflow-hidden rounded-sm border border-slate-200 bg-white shadow-sm transition-shadow duration-200 hover:shadow-md flex flex-col">

    <!-- IMAGE -->
    <div>
        <?php if ( has_post_thumbnail() ) : ?>
            <?php the_post_thumbnail(
                    'large',
                    [
                            'class' => 'aspect-[16/10] w-full object-cover transition-transform duration-300 group-hover:scale-[1.02]',
                            'loading' => 'lazy',
                    ]
            ); ?>
        <?php else : ?>
            <div class="aspect-[16/10] bg-slate-100"></div>
        <?php endif; ?>
    </div>

    <!-- CONTENT -->
    <div class="flex flex-col flex-1 px-5 py-6">

        <!-- TITLE + BRAND -->
        <div class="flex items-start justify-between mb-2">
            <h3 class="font-heading text-xl font-medium leading-snug tracking-wide text-slate-900">
                <a
                        href="<?php the_permalink(); ?>"
                        class="group-hover:underline decoration-1 underline-offset-4 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-900 focus-visible:ring-offset-2 rounded-sm"
                        aria-label="View <?php echo esc_attr( get_the_title() ); ?>"
                >
                    <?php the_title(); ?>
                </a>
            </h3>
        </div>
            <!-- fallback: show excerpt -->
            <?php if ( has_excerpt() ) : ?>
                <p class="mt-2 mb-6 font-body font-light text-sm leading-relaxed text-slate-700">
                    <?php echo esc_html( get_the_excerpt() ); ?>
                </p>
            <?php endif; ?>

        <!-- CTA BUTTON -->
        <a
                href="<?php the_permalink(); ?>"
                class="mt-auto block w-full rounded-md bg-slate-900 px-4 py-3 text-center font-body text-sm font-medium text-white hover:bg-black focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-900 focus-visible:ring-offset-2"
        >
            See this project
        </a>

    </div>
</article>
