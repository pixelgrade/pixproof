<?php
/**
 * Include and setup custom metaboxes and fields.
 */

if ( file_exists( dirname( __FILE__ ) . '/cmb2/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/cmb2/init.php';
} elseif ( file_exists( dirname( __FILE__ ) . '/CMB2/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/CMB2/init.php';
}

require_once dirname( __FILE__ ) . '/cmb2-pixproof_gallery-field/cmb2-pixproof_gallery-field.php';

/**
 * Define the metabox and field configurations.
 */
function pixproof_metaboxes() {

	$plugin_config = get_option( 'pixproof_settings' );

	$prefix = '_pixproof_';

	$gallery_metabox = new_cmb2_box( array(
		'id'            => $prefix . 'gallery',
		'title'         => esc_html__( 'Pixproof Gallery', 'cmb2' ),
		'object_types'      => array( 'proof_gallery', ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
	) );

	$gallery_metabox->add_field( array(
		'name'         => esc_html__( 'Photo Gallery', 'pixproof' ),
		'id'           => $prefix . 'main_gallery',
		'type'         => 'pixproof_gallery',
		'desc'          => esc_html__( 'Some description', 'pixproof'),
	) );

//	$gallery_metabox->add_field( array(
//		'name'         => esc_html__( 'Gallery Settings', 'pixproof' ),
//		'id'           => $prefix . 'main_gallery_settings_title',
//		'type'         => 'title',
//	) );
//
//	$gallery_metabox->add_field( array(
//		'name'    => esc_html__( 'Link To', 'pixproof' ),
//		'desc'    => esc_html__( 'Where should the visitor be taken when clicking on an image?', 'pixproof' ),
//		'id'      => $prefix . 'main_gallery_settings_linkto',
//		'type'    => 'select',
//		'show_option_none' => false,
//		'options' => array(
//			'attachment_page'  => esc_html__( 'Attachment Page', 'pixproof' ),
//			'media_file'  => esc_html__( 'Media File', 'pixproof' ),
//			'none'  => esc_html__( 'No Link', 'pixproof' ),
//		),
//		'default'     => 'media_file',
//	) );
//
//	$gallery_metabox->add_field( array(
//		'name' => esc_html__( 'Random Order', 'pixproof' ),
//		'desc' => esc_html__( 'Enable this if you want the images to be displayed in a random order.', 'pixproof' ),
//		'id'   => $prefix . 'main_gallery_settings_randomorder',
//		'type' => 'checkbox',
//	) );
//
//	$gallery_metabox->add_field( array(
//		'name'    => esc_html__( 'Thumbnail Size', 'pixproof' ),
//		'desc'    => esc_html__( 'How big should the displayed image thumbnails should be?', 'pixproof' ),
//		'id'      => $prefix . 'main_gallery_settings_thumbnailsize',
//		'type'    => 'select',
//		'show_option_none' => false,
//		'options' => array(
//			'thumbnail'  => esc_html__( 'Thumbnail', 'pixproof' ),
//			'medium'  => esc_html__( 'Medium', 'pixproof' ),
//			'large'  => esc_html__( 'Large', 'pixproof' ),
//			'full'  => esc_html__( 'Full Size', 'pixproof' ),
//		),
//		'default'     => 'thumbnail',
//	) );

	$gallery_metabox->add_field( array(
		'name'         => esc_html__( 'Client Details', 'pixproof' ),
		'id'           => $prefix . 'main_gallery_client_title',
		'type'         => 'title',
	) );

	$gallery_metabox->add_field( array(
		'name'       => esc_html__( 'Client Name', 'pixproof' ),
		'id'         => $prefix . 'client_name',
		'type'       => 'text',
	) );

	$gallery_metabox->add_field( array(
		'name' => esc_html__( 'Date', 'pixproof' ),
		'id'   => $prefix . 'event_date',
		'type' => 'text_date',
		'desc' => esc_html__( 'Add the date when these photos were taken, if you wish.', 'pixproof' ),
	) );

	$gallery_metabox->add_field( array(
		'name'    => esc_html__( 'Photos Display Name', 'pixproof' ),
		'desc'    => esc_html__( 'How would you like to identify each photo?', 'pixproof' ),
		'id'      => $prefix . 'photo_display_name',
		'type'    => 'select',
		'show_option_none' => false,
		'options' => array(
			'unique_ids'  => esc_html__( 'Unique IDs', 'pixproof' ),
			'consecutive_ids'  => esc_html__( 'Consecutive IDs', 'pixproof' ),
			'file_name'  => esc_html__( 'File Name', 'pixproof' ),
			'unique_ids_photo_title'  => esc_html__( 'Unique IDs and Photo Title', 'pixproof' ),
			'consecutive_ids_photo_title'  => esc_html__( 'Consecutive IDs and Photo Title', 'pixproof' ),
		),
		'default'     => 'fullwidth',
	) );

	if ( ( $plugin_config[ 'enable_archive_zip_download' ] ) && ( ! isset( $plugin_config[ 'zip_archive_generation' ] ) || $plugin_config[ 'zip_archive_generation' ] == 'manual' ) ) {
		$gallery_metabox->add_field( array(
			'name' => esc_html__( 'Client .zip archive', 'pixproof' ),
			'desc' => esc_html__( 'Upload a .zip archive so the client can download it via the Download link. Leave it empty to hide the link.', 'pixproof' ),
			'id'   => $prefix . 'file',
			'type' => 'file',
		) );
	}

	if ( ( $plugin_config[ 'enable_archive_zip_download' ] ) && ( ! isset( $plugin_config[ 'zip_archive_generation' ] ) || $plugin_config[ 'zip_archive_generation' ] !== 'manual' ) ) {
		$gallery_metabox->add_field( array(
			'name' => esc_html__( 'Disable Archive Download', 'pixproof' ),
			'desc' => esc_html__( 'You can remove the ability to download the zip archive for this gallery', 'pixproof' ),
			'id'   => $prefix . 'disable_archive_download',
			'type' => 'checkbox',
		) );
	}
}
add_action( 'cmb2_admin_init', 'pixproof_metaboxes' );
