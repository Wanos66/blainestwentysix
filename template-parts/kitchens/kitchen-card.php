<?php
/**
 * Kitchen card template
 *
 * @package blainestwentysix
 */

$sale_badge = get_field('sale_banner');

// Brand (always one term)
$brand      = get_the_terms(get_the_ID(), 'kitchen_brand');
$brand_name = $brand && ! is_wp_error($brand) ? $brand[0]->name : '';

// Features repeater
$features = get_field('features');

$show_prices = (bool) get_field('show_kitchen_prices', 'option');

$from_price  = get_field('kitchen_from_price');   // e.g. "£2,918"
$from_month  = get_field('kitchen_from_monthly'); // e.g. "£39*/month"

$is_freedom_available = (bool) get_field('is_freedom_available');
?>

<article class="group flex flex-col overflow-hidden rounded-sm border border-slate-200 bg-white shadow-sm transition-shadow duration-200 hover:shadow-md">

    <!-- IMAGE -->
    <div class="relative block">
        <?php if ($sale_badge) : ?>
            <span class="absolute left-4 top-4 z-10 rounded-br-sm bg-red-800 px-3 py-1 text-sm font-semibold text-white">
                <?php echo esc_html($sale_badge); ?>% OFF
            </span>
        <?php endif; ?>

        <?php if ($is_freedom_available) : ?>
            <span
                    class="absolute right-4 top-4 z-10 flex h-[34px] w-[34px] items-center justify-center rounded-full border border-slate-300 bg-white text-sm leading-none text-[var(--freedom-accent)]"
                    aria-label="Available with Freedom accessibility options"
                    title="Available with Freedom accessibility options"
            >
                F
            </span>
        <?php endif; ?>

        <?php if (has_post_thumbnail()) : ?>
            <?php
            the_post_thumbnail(
                'large',
                [
                    'class'   => 'aspect-[16/10] w-full object-cover transition-transform duration-300 group-hover:scale-[1.02]',
                    'loading' => 'lazy',
                ]
            );
            ?>
        <?php else : ?>
            <div class="aspect-[16/10] bg-slate-100"></div>
        <?php endif; ?>
    </div>

    <!-- CONTENT -->
    <div class="flex flex-1 flex-col px-5 py-6">

        <!-- TITLE + BRAND -->
        <div class="mb-2 flex items-start justify-between">
            <h3 class="font-heading text-3xl leading-snug tracking-wide text-slate-900">
                <a
                        href="<?php the_permalink(); ?>"
                        class="rounded-sm decoration-1 underline-offset-4 group-hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-900 focus-visible:ring-offset-2"
                        aria-label="View <?php echo esc_attr(get_the_title()); ?> kitchen range"
                >
                    <?php the_title(); ?>
                </a>
            </h3>

            <?php if ($brand_name) : ?>
                <span class="ml-3 shrink-0 rounded-sm border border-slate-300 px-2 py-0.5 text-[10px] uppercase tracking-wide text-slate-700">
                    <?php echo esc_html($brand_name); ?>
                </span>
            <?php endif; ?>
        </div>

        <?php if ($show_prices && ($from_price || $from_month)) : ?>
            <p class="mt-2 text-slate-800">
                <span class="font-medium">From</span>
                <?php if ($from_price) : ?>
                    <span><?php echo esc_html($from_price); ?></span>
                <?php endif; ?>

                <?php if ($from_month) : ?>
                    <span class="text-slate-600"> or </span>
                    <span><?php echo esc_html($from_month); ?></span>
                <?php endif; ?>
            </p>
        <?php endif; ?>

        <!-- FEATURES (bullet points) -->
        <?php if ($features) : ?>
            <ul class="mb-6 mt-3 space-y-1.5">
                <?php foreach ($features as $row) : ?>
                    <?php if (! empty($row['feature_text'])) : ?>
                        <li class="flex items-start gap-2 text-sm leading-relaxed text-slate-700">
                            <span class="mt-0.5 text-green-700">✓</span>
                            <span><?php echo esc_html($row['feature_text']); ?></span>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        <?php else : ?>
            <?php if (has_excerpt()) : ?>
                <p class="font-body font-extralight mb-6 mt-2 text-sm leading-relaxed text-slate-700">
                    <?php echo esc_html(get_the_excerpt()); ?>
                </p>
            <?php endif; ?>
        <?php endif; ?>

        <!-- CTA BUTTON -->
        <a
                href="<?php the_permalink(); ?>"
                class="mt-auto block w-full rounded-md bg-slate-900 px-4 py-3 text-center text-sm font-medium text-white hover:bg-black focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-900 focus-visible:ring-offset-2"
        >
            View <?php echo esc_html(get_the_title()); ?> kitchen collection
        </a>

    </div>
</article>