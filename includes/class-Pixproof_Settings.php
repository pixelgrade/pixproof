<?php
/**
 * Document for class Pixproof_Settings.
 *
 * @package Pixproof
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to handle the settings page logic.
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @since       2.0.0
 */
class Pixproof_Settings extends Pixproof_Singleton_Registry {

	/**
 	 * Option key, and, at the same time, option page slug
 	 * @var string
 	 */
	public static $key = null;

	/**
	 * Settings Page title
	 * @var string
	 * @access protected
	 * @since 2.0.0
	 */
	protected $title = '';

	/**
	 * Settings Page hook
	 * @var string
	 * @access protected
	 * @since 2.0.0
	 */
	protected $options_page = '';

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	protected function __construct() {

		self::$key = $this->prefix('settings' );

		// Set our settings page title.
		$this->title = esc_html__( 'PixProof Setup', 'pixproof' );

		$this->add_hooks();
	}

	/**
	 * Initiate our hooks
	 *
	 * @since 2.0.0
	 */
	public function add_hooks() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'cmb2_admin_init', array( $this, 'cmb2_init' ) );

		// For when the plugin is used on a per site basis
		add_action( 'load-appearance_page_' . self::$key, array( $this, 'register_admin_scripts' ) );
		// For when the plugin is network activated
		add_action( 'load-settings_page_' . self::$key, array( $this, 'register_admin_scripts' ) );

		add_action( 'cmb2_save_options-page_fields_' . $this->prefix( 'setup_page' ), array( $this, 'on_save_settings' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
	}

	/**
	 * Do general things on the `init` hook.
	 *
	 * @since  2.0.0
	 */
	public function init() {

	}

	/**
	 * Register the fields, tabs, etc for our settings page.
	 *
	 * @since  2.0.0
	 */
	public function cmb2_init() {
		// If we are in a multisite context and the plugin is network activated (rather than on individual blogs in the network),
		// We will only add a network-wide settings page.

		$box_args = array(
			'id'           => $this->prefix( 'setup_page' ),
			'title'        => $this->title,
			'desc'         => 'description',
			'object_types' => array( 'options-page' ),
			'option_key'   => self::$key, // The option key and admin menu page slug.
			'menu_title'   => esc_html__( 'PixProof', 'pixproof' ),
			'autoload'     => true,
			'show_in_rest' => false,
		);

		if ( Pixproof_Plugin()->is_plugin_network_activated() ) {
			$box_args['admin_menu_hook'] = 'network_admin_menu'; // 'network_admin_menu' to add network-level options page.
			$box_args['parent_slug'] = 'settings.php'; // Make options page a submenu item of the settings menu.
		} else {
			$box_args['parent_slug'] = 'options-general.php'; // Make options page a submenu item of the settings menu.
			$box_args['capability'] = 'manage_options';
		}


		$cmb = new_cmb2_box( apply_filters( 'pixproof_cmb2_box_args', $box_args ) );

		/* ================================
		 * Fields for General settings.
		 * ================================ */

		$cmb->add_field( array(
			'name' => esc_html__( 'General Settings', 'pixproof' ),
			'desc' => esc_html__( 'Setup how things will behave in general.', 'pixproof' ),
			'id'   => $this->prefix( 'general_settings_title' ),
			'type' => 'title',
		) );

		$cmb->add_field( array(
			'name' => esc_html__( 'Position', 'pixproof' ),
			'desc' => esc_html__( 'Where should the photo proofing gallery should pe displayed in relation to the content.', 'pixproof' ),
			'id'   => 'gallery_position_in_content',
			'type' => 'select',
			'default' => 'before',
			'options' => array(
				'before' => esc_html__( 'Before the content', 'pixproof' ),
				'after'  => esc_html__( 'After the content', 'pixproof' ),
			),
		) );

		$cmb->add_field( array(
			'name' => esc_html__( 'Enable Images Download', 'pixproof' ),
			'desc' => esc_html__( 'Allow your customer to download a .zip archive of the photos you have attached to a certain gallery.', 'pixproof' ),
			'id'   => 'enable_archive_zip_download',
			'type' => 'checkbox',
			'default' => 'on',
		) );

		$cmb->add_field( array(
			'name' => esc_html__( 'ZIP archive generation method', 'pixproof' ),
			'desc' => esc_html__( 'Select "Manually" if you want to upload a .zip archive of your own making with all the photos you wish to share. Chose "Automatically" if you wish to leave the .zip generation to each client, allowing them to only include the selected photos.', 'pixproof' ),
			'id'   => 'zip_archive_generation',
			'type' => 'select',
			'default' => 'manual',
			'options' => array(
				'manual'    => esc_html__( 'Manually (uploaded by the gallery owner)', 'pixproof' ),
				'automatic' => esc_html__( 'Automatically (from the selected images)', 'pixproof' ),
			),
			'attributes' => array(
				'data-conditional-id'    => 'enable_archive_zip_download',
				'data-conditional-value' => 'on',
			),
		) );

		$cmb->add_field( array(
			'name' => esc_html__( 'Disable Plugin CSS Style', 'pixproof' ),
			'desc' => esc_html__( 'Check this if you want the plugin to stop including its own CSS file. This assumes you want more control over the galleries styling and you will add it somewhere else (like in the theme).', 'pixproof' ),
			'id'   => 'disable_pixproof_style',
			'type' => 'checkbox',
			'default' => 'off',
		) );

		/* ================================
		 * Fields for global styles.
		 * ================================ */

		$cmb->add_field( array(
			'name' => esc_html__( 'Proof Galleries Global Styles', 'pixproof' ),
			'desc' => esc_html__( 'Customize the global style options applied to galleries.', 'pixproof' ),
			'id'   => $this->prefix( 'galleries_global_styles_title' ),
			'type' => 'title',
		) );

		$cmb->add_field( array(
			'name' => esc_html__( 'Change Global Styles', 'pixproof' ),
			'desc' => esc_html__( 'Do you want to overwrite the style of all proof galleries?', 'pixproof' ),
			'id'   => 'enable_pixproof_gallery_global_style',
			'type' => 'checkbox',
			'default' => 'off',
		) );

		$size_options = array(
			/* translators: 1: The width of the thumbnail, 2: The height of the thumbnail.  */
			'thumbnail' => sprintf( esc_html__( 'Thumbnail (%1$s x %2$s cropped)', 'pixproof' ), get_option( 'thumbnail_size_w' ), get_option( 'thumbnail_size_h' ) ),
			/* translators: 1: The width of the thumbnail, 2: The height of the thumbnail.  */
			'medium'    => sprintf( esc_html__( 'Medium (%1$s x %2$s)', 'pixproof' ), get_option( 'medium_size_w' ), get_option( 'medium_size_h' ) ),
			/* translators: 1: The width of the thumbnail, 2: The height of the thumbnail.  */
			'large'     => sprintf( esc_html__( 'Large ( %1$s x %2$s)', 'pixproof' ), get_option( 'large_size_w' ), get_option( 'large_size_h' ) ),
			'full'      => esc_html__( 'Full size', 'pixproof' ),

		);
		$additional_sizes = wp_get_additional_image_sizes();
		foreach ( $additional_sizes as $key => $size ) {
			$size_options[ $key ] = ucfirst( $key );
			if ( isset( $size['width'] ) && isset( $size['height'] ) ) {
				$size_options[ $key ] .= ' (' . $size['width'] . ' x ' . $size['height'];

				if ( isset( $size['crop'] ) && $size['crop'] ) {
					$size_options[ $key ] .= esc_html__( ' cropped', 'pixproof' );
				}

				$size_options[ $key ] .= ')';
			}
		}

		$cmb->add_field( array(
			'name' => esc_html__( 'Thumbnails Size', 'pixproof' ),
			'desc' => esc_html__( 'How big should the image thumbnails be?', 'pixproof' ),
			'id'   => 'gallery_thumbnail_sizes',
			'type' => 'select',
			'default' => 'medium',
			'options' => $size_options,
			'attributes' => array(
				'data-conditional-id'    => 'enable_pixproof_gallery_global_style',
				'data-conditional-value' => 'on',
			),
		) );

		$cmb->add_field( array(
			'name' => esc_html__( 'Grid Size', 'pixproof' ),
			'desc' => esc_html__( 'How many columns should the grid have?', 'pixproof' ),
			'id'   => 'gallery_grid_sizes',
			'type' => 'select',
			'default' => '3',
			'options' => array(
				'99999999' => esc_html__( 'Auto', 'pixproof' ),
				'1'        => esc_html__( 'One Column', 'pixproof' ),
				'2'        => esc_html__( 'Two Columns', 'pixproof' ),
				'3'        => esc_html__( 'Three Columns', 'pixproof' ),
				'4'        => esc_html__( 'Four Columns', 'pixproof' ),
				'5'        => esc_html__( 'Five Columns', 'pixproof' ),
				'6'        => esc_html__( 'Six Columns', 'pixproof' ),
			),
			'attributes' => array(
				'data-conditional-id'    => 'enable_pixproof_gallery_global_style',
				'data-conditional-value' => 'on',
			),
		) );

		/* ==================================
		 * Advanced fields.
		 * ================================== */

		$cmb->add_field( array(
			'name' => esc_html__( 'Advanced Settings', 'pixproof' ),
			'desc' => esc_html__( 'If you really know your way around, dive in.', 'pixproof' ),
			'id'   => $this->prefix( 'advanced_title' ),
			'type' => 'title',
		) );

		$cmb->add_field( array(
			'name' => esc_html__( 'Custom CSS', 'pixproof' ),
			'desc' => esc_html__( 'Add here the custom CSS you want to output on the frontend of your site. It\'s OK to leave it empty if you have the CSS elsewhere.', 'pixproof' ),
			'id'   => $this->prefix( 'frontend_custom_css' ),
			'type' => 'textarea_code',
			'default' => '',
			'attributes' => array(
				'required'               => false,

				'data-codeeditor' => json_encode( array(
					'codemirror' => array(
						'mode' => 'text/css',
					),
				) ),
			),
		) );

		/* ================================
		 * Fields for post types.
		 * ================================ */

		$cmb->add_field( array(
			'name' => ' Proof Galleries Custom Post Type',
			'desc' => '',
			'id'   => $this->prefix( 'galleries_title' ),
			'type' => 'title',
		) );

		$cmb->add_field( array(
			'name' => esc_html__( 'Enable Pixproof Galleries', 'pixproof' ),
			'desc' => esc_html__( 'Enable this to register a new custom post type so you can manage Proof Galleries.', 'pixproof' ),
			'id'   => 'enable_pixproof_gallery',
			'type' => 'checkbox',
			'default' => 'on',
		) );

		$cmb->add_field( array(
			'name' => esc_html__( 'Single Item Label', 'pixproof' ),
			'desc' => esc_html__( 'Here you can change the singular label.', 'pixproof' ),
			'id'   => $this->prefix( 'single_item_label' ),
			'type' => 'text',
			'default' => esc_html_x( 'Proof Gallery', 'Post Type Single Item Name', 'pixproof' ),
			'attributes' => array(
				'required'               => true, // Will be required only if visible.
				'data-conditional-id'    => 'enable_pixproof_gallery',
				'data-conditional-value' => 'on',
			),
		) );

		$cmb->add_field( array(
			'name' => esc_html__( 'Multiple Items Label', 'pixproof' ),
			'desc' => esc_html__( 'Here you can change the plural label.', 'pixproof' ),
			'id'   => $this->prefix( 'multiple_items_label' ),
			'type' => 'text',
			'default' => esc_html__( 'Proof Galleries', 'pixproof' ),
			'attributes' => array(
				'required'               => true, // Will be required only if visible.
				'data-conditional-id'    => 'enable_pixproof_gallery',
				'data-conditional-value' => 'on',
			),
		) );

		$cmb->add_field( array(
			'name' => esc_html__( 'Change Single Slug', 'pixproof' ),
			'desc' => esc_html__( 'Do you want to rewrite the single gallery item slug?', 'pixproof' ),
			'id'   => $this->prefix( 'change_single_item_slug' ),
			'type' => 'checkbox',
			'default' => 'off',
			'attributes' => array(
				'data-conditional-id'    => 'enable_pixproof_gallery',
				'data-conditional-value' => 'on',
			),
		) );

		$cmb->add_field( array(
			'name' => esc_html__( 'New Single Item Slug', 'pixproof' ),
			'desc' => esc_html__( 'Change the single gallery slug as you need it.', 'pixproof' ),
			'id'   => $this->prefix( 'gallery_new_single_item_slug' ),
			'type' => 'text',
			'default' => 'pixproof_gallery',
			'attributes' => array(
				'required'               => true, // Will be required only if visible.
				'data-conditional-id'    => $this->prefix( 'change_single_item_slug' ),
				'data-conditional-value' => 'on',
			),
		) );

	}

	public function on_save_settings() {
		// Since the user can control stuff like slugs, we need to be proactive.
		flush_rewrite_rules();
	}

	public function is_settings_page() {
		$current_screen = get_current_screen();

		if ( ! empty( $current_screen ) && $current_screen instanceof WP_Screen ) {
			if ( in_array( $current_screen->id, array( 'settings_page_pixproof_settings', 'settings_page_pixproof_settings-network' ) ) ) {
				return true;
			}
		}

		return false;
	}

	public function register_admin_scripts() {
		// The styles.
		wp_register_style( $this->prefix( 'admin-style' ), plugins_url( 'assets/css/admin.css', Pixproof_Plugin()->get_file() ), array(), Pixproof_Plugin()->get_version() );

		wp_register_script( $this->prefix( 'settings-js' ), plugins_url( 'assets/js/settings-page.js', Pixproof_Plugin()->get_file() ),
			array(
				'jquery',
				$this->prefix( 'cmb2-conditionals' ),
				'wp-api',
			), Pixproof_Plugin()->get_version() );

		wp_localize_script( pixproof_prefix( 'settings-js' ), 'locals', array(
			'ajax_url' => admin_url( 'admin-ajax.php' )
		) );
	}

	public function enqueue_admin_scripts() {
		if ( ! $this->is_settings_page() ) {
			return;
		}

		// The styles.
		wp_enqueue_style( $this->prefix( 'admin-style' ) );

		// The scripts.
		wp_enqueue_script( $this->prefix( 'settings-js' ) );
	}

	/**
	 * Retrieves an option, even before the CMB2 has loaded.
	 *
	 * @since 2.0.0
	 *
	 * @param string $id The settings identifier, including prefixing if that is the case.
	 * @param mixed $default Optional. The default value in case the option wasn't saved.
	 * @return mixed
	 */
	public function get_option( $id, $default = false ) {
		if ( Pixproof_Plugin()->is_plugin_network_activated() ) {
			$options = get_site_option( self::$key );
		} else {
			$options = get_option( self::$key );
		}
		if ( isset( $options[ $id ] ) ) {
			// We need to do a little cleaning for checkboxes - standardize the values.
			if ( in_array( $id, array( 'enable_archive_zip_download', 'disable_pixproof_style', 'enable_pixproof_gallery_global_style', 'enable_pixproof_gallery', $this->prefix( 'change_single_item_slug' ), ) ) ) {
				if ( in_array( $options[ $id ], array( 1, '1', 'true', true ) ) ) {
					$options[ $id ] = 'on';
				}
			}

			return $options[ $id ];
		}
		return $default;
	}

	/**
	 * Update an option, even before the CMB2 has loaded.
	 *
	 * @since 1.3.0
	 *
	 * @param string $id
	 * @param mixed $value
	 */
	public function update_option( $id, $value ) {
		if ( Pixproof_Plugin()->is_plugin_network_activated() ) {
			$options = get_site_option( self::$key );
			if ( empty( $options ) ) {
				$options = array();
			}
			$options[ $this->prefix( $id ) ] = $value;
			update_site_option( self::$key, $options );
		} else {
			$options = get_option( self::$key );
			if ( empty( $options ) ) {
				$options = array();
			}
			$options[ $this->prefix( $id ) ] = $value;
			update_option( self::$key, $options );
		}
	}

	/**
	 * Public getter method for retrieving protected/private properties.
	 *
	 * @throws Exception
	 * @param  string  $field Field to retrieve
	 * @return mixed          Field value or exception is thrown
	 */
	public function __get( $field ) {
		// Allowed fields to retrieve
		if ( in_array( $field, array( 'title', 'options_page' ), true ) ) {
			return $this->{$field};
		}

		throw new Exception( 'Invalid property: ' . $field );
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

	/**
	 * Remove any data we may have in the DB. This is intended to only be called on plugin uninstall.
	 */
	public static function cleanup() {

		if ( is_multisite() ) {
			delete_site_option( self::$key );

			$sites = get_sites( array( 'fields' => 'ids' ) );
			foreach ( $sites as $site_id ) {
				delete_blog_option( $site_id, self::$key );
			}
		} else {
			delete_option( self::$key );
		}
	}
}
