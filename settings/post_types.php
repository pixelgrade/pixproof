<?php
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
	)
); # config