<?php
/**
 * Meta block for Single Bathroom
 */

$styles = get_the_term_list( get_the_ID(), 'bathroom_style', '', ', ' );
$brands = get_the_term_list( get_the_ID(), 'bathroom_brand', '', ', ' );
$features = get_field( 'features' );

// Sale banner field (Example: "20" → "20% OFF")
$sale = get_field('sale_banner');
?>

<!-- TITLE -->
<h1 class="font-heading text-[32px] font-medium tracking-wide leading-tight text-slate-900 mb-2">
    <?php the_title(); ?>
</h1>

<!-- STYLE + BRAND -->
<div class="flex items-start justify-between w-full mb-4">
<div class="space-y-1 text-sm text-slate-700">
    <?php if ( $styles ) : ?>
        <p><span class="font-semibold text-slate-900">Style:</span> <?php echo wp_kses_post( $styles ); ?></p>
    <?php endif; ?>

    <?php if ( $brands ) : ?>
        <p><span class="font-semibold text-slate-900">Brand:</span> <?php echo wp_kses_post( $brands ); ?></p>
    <?php endif; ?>
</div>

    <!-- Right: Sale badge -->
    <?php if ( $sale ) : ?>
        <span class="ml-4 bg-red-700 text-white text-xl font-semibold px-3 py-1 uppercase tracking-wide">
            <?php echo esc_html( $sale ); ?>% OFF
        </span>
    <?php endif; ?>
</div>

<div>
    <!-- FEATURES (bullet points) -->
    <?php if ( $features ) : ?>
        <ul class="mt-3 mb-3 space-y-1.5">
            <?php foreach ( $features as $row ) : ?>
                <?php if ( ! empty( $row['feature_text'] ) ) : ?>
                    <li class="flex items-start gap-2 text-sm text-slate-700 font-lexend-extralight leading-relaxed">
                        <span class="mt-0.5">&bull;</span>
                        <span><?php echo esc_html( $row['feature_text'] ); ?></span>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

<!-- DESCRIPTION / CONTENT -->
<div class="prose prose-slate max-w-none mt-3 text-[15px] font-extralight leading-relaxed">
    <?php the_content(); ?>
</div>

