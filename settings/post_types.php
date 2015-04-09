<?php

global $_wp_additional_image_sizes;

$sizes = get_intermediate_image_sizes();
$size_options = array(
	'thumbnail' => 'Thumbnail ( ' .get_option('thumbnail_size_w') . ' x ' .get_option('thumbnail_size_h') . ' cropped )',
	'medium' => 'Medium ( ' .get_option('medium_size_w') . ' x ' .get_option('medium_size_h') . ' )',
	'large' => 'Large ( ' .get_option('large_size_w') . ' x ' .get_option('large_size_h') . ' )',
	'full' => 'Full size'

);

if ( is_array( $_wp_additional_image_sizes ) ) {
	foreach ( $_wp_additional_image_sizes as $key => $size ) {
		$size_options[ $key ] = ucfirst( $key );
		if ( isset( $size['width'] ) && isset( $size['height'] ) ) {
			$size_options[ $key ] .= ' ( ' .$size['width']. ' x ' . $size['height'];

			if ( isset( $size['crop'] ) && $size['crop']) {
				$size_options[ $key ] .= ' cropped';
			}

			$size_options[ $key ] .= ' )';
		}
	}
}

return array(
	'type'    => 'postbox',
	'label'   => __( 'Proof Galleries Settings', 'pixproof_txtd' ),
	// Custom field settings
	// ---------------------

	'options' => array(
		'enable_pixproof_gallery'       => array(
			'label'          => __( 'Enable Pixproof Galleries', 'pixproof_txtd' ),
			'default'        => true,
			'type'           => 'switch',
			'show_group'     => 'enable_pixproof_gallery_group',
			'display_option' => ''
		), /* ALL THESE PREFIXED WITH PORTFOLIO SHOULD BE KIDS!! **/

		'enable_pixproof_gallery_group' => array(
			'type'    => 'group',
			'options' => array(
				'pixproof_single_item_label'             => array(
					'label'   => __( 'Single Item Label', 'pixproof_txtd' ),
					'desc'    => __( 'Here you can change the singular label.The default is "Proof Gallery"', 'pixproof_txtd' ),
					'default' => __( 'Proof Gallery', 'pixproof_txtd' ),
					'type'    => 'text',
				),
				'pixproof_multiple_items_label'          => array(
					'label'   => __( 'Multiple Items Label (plural)', 'pixproof_txtd' ),
					'desc'    => __( 'Here you can change the plural label.The default is "Proof Galleries"', 'pixproof_txtd' ),
					'default' => __( 'Proof Galleries', 'pixproof_txtd' ),
					'type'    => 'text',
				),
				'pixproof_change_single_item_slug'       => array(
					'label'      => __( 'Change Gallery Slug', 'pixproof_txtd' ),
					'desc'       => __( 'Do you want to rewrite the single gallery item slug?', 'pixproof_txtd' ),
					'default'    => false,
					'type'       => 'switch',
					'show_group' => 'pixproof_change_single_item_slug_group',
				),
				'pixproof_change_single_item_slug_group' => array(
					'type'    => 'group',
					'options' => array(
						'pixproof_gallery_new_single_item_slug' => array(
							'label'   => __( 'New Single Item Slug', 'pixproof_txtd' ),
							'desc'    => __( 'Change the single gallery slug as you need it.', 'pixproof_txtd' ),
							'default' => 'pixproof_gallery',
							'type'    => 'text',
						),
					),
				),
				//				'pixproof_change_archive_slug' => array
				//				(
				//					'label' => __('Change Archive Slug', 'pixproof_txtd'),
				//					'desc' => __('Do you want to rewrite the proof gallery archive slug? This will only be used if you don\'t have a page with the Portfolio template.', 'pixproof_txtd'),
				//					'default' => false,
				//					'type' => 'switch',
				//					'show_group' => 'pixproof_change_archive_slug_group',
				//				),
				//				'pixproof_change_archive_slug_group' => array
				//				(
				//					'type' => 'group',
				//					'options' => array
				//					(
				//						'pixproof_new_archive_slug' => array
				//						(
				//							'label' => __('New Category Slug', 'pixproof_txtd'),
				//							'desc' => __('Change the pixproof category slug as you need it.', 'pixproof_txtd'),
				//							'default' => 'pixproof',
				//							'type' => 'text',
				//						),
				//					),
				//				),
			),
		),

		'enable_pixproof_gallery_global_style'       => array(
			'label'      => __( 'Change Gallery Global Style', 'pixproof_txtd' ),
			'desc'       => __( 'Do you want to overwrite the style of all proof galleries?', 'pixproof_txtd' ),
			'default'    => false,
			'type'       => 'switch',
			'show_group' => 'enable_pixproof_gallery_global_style_group',
		),

		'enable_pixproof_gallery_global_style_group' => array(
			'type'    => 'group',
			'options' => array(

				'gallery_thumbnail_sizes' => array(
					'name'    => 'gallery_thumbnail_sizes',
					'label'   => __( 'How big the image thumbnails should be?', 'pixproof_txtd' ),
					'default' => 'medium',
					'type'    => 'select',
					'options' => $size_options,
				),
				'gallery_grid_sizes' => array(
					'name'    => 'gallery_grid_sizes',
					'label'   => __( 'How big should be the grid?', 'pixproof_txtd' ),
					'default' => '3',
					'type'    => 'select',
					'options' => array(
						'99999999' => 'Auto',
						'1' => 'One Column',
						'2' => 'Two Columns',
						'3' => 'Three Columns',
						'4' => 'Four Columns',
						'5' => 'Five Columns',
						'6' => 'Six Columns',
					),
				)

			)
		)
	)
); # config