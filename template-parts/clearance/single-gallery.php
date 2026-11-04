<?php
$product = $args['product'] ?? null;

if ( ! $product instanceof WC_Product ) {
    return;
}

$product_id  = $product->get_id();
$main_image  = $product->get_image_id();
$gallery_ids = $product->get_gallery_image_ids();

$image_ids = [];

if ( $main_image ) {
    $image_ids[] = $main_image;
}

if ( ! empty( $gallery_ids ) && is_array( $gallery_ids ) ) {
    $image_ids = array_merge( $image_ids, $gallery_ids );
}

$image_ids = array_values( array_unique( array_filter( $image_ids ) ) );

if ( empty( $image_ids ) ) {
    return;
}

$images = [];

foreach ( $image_ids as $image_id ) {
    $full_url  = wp_get_attachment_image_url( $image_id, 'large' );
    $thumb_url = wp_get_attachment_image_url( $image_id, 'medium' );

    if ( ! $full_url ) {
        continue;
    }

    $alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
    $alt = $alt ? $alt : get_the_title( $product_id );

    $caption = wp_get_attachment_caption( $image_id );
    if ( ! $caption ) {
        $attachment = get_post( $image_id );
        if ( $attachment instanceof WP_Post ) {
            $caption = trim( (string) $attachment->post_excerpt );
        }
    }

    $images[] = [
        'url'     => $full_url,
        'thumb'   => $thumb_url ?: $full_url,
        'alt'     => $alt,
        'caption' => $caption ?: '',
    ];
}

if ( empty( $images ) ) {
    return;
}
?>

<div
        x-data="kitchenGallery(JSON.parse($el.querySelector('[data-gallery-json]').textContent))"
        x-init="init()"
        class="space-y-4"
>
    <script type="application/json" data-gallery-json>
        <?php echo wp_json_encode( $images ); ?>
    </script>

    <div class="single-product-main-image">
        <img
                :src="active.url"
                :alt="active.alt"
                @click="openFancybox()"
                class="max-h-[560px] w-full cursor-zoom-in object-contain"
        >
    </div>

    <?php if ( count( $images ) > 1 ) : ?>
        <div class="relative w-full">
            <div
                    class="flex gap-3 overflow-x-auto scrollbar-hide"
                    x-ref="thumbTrack"
            >
                <?php foreach ( $images as $index => $image ) : ?>
                    <button
                            type="button"
                            @click="setActive(<?php echo esc_js( $index ); ?>)"
                            class="flex w-24 flex-shrink-0 border border-neutral-200 bg-neutral-50 p-1.5 transition hover:border-neutral-400"
                            :class="{ 'border-neutral-900': activeIndex === <?php echo esc_js( $index ); ?> }"
                            aria-label="<?php echo esc_attr( sprintf( 'Show product image %d', $index + 1 ) ); ?>"
                    >
                        <img
                                src="<?php echo esc_url( $image['thumb'] ); ?>"
                                alt="<?php echo esc_attr( $image['alt'] ); ?>"
                                class="h-20 w-full object-contain"
                        >
                    </button>
                <?php endforeach; ?>
            </div>

            <button
                    x-show="$refs.thumbTrack.scrollWidth > $refs.thumbTrack.clientWidth"
                    @click="$refs.thumbTrack.scrollLeft -= 200"
                    type="button"
                    class="absolute left-0 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center border border-neutral-300 bg-white/90 hover:bg-white"
                    aria-label="Scroll thumbnails left"
            >
                ‹
            </button>

            <button
                    x-show="$refs.thumbTrack.scrollWidth > $refs.thumbTrack.clientWidth"
                    @click="$refs.thumbTrack.scrollLeft += 200"
                    type="button"
                    class="absolute right-0 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center border border-neutral-300 bg-white/90 hover:bg-white"
                    aria-label="Scroll thumbnails right"
            >
                ›
            </button>
        </div>
    <?php endif; ?>
</div>