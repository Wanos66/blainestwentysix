<?php
/**
 * ACF-backed Electrical archive promotion panels.
 *
 * Provides:
 * - Version-controlled ACF field registration.
 * - Normalised image-only promotion data for archive templates.
 *
 * @package Blaines
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'blaines_transactional_promotion_image_id' ) ) {
	/**
	 * Resolve an ACF image return value to an attachment ID.
	 *
	 * @param mixed $value ACF image value.
	 *
	 * @return int
	 */
	function blaines_transactional_promotion_image_id( $value ): int {
		if ( is_numeric( $value ) ) {
			return absint( $value );
		}

		if ( is_array( $value ) ) {
			if ( ! empty( $value['ID'] ) ) {
				return absint( $value['ID'] );
			}

			if ( ! empty( $value['id'] ) ) {
				return absint( $value['id'] );
			}
		}

		return 0;
	}
}

if ( ! function_exists( 'blaines_get_transactional_archive_promotions' ) ) {
	/**
	 * Retrieve and normalise archive promotion images from ACF.
	 *
	 * The queried department page is used by default. Product-type views use
	 * query parameters on that same page, so they inherit the department images.
	 *
	 * @param int $source_id Page ID containing the ACF repeater.
	 *
	 * @return array<int,array<string,int>>
	 */
	function blaines_get_transactional_archive_promotions(
		int $source_id = 0
	): array {
		if ( ! function_exists( 'get_field' ) ) {
			return [];
		}

		$source_id = $source_id ?: get_queried_object_id();

		if ( ! $source_id ) {
			return [];
		}

		$rows = get_field(
			'transactional_archive_promotions',
			$source_id
		);

		if ( ! is_array( $rows ) || empty( $rows ) ) {
			return [];
		}

		$items = [];

		foreach ( array_slice( array_values( $rows ), 0, 3 ) as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}

			$image_id = blaines_transactional_promotion_image_id(
				$row['image'] ?? 0
			);

			$mobile_image_id = blaines_transactional_promotion_image_id(
				$row['mobile_image'] ?? 0
			);

			if ( ! $image_id && ! $mobile_image_id ) {
				continue;
			}

			$items[] = [
				'image_id'        => $image_id,
				'mobile_image_id' => $mobile_image_id,
			];
		}

		return $items;
	}
}

if ( ! function_exists( 'blaines_register_transactional_archive_promotion_fields' ) ) {
	/**
	 * Register the Electrical archive promotion field group.
	 *
	 * The group appears on direct child pages of /electrical/.
	 *
	 * @return void
	 */
	function blaines_register_transactional_archive_promotion_fields(): void {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		$electrical_page = get_page_by_path(
			'electrical',
			OBJECT,
			'page'
		);

		if ( ! $electrical_page instanceof WP_Post ) {
			return;
		}

		acf_add_local_field_group(
			[
				'key'                   => 'group_blaines_transactional_archive_promotions',
				'title'                 => 'Electrical archive promotions',
				'fields'                => [
					[
						'key'          => 'field_blaines_transactional_archive_promotions',
						'label'        => 'Promotion panels',
						'name'         => 'transactional_archive_promotions',
						'type'         => 'repeater',
						'instructions' => 'Add up to three image panels displayed above the product results. Use 600 × 400 px artwork. Empty second or third positions remain blank rather than stretching the supplied panels. Product-type filtered views inherit these department panels.',
						'required'     => 0,
						'layout'       => 'block',
						'button_label' => 'Add promotion panel',
						'min'          => 0,
						'max'          => 3,
						'sub_fields'   => [
							[
								'key'           => 'field_blaines_transactional_archive_promotion_image',
								'label'         => 'Desktop image',
								'name'          => 'image',
								'type'          => 'image',
								'instructions'  => 'Recommended size: exactly 600 × 400 px (3:2). The original artwork is displayed at its native ratio and scales responsively without cropping. Any wording should be included within the artwork itself.',
								'required'      => 0,
								'return_format' => 'id',
								'preview_size'  => 'medium',
								'library'       => 'all',
							],
							[
								'key'           => 'field_blaines_transactional_archive_promotion_mobile_image',
								'label'         => 'Mobile image',
								'name'          => 'mobile_image',
								'type'          => 'image',
								'instructions'  => 'Optional mobile-specific version. Use the same 600 × 400 px (3:2) size to preserve consistent panel height.',
								'required'      => 0,
								'return_format' => 'id',
								'preview_size'  => 'medium',
								'library'       => 'all',
							],
						],
					],
				],
				'location'              => [
					[
						[
							'param'    => 'page_parent',
							'operator' => '==',
							'value'    => (string) $electrical_page->ID,
						],
					],
				],
				'menu_order'            => 20,
				'position'              => 'normal',
				'style'                 => 'default',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen'        => '',
				'active'                => true,
				'description'           => 'Up to three image-only promotional panels above Electrical archive product results.',
				'show_in_rest'          => 0,
			]
		);
	}
}

add_action(
	'acf/init',
	'blaines_register_transactional_archive_promotion_fields'
);
