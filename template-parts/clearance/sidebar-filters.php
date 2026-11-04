<?php
$current_showroom = isset( $_GET['showroom'] ) ? absint( $_GET['showroom'] ) : 0;
$current_cat      = isset( $_GET['product_cat'] ) ? sanitize_title( wp_unslash( $_GET['product_cat'] ) ) : '';
$current_sort     = isset( $_GET['sort'] ) ? sanitize_key( wp_unslash( $_GET['sort'] ) ) : '';

$showrooms_parent = get_page_by_path( 'showrooms' );

$showroom_pages = $showrooms_parent
    ? get_pages(
        [
            'child_of'    => $showrooms_parent->ID,
            'sort_column' => 'menu_order,post_title',
            'post_status' => 'publish',
        ]
    )
    : [];

$electrical_clearance_term = get_term_by( 'slug', 'electrical-clearance', 'product_cat' );

$product_categories = [];

if ( $electrical_clearance_term && ! is_wp_error( $electrical_clearance_term ) ) {
    $product_categories = get_terms(
        [
            'taxonomy'   => 'product_cat',
            'hide_empty' => true,
            'orderby'    => 'name',
            'order'      => 'ASC',
            'parent'     => (int) $electrical_clearance_term->term_id,
        ]
    );

    if ( is_wp_error( $product_categories ) ) {
        $product_categories = [];
    }
}

$current_url    = get_permalink();
$results_anchor = '#clearance-results';

$clear_all_url = $current_url . $results_anchor;

$all_showrooms_url = add_query_arg(
                         array_filter(
                             [
                                 'product_cat' => $current_cat,
                                 'sort'        => $current_sort,
                             ]
                         ),
                         $current_url
                     ) . $results_anchor;

$all_categories_url = add_query_arg(
                          array_filter(
                              [
                                  'showroom' => $current_showroom,
                                  'sort'     => $current_sort,
                              ]
                          ),
                          $current_url
                      ) . $results_anchor;
?>

<div class="border border-neutral-200 bg-white p-5">
    <h2 class="text-base font-semibold text-neutral-900">Refine results</h2>

    <?php if ( $current_showroom || $current_cat ) : ?>
        <div class="mt-4 border-t border-neutral-200 pt-4">
            <a
                    href="<?php echo esc_url( $clear_all_url ); ?>"
                    class="inline-flex items-center text-sm font-medium text-neutral-900 underline underline-offset-2 hover:text-neutral-700"
            >
                Clear all filters
            </a>
        </div>
    <?php endif; ?>

    <div class="font-body mt-5 border-t border-neutral-200 pt-5">
        <h3 class="text-sm font-semibold uppercase tracking-[0.08em] text-neutral-700">Showroom</h3>

        <ul class="mt-3 space-y-2">
            <li>
                <a
                        href="<?php echo esc_url( $all_showrooms_url ); ?>"
                        class="text-sm <?php echo ! $current_showroom ? 'font-semibold text-neutral-900' : 'text-neutral-600 hover:text-neutral-900'; ?>"
                >
                    All showrooms
                </a>
            </li>

            <?php foreach ( $showroom_pages as $showroom_page ) : ?>
                <?php
                $url = add_query_arg(
                           array_filter(
                               [
                                   'showroom'    => $showroom_page->ID,
                                   'product_cat' => $current_cat,
                                   'sort'        => $current_sort,
                               ]
                           ),
                           $current_url
                       ) . $results_anchor;
                ?>
                <li>
                    <a
                            href="<?php echo esc_url( $url ); ?>"
                            class="text-sm <?php echo ( $current_showroom === (int) $showroom_page->ID ) ? 'font-semibold text-black underline underline-offset-2' : 'text-neutral-600 hover:text-neutral-900'; ?>"
                    >
                        <?php echo esc_html( get_the_title( $showroom_page->ID ) ); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="font-body mt-5 border-t border-neutral-200 pt-5">
        <h3 class="text-sm font-semibold uppercase tracking-[0.08em] text-neutral-700">Category</h3>

        <ul class="mt-3 space-y-2">
            <li>
                <a
                        href="<?php echo esc_url( $all_categories_url ); ?>"
                        class="text-sm <?php echo ! $current_cat ? 'font-semibold text-neutral-900' : 'text-neutral-600 hover:text-neutral-900'; ?>"
                >
                    All categories
                </a>
            </li>

            <?php foreach ( $product_categories as $category ) : ?>
                <?php
                if ( 'clearance' === $category->slug ) {
                    continue;
                }

                $url = add_query_arg(
                           array_filter(
                               [
                                   'showroom'    => $current_showroom,
                                   'product_cat' => $category->slug,
                                   'sort'        => $current_sort,
                               ]
                           ),
                           $current_url
                       ) . $results_anchor;
                ?>
                <li>
                    <a
                            href="<?php echo esc_url( $url ); ?>"
                            class="text-sm <?php echo ( $current_cat === $category->slug ) ? 'font-semibold text-black underline underline-offset-2' : 'text-neutral-600 hover:text-neutral-900'; ?>"
                    >
                        <?php echo esc_html( $category->name ); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>