<?php
/**
 * Transactional single-product gallery.
 */

defined('ABSPATH') || exit;

$product = $args['product'] ?? null;

if ( ! $product instanceof WC_Product) {
    return;
}

$product_id    = $product->get_id();
$product_title = get_the_title($product_id);
$main_image_id = (int)$product->get_image_id();
$gallery_ids   = $product->get_gallery_image_ids();
$image_ids     = [];

if ($main_image_id > 0) {
    $image_ids[] = $main_image_id;
}

if (is_array($gallery_ids) && ! empty($gallery_ids)) {
    $image_ids = array_merge($image_ids, array_map('absint', $gallery_ids));
}

$image_ids = array_values(array_unique(array_filter($image_ids)));
$images    = [];

foreach ($image_ids as $image_id) {
    $display_url = wp_get_attachment_image_url($image_id, 'woocommerce_single');
    $full_url    = wp_get_attachment_image_url($image_id, 'full');
    $thumb_url   = wp_get_attachment_image_url($image_id, 'woocommerce_thumbnail');

    if ( ! $display_url) {
        $display_url = wp_get_attachment_image_url($image_id, 'large');
    }

    if ( ! $display_url) {
        continue;
    }

    if ( ! $full_url) {
        $full_url = $display_url;
    }

    if ( ! $thumb_url) {
        $thumb_url = $display_url;
    }

    $alt = trim((string)get_post_meta($image_id, '_wp_attachment_image_alt', true));

    if ('' === $alt) {
        $alt = $product_title;
    }

    $images[] = [
        'display' => $display_url,
        'full'    => $full_url,
        'thumb'   => $thumb_url,
        'alt'     => $alt,
        'caption' => trim((string)wp_get_attachment_caption($image_id)),
    ];
}

if (empty($images)) {
    $placeholder = wc_placeholder_img_src('woocommerce_single');

    if ($placeholder) {
        $images[] = [
            'display' => $placeholder,
            'full'    => $placeholder,
            'thumb'   => wc_placeholder_img_src('woocommerce_thumbnail') ?: $placeholder,
            'alt'     => $product_title,
            'caption' => '',
        ];
    }
}

if (empty($images)) {
    return;
}

$image_count = count($images);
$first_image = $images[0];
?>

<div x-data="transactionalProductGallery(JSON.parse($el.querySelector('[data-gallery-json]').textContent))"
        x-init="init()"
        class="retail-product-gallery min-w-0 max-w-full space-y-4"
        @keydown.left.prevent="previous()"
        @keydown.right.prevent="next()">
    <script type="application/json" data-gallery-json>
        <?php echo wp_json_encode($images); ?>

    </script>

    <div class="retail-product-gallery__stage relative bg-white">
        <button type="button"
                class="retail-product-gallery__open group flex min-h-[360px] w-full items-center justify-center p-3 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-blue-700 sm:min-h-[460px] sm:p-5 lg:min-h-[560px]"
                @click="openFancybox()"
                aria-label="Open product image gallery">
            <img src="<?php echo esc_url($first_image['display']); ?>"
                    alt="<?php echo esc_attr($first_image['alt']); ?>"
                    :src="active.display"
                    :alt="active.alt"
                    class="retail-product-gallery__image max-h-[330px] w-full object-contain transition duration-200 group-hover:scale-[1.01] sm:max-h-[430px] lg:max-h-[530px]"
                    fetchpriority="high">
        </button>

        <?php if ($image_count > 1) : ?>
            <p class="retail-product-gallery__count pointer-events-none absolute left-2 top-2 text-xs font-semibold text-neutral-500 sm:left-3 sm:top-3" aria-live="polite">
                <span x-text="activeIndex + 1">1</span> <span aria-hidden="true"> / </span> <span><?php echo esc_html($image_count); ?></span>
            </p>

            <button type="button"
                    class="retail-product-gallery__nav retail-product-gallery__nav--previous absolute left-2 top-1/2 flex h-10 w-10 -translate-y-1/2 items-center justify-center bg-white/90 text-neutral-900 shadow-sm transition hover:bg-neutral-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-700 focus-visible:ring-offset-2 sm:left-3"
                    @click.stop="previous()"
                    aria-label="Show previous product image">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                    <path d="m12.5 4.5-5.5 5.5 5.5 5.5" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>

            <button type="button"
                    class="retail-product-gallery__nav retail-product-gallery__nav--next absolute right-2 top-1/2 flex h-10 w-10 -translate-y-1/2 items-center justify-center bg-white/90 text-neutral-900 shadow-sm transition hover:bg-neutral-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-700 focus-visible:ring-offset-2 sm:right-3"
                    @click.stop="next()"
                    aria-label="Show next product image">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                    <path d="m7.5 4.5 5.5 5.5-5.5 5.5" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        <?php endif; ?>
    </div>

    <button type="button"
            class="retail-product-gallery__full-size mx-auto flex items-center gap-2 text-xs font-medium text-neutral-500 transition hover:text-neutral-950 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-700 focus-visible:ring-offset-2"
            @click="openFancybox()">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle cx="11" cy="11" r="6.5" stroke="currentColor" stroke-width="1.75"/>
            <path d="m16 16 4 4M11 8.5v5M8.5 11h5" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/>
        </svg>
        <span>View full-size image</span>
    </button>

    <?php if ($image_count > 1) : ?>
        <div class="relative min-w-0 max-w-full">

            <?php if ($image_count > 6) : ?>
                <button
                        type="button"
                        class="absolute left-0 top-1/2 z-10 hidden h-10 w-10 -translate-y-1/2 items-center justify-center border border-neutral-300 bg-white text-neutral-900 shadow-sm transition hover:border-neutral-500 hover:bg-neutral-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-700 sm:flex"
                        @click="$refs.thumbTrack.scrollBy({
                    left: -440,
                    behavior: 'smooth'
                })"
                        aria-label="Scroll product thumbnails left"
                >
                    <svg
                            class="h-5 w-5"
                            viewBox="0 0 20 20"
                            fill="none"
                            aria-hidden="true"
                    >
                        <path
                                d="m12.5 4.5-5.5 5.5 5.5 5.5"
                                stroke="currentColor"
                                stroke-width="1.75"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                        />
                    </svg>
                </button>
            <?php endif; ?>

            <div
                    class="retail-product-gallery__thumb-track flex min-w-0 max-w-full snap-x snap-mandatory gap-3 overflow-x-auto pb-1 scrollbar-hide <?php echo $image_count > 6 ? 'sm:mx-12 sm:max-w-[calc(100%-6rem)]' : ''; ?>"
                    x-ref="thumbTrack"
                    aria-label="Product image thumbnails"
            >
                <?php foreach ($images as $index => $image) : ?>
                    <button
                            type="button"
                            data-gallery-thumb="<?php echo esc_attr($index); ?>"
                            @click="setActive(<?php echo esc_js($index); ?>)"
                            class="retail-product-gallery__thumb relative flex h-24 w-24 shrink-0 snap-start items-center justify-center bg-neutral-50 p-2 transition hover:bg-neutral-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-700 focus-visible:ring-offset-2"
                            :class="activeIndex === <?php echo esc_js($index); ?>
                        ? 'bg-white opacity-100'
                        : 'opacity-60 hover:opacity-100'"
                            :aria-current="activeIndex === <?php echo esc_js($index); ?>
                        ? 'true'
                        : 'false'"
                            aria-label="<?php echo esc_attr(
                                sprintf(
                                    'Show product image %d of %d',
                                    $index + 1,
                                    $image_count
                                )
                            ); ?>"
                    >
                        <img
                                src="<?php echo esc_url($image['thumb']); ?>"
                                alt="<?php echo esc_attr($image['alt']); ?>"
                                class="h-full w-full object-contain"
                                loading="lazy"
                        >

                        <span
                                aria-hidden="true"
                                class="absolute inset-x-2 bottom-0 h-0.5 bg-blue-700 transition-opacity"
                                :class="activeIndex === <?php echo esc_js($index); ?>
                            ? 'opacity-100'
                            : 'opacity-0'"
                        ></span>
                    </button>
                <?php endforeach; ?>
            </div>

            <?php if ($image_count > 6) : ?>
                <button
                        type="button"
                        class="absolute right-0 top-1/2 z-10 hidden h-10 w-10 -translate-y-1/2 items-center justify-center border border-neutral-300 bg-white text-neutral-900 shadow-sm transition hover:border-neutral-500 hover:bg-neutral-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-700 sm:flex"
                        @click="$refs.thumbTrack.scrollBy({
                    left: 440,
                    behavior: 'smooth'
                })"
                        aria-label="Scroll product thumbnails right"
                >
                    <svg
                            class="h-5 w-5"
                            viewBox="0 0 20 20"
                            fill="none"
                            aria-hidden="true"
                    >
                        <path
                                d="m7.5 4.5 5.5 5.5-5.5 5.5"
                                stroke="currentColor"
                                stroke-width="1.75"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                        />
                    </svg>
                </button>
            <?php endif; ?>

        </div>
    <?php endif; ?>

    <p x-show="active.caption" x-cloak x-text="active.caption" class="retail-product-gallery__caption text-sm leading-relaxed text-neutral-500"></p>
</div>
