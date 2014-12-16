<?php
/**
 * PixProof.
 * @package   PixProof
 * @author    Pixelgrade <contact@pixelgrade.com>
 * @license   GPL-2.0+
 * @link      http://pixelgrade.com
 * @copyright 2014 Pixelgrade
 */

/**
 * Plugin class.
 * @package   PixProof
 * @author    Pixelgrade <contact@pixelgrade.com>
 */
class PixProofPlugin {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 * @since   1.0.0
	 * @const   string
	 */
	protected $version = '1.0.3';
	/**
	 * Unique identifier for your plugin.
	 * Use this value (not the variable name) as the text domain when internationalizing strings of text. It should
	 * match the Text Domain file header in the main plugin file.
	 * @since    1.0.0
	 * @var      string
	 */
	protected $plugin_slug = 'pixproof';

	/**
	 * Instance of this class.
	 * @since    1.0.0
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 * @since    1.0.0
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Path to the plugin.
	 * @since    1.0.0
	 * @var      string
	 */
	protected $plugin_basepath = null;

	public $display_admin_menu = false;

	protected $config;

	protected static $number_of_images;

	public static $plugin_settings;

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 * @since     1.0.0
	 */
	protected function __construct() {

		$this->plugin_basepath = plugin_dir_path( __FILE__ );
		$this->config          = self::config();
		self::$plugin_settings = get_option( 'pixproof_settings' );

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( __FILE__ ) . 'pixproof.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );


		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 99999999999 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'plugins_loaded', array( $this, 'register_metaboxes' ), 14 );
		add_action( 'init', array( $this, 'register_entities' ), 99999 );

		// a little hook into the_content
		add_filter( 'the_content', array( $this, 'hook_into_the_content' ), 10, 1 );

		// parse comments to find referances for images
		add_filter( 'comment_text', array( $this, 'parse_comments' ) );

		/**
		 * Ajax Callbacks
		 */
		add_action( 'wp_ajax_pixproof_image_click', array( &$this, 'ajax_click_on_photo' ) );
		add_action( 'wp_ajax_nopriv_pixproof_image_click', array( &$this, 'ajax_click_on_photo' ) );
		add_action( 'wp_ajax_pixproof_zip_file_url', array( &$this, 'generate_photos_zip_file' ) );
		add_action( 'wp_ajax_nopriv_pixproof_zip_file_url', array( &$this, 'generate_photos_zip_file' ) );
	}

	/**
	 * Return an instance of this class.
	 * @since     1.0.0
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public static function config() {
		// @TODO maybe check this
		return include 'plugin-config.php';
	}

	/**
	 * Fired when the plugin is activated.
	 * @since    1.0.0
	 *
	 * @param    boolean $network_wide True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

	}

	/**
	 * Fired when the plugin is deactivated.
	 * @since    1.0.0
	 *
	 * @param    boolean $network_wide True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
	 */
	static function deactivate( $network_wide ) {
		// TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 * @since    1.0.0
	 */
	function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, false, basename( dirname( __FILE__ ) ) . '/lang/' );
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 * @since     1.0.0
	 * @return    null    Return early if no settings page is registered.
	 */
	function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $screen->id == $this->plugin_screen_hook_suffix ) {
			wp_enqueue_style( $this->plugin_slug . '-admin-styles', plugins_url( 'css/admin.css', __FILE__ ), array(), $this->version );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 * @since     1.0.0
	 * @return    null    Return early if no settings page is registered.
	 */
	function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $screen->id == $this->plugin_screen_hook_suffix ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'js/admin.js', __FILE__ ), array( 'jquery' ), $this->version );
			wp_localize_script( $this->plugin_slug . '-admin-script', 'locals', array(
				'ajax_url' => admin_url( 'admin-ajax.php' )
			) );
		}
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 * @since    1.0.0
	 */
	function enqueue_styles() {

		if ( ! wp_style_is( 'wpgrade-main-style' ) ) {
			wp_enqueue_style( 'pixproof_inuit', plugins_url( 'css/inuit.css', __FILE__ ), array(), $this->version );
			wp_enqueue_style( 'pixproof_magnific-popup', plugins_url( 'css/mangnific-popup.css', __FILE__ ), array(), $this->version );
		}

		//		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'css/public.css', __FILE__ ), array('wpgrade-main-style'), $this->version );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		$zip_archive_generation = self::$plugin_settings['zip_archive_generation'];

		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'js/public.js', __FILE__ ), array( 'jquery' ), $this->version, true );
		wp_localize_script( $this->plugin_slug . '-plugin-script', 'pixproof', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'pixproof_settings' => array(
				'zip_archive_generation' => $zip_archive_generation
			)
		) );
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 */
	function add_plugin_admin_menu() {

		$this->plugin_screen_hook_suffix = add_options_page( __( 'PixProof', $this->plugin_slug ), __( 'PixProof', $this->plugin_slug ), 'edit_plugins', $this->plugin_slug, array(
			$this,
			'display_plugin_admin_page'
		) );

	}

	/**
	 * Render the settings page for this plugin.
	 */
	function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 */
	function add_action_links( $links ) {
		return array_merge( array( 'settings' => '<a href="' . admin_url( 'options-general.php?page=pixproof' ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>' ), $links );
	}

	function register_entities() {
		require_once( $this->plugin_basepath . 'features/custom_post_types.php' );
	}

	function register_metaboxes() {
		require_once( $this->plugin_basepath . 'features/metaboxes/metaboxes.php' );
	}

	function hook_into_the_content( $content ) {
		if ( get_post_type() !== 'proof_gallery' || post_password_required() ) {
			return $content;
		}
		$style = '';
		// == This order is important ==
		$pixproof_path = self::get_base_path();
		if ( file_exists( $pixproof_path . 'css/public.css' ) ) {
			ob_start();
			echo '<style>';
			include( $pixproof_path . 'css/public.css' );
			echo '</style>';
			$style = ob_get_clean();
		}

		$gallery  = self::get_gallery();
		$metadata = self::get_metadata();

		// == This order is important ==

		return $style . $metadata . $gallery . $content;
	}

	static function get_gallery( $post_id = null ) {
		// get the global $post variable or a specific post
		if ( $post_id == null ) {
			$post = get_post( $post_id );
		} else {
			global $post;
		}

		//		$attachments = get_children( array( 'post_parent' => $post->post_parent, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID' ) );
		// get this gallery's metadata
		$gallery_data = get_post_meta( get_the_ID(), '_pixproof_main_gallery', true );
		// quit if there is no gallery data
		if ( empty( $gallery_data ) || ! isset( $gallery_data[ 'gallery' ] ) ) {
			return false;
		}

		$gallery_ids = explode( ',', $gallery_data[ 'gallery' ] );
		if ( empty( $gallery_ids ) ) {
			return false;
		}

		$order = 'menu_order ID';
		if ( isset( $gallery_data[ 'random' ] ) && ! empty( $gallery_data[ 'random' ] ) ) {
			$order = $gallery_data[ 'random' ];
		}

		$columns = 3;
		if ( isset( $gallery_data[ 'columns' ] ) && ! empty( $gallery_data[ 'columns' ] ) ) {
			$columns = $gallery_data[ 'columns' ];
		}

		// get attachments
		$attachments = get_posts( array(
			'post_status'    => 'any',
			'post_type'      => 'attachment',
			'post__in'       => $gallery_ids,
			'orderby'        => 'post__in',
			'posts_per_page' => '-1'
		) );
		if ( is_wp_error( $attachments ) || empty( $attachments ) ) {
			return false;
		}
		$number_of_images = self::set_number_of_images( count( $attachments ) );
		$template_name    = 'pixproof_gallery' . EXT;
		$_located         = locate_template( "templates/" . $template_name, false, false );

		// use the default one if the (child) theme doesn't have it
		if ( ! $_located ) {
			$_located = dirname( __FILE__ ) . '/views/' . $template_name;
		}

		//get the settings so they are available in the template
		$photo_display_name = get_post_meta( get_the_ID(), '_pixproof_photo_display_name', true );

		ob_start();
		require $_located;

		return ob_get_clean();
	}

	static function get_metadata( $post_id = null ) {

		if ( $post_id == null ) {
			$post = get_post( $post_id );
		} else {
			global $post;
		}

		$template_name = 'pixproof_metadata' . EXT;
		$_located      = locate_template( "templates/" . $template_name, false, false );

		// use the default one if the (child) theme doesn't have it
		if ( ! $_located ) {
			$_located = dirname( __FILE__ ) . '/views/' . $template_name;
		}

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

		if ( self::$plugin_settings[ 'enable_archive_zip_download' ] ) {

			// this must be here
			if (!class_exists('PclZip')) {
				require ABSPATH . 'wp-admin/includes/class-pclzip.php';
			}

			// if the user wants a download link, now we qenerate it
			if ( ! isset( self::$plugin_settings[ 'zip_archive_generation' ] ) || self::$plugin_settings[ 'zip_archive_generation' ] == 'manual' ) {
				$file = get_post_meta( get_the_ID(), '_pixproof_file', true );
			} elseif ( class_exists( 'PclZip' ) ) {
				$file = new PclZip( 'photos' );
				$file = PixProofPlugin::get_zip_file_url( get_the_ID() );
			}
		}

		$number_of_images = self::get_number_of_images();

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

	function ajax_click_on_photo() {

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

	function parse_comments( $comment = '' ) {

		global $post;
		if ( 'proof_gallery' !== $post->post_type ) {
			return $comment;
		}

		//		$comment = preg_replace_callback('/(^| )#*(\d+)( |$)/ism', 'match_callback', $comment);
		$comment = preg_replace_callback( "=(^| )+#[\w\-]+=", 'match_callback', $comment );

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

		$gallery_id = $_REQUEST[ 'gallery_id' ];
		// get this gallery's metadata
		$gallery_data = get_post_meta( $gallery_id, '_pixproof_main_gallery', true );
		// quit if there is no gallery data
		if ( empty( $gallery_data ) || ! isset( $gallery_data[ 'gallery' ] ) ) {
			return false;
		}

		$gallery_ids = explode( ',', $gallery_data[ 'gallery' ] );
		if ( empty( $gallery_ids ) ) {
			return false;
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

		// create the output of the archive
		header( 'Content-Description: File Transfer' );
		header( 'Content-Type: application/zip' );
		header( 'Content-Disposition: attachment; filename=gallery_' . get_the_title( $gallery_id ) . "_" . date( 'd_m_Y' ) );
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

}

function match_callback( $matches ) {
	$the_id = substr( trim( $matches[ 0 ] ), 1 );

	$matches[ 0 ] = '<span class="pixproof_photo_ref" data-href="#item-' . $the_id . '">#' . $the_id . '</span>';

	return $matches[ 0 ];

}