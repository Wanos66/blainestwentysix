<?php
/**
 * Single Kitchen — Gallery (4 visible thumbs, scrollable, always-on caption, NO rounded edges)
 */

$gallery = get_field('project_gallery');
if (! $gallery || ! is_array($gallery)) return;
?>

<div
        x-data="kitchenGallery(JSON.parse($el.querySelector('[data-gallery-json]').textContent))"
        x-init="init()"
        class="space-y-8"
>

    <script type="application/json" data-gallery-json>
        <?php echo wp_json_encode($gallery); ?>
    </script>

    <!-- MAIN IMAGE WITH ALWAYS-VISIBLE CAPTION -->
    <div class="relative w-full border border-black/10 bg-black/5">

        <img
                :src="active.url"
                :alt="active.alt"
                @click="openFancybox()"
                class="w-full h-auto object-contain cursor-zoom-in"
        >

        <!-- Always-visible caption (like your other dev project) -->
        <div class="absolute bottom-0 left-0 w-full bg-black/70 px-5 py-3 font-body text-sm text-white backdrop-blur-sm">
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
                        :class="index === activeIndex ? 'ring-2 ring-black' : ''"
                        class="flex-shrink-0 w-1/4 border border-black/10 hover:opacity-80 transition-opacity duration-200"
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
                class="absolute left-0 top-1/2 flex h-9 w-9 -translate-y-1/2 items-center justify-center border border-black/10 bg-white font-body shadow-sm hover:bg-white"
        >
            ‹
        </button>

        <!-- RIGHT ARROW -->
        <button
                x-show="$refs.thumbTrack.scrollWidth > $refs.thumbTrack.clientWidth"
                @click="$refs.thumbTrack.scrollLeft += 200"
                class="absolute right-0 top-1/2 flex h-9 w-9 -translate-y-1/2 items-center justify-center border border-black/10 bg-white font-body shadow-sm hover:bg-white"
        >
            ›
        </button>

    </div>

</div>
