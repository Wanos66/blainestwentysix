<?php
/**
 * Meta block for Single Kitchen
 */

$post_id = $args['post_id'] ?? get_the_ID();
$is_freedom_mode  = ! empty( $args['is_freedom_mode'] );

$styles   = get_the_term_list($post_id, 'kitchen_style', '', ', ');
$brands   = get_the_term_list($post_id, 'kitchen_brand', '', ', ');
$features = get_field('features', $post_id);

// Standard tagline
$kitchen_tagline = get_field('kitchen_tagline', $post_id);

// Sale banner fields
$standard_sale         = get_field( 'sale_banner', $post_id );
$standard_sale_message = get_field( 'sale_banner_message', $post_id );

$freedom_sale_enabled  = get_field( 'freedom_sale_banner_enabled', $post_id );
$freedom_sale          = get_field( 'freedom_sale_banner', $post_id );
$freedom_sale_message  = get_field( 'freedom_sale_banner_message', $post_id );

$sale         = $standard_sale;
$sale_message = $standard_sale_message;

if ( $is_freedom_mode ) {
    if ( ! $freedom_sale_enabled ) {
        $sale         = '';
        $sale_message = '';
    } elseif ( ! empty( $freedom_sale ) ) {
        $sale         = $freedom_sale;
        $sale_message = $freedom_sale_message;
    }
}

// Freedom state passed in from single-kitchen.php
$is_freedom_available = ! empty($args['is_freedom_available']);
$is_freedom_mode      = ! empty($args['is_freedom_mode']);
$standard_url         = $args['standard_url'] ?? get_permalink($post_id);
$freedom_url          = $args['freedom_url'] ?? add_query_arg('view', 'freedom', $standard_url);
$freedom_content      = $args['freedom_content'] ?? '';

$return_url = '';

if ( isset( $_GET['return'] ) ) {
    $candidate_return = rawurldecode( wp_unslash( $_GET['return'] ) );

    if (
        is_string( $candidate_return ) &&
        str_starts_with( $candidate_return, '/' )
    ) {
        $return_url = home_url( $candidate_return );
    }
}
?>

<!-- TITLE -->
<h1 class="mb-2 font-heading text-3xl text-slate-900 sm:text-4xl">
    <?php the_title(); ?>
</h1>

<!-- STYLE + BRAND -->
<div class="mb-4 flex w-full items-start justify-between gap-4">
    <div class="space-y-1 font-body text-sm text-slate-700">
        <?php if ($styles) : ?>
            <p class="font-body"><span class="font-semibold text-slate-900">Style:</span> <span class="font-light"><?php echo wp_kses_post( $styles ); ?></span></p>
        <?php endif; ?>

        <p class="font-body">
            <span class="font-semibold text-slate-900">Brand:</span>
            <?php if ($is_freedom_mode) : ?>
                <span class="text-[var(--freedom-accent)]">Freedom</span>
            <?php elseif ($brands) : ?>
                <span class="font-light"><?php echo wp_kses_post( $brands ); ?></span>
            <?php endif; ?>
        </p>
    </div>

    <?php if ($sale) : ?>
        <div class="ml-4 inline-block bg-red-700 px-4 py-2 text-center uppercase tracking-wide text-white">
            <div class="font-heading text-xl font-semibold leading-none">
                <?php echo esc_html($sale); ?>% OFF
            </div>

            <?php if ($sale_message) : ?>
                <div class="mt-1 font-body text-[11px] leading-tight text-white/90">
                    <?php echo esc_html($sale_message); ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<!-- FREEDOM MODE / SWITCH -->
<?php if ($is_freedom_available) : ?>
    <?php if ($is_freedom_mode) : ?>
        <div class="mb-5 border border-[color:var(--freedom-accent)]/20 bg-[color:var(--freedom-accent)]/5 px-4 py-4">
            <p class="font-body text-[11px] font-semibold uppercase tracking-[0.14em] text-[var(--freedom-accent)]">
                Freedom accessibility version
            </p>

            <div class="mt-3 space-y-2 font-body">
                <p>
                    <a
                            href="<?php echo esc_url($standard_url); ?>"
                            class="text-sm font-medium text-slate-900 underline underline-offset-4 hover:no-underline"
                    >
                        Return to Gallery range view
                    </a>
                </p>

                <?php if ($return_url) : ?>
                    <p>
                        <a
                                href="<?php echo esc_url($return_url); ?>"
                                class="text-sm font-medium text-slate-900 underline underline-offset-4 hover:no-underline"
                        >
                            Back to Freedom kitchen ranges
                        </a>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    <?php else : ?>
        <div class="mb-5 border border-slate-200 px-4 py-4">
            <p class="font-body">
                <a
                        href="<?php echo esc_url($freedom_url); ?>"
                        class="text-sm font-medium text-slate-900 underline underline-offset-4 hover:no-underline"
                >
                    Available with Freedom accessibility options
                </a>
            </p>
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- TAGLINE -->
<?php if (! empty($kitchen_tagline)) : ?>
    <div class="prose-site prose-news mb-4 max-w-none">
        <?php echo wp_kses_post($kitchen_tagline); ?>
    </div>
<?php endif; ?>

<!-- FREEDOM ACCESSIBILITY CONTENT -->
<?php if ($is_freedom_mode && ! empty($freedom_content)) : ?>
    <div class="prose-site prose-news mb-5 max-w-none">
        <?php echo wp_kses_post($freedom_content); ?>
    </div>
<?php endif; ?>

<!-- FEATURES -->
<?php if ($features) : ?>
    <div>
        <ul class="mb-3 mt-3 space-y-1.5">
            <?php foreach ($features as $row) : ?>
                <?php if (! empty($row['feature_text'])) : ?>
                    <li class="flex items-start gap-2 font-body text-sm leading-relaxed text-slate-700">
                        <span class="mt-0.5">&bull;</span>
                        <span><?php echo esc_html($row['feature_text']); ?></span>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- DESCRIPTION / CONTENT -->
<div class="prose-site prose-news mt-3 max-w-none">
    <?php the_content(); ?>
</div>