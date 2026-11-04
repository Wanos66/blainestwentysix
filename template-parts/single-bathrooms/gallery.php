<?php
/**
 * Single Bathhroom — Gallery (4 visible thumbs, scrollable, always-on caption, NO rounded edges)
 */

$gallery = get_field('gallery_image');
if (! $gallery || ! is_array($gallery)) return;
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
                class="w-full h-auto object-contain cursor-zoom-in"
        >

        <!-- Always-visible caption (like your other dev project) -->
        <div class="absolute bottom-0 left-0 w-full bg-black/60 text-white font-body px-4 py-3 text-sm">
            <span x-text="active.caption"></span>
        </div>
    </div>

    <!-- THUMBNAIL STRIP (exactly 4 visible) -->
    <div class="relative w-full">

        <div
                class="flex gap-3 overflow-x-auto scrollbar-hide"
                x-ref="thumbTrack"
        >

            <?php foreach ($gallery as $index => $img): ?>
                <?php
                $thumb = $img['sizes']['medium'] ?? $img['url'];
                $alt   = $img['alt'] ?? '';
                ?>
                <button
                        type="button"
                        @click="setActive(<?php echo esc_js($index); ?>)"
                        class="flex-shrink-0 w-1/4 border border-slate-300 hover:opacity-70 transition"
                >
                    <img
                            src="<?php echo esc_url($thumb); ?>"
                            alt="<?php echo esc_attr($alt); ?>"
                            class="w-full h-auto object-contain"
                    >
                </button>
            <?php endforeach; ?>

        </div>

        <!-- LEFT ARROW -->
        <button
                x-show="$refs.thumbTrack.scrollWidth > $refs.thumbTrack.clientWidth"
                @click="$refs.thumbTrack.scrollLeft -= 200"
                class="absolute left-0 top-1/2 -translate-y-1/2
                   w-8 h-8 bg-white/90 border border-slate-300
                   flex items-center justify-center
                   hover:bg-white"
        >
            ‹
        </button>

        <!-- RIGHT ARROW -->
        <button
                x-show="$refs.thumbTrack.scrollWidth > $refs.thumbTrack.clientWidth"
                @click="$refs.thumbTrack.scrollLeft += 200"
                class="absolute right-0 top-1/2 -translate-y-1/2
                   w-8 h-8 bg-white/90 border border-slate-300
                   flex items-center justify-center
                   hover:bg-white"
        >
            ›
        </button>

    </div>

</div>
