<?php
/**
 * Component: Project Type filter bar (simple links)
 *
 * Args:
 * - taxonomy (string)  default: 'project_type'
 * - post_type (string) default: 'project'
 * - all_label (string) default: 'All'
 * - aria_label (string) default: 'Project type filters'
 */

$args = wp_parse_args($args ?? [], [
    'taxonomy'   => 'project_type',
    'post_type'  => 'project',
    'all_label'  => __('All', 'blainestwentysix'),
    'aria_label' => __('Project type filters', 'blainestwentysix'),
]);

$taxonomy  = $args['taxonomy'];
$post_type = $args['post_type'];

$terms = get_terms([
    'taxonomy'   => $taxonomy,
    'hide_empty' => true,
    'orderby'    => 'name',
    'order'      => 'ASC',
]);

if (is_wp_error($terms) || empty($terms)) {
    return;
}

$current_term_id = is_tax($taxonomy) ? get_queried_object_id() : 0;

$all_url    = get_post_type_archive_link($post_type);
$all_active = is_post_type_archive($post_type) && ! is_tax($taxonomy);

// Tailwind classes (single source of truth)
$pill_base   = 'inline-flex items-center justify-center whitespace-nowrap px-4 py-2 rounded-full border font-body text-sm font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-black/30 focus-visible:ring-offset-2';
$pill_idle   = 'bg-white text-slate-800 border-slate-200 hover:bg-slate-50 hover:border-slate-300';
$pill_active = 'bg-black text-white border-black';
?>

<nav class="mt-6 mb-10" aria-label="<?php echo esc_attr($args['aria_label']); ?>">
    <div class="-mx-4 overflow-x-auto px-4">
        <div class="flex w-max gap-3 sm:w-full sm:flex-wrap">

            <a
                    href="<?php echo esc_url($all_url); ?>"
                    class="<?php echo esc_attr($pill_base . ' ' . ($all_active ? $pill_active : $pill_idle)); ?>"
                <?php echo $all_active ? 'aria-current="page"' : ''; ?>
            >
                <?php echo esc_html($args['all_label']); ?>
            </a>

            <?php foreach ($terms as $term) : ?>
                <?php
                $url = get_term_link($term);
                if (is_wp_error($url)) {
                    continue;
                }
                $is_active = ($current_term_id && (int) $term->term_id === (int) $current_term_id);
                ?>
                <a
                        href="<?php echo esc_url($url); ?>"
                        class="<?php echo esc_attr($pill_base . ' ' . ($is_active ? $pill_active : $pill_idle)); ?>"
                    <?php echo $is_active ? 'aria-current="page"' : ''; ?>
                >
                    <?php echo esc_html($term->name); ?>
                </a>
            <?php endforeach; ?>

        </div>
    </div>
</nav>