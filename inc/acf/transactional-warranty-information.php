<?php
/**
 * Transactional product warranty information fields.
 *
 * @package Blaines
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register manual warranty / guarantee fields for transactional products.
 */
function blaines_register_transactional_warranty_information_fields(): void {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    acf_add_local_field_group(
        [
            'key'                   => 'group_blaines_transactional_warranty_information',
            'title'                 => 'Transactional warranty information',
            'fields'                => [
                [
                    'key'          => 'field_blaines_product_warranty_label',
                    'label'        => 'Warranty / guarantee label',
                    'name'         => 'product_warranty_label',
                    'type'         => 'text',
                    'instructions' => 'Optional short label shown on the product page. Example: Manufacturer’s guarantee, 2 year manufacturer’s guarantee.',
                    'required'     => 0,
                    'placeholder'  => 'Manufacturer’s guarantee',
                ],
                [
                    'key'          => 'field_blaines_product_warranty_detail',
                    'label'        => 'Warranty / guarantee detail',
                    'name'         => 'product_warranty_detail',
                    'type'         => 'textarea',
                    'instructions' => 'Optional product-specific warranty wording. This takes priority over imported or fallback warranty text.',
                    'required'     => 0,
                    'rows'         => 3,
                    'new_lines'    => 'br',
                    'placeholder'  => 'Covered by the manufacturer’s guarantee.',
                ],
                [
                    'key'          => 'field_blaines_product_warranty_registration_note',
                    'label'        => 'Warranty registration note',
                    'name'         => 'product_warranty_registration_note',
                    'type'         => 'textarea',
                    'instructions' => 'Optional registration or extended guarantee note, if applicable.',
                    'required'     => 0,
                    'rows'         => 3,
                    'new_lines'    => 'br',
                    'placeholder'  => 'Registration may be required for extended guarantee cover where offered by the manufacturer.',
                ],
            ],
            'location'              => [
                [
                    [
                        'param'    => 'post_type',
                        'operator' => '==',
                        'value'    => 'product',
                    ],
                ],
            ],
            'menu_order'            => 36,
            'position'              => 'normal',
            'style'                 => 'default',
            'label_placement'       => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen'        => '',
            'active'                => true,
            'description'           => 'Manual warranty and guarantee wording for transactional electrical products.',
            'show_in_rest'          => 0,
        ]
    );
}
add_action( 'acf/init', 'blaines_register_transactional_warranty_information_fields' );
