<?php
/**
 * Kitchens — Active Filters Bar (News-style)
 *
 * Expects $args:
 *  - 'style'  => (string) selected kitchen_style slug
 *  - 'finish' => (string) selected kitchen_finish slug
 *  - 'brand'  => (string) selected kitchen_brand slug
 *  - 'search' => (string) optional search term (if you wire it later)
 */

$style  = isset($args['style'])  ? sanitize_text_field($args['style'])  : (isset($_GET['style'])  ? sanitize_text_field($_GET['style'])  : '');
$finish = isset($args['finish']) ? sanitize_text_field($args['finish']) : (isset($_GET['finish']) ? sanitize_text_field($_GET['finish']) : '');
$brand  = isset($args['brand'])  ? sanitize_text_field($args['brand'])  : (isset($_GET['brand'])  ? sanitize_text_field($_GET['brand'])  : '');
$search = isset($args['search']) ? sanitize_text_field($args['search']) : (isset($_GET['s'])      ? sanitize_text_field($_GET['s'])      : '');

// Resolve term names (so chips show human labels)
$style_term  = $style  ? get_term_by('slug', $style,  'kitchen_style')  : null;
$finish_term = $finish ? get_term_by('slug', $finish, 'kitchen_finish') : null;
$brand_term  = $brand  ? get_term_by('slug', $brand,  'kitchen_brand')  : null;

$has_filters = ($style || $finish || $brand || $search);
?>

<?php if ($has_filters) : ?>

    <div class="mb-6 p-4 bg-gray-100 border border-gray-300 rounded-xs">

        <div class="text-sm font-semibold text-gray-700 mb-2">
            Active filters:
        </div>

        <div class="flex flex-wrap gap-2">

            <!-- STYLE CHIP -->
            <?php if ($style) : ?>
                <button
                        type="button"
                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xs bg-black text-white font-semibold transition"
                        data-chip-tax="kitchen_style"
                        data-chip-term="<?php echo esc_attr($style); ?>"
                >
                    <?php echo esc_html($style_term ? $style_term->name : $style); ?>
                    <span class="text-white text-sm">×</span>
                </button>
            <?php endif; ?>

            <!-- FINISH CHIP -->
            <?php if ($finish) : ?>
                <button
                        type="button"
                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xs bg-black text-white font-semibold transition"
                        data-chip-tax="kitchen_finish"
                        data-chip-term="<?php echo esc_attr($finish); ?>"
                >
                    <?php echo esc_html($finish_term ? $finish_term->name : $finish); ?>
                    <span class="text-white text-sm">×</span>
                </button>
            <?php endif; ?>

            <!-- BRAND CHIP -->
            <?php if ($brand) : ?>
                <button
                        type="button"
                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xs bg-black text-white font-semibold transition"
                        data-chip-tax="kitchen_brand"
                        data-chip-term="<?php echo esc_attr($brand); ?>"
                >
                    <?php echo esc_html($brand_term ? $brand_term->name : $brand); ?>
                    <span class="text-white text-sm">×</span>
                </button>
            <?php endif; ?>

            <!-- SEARCH CHIP (optional; only if you wire search later) -->
            <?php if ($search) : ?>
                <button
                        type="button"
                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xs bg-black text-white font-semibold transition"
                        data-chip-tax="search"
                        data-chip-term="1"
                >
                    "<?php echo esc_html($search); ?>"
                    <span class="text-white text-sm">×</span>
                </button>
            <?php endif; ?>

            <!-- CLEAR ALL -->
            <button
                    type="button"
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xs border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 transition"
                    data-chip-clear-all="1"
            >
                Clear All
            </button>

        </div>
    </div>

<?php endif; ?>
