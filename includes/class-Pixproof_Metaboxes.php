<?php
/**
 * Document for class Pixproof_Metaboxes.
 *
 * @package Pixproof
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Ensures the metaboxes logic is loaded and ready for use (CMB2 right now).
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @since       2.0.0
 */
class Pixproof_Metaboxes extends Pixproof_Singleton_Registry {

	/**
	 * Prefix for all of our metas or options.
	 * @var string
	 */
	protected $prefix = null;

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param string $prefix
	 */
	protected function __construct( $prefix = null ) {
		$this->prefix = $prefix;

		$this->load_cmb2();

		$this->add_hooks();
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since 2.0.0
	 */
	public function add_hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ), 9, 1 );
	}

	/**
	 * Fire up CMB2.
	 *
	 * @access  public
	 * @since   2.0.0
	 * @return  void
	 */
	public function load_cmb2() {

		// Load everything about CMB2 - if that is the case.
		if ( file_exists( dirname( __FILE__ ) . '/vendor/cmb2/init.php' ) ) {
			require_once dirname( __FILE__ ) . '/vendor/cmb2/init.php';
		}

		//The CMB2 conditional display of fields
		if ( ! defined( 'CMB2_CONDITIONALS_PRIORITY' ) && file_exists( dirname( __FILE__ ) . '/vendor/cmb2-conditionals/cmb2-conditionals.php' ) ) {
			require_once dirname( __FILE__ ) . '/vendor/cmb2-conditionals/cmb2-conditionals.php';
		}

		require_once dirname( __FILE__ ) . '/vendor/cmb2-pixproof_gallery-field/cmb2-pixproof_gallery-field.php';
	}

	public function register_admin_scripts() {
		wp_register_script( pixproof_prefix( 'cmb2-conditionals' ), plugins_url( 'assets/js/cmb2-conditionals.js', Pixproof_Plugin::getInstance()->get_file() ), array('jquery'), '1.0.4');
	}

	/**
	 * Adds a prefix to an option name.
	 *
	 * @param string $option
	 * @param string $separator Optional. Defaults to '_'.
	 *
	 * @return string
	 */
	public function prefix( $option, $separator = '_' ) {
		return $this->prefix . $separator . $option;
	}
}
