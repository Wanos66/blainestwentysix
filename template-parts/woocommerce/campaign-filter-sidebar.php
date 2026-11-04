<?php
/**
 * Reusable Campaign Catalogue filter sidebar.
 *
 * Expected args:
 * - facets
 * - active
 * - sort
 * - has_active_filters
 * - active_filter_count
 * - clear_all_url
 * - panel_id
 * - heading_id
 *
 * @package Blaines
 */

defined( 'ABSPATH' ) || exit;

$facets = isset( $args['facets'] ) && is_array( $args['facets'] )
    ? $args['facets']
    : [];

$active = isset( $args['active'] ) && is_array( $args['active'] )
    ? $args['active']
    : [];

$department_filter = sanitize_title( (string) ( $active['department'] ?? '' ) );
$type_filter       = sanitize_title( (string) ( $active['type'] ?? '' ) );
$brand_filter      = sanitize_key( (string) ( $active['brand'] ?? '' ) );
$price_filter = sanitize_key( (string) ( $active['price'] ?? '' ) );
$sort         = sanitize_key( (string) ( $args['sort'] ?? $active['sort'] ?? 'featured' ) );

$type_options = isset( $facets['types'] ) && is_array( $facets['types'] )
    ? $facets['types']
    : [];

$brand_options = isset( $facets['brands'] ) && is_array( $facets['brands'] )
    ? $facets['brands']
    : [];

$price_options = isset( $facets['prices'] ) && is_array( $facets['prices'] )
    ? $facets['prices']
    : [];

$has_active_filters = ! empty( $args['has_active_filters'] );
$active_filter_count = isset( $args['active_filter_count'] )
    ? max( 0, absint( $args['active_filter_count'] ) )
    : 0;

$clear_all_url = isset( $args['clear_all_url'] )
    ? esc_url_raw( (string) $args['clear_all_url'] )
    : '';

$panel_id = isset( $args['panel_id'] )
    ? sanitize_html_class( (string) $args['panel_id'] )
    : 'campaign-filter-panel';

$heading_id = isset( $args['heading_id'] )
    ? sanitize_html_class( (string) $args['heading_id'] )
    : 'campaign-filter-heading';

$active_query = [];

if ( $department_filter ) {
    $active_query['department'] = $department_filter;
}

if ( $type_filter ) {
    $active_query['type'] = $type_filter;
}

if ( $brand_filter ) {
    $active_query['brand'] = $brand_filter;
}

if ( $price_filter ) {
    $active_query['price'] = $price_filter;
}

if ( 'featured' !== $sort ) {
    $active_query['sort'] = $sort;
}

$get_preserved_query = static function ( string $excluded_query_var ) use ( $active_query ): array {
    $preserved = $active_query;

    unset(
        $preserved[ $excluded_query_var ],
        $preserved['pg']
    );

    return $preserved;
};

$render_option = static function (
    string $query_var,
    string $slug,
    string $label,
    int $count,
    bool $is_checked
): void {
    ?>
    <label class="<?php echo esc_attr( $is_checked ? 'border-neutral-950 bg-neutral-950 font-medium text-white' : 'border-neutral-200 bg-white text-neutral-700 hover:border-neutral-400 hover:bg-neutral-50' ); ?> flex cursor-pointer items-center gap-3 border px-3 py-2.5 transition focus-within:ring-2 focus-within:ring-blue-700 focus-within:ring-offset-1">
        <input
            type="radio"
            name="<?php echo esc_attr( $query_var ); ?>"
            value="<?php echo esc_attr( $slug ); ?>"
            <?php checked( $is_checked ); ?>
            onchange="this.form.submit()"
            class="sr-only"
        >

        <span class="<?php echo esc_attr( $is_checked ? 'border-white bg-white' : 'border-neutral-300 bg-white' ); ?> flex h-3.5 w-3.5 shrink-0 items-center justify-center border">
            <?php if ( $is_checked ) : ?>
                <span class="h-1.5 w-1.5 bg-neutral-950"></span>
            <?php endif; ?>
        </span>

        <span class="min-w-0 flex-1">
            <?php echo esc_html( $label ); ?>
        </span>

        <span class="<?php echo esc_attr( $is_checked ? 'text-neutral-300' : 'text-neutral-400' ); ?> text-xs tabular-nums">
            <?php echo esc_html( (string) $count ); ?>
        </span>
    </label>
    <?php
};
?>

<aside
    id="<?php echo esc_attr( $panel_id ); ?>"
    class="fixed inset-y-0 left-0 z-[70] hidden w-[min(90vw,22rem)] overflow-y-auto bg-white shadow-2xl focus:outline-none lg:static lg:z-auto lg:col-span-3 lg:block lg:w-auto lg:self-start lg:overflow-visible lg:bg-transparent lg:shadow-none"
    data-transactional-filter-panel
    aria-labelledby="<?php echo esc_attr( $heading_id ); ?>"
    tabindex="-1"
>
    <div class="min-h-full border-r border-neutral-200 bg-white lg:min-h-0 lg:border lg:border-neutral-300">
        <div class="sticky top-0 z-10 border-b border-neutral-200 bg-white px-4 py-4 lg:static">
            <div class="flex items-center justify-between gap-4">
                <div class="flex min-w-0 items-center gap-2">
                    <h2
                        id="<?php echo esc_attr( $heading_id ); ?>"
                        class="text-lg font-semibold text-neutral-950"
                    >
                        Filter deals
                    </h2>

                    <?php if ( $active_filter_count > 0 ) : ?>
                        <span class="inline-flex min-h-7 shrink-0 items-center bg-blue-50 px-2 text-xs font-semibold text-blue-800">
                            <?php echo esc_html( (string) $active_filter_count ); ?> active
                        </span>
                    <?php endif; ?>
                </div>

                <div class="flex shrink-0 items-center gap-3">
                    <?php if ( $has_active_filters && $clear_all_url ) : ?>
                        <a
                            href="<?php echo esc_url( $clear_all_url ); ?>"
                            class="text-xs font-semibold text-blue-700 transition hover:text-blue-900 hover:underline hover:underline-offset-4"
                        >
                            Clear all
                        </a>
                    <?php endif; ?>

                    <button
                        type="button"
                        class="inline-flex h-9 w-9 items-center justify-center border border-neutral-300 text-neutral-700 transition hover:border-neutral-950 hover:text-neutral-950 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-700 focus-visible:ring-offset-2 lg:hidden"
                        data-transactional-filter-close
                        aria-label="Close campaign filters"
                    >
                        <svg
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            aria-hidden="true"
                        >
                            <path d="M6 6l12 12"></path>
                            <path d="M18 6L6 18"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="divide-y divide-neutral-200 pb-6 text-sm lg:pb-0">

            <?php if ( ! empty( $type_options ) ) : ?>
                <section class="px-4 py-4">
                    <div class="flex items-center justify-between gap-4">
                        <h3 class="font-semibold text-neutral-950">
                            Product type
                        </h3>

                        <?php if ( $type_filter ) : ?>
                            <a
                                href="<?php echo esc_url( remove_query_arg( [ 'type', 'pg' ] ) ); ?>"
                                class="text-xs font-semibold text-blue-700 transition hover:text-blue-900 hover:underline hover:underline-offset-4"
                            >
                                Clear
                            </a>
                        <?php endif; ?>
                    </div>

                    <form method="get" class="mt-3 space-y-2 text-neutral-700">
                        <?php foreach ( $get_preserved_query( 'type' ) as $name => $value ) : ?>
                            <input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>">
                        <?php endforeach; ?>

                        <?php foreach ( $type_options as $option ) : ?>
                            <?php
                            $slug       = sanitize_title( (string) ( $option['slug'] ?? '' ) );
                            $label      = trim( (string) ( $option['label'] ?? '' ) );
                            $count      = max( 0, (int) ( $option['count'] ?? 0 ) );
                            $is_checked = $type_filter === $slug;

                            if ( '' === $slug || '' === $label ) {
                                continue;
                            }

                            $render_option( 'type', $slug, $label, $count, $is_checked );
                            ?>
                        <?php endforeach; ?>
                    </form>
                </section>
            <?php endif; ?>

            <?php if ( ! empty( $brand_options ) ) : ?>
                <section class="px-4 py-4">
                    <div class="flex items-center justify-between gap-4">
                        <h3 class="font-semibold text-neutral-950">
                            Brand
                        </h3>

                        <?php if ( $brand_filter ) : ?>
                            <a
                                href="<?php echo esc_url( remove_query_arg( [ 'brand', 'pg' ] ) ); ?>"
                                class="text-xs font-semibold text-blue-700 transition hover:text-blue-900 hover:underline hover:underline-offset-4"
                            >
                                Clear
                            </a>
                        <?php endif; ?>
                    </div>

                    <form method="get" class="mt-3 space-y-2 text-neutral-700">
                        <?php foreach ( $get_preserved_query( 'brand' ) as $name => $value ) : ?>
                            <input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>">
                        <?php endforeach; ?>

                        <?php foreach ( $brand_options as $option ) : ?>
                            <?php
                            $slug       = sanitize_key( (string) ( $option['slug'] ?? '' ) );
                            $label      = trim( (string) ( $option['label'] ?? '' ) );
                            $count      = max( 0, (int) ( $option['count'] ?? 0 ) );
                            $is_checked = $brand_filter === $slug;

                            if ( '' === $slug || '' === $label ) {
                                continue;
                            }

                            $render_option( 'brand', $slug, $label, $count, $is_checked );
                            ?>
                        <?php endforeach; ?>
                    </form>
                </section>
            <?php endif; ?>

            <?php if ( ! empty( $price_options ) ) : ?>
                <section class="px-4 py-4">
                    <div class="flex items-center justify-between gap-4">
                        <h3 class="font-semibold text-neutral-950">
                            Price
                        </h3>

                        <?php if ( $price_filter ) : ?>
                            <a
                                href="<?php echo esc_url( remove_query_arg( [ 'price', 'pg' ] ) ); ?>"
                                class="text-xs font-semibold text-blue-700 transition hover:text-blue-900 hover:underline hover:underline-offset-4"
                            >
                                Clear
                            </a>
                        <?php endif; ?>
                    </div>

                    <form method="get" class="mt-3 space-y-2 text-neutral-700">
                        <?php foreach ( $get_preserved_query( 'price' ) as $name => $value ) : ?>
                            <input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>">
                        <?php endforeach; ?>

                        <?php foreach ( $price_options as $option ) : ?>
                            <?php
                            $slug       = sanitize_key( (string) ( $option['slug'] ?? '' ) );
                            $label      = trim( (string) ( $option['label'] ?? '' ) );
                            $count      = max( 0, (int) ( $option['count'] ?? 0 ) );
                            $is_checked = $price_filter === $slug;

                            if ( '' === $slug || '' === $label ) {
                                continue;
                            }

                            $render_option( 'price', $slug, $label, $count, $is_checked );
                            ?>
                        <?php endforeach; ?>
                    </form>
                </section>
            <?php endif; ?>

        </div>
    </div>
</aside>
