<?php
/**
 * ACF fields for Electrical landing-page department imagery.
 *
 * Stores the seven department images against the Electrical page so they can
 * be changed in WordPress without editing the theme template.
 *
 * @package Blaines
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'acf/init',
	static function (): void {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		$electrical_page = get_page_by_path( 'electrical' );

		if ( ! $electrical_page instanceof WP_Post ) {
			return;
		}

		acf_add_local_field_group(
			[
				'key'      => 'group_blaines_electrical_department_images',
				'title'    => 'Electrical department images',
				'fields'   => [
					[
						'key'        => 'field_blaines_electrical_department_images',
						'label'      => 'Department images',
						'name'       => 'electrical_department_images',
						'type'       => 'group',
						'layout'     => 'block',
						'sub_fields' => [
							[
								'key'           => 'field_blaines_electrical_department_image_laundry',
								'label'         => 'Laundry',
								'name'          => 'laundry',
								'type'          => 'image',
								'instructions'  => 'Choose the image used for the Laundry department tile.',
								'required'      => 0,
								'return_format' => 'id',
								'preview_size'  => 'medium',
								'library'       => 'all',
							],
							[
								'key'           => 'field_blaines_electrical_department_image_refrigeration',
								'label'         => 'Refrigeration',
								'name'          => 'refrigeration',
								'type'          => 'image',
								'instructions'  => 'Choose the image used for the Refrigeration department tile.',
								'required'      => 0,
								'return_format' => 'id',
								'preview_size'  => 'medium',
								'library'       => 'all',
							],
							[
								'key'           => 'field_blaines_electrical_department_image_dishwashers',
								'label'         => 'Dishwashers',
								'name'          => 'dishwashers',
								'type'          => 'image',
								'instructions'  => 'Choose the image used for the Dishwashers department tile.',
								'required'      => 0,
								'return_format' => 'id',
								'preview_size'  => 'medium',
								'library'       => 'all',
							],
							[
								'key'           => 'field_blaines_electrical_department_image_cooking',
								'label'         => 'Cooking',
								'name'          => 'cooking',
								'type'          => 'image',
								'instructions'  => 'Choose the image used for the Cooking department tile.',
								'required'      => 0,
								'return_format' => 'id',
								'preview_size'  => 'medium',
								'library'       => 'all',
							],
							[
								'key'           => 'field_blaines_electrical_department_image_small_appliances',
								'label'         => 'Small Appliances',
								'name'          => 'small_appliances',
								'type'          => 'image',
								'instructions'  => 'Choose the image used for the Small Appliances department tile.',
								'required'      => 0,
								'return_format' => 'id',
								'preview_size'  => 'medium',
								'library'       => 'all',
							],
							[
								'key'           => 'field_blaines_electrical_department_image_floorcare',
								'label'         => 'Floorcare',
								'name'          => 'floorcare',
								'type'          => 'image',
								'instructions'  => 'Choose the image used for the Floorcare department tile.',
								'required'      => 0,
								'return_format' => 'id',
								'preview_size'  => 'medium',
								'library'       => 'all',
							],
							[
								'key'           => 'field_blaines_electrical_department_image_tv_entertainment',
								'label'         => 'TV & Entertainment',
								'name'          => 'tv_entertainment',
								'type'          => 'image',
								'instructions'  => 'Choose the image used for the TV & Entertainment department tile.',
								'required'      => 0,
								'return_format' => 'id',
								'preview_size'  => 'medium',
								'library'       => 'all',
							],
						],
					],
				],
				'location' => [
					[
						[
							'param'    => 'post',
							'operator' => '==',
							'value'    => (string) $electrical_page->ID,
						],
					],
				],
				'menu_order'            => 0,
				'position'              => 'normal',
				'style'                 => 'default',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen'        => '',
				'active'                => true,
				'description'           => 'Images used by the seven department tiles on the Electrical landing page.',
				'show_in_rest'          => 0,
			]
		);
	}
);
