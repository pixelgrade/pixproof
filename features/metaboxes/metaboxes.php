<?php
/**
 * Include and setup custom metaboxes and fields.
 * @category YourThemeOrPlugin
 * @package  Metaboxes
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     https://github.com/webdevstudios/Custom-Metaboxes-and-Fields-for-WordPress
 */

add_filter( 'pixproof_meta_boxes', 'pixproof_sample_metaboxes' );
/**
 * Define the metabox and field configurations.
 *
 * @param  array $meta_boxes
 *
 * @return array
 */
function pixproof_sample_metaboxes( array $meta_boxes ) {

	// Start with an underscore to hide fields from custom fields list
	$plugin_config = get_option( 'pixproof_settings' );

	$prefix = '_pixproof_';

	$meta_boxes[ 'test_metabox' ] = array(
		'id'         => 'pixroof_gallery',
		'title'      => __( 'Pixproof Gallery', 'pixproof_l10n' ),
		'pages'      => array( 'proof_gallery', ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		// 'pixproof_styles' => true, // Enqueue the CMB stylesheet on the frontend
		'fields'     => array(
			array(
				'name'       => __( 'Gallery', 'pixproof_l10n' ),
				'id'         => $prefix . 'main_gallery',
				'type'       => 'gallery',
				'show_names' => false,
			),
			array(
				'name' => __( 'Client Name', 'pixproof_l10n' ),
				//				'desc' => __( 'field description (optional)', 'pixproof_l10n' ),
				'id'   => $prefix . 'client_name',
				'type' => 'text',
			),
			array(
				'name' => __( 'Date', 'pixproof_l10n' ),
				'id'   => $prefix . 'event_date',
				'type' => 'text_date',
			),
			array(
				'name'    => __( 'Photos Display Name', 'pixproof_l10n' ),
				'desc'    => __( 'How would you like to identify each photo?', 'pixproof_l10n' ),
				'id'      => $prefix . 'photo_display_name',
				'type'    => 'select',
				'options' => array(
					array(
						'name'  => __( 'Unique IDs', 'pixproof_l10n' ),
						'value' => 'unique_ids'
					),
					array(
						'name'  => __( 'Consecutive IDs', 'pixproof_l10n' ),
						'value' => 'consecutive_ids'
					),
					array(
						'name'  => __( 'File Name', 'pixproof_l10n' ),
						'value' => 'file_name'
					),
					array(
						'name'  => __( 'Unique IDs and Photo Title', 'pixproof_l10n' ),
						'value' => 'unique_ids_photo_title'
					),
					array(
						'name'  => __( 'Consecutive IDs and Photo Title', 'pixproof_l10n' ),
						'value' => 'consecutive_ids_photo_title'
					),
				),
				'std'     => 'fullwidth',
			),
		),
	);

	if ( ( $plugin_config[ 'enable_archive_zip_download' ] ) && ( ! isset( $plugin_config[ 'zip_archive_generation' ] ) || $plugin_config[ 'zip_archive_generation' ] == 'manual' ) ) {
		array_push( $meta_boxes[ 'test_metabox' ][ 'fields' ], array(
			'name' => __( 'Client .zip archive', 'pixproof_l10n' ),
			'desc' => __( 'Upload a .zip archive so the client can download it via the Download link. Leave it empty to hide the link.', 'pixproof_l10n' ),
			'id'   => $prefix . 'file',
			'type' => 'file',
		) );
	}

	// Add other metaboxes as needed
	return $meta_boxes;
}

add_action( 'init', 'pixproof_initialize_pixproof_meta_boxes', 9999 );
/**
 * Initialize the metabox class.
 */
function pixproof_initialize_pixproof_meta_boxes() {

	if ( ! class_exists( 'pixproof_Meta_Box' ) ) {
		require_once 'init.php';
	}

}
