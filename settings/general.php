<?php
//not used yet - moved them to a per gallery option
return array(
	'type'    => 'postbox',
	'label'   => 'General Settings',
	'options' => array(
		'enable_archive_zip_download'   => array(
			'label'          => __( 'Enable Images Download', 'pixproof_txtd' ),
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
					'label'   => __( 'The ZIP archive should be generated:', 'pixproof_txtd' ),
					'desc'    => __( 'How the archive file should be generated?', 'pixproof_txtd' ),
					'default' => 'manual',
					'type'    => 'select',
					'options' => array(
						'manual'    => __( 'Manually (uploaded by the gallery owner)', 'pixproof_txtd' ),
						'automatic' => __( 'Automatically (from the selected images)', 'pixproof_txtd' ),
					),
				),
			)
		)
	)
); # config