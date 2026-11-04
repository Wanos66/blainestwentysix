<?php
/**
 * Single Kitchen — Gallery
 * - 4 visible thumbs
 * - scrollable thumbnail strip
 * - always-visible caption
 * - no rounded edges
 * - supports Freedom mode gallery override
 */

$post_id             = $args['post_id'] ?? get_the_ID();
$is_freedom_mode     = ! empty($args['is_freedom_mode']);
$use_freedom_gallery = ! empty($args['use_freedom_gallery']);
$freedom_gallery     = $args['freedom_gallery'] ?? [];

/*
|--------------------------------------------------------------------------
| Resolve gallery source
|--------------------------------------------------------------------------
| Freedom mode uses the Freedom accessibility gallery when available.
| Otherwise fall back to the normal kitchen gallery field.
|--------------------------------------------------------------------------
*/
if ($is_freedom_mode && $use_freedom_gallery && ! empty($freedom_gallery) && is_array($freedom_gallery)) {
    $gallery = $freedom_gallery;
} else {
    $gallery = get_field('kitchen_gallery_image', $post_id);
}

if (! $gallery || ! is_array($gallery)) {
    return;
}
?>

<div
        x-data="kitchenGallery(JSON.parse($el.querySelector('[data-gallery-json]').textContent))"
        x-init="init()"
        class="space-y-6"
>
    <script type="application/json" data-gallery-json>
        <?php echo wp_json_encode($gallery); ?>
    </script>

    <!-- MAIN IMAGE WITH ALWAYS-VISIBLE CAPTION -->
    <div class="relative w-full">
        <img
                :src="active.url"
                :alt="active.alt"
                @click="openFancybox()"
                class="h-auto w-full cursor-zoom-in object-contain"
        >

        <!-- Always-visible caption -->
        <div class="absolute bottom-0 left-0 w-full bg-black/60 px-4 py-3 text-sm font-body text-white">
            <span x-text="active.caption"></span>
        </div>
    </div>

    <!-- THUMBNAIL STRIP (exactly 4 visible) -->
    <div class="relative w-full">
        <div
                class="flex gap-3 overflow-x-auto scrollbar-hide"
                x-ref="thumbTrack"
        >
            <?php foreach ($gallery as $index => $img) : ?>
                <?php
                $thumb = $img['sizes']['medium'] ?? $img['url'];
                $alt   = $img['alt'] ?? '';
                ?>
                <button
                        type="button"
                        @click="setActive(<?php echo esc_js($index); ?>)"
                        class="w-1/4 flex-shrink-0 border border-slate-300 transition hover:opacity-70"
                >
                    <img
                            src="<?php echo esc_url($thumb); ?>"
                            alt="<?php echo esc_attr($alt); ?>"
                            class="h-auto w-full object-contain"
                    >
                </button>
            <?php endforeach; ?>
        </div>

        <!-- LEFT ARROW -->
        <button
                x-show="$refs.thumbTrack.scrollWidth > $refs.thumbTrack.clientWidth"
                @click="$refs.thumbTrack.scrollLeft -= 200"
                class="absolute left-0 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center border border-slate-300 bg-white/90 hover:bg-white"
                type="button"
        >
            ‹
        </button>

        <!-- RIGHT ARROW -->
        <button
                x-show="$refs.thumbTrack.scrollWidth > $refs.thumbTrack.clientWidth"
                @click="$refs.thumbTrack.scrollLeft += 200"
                class="absolute right-0 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center border border-slate-300 bg-white/90 hover:bg-white"
                type="button"
        >
            ›
        </button>
    </div>
</div>