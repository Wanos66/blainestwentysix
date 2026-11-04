<?php
/**
 * Bathroom card template
 *
 * @package blainestwentysix
 */

$sale_badge = get_field( 'sale_banner' );

// Brand (always one term)
$brand      = get_the_terms( get_the_ID(), 'bathroom_brand' );
$brand_name = $brand && ! is_wp_error( $brand ) ? $brand[0]->name : '';

// Features repeater
$features = get_field( 'features' );
?>

<article class="group overflow-hidden rounded-sm border border-slate-200 bg-white shadow-sm transition-shadow duration-200 hover:shadow-md flex flex-col">

    <!-- IMAGE -->
    <div class="block relative">
        <?php if ( $sale_badge ) : ?>
            <span class="absolute left-4 top-4 z-10 bg-red-800 px-3 py-1 text-sm font-semibold text-white">
                <?php echo esc_html( $sale_badge ); ?>% OFF
            </span>
        <?php endif; ?>

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
            <h3 class="text-3xl font-heading font-medium tracking-wide text-slate-900 leading-snug">
                <a
                        href="<?php the_permalink(); ?>"
                        class="group-hover:underline decoration-1 underline-offset-4 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-900 focus-visible:ring-offset-2 rounded-sm"
                        aria-label="View <?php echo esc_attr( get_the_title() ); ?> bathroom range"
                >
                    <?php the_title(); ?>
                </a>
            </h3>


            <?php if ( $brand_name ) : ?>
                <span class="ml-3 shrink-0 rounded-sm border border-slate-300 px-2 py-0.5 text-[10px] uppercase tracking-wide text-slate-700">
                    <?php echo esc_html( $brand_name ); ?>
                </span>
            <?php endif; ?>
        </div>

        <!-- FEATURES (bullet points) -->
        <?php if ( $features ) : ?>
            <ul class="mt-3 mb-6 space-y-1.5">
                <?php foreach ( $features as $row ) : ?>
                    <?php if ( ! empty( $row['feature_text'] ) ) : ?>
                        <li class="flex items-start gap-2 text-sm text-slate-700 font-lexend-extralight leading-relaxed">
                            <span class="mt-0.5 text-green-700">✓</span>
                            <span><?php echo esc_html( $row['feature_text'] ); ?></span>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        <?php else : ?>
            <!-- fallback: show excerpt -->
            <?php if ( has_excerpt() ) : ?>
                <p class="font-body font-light mb-6 mt-2 text-sm leading-relaxed text-slate-700">
                    <?php echo esc_html( get_the_excerpt() ); ?>
                </p>
            <?php endif; ?>
        <?php endif; ?>

        <!-- CTA BUTTON -->
        <a
            href="<?php the_permalink(); ?>"
                class="mt-auto block w-full rounded-md bg-slate-900 px-4 py-3 text-center text-sm font-body font-medium text-white hover:bg-black focus:outline-none
                focus-visible:ring-2 focus-visible:ring-slate-900 focus-visible:ring-offset-2"
        >
            View <?php the_title(); ?> bathroom collection
        </a>

    </div>
</article>

