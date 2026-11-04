<?php
$query            = $args['query'] ?? null;
$current_sort     = $args['current_sort'] ?? '';
$current_showroom = $args['current_showroom'] ?? 0;
$current_cat      = $args['current_cat'] ?? '';
$current_page     = $args['current_page'] ?? 1;
$per_page         = $args['per_page'] ?? 12;

if ( ! $query instanceof WP_Query ) {
    return;
}

$showroom_title = $current_showroom ? get_the_title( $current_showroom ) : '';
$category_obj   = $current_cat ? get_term_by( 'slug', $current_cat, 'product_cat' ) : null;

$total_results = (int) $query->found_posts;
$result_label  = 1 === $total_results ? 'product' : 'products';

if ( $total_results > 0 ) {
    $start = ( ( max( 1, (int) $current_page ) - 1 ) * (int) $per_page ) + 1;
    $end   = min( $start + (int) $query->post_count - 1, $total_results );
} else {
    $start = 0;
    $end   = 0;
}
?>

<div class="border-b border-neutral-200 pb-4">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm font-medium text-neutral-900">
                <?php if ( $total_results > 0 ) : ?>
                    Showing <?php echo esc_html( $start ); ?> – <?php echo esc_html( $end ); ?> of <?php echo esc_html( $total_results ); ?> <?php echo esc_html( $result_label )
                    ; ?>
                <?php else : ?>
                    No products found
                <?php endif; ?>
            </p>

            <?php if ( $showroom_title || $category_obj ) : ?>
                <p class="mt-1 text-sm text-neutral-600">
                    <?php if ( $showroom_title ) : ?>
                        <span>Showroom: <?php echo esc_html( $showroom_title ); ?></span>
                    <?php endif; ?>

                    <?php if ( $showroom_title && $category_obj ) : ?>
                        <span> · </span>
                    <?php endif; ?>

                    <?php if ( $category_obj ) : ?>
                        <span>Category: <?php echo esc_html( $category_obj->name ); ?></span>
                    <?php endif; ?>
                </p>
            <?php endif; ?>
        </div>

        <form method="get" class="flex flex-col gap-2 sm:flex-row sm:items-center">
            <label for="clearance-sort" class="text-sm font-medium text-neutral-700">
                Sort by
            </label>

            <select
                id="clearance-sort"
                name="sort"
                class="min-w-[220px] border border-neutral-300 bg-white px-4 py-2 text-sm text-neutral-700"
                onchange="this.form.submit()"
            >
                <option value="" <?php selected( $current_sort, '' ); ?>>Newest first</option>
                <option value="price_asc" <?php selected( $current_sort, 'price_asc' ); ?>>Price: low to high</option>
                <option value="price_desc" <?php selected( $current_sort, 'price_desc' ); ?>>Price: high to low</option>
                <option value="title_asc" <?php selected( $current_sort, 'title_asc' ); ?>>Title: A to Z</option>
                <option value="title_desc" <?php selected( $current_sort, 'title_desc' ); ?>>Title: Z to A</option>
            </select>

            <?php if ( $current_showroom ) : ?>
                <input type="hidden" name="showroom" value="<?php echo esc_attr( $current_showroom ); ?>">
            <?php endif; ?>

            <?php if ( $current_cat ) : ?>
                <input type="hidden" name="product_cat" value="<?php echo esc_attr( $current_cat ); ?>">
            <?php endif; ?>
        </form>
    </div>
</div>