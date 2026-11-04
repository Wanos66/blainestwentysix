<?php
/**
 * Transactional product energy-information fields.
 *
 * @package Blaines
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register model-specific energy label and product information sheet fields.
 */
function blaines_register_transactional_energy_information_fields(): void {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    acf_add_local_field_group(
        [
            'key'                   => 'group_blaines_transactional_energy_information',
            'title'                 => 'Transactional energy information',
            'fields'                => [
                [
                    'key'          => 'field_blaines_full_energy_label_file',
                    'label'        => 'Full energy label',
                    'name'         => 'product_full_energy_label_file',
                    'type'         => 'file',
                    'instructions' => 'Upload the supplier-provided, model-specific full energy label. An uploaded file takes priority over the external URL below.',
                    'required'     => 0,
                    'return_format'=> 'id',
                    'library'      => 'all',
                    'mime_types'   => 'jpg,jpeg,png,webp,pdf',
                ],
                [
                    'key'          => 'field_blaines_full_energy_label_external_url',
                    'label'        => 'Full energy label external URL',
                    'name'         => 'product_full_energy_label_url',
                    'type'         => 'url',
                    'instructions' => 'Optional supplier-hosted label URL. Used only when no uploaded label is selected.',
                    'required'     => 0,
                    'placeholder'  => 'https://',
                ],
                [
                    'key'          => 'field_blaines_product_information_sheet_file',
                    'label'        => 'Product information sheet',
                    'name'         => 'product_information_sheet_file',
                    'type'         => 'file',
                    'instructions' => 'Upload the supplier-provided product information sheet. An uploaded file takes priority over the external URL below.',
                    'required'     => 0,
                    'return_format'=> 'id',
                    'library'      => 'all',
                    'mime_types'   => 'pdf',
                ],
                [
                    'key'          => 'field_blaines_product_information_sheet_external_url',
                    'label'        => 'Product information sheet external URL',
                    'name'         => 'product_information_sheet_url',
                    'type'         => 'url',
                    'instructions' => 'Optional supplier-hosted sheet URL. Used only when no uploaded sheet is selected.',
                    'required'     => 0,
                    'placeholder'  => 'https://',
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
            'menu_order'            => 35,
            'position'              => 'normal',
            'style'                 => 'default',
            'label_placement'       => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen'        => '',
            'active'                => true,
            'description'           => 'Model-specific energy documents for transactional electrical products.',
            'show_in_rest'          => 0,
        ]
    );
}
add_action( 'acf/init', 'blaines_register_transactional_energy_information_fields' );
