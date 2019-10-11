<?php
/**
 * Document for class Pixproof.
 *
 * @package Pixproof
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Pixproof' ) ) :

/**
 * This is the class that handles the overall logic for the Pixproof.
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @since       1.0.0
 */
class Pixproof extends Pixproof_Singleton_Registry {

	protected static $number_of_images;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param $parent
	 */
	protected function __construct( $parent = null ) {
		// We need to initialize at this action so we can do some checks before hooking up.
		add_action( 'plugins_loaded', array( $this, 'init' ), 1 );
	}

	/**
	 * Initialize this module.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		if ( ! self::check_setup() ) {
			return;
		}

		// Hook up.
		$this->add_hooks();
	}

	/**
	 * Initiate our hooks
	 *
	 * @since 1.0.0
	 */
	public function add_hooks() {

		add_action( 'init', array( $this, 'register_post_types' ), 99999 );

		add_action( 'cmb2_admin_init', array( $this, 'proof_gallery_metaboxes' ) );

		// Load admin dashboard stylesheets and scripts, dedicated to edit post.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts_and_styles' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 99999999999 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		/*
		 * FRONTEND RELATED
		 */
		// a little hook into the_content
		add_filter( 'the_content', array( $this, 'hook_into_the_content' ), 10, 1 );

		add_filter( 'pixproof_filter_gallery_filename', array( $this, 'prepare_the_gallery_name' ), 10, 4 );

		// parse comments to find referances for images
		add_filter( 'comment_text', array( $this, 'parse_comments' ) );

		// AJAX callbacks
		add_action( 'wp_ajax_pixproof_image_click', array( &$this, 'ajax_click_on_photo' ) );
		add_action( 'wp_ajax_nopriv_pixproof_image_click', array( &$this, 'ajax_click_on_photo' ) );
		add_action( 'wp_ajax_pixproof_zip_file_url', array( &$this, 'generate_photos_zip_file' ) );
		add_action( 'wp_ajax_nopriv_pixproof_zip_file_url', array( &$this, 'generate_photos_zip_file' ) );
	}

	/**
	 * Check if everything needed for the core plugin logic is in place.
	 *
	 * @return bool
	 */
	public static function check_setup() {

		$all_good = true;

		return $all_good;
	}

	public function register_post_types() {

		// Bail if the CPT is not enabled.
		if ( pixproof_get_setting( 'enable_pixproof_gallery', 'on' ) !== 'on' ) {
			return;
		}

		$slug = 'proof_gallery';
		if ( pixproof_get_setting( pixproof_prefix( 'change_single_item_slug' ) ) === 'on' ) {
			$slug = pixproof_get_setting( pixproof_prefix( 'gallery_new_single_item_slug' ) );
		}

		$labels = apply_filters( 'pixproof_proof_gallery_cpt_labels', array(
			'name'               => pixproof_get_setting( pixproof_prefix( 'multiple_items_label' ), esc_html__( 'Proof Galleries', 'pixproof' ) ),
			'singular_name'      => pixproof_get_setting( pixproof_prefix( 'single_item_label' ), esc_html_x( 'Proof Gallery', 'Post Type Single Item Name', 'pixproof' ) ),
			'menu_name'          => pixproof_get_setting( pixproof_prefix( 'multiple_items_label' ), esc_html__( 'Proof Galleries', 'pixproof' ) ),
			'parent_item_colon'  => esc_html__( 'Parent Item:', 'pixproof' ),
			'all_items'          => esc_html__( 'All Items', 'pixproof' ),
			'view_item'          => esc_html__( 'View Item', 'pixproof' ),
			'add_new_item'       => esc_html__( 'Add New Proof Gallery', 'pixproof' ),
			'add_new'            => esc_html__( 'Add New', 'pixproof' ),
			'edit_item'          => esc_html__( 'Edit Proof Gallery', 'pixproof' ),
			'update_item'        => esc_html__( 'Update Proof Gallery', 'pixproof' ),
			'search_items'       => esc_html__( 'Search Proof Galleries', 'pixproof' ),
			'not_found'          => esc_html__( 'Not found', 'pixproof' ),
			'not_found_in_trash' => esc_html__( 'Not found in Trash', 'pixproof' ),
		) );

		$args = apply_filters( 'pixproof_proof_gallery_cpt_args', array(
			'label'               => pixproof_get_setting( pixproof_prefix( 'single_item_label' ), esc_html_x( 'Proof Gallery', 'Post Type Single Item Name', 'pixproof' ) ),
			'description'         => pixproof_get_setting( pixproof_prefix( 'multiple_items_label' ), esc_html__( 'Proof Galleries', 'pixproof' ) ),
			'labels'              => $labels,
			'supports'            => array(
				'title',
				'editor',
				'author',
				'thumbnail',
				'comments',
				'revisions',
				'page-attributes',
			),
			'hierarchical'        => true,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => null,
			'menu_icon'           => 'dashicons-visibility',
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'query_var'           => $slug,
			'rewrite'             => array( 'slug' => $slug ),
			'capability_type'     => 'page',
			'yarpp_support'       => false,
		) );

		register_post_type( 'proof_gallery', $args );
	}

	/**
	 * Define the proof_gallery CPT metabox and fields.
	 */
	function proof_gallery_metaboxes() {

		$gallery_metabox = new_cmb2_box( array(
			'id'            => $this->prefix( 'gallery', true ),
			'title'         => esc_html__( 'Pixproof Gallery', 'cmb2' ),
			'object_types'  => array( 'proof_gallery', ), // Post type
			'context'    => 'normal',
			'priority'   => 'high',
			'show_names' => true, // Show field names on the left
		) );

		$gallery_metabox->add_field( array(
			'name'         => '',
			'id'           => $this->prefix( 'main_gallery', true ),
			'type'         => 'pixproof_gallery',
			'desc'          => esc_html__( 'Some description', 'pixproof'),
		) );

		$gallery_metabox->add_field( array(
			'name'         => esc_html__( 'Client Details', 'pixproof' ),
			'id'           => $this->prefix( 'main_gallery_client_title', true ),
			'type'         => 'title',
		) );

		$gallery_metabox->add_field( array(
			'name'       => esc_html__( 'Name', 'pixproof' ),
			'id'         => $this->prefix( 'client_name', true ),
			'type'       => 'text',
		) );

		$gallery_metabox->add_field( array(
			'name' => esc_html__( 'Date', 'pixproof' ),
			'id'   => $this->prefix( 'event_date', true ),
			'type' => 'text_date',
			'desc' => esc_html__( 'Add the date when these photos were taken, if you wish.', 'pixproof' ),
		) );

		$gallery_metabox->add_field( array(
			'name'    => esc_html__( 'Photos Display Name', 'pixproof' ),
			'desc'    => esc_html__( 'How would you like to identify each photo?', 'pixproof' ),
			'id'      => $this->prefix( 'photo_display_name', true ),
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

		if ( ( pixproof_get_setting( 'enable_archive_zip_download' ) === 'on' && pixproof_get_setting( 'zip_archive_generation' ) === 'manual' ) ) {
			$gallery_metabox->add_field( array(
				'name' => esc_html__( 'Client .zip archive', 'pixproof' ),
				'desc' => esc_html__( 'Upload a .zip archive so the client can download it via the Download link. Leave it empty to hide the link.', 'pixproof' ),
				'id'   => $this->prefix( 'file', true ),
				'type' => 'file',
			) );
		}

		if ( pixproof_get_setting( 'enable_archive_zip_download' ) === 'on' && pixproof_get_setting( 'zip_archive_generation' ) !== 'manual' ) {
			$gallery_metabox->add_field( array(
				'name' => esc_html__( 'Disable Archive Download', 'pixproof' ),
				'desc' => esc_html__( 'You can remove the ability to download the zip archive for this gallery', 'pixproof' ),
				'id'   => $this->prefix( 'disable_archive_download', true ),
				'type' => 'checkbox',
			) );
		}
	}

	public function enqueue_admin_scripts_and_styles() {

		if ( ! $this->is_editing_post_type() ) {
			return;
		}

		// The styles.
		wp_register_style( $this->prefix( 'edit-post-style' ), plugins_url( 'assets/css/edit-post.css', Pixproof_Plugin()->get_file() ), array(), Pixproof_Plugin()->get_version() );
		wp_enqueue_style( $this->prefix( 'edit-post-style' ) );
	}

	public function is_editing_post_type() {
		$current_screen = get_current_screen();

		if ( ! empty( $current_screen ) && $current_screen instanceof WP_Screen ) {
			if ( $current_screen->base === 'post' && $current_screen->post_type === 'proof_gallery' ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 * @since    1.0.0
	 */
	function enqueue_styles() {

		if ( ! wp_style_is( 'wpgrade-main-style' ) && pixproof_get_setting( 'disable_pixproof_style' ) !== '1' ) {
			wp_enqueue_style( 'pixproof_inuit', plugins_url( 'assets/css/inuit.css', Pixproof_Plugin()->get_file() ), array(), Pixproof_Plugin()->get_version() );
			wp_enqueue_style( 'pixproof_magnific-popup', plugins_url( 'assets/css/mangnific-popup.css', Pixproof_Plugin()->get_file() ), array(), Pixproof_Plugin()->get_version() );
		}
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( pixproof_prefix( 'plugin-script' ), plugins_url( 'assets/js/public.js', Pixproof_Plugin()->get_file() ), array( 'jquery' ), Pixproof_Plugin()->get_version(), true );
		wp_localize_script( pixproof_prefix( 'plugin-script' ), 'pixproof', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'pixproof_settings' => array(
				'zip_archive_generation' => pixproof_get_setting( 'zip_archive_generation' )
			),
			'l10n' => array(
				'select' => esc_html__('Select', 'pixproof'),
				'deselect' => esc_html__('Deselect', 'pixproof'),
				'ofCounter' => esc_html__('of', 'pixproof'),
				'next' => esc_html__('Next', 'pixproof'),
				'previous' => esc_html__('Previous', 'pixproof')
			)
		) );
	}

	public function hook_into_the_content( $content ) {
		if ( get_post_type() !== 'proof_gallery' || post_password_required() ) {
			return $content;
		}

		$style = '';
		// == This order is important ==
		$pixproof_path = trailingslashit( Pixproof_Plugin()->get_basepath() );

		if ( pixproof_get_setting( 'disable_pixproof_style', 'off' ) !== 'on' && file_exists( $pixproof_path . 'assets/css/public.css' ) ) {
			ob_start();
			echo '<style>';
			include( $pixproof_path . 'assets/css/public.css' );
			echo '</style>';
			$style = ob_get_clean();
		}

		$gallery  = self::get_gallery();
		$metadata = self::get_metadata();

		// == This order is important ==
		$pixproof_output = $style . $metadata . $gallery;
		$gallery_position = pixproof_get_setting( 'gallery_position_in_content', 'before' );
		if ( $gallery_position === 'before' ) {
			return $pixproof_output . $content;
		} else {
			return $content . $pixproof_output;
		}
	}

	static function get_gallery() {
		// get this gallery's metadata
		$gallery_data = get_post_meta( get_the_ID(), '_pixproof_main_gallery', true );
		// quit if there is no gallery data
		if ( empty( $gallery_data ) || ! isset( $gallery_data[ 'gallery' ] ) ) {
			return false;
		}

		/*
		 * All the variables in this function will be available in the scope of the template.
		 */

		$gallery_ids = explode( ',', $gallery_data[ 'gallery' ] );
		if ( empty( $gallery_ids ) ) {
			return false;
		}

		$order = 'post__in';
		if ( isset( $gallery_data[ 'random' ] ) && ( $gallery_data[ 'random' ] === 'true' ) ) {
			$order = 'rand';
		}

		$columns = 3;
		if ( ! empty( $gallery_data[ 'columns' ] ) ) {
			$columns = $gallery_data[ 'columns' ];
		}

		$thumbnails_size = 'thumbnail';
		if ( ! empty( $gallery_data[ 'size' ] ) ) {
			$thumbnails_size = $gallery_data[ 'size' ];
		}


		if ( self::has_global_style() ) {
			$thumbnails_size = self::get_thumbnails_size();
			$columns = self::get_gallery_grid_size();
		}

		// get attachments
		$attachments = get_posts( array(
			'post_status'    => 'any',
			'post_type'      => 'attachment',
			'post__in'       => $gallery_ids,
			'orderby'        => $order,
			'posts_per_page' => '-1'
		) );
		if ( is_wp_error( $attachments ) || empty( $attachments ) ) {
			return false;
		}

		$number_of_images = self::set_number_of_images( count( $attachments ) );

		// Get the settings so they are available in the template.
		$photo_display_name = get_post_meta( get_the_ID(), '_pixproof_photo_display_name', true );

		// Locate the template to be used.
		$template_name    = 'pixproof_gallery.php';
		$_located         = locate_template( 'templates/' . $template_name, false, false );
		// Use the default one if the (child) theme doesn't have it
		if ( ! $_located ) {
			$_located = trailingslashit( Pixproof_Plugin()->get_basepath() ) . 'views/' . $template_name;
		}

		ob_start();
		require $_located;

		return ob_get_clean();
	}

	static function get_metadata() {
		global $post;

		$client_name = get_post_meta( get_the_ID(), '_pixproof_client_name', true );

		$attachments = get_children( array(
			'post_parent'    => $post->post_parent,
			'post_status'    => 'inherit',
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'order'          => 'ASC',
			'orderby'        => 'menu_order ID'
		) );
		$event_date  = get_post_meta( get_the_ID(), '_pixproof_event_date', true );
		$download_is_disabled  = get_post_meta( get_the_ID(), '_pixproof_disable_archive_download', true );

		if ( pixproof_get_setting( 'enable_archive_zip_download', 'on' ) === 'on' && $download_is_disabled !== 'on' ) {

			// This must be here
			if (! class_exists('PclZip')) {
				require ABSPATH . 'wp-admin/includes/class-pclzip.php';
			}

			// If the user wants a download link, now we qenerate it
			if ( pixproof_get_setting( 'zip_archive_generation', 'manual' ) === 'manual' ) {
				$file = get_post_meta( get_the_ID(), '_pixproof_file', true );
			} elseif ( class_exists( 'PclZip' ) ) {
				$file = new PclZip( 'photos' );
				$file = PixProofPlugin::get_zip_file_url( get_the_ID() );
			}
		}

		$number_of_images = self::get_number_of_images();

		$template_name = 'pixproof_metadata.php';
		$_located      = locate_template( 'templates/' . $template_name, false, false );
		// Use the default one if the (child) theme doesn't have it
		if ( ! $_located ) {
			$_located = trailingslashit( Pixproof_Plugin()->get_basepath() ) . 'views/' . $template_name;
		}

		ob_start();
		require $_located;

		return ob_get_clean();

	}

	static function get_attachment_class( $attachment ) {

		$data = wp_get_attachment_metadata( $attachment->ID );

		if ( isset( $data[ 'selected' ] ) && ! empty( $data[ 'selected' ] ) && $data[ 'selected' ] == 'true' ) {
			return 'selected';
		} else {
			return '';
		}
	}

	static function attachment_class( $attachment ) {
		echo self::get_attachment_class( $attachment );
	}

	static function attachment_data( $attachment ) {

		$data   = wp_get_attachment_metadata( $attachment->ID );
		$output = '';

		$output .= ' data-attachment_id="' . $attachment->ID . '"';

		echo $output;
	}

	static function set_number_of_images( $number_of_images ) {
		return self::$number_of_images = $number_of_images;
	}

	static function get_number_of_images() {
		return self::$number_of_images;
	}

	static function get_thumbnails_size() {
		return pixproof_get_setting( 'gallery_thumbnail_sizes', 'thumbnail' );
	}

	static function get_gallery_grid_size() {
		return pixproof_get_setting( 'gallery_grid_sizes', '3' );
	}

	static function has_global_style() {
		return pixproof_get_setting( 'enable_pixproof_gallery_global_style', 'off' ) === 'on' ? true : false;
	}

	public function ajax_click_on_photo() {

		ob_start();

		if ( ! isset( $_POST[ 'attachment_id' ] ) || ! isset( $_POST[ 'selected' ] ) ) {
			return false;
		}
		$attachment_id = $_POST[ 'attachment_id' ];
		$selected      = $_POST[ 'selected' ];

		$data               = wp_get_attachment_metadata( $attachment_id );
		$data[ 'selected' ] = $selected;

		wp_update_attachment_metadata( $attachment_id, $data );

		echo json_encode( ob_get_clean() );
		die();
	}

	public function parse_comments( $comment = '' ) {
		global $post;

		if ( 'proof_gallery' !== $post->post_type ) {
			return $comment;
		}
		$comment = preg_replace_callback( "=(^| )+#[\w\-]+=", 'pixproof_comments_match_callback', $comment );

		return $comment;
	}

	static function get_base_path() {
		return plugin_dir_path( __FILE__ );
	}

	// create an ajax call link
	static function get_zip_file_url( $post_id ) {
		return add_query_arg( array(
			'action'     => 'pixproof_zip_file_url',
			'gallery_id' => $post_id,
		), admin_url( 'admin-ajax.php' ) );
	}

	public function generate_photos_zip_file() {

		if ( ! isset ( $_REQUEST[ 'gallery_id' ] ) ) {
			return 'no gallery';
		}

		global $post;
		$gallery_id = $_REQUEST[ 'gallery_id' ];

		$post = get_post( $gallery_id );

		if ( post_password_required( $post ) ) {
			wp_send_json_error( esc_html__('The gallery password is required', 'pixproof') );
		}

		// get this gallery's metadata
		$gallery_data = get_post_meta( $gallery_id, '_pixproof_main_gallery', true );
		// quit if there is no gallery data
		if ( empty( $gallery_data ) || ! isset( $gallery_data[ 'gallery' ] ) ) {
			wp_send_json_error( esc_html__('No gallery data', 'pixproof') );
		}

		$gallery_ids = explode( ',', $gallery_data[ 'gallery' ] );
		if ( empty( $gallery_ids ) ) {
			wp_send_json_error( esc_html__('Empty gallery', 'pixproof') );
		}

		// get attachments
		$attachments = get_posts( array(
			'post_status'    => 'any',
			'post_type'      => 'attachment',
			'post__in'       => $gallery_ids,
			'orderby'        => 'post__in',
			'posts_per_page' => '-1',
			//			'meta_query'  => array( // this doesn't work :(
			//				array(
			//					'key'     => 'selected',
			//					'value'   => 'true',
			//					'compare' => '=',
			//				)
			//			)
		) );

		if ( is_wp_error( $attachments ) || empty( $attachments ) ) {
			return false;
		}

		// turn off compression on the server
		if ( function_exists( 'apache_setenv' ) ) {
			@apache_setenv( 'no-gzip', 1 );
		}
		@ini_set( 'zlib.output_compression', 'Off' );


		// create the archive
		if ( ! class_exists( 'PclZip' ) ) {
			require ABSPATH . 'wp-admin/includes/class-pclzip.php';
		}

		$filename = tempnam( get_temp_dir(), 'zip' );
		$zip      = new PclZip( $filename );
		$images   = array();

		foreach ( $attachments as $key => $attachment ) {
			$metadata = wp_get_attachment_metadata( $attachment->ID );

			// only those selected
			if ( isset( $metadata[ 'selected' ] ) && $metadata[ 'selected' ] == 'true' ) {
				$images[ ] = get_attached_file( $attachment->ID );
			}
		}

		$debug = $zip->create( $images, PCLZIP_OPT_REMOVE_ALL_PATH, PCLZIP_OPT_NO_COMPRESSION );

		if ( ! is_array( $debug ) ) {
			die( $zip->errorInfo( true ) );
		}
		unset( $zip );

		$uniqness = date( 'd_m_Y' );
		$file_name = apply_filters( 'pixproof_filter_gallery_filename', 'gallery_', $post->post_name, $uniqness, '.zip' );

		// create the output of the archive
		header( 'Content-Description: File Transfer' );
		header( 'Content-Type: application/zip' );
		header( 'Content-Disposition: attachment; filename=' . $file_name  );
		header( 'Content-Transfer-Encoding: binary' );
		header( 'Expires: 0' );
		header( 'Cache-Control: must-revalidate' );
		header( 'Pragma: public' );
		header( 'Content-Length: ' . filesize( $filename ) );

		$chunksize = 512 * 1024;
		$file      = @fopen( $filename, 'rb' );
		while ( ! feof( $file ) ) {
			echo @fread( $file, $chunksize );
			flush();
		}
		fclose( $file );

		// check for bug in some old PHP versions, close a second time!
		if ( is_resource( $file ) ) {
			@fclose( $file );
		}

		// delete the temporary file
		@unlink( $filename );

		exit;
	}

	/**
	 * This filter must return the gallery's zip filename
	 *
	 * @param $file_name_prefix
	 * @param $post_slug
	 * @param $unique
	 * @param $extension
	 *
	 * @return string
	 */
	public function prepare_the_gallery_name( $file_name_prefix, $post_slug, $unique, $extension ) {
		return $file_name_prefix . $post_slug . '_' . $unique . $extension;
	}

	/**
	 * Adds a prefix to an option name.
	 *
	 * @param string $option
	 * @param bool $private Optional. Whether this option name should also get a '_' in front, marking it as private.
	 *
	 * @return string
	 */
	public function prefix( $option, $private = false ) {
		$option = pixproof_prefix( $option );

		if ( true === $private ) {
			return '_' . $option;
		}

		return $option;
	}
}

endif;
