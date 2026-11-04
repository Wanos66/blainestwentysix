<?php
global $wp_query;

$query    = $args['query'] ?? $wp_query;
$current  = $args['current'] ?? max( 1, get_query_var( 'paged' ) ? get_query_var( 'paged' ) : get_query_var( 'page' ) );
$format   = $args['format'] ?? '';
$total    = $args['total'] ?? ( isset( $query->max_num_pages ) ? (int) $query->max_num_pages : 1 );
$base     = $args['base'] ?? str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) );
$add_args = $args['add_args'] ?? [];
$mid_size = $args['mid_size'] ?? 2;
$prev_text = $args['prev_text'] ?? '«';
$next_text = $args['next_text'] ?? '»';

$pagination = paginate_links(
    [
        'base'      => $base,

        'current'   => $current,
        'format'    => $format,
        'total'     => $total,
        'type'      => 'array',
        'mid_size'  => $mid_size,
        'prev_text' => $prev_text,
        'next_text' => $next_text,
        'add_args'  => $add_args,
    ]
);

if ( $pagination ) : ?>
    <nav class="mt-12 flex justify-center" aria-label="Pagination">
        <ul class="flex items-center gap-2">
            <?php foreach ( $pagination as $link ) : ?>
                <li class="group">
                    <?php
                    echo str_replace(
                        [ 'page-numbers current', 'page-numbers' ],
                        [
                            'page-numbers current inline-flex items-center justify-center bg-black px-4 py-2 font-semibold text-white',
                            'page-numbers inline-flex items-center justify-center border border-gray-300 px-4 py-2 text-gray-700 transition hover:bg-gray-100'
                        ],
                        $link
                    );
                    ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
<?php endif; ?>