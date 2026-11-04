<?php
get_template_part('template-parts/components/related-by-style-brand', null, [
    'post_type' => 'bathroom',
    'style_tax' => 'bathroom_style',
    'brand_tax' => 'bathroom_brand',
    'heading'   => 'Related Bathrooms',
    'limit'     => 3,
]);