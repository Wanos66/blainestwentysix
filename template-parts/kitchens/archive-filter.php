<?php
/**
 * Kitchen Archive Filter (Dropdown)
 *
 * Expects:
 *  $styles   → array of kitchen_style terms
 *  $finishes → array of kitchen_finish terms
 *  $brands   → array of kitchen_brand terms
 */

$styles   = $args['styles'] ?? [];
$finishes = $args['finishes'] ?? [];
$brands   = $args['brands'] ?? [];

// For initial render only; JS becomes source of truth after first REST call.
$current_search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
?>

<div
        x-data="{
    open: false,
    focusFirst() {
        this.$nextTick(() => {
            requestAnimationFrame(() => {
                const panel = this.$refs.panel;
                if (!panel) return;

                const el = panel.querySelector('button, input, [href], [tabindex]:not([tabindex=\'-1\'])');
                if (el) el.focus();
            });
        });
    }
}"
        x-effect="if (open) focusFirst()"
        @keydown.escape.window="
  open = false;
  $nextTick(() => {
    const t = document.getElementById('js-kitchen-filter-trigger');
    if (t) t.focus();
  })
" @click.outside="open = false" data-kitchen-filter-container class="relative w-full md:w-[22rem]">

    <!-- Trigger -->
    <button type="button"
            id="js-kitchen-filter-trigger"
            @click="open = !open"
            :aria-expanded="open.toString()"
            aria-controls="kitchen-filter-panel"
            class="flex items-center justify-between w-full rounded-xs border border-gray-300 bg-white shadow-md px-4 py-3 text-sm font-semibold text-gray-800 focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2">

        <span><?php esc_html_e('Filter kitchens', 'blainestwentysix'); ?></span>
        <svg :class="open ? 'rotate-180' : ''"
                class="w-4 h-4 transition-transform duration-200 text-gray-500"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <!-- Panel -->
    <div id="kitchen-filter-panel"
            x-ref="panel"
            tabindex="-1"
            role="dialog"
            aria-modal="false"
            aria-labelledby="js-kitchen-filter-trigger"
            x-show="open"
            x-transition.origin.top.right
            x-cloak
            class="absolute right-0 top-full z-50 mt-2 w-[min(22rem,90vw)] rounded-xs border border-gray-200 bg-white shadow-lg px-4 py-4 space-y-6">

        <!-- STYLE -->
        <?php if ( ! empty($styles)) : ?>
            <div class="space-y-2">
                <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <?php esc_html_e('Style', 'blainestwentysix'); ?>
                </h3>

                <div class="flex flex-wrap gap-2" data-kitchen-style-list>

                    <!-- ALL -->
                    <button type="button"
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xs text-xs font-medium
       border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 transition
       focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2"
                            data-filter-link
                            data-taxonomy="kitchen_style"
                            data-term=""><?php esc_html_e('All', 'blainestwentysix'); ?></button>

                    <?php foreach ($styles as $t) : ?>
                        <button type="button"
                                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xs text-xs font-medium
       border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 transition
       focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2"
                                data-filter-link
                                data-taxonomy="kitchen_style"
                                data-term="<?php echo esc_attr($t->slug); ?>"><?php echo esc_html($t->name); ?></button>
                    <?php endforeach; ?>

                </div>
            </div>
        <?php endif; ?>

        <!-- FINISH -->
        <?php if ( ! empty($finishes)) : ?>
            <div class="space-y-2">
                <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <?php esc_html_e('Finish', 'blainestwentysix'); ?>
                </h3>

                <div class="flex flex-wrap gap-2" data-kitchen-finish-list>

                    <!-- ALL -->
                    <button type="button"
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xs text-xs font-medium
       border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 transition
       focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2"
                            data-filter-link
                            data-taxonomy="kitchen_finish"
                            data-term=""><?php esc_html_e('All', 'blainestwentysix'); ?></button>

                    <?php foreach ($finishes as $t) : ?>
                        <button type="button"
                                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xs text-xs font-medium
       border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 transition
       focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2"
                                data-filter-link
                                data-taxonomy="kitchen_finish"
                                data-term="<?php echo esc_attr($t->slug); ?>"><?php echo esc_html($t->name); ?></button>
                    <?php endforeach; ?>

                </div>
            </div>
        <?php endif; ?>

        <!-- BRAND -->
        <?php if ( ! empty($brands)) : ?>
            <div class="space-y-2">
                <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <?php esc_html_e('Brand', 'blainestwentysix'); ?>
                </h3>

                <div class="flex flex-wrap gap-2" data-kitchen-brand-list>

                    <!-- ALL -->
                    <button type="button"
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xs text-xs font-medium
       border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 transition
       focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2"
                            data-filter-link
                            data-taxonomy="kitchen_brand"
                            data-term=""><?php esc_html_e('All', 'blainestwentysix'); ?></button>

                    <?php foreach ($brands as $t) : ?>
                        <button type="button"
                                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xs text-xs font-medium
       border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 transition
       focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2"
                                data-filter-link
                                data-taxonomy="kitchen_brand"
                                data-term="<?php echo esc_attr($t->slug); ?>"><?php echo esc_html($t->name); ?></button>
                    <?php endforeach; ?>

                </div>
            </div>
        <?php endif; ?>

        <!-- SEARCH -->
        <div class="space-y-2">
            <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                <?php esc_html_e('Search', 'blainestwentysix'); ?>
            </h3>

            <div class="relative">
                <!-- Icon -->
                <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>

                <!-- Input -->
                <input type="text"
                        id="js-kitchen-filter-search"
                        name="s"
                        placeholder="<?php esc_attr_e('Search kitchens…', 'blainestwentysix'); ?>"
                        value="<?php echo esc_attr($current_search); ?>"
                        class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-xs focus:ring-2 focus:ring-black focus:outline-none">

                <!-- Clear button -->
                <button id="js-kitchen-filter-search-clear"
                        type="button"
                        class="<?php echo $current_search ? '' : 'hidden'; ?> absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">×
                </button>
            </div>
        </div>

        <!-- CLEAR ALL -->
        <div class="pt-4 border-t border-gray-200">
            <button
                    type="button"
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xs
           border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 transition text-xs
           focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2"
                    data-chip-clear-all="1"
            >
                <?php esc_html_e( 'Clear all', 'blainestwentysix' ); ?>
            </button>

        </div>

    </div>
</div>
