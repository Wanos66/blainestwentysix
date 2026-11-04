<?php
/**
 * Single Project — Facts (Hybrid panel: facts grid + chips)
 */

$types_list = get_the_terms( get_the_ID(), 'project_type' );
$completion = get_field( 'completion_date' );

// For now, treat tags as "Brands used" until a dedicated field exists.
$tags = get_the_tags();

$type_label = '';
if ( ! empty( $types_list ) && ! is_wp_error( $types_list ) ) {
    // Use the first term as primary (consistent with other templates)
    $type_label = $types_list[0]->name ?? '';
}
?>

<?php if ( $type_label || $completion || $tags ) : ?>
    <section class="mt-10 border border-black/10 bg-white p-6">
        <header class="mb-5">
            <h3 class="font-body text-lg font-medium tracking-wide text-black">Project details</h3>
        </header>

        <!-- Facts grid -->
        <dl class="grid grid-cols-1 gap-x-8 gap-y-5 font-body text-sm sm:grid-cols-2">

            <?php if ( $type_label ) : ?>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-[0.08em] text-black/50">Project type</dt>
                    <dd class="mt-1 text-black/80"><?php echo esc_html( $type_label ); ?></dd>
                </div>
            <?php endif; ?>

            <?php if ( $completion ) : ?>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-[0.08em] text-black/50">Completed</dt>
                    <dd class="mt-1 text-black/80"><?php echo esc_html( $completion ); ?></dd>
                </div>
            <?php endif; ?>

        </dl>

        <?php
        $brands = get_the_terms( get_the_ID(), 'project_brand' );

        if ( ! empty( $brands ) && ! is_wp_error( $brands ) ) : ?>
            <div class="mt-6 border-t border-black/10 pt-5">
                <div class="mb-3 font-body text-xs font-semibold uppercase tracking-[0.08em] text-black/50">
                    Brands used
                </div>

                <div class="flex flex-wrap gap-2">
                    <?php foreach ( $brands as $brand ) : ?>
                        <a
                                href="<?php echo esc_url( get_term_link( $brand ) ); ?>"
                                class="inline-flex items-center border border-black/10 bg-black/[0.02] px-3 py-1 font-body text-sm text-black/70 transition hover:bg-black/[0.05]"
                        >
                            <?php echo esc_html( $brand->name ); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

    </section>
<?php endif; ?>
