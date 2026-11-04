<?php
/**
 * Ex-display Product — Gallery
 *
 * Reuses the existing kitchenGallery Alpine behaviour by converting
 * WooCommerce product images into an ACF-gallery-style array.
 */

defined( 'ABSPATH' ) || exit;

$product = $args['product'] ?? null;

if ( ! $product instanceof WC_Product ) {
    return;
}

$product_id = $product->get_id();

$image_ids = [];

/**
 * Main product image first.
 */
$main_image_id = $product->get_image_id();

if ( $main_image_id ) {
    $image_ids[] = $main_image_id;
}

/**
 * Then WooCommerce gallery images.
 */
$gallery_image_ids = $product->get_gallery_image_ids();

if ( ! empty( $gallery_image_ids ) ) {
    $image_ids = array_merge( $image_ids, $gallery_image_ids );
}

$image_ids = array_values( array_unique( array_filter( $image_ids ) ) );

if ( empty( $image_ids ) ) {
    return;
}

$gallery = [];

foreach ( $image_ids as $image_id ) {
    $full_image = wp_get_attachment_image_src( $image_id, 'full' );
    $medium     = wp_get_attachment_image_src( $image_id, 'medium' );

    if ( ! $full_image ) {
        continue;
    }

    $alt     = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
    $caption = wp_get_attachment_caption( $image_id );

    $gallery[] = [
        'ID'      => $image_id,
        'id'      => $image_id,
        'url'     => $full_image[0],
        'alt'     => $alt ?: get_the_title( $image_id ),
        'caption' => $caption ?: '',
        'sizes'   => [
            'medium' => $medium[0] ?? $full_image[0],
        ],
    ];
}

if ( empty( $gallery ) ) {
    return;
}
?>

<div
    x-data="kitchenGallery(JSON.parse($el.querySelector('[data-gallery-json]').textContent))"
    x-init="init()"
    class="space-y-6"
>
    <script type="application/json" data-gallery-json>
        <?php echo wp_json_encode( $gallery ); ?>
    </script>

    <div class="relative w-full border border-neutral-200 bg-neutral-50">
        <img
            :src="active.url"
            :alt="active.alt"
            @click="openFancybox()"
            class="h-auto w-full cursor-zoom-in object-contain"
        >

        <template x-if="active.caption">
            <div class="absolute bottom-0 left-0 w-full bg-black/60 px-4 py-3 font-body text-sm text-white">
                <span x-text="active.caption"></span>
            </div>
        </template>
    </div>

    <div class="relative w-full">
        <div
            class="flex gap-3 overflow-x-auto scrollbar-hide"
            x-ref="thumbTrack"
        >
            <?php foreach ( $gallery as $index => $img ) : ?>
                <?php
                $thumb = $img['sizes']['medium'] ?? $img['url'];
                $alt   = $img['alt'] ?? '';
                ?>
                <button
                    type="button"
                    @click="setActive(<?php echo esc_js( $index ); ?>)"
                    class="w-1/4 flex-shrink-0 border border-neutral-300 bg-white transition hover:opacity-70"
                >
                    <img
                        src="<?php echo esc_url( $thumb ); ?>"
                        alt="<?php echo esc_attr( $alt ); ?>"
                        class="h-auto w-full object-contain"
                    >
                </button>
            <?php endforeach; ?>
        </div>

        <button
            x-show="$refs.thumbTrack.scrollWidth > $refs.thumbTrack.clientWidth"
            @click="$refs.thumbTrack.scrollLeft -= 200"
            class="absolute left-0 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center border border-neutral-300 bg-white/90 hover:bg-white"
            type="button"
            aria-label="Scroll thumbnails left"
        >
            ‹
        </button>

        <button
            x-show="$refs.thumbTrack.scrollWidth > $refs.thumbTrack.clientWidth"
            @click="$refs.thumbTrack.scrollLeft += 200"
            class="absolute right-0 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center border border-neutral-300 bg-white/90 hover:bg-white"
            type="button"
            aria-label="Scroll thumbnails right"
        >
            ›
        </button>
    </div>
</div>
