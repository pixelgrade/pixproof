<?php
//not used yet - moved them to a per gallery option
return array(
	'type'    => 'postbox',
	'label'   => 'General Settings',
	'options' => array(
		'gallery_position_in_content' => array (
			'default'        => 'before',
			'type'           => 'select',
			'desc'	  => __( 'Select the PixProof gallery position in content: ', 'pixproof'),
			'options' => array(
						'before'    => __( 'Before the content', 'pixproof' ),
						'after' => __( 'After the content', 'pixproof' ),
					),
		),

		'enable_archive_zip_download'   => array(
			'label'          => __( 'Enable Images Download', 'pixproof' ),
			'default'        => true,
			'type'           => 'switch',
			'show_group'     => 'enable_pixproof_gallery_group',
			'display_option' => true
		), /* ALL THESE PREFIXED WITH PORTFOLIO SHOULD BE KIDS!! **/

		'enable_pixproof_gallery_group' => array(
			'type'    => 'group',
			'options' => array(
				'zip_archive_generation' => array(
					'name'    => 'zip_archive_generation',
					'label'   => __( 'The ZIP archive should be generated:', 'pixproof' ),
					'desc'    => __( 'How the archive file should be generated?', 'pixproof' ),
					'default' => 'manual',
					'type'    => 'select',
					'options' => array(
						'manual'    => __( 'Manually (uploaded by the gallery owner)', 'pixproof' ),
						'automatic' => __( 'Automatically (from the selected images)', 'pixproof' ),
					),
				),
			)
		),


		'disable_pixproof_style'   => array(
			'label'          => __( 'Disable Plugin Style', 'pixproof' ),
			'desc'           => __( 'If you want to style the PixProof galleries yourself you can remove the plugin style here ', 'pixproof'),
			'default'        => false,
			'type'           => 'switch',
			'display_option' => true
		),
	)
); # config