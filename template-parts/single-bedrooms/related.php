<?php
get_template_part('template-parts/components/related-by-style-brand', null, [
    'post_type' => 'bedroom',
    'style_tax' => 'bedroom_style',
    'brand_tax' => 'bedroom_brand',
    'heading'   => 'Related Bedrooms',
    'limit'     => 3,
]);