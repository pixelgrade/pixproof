<?php
/**
 * @wordpress-plugin
 * Plugin Name: PixProof
 * Plugin URI:  https://pixelgrade.com
 * Description: WordPress photo gallery proofing plugin.
 * Version: 2.0.1
 * Author: Pixelgrade
 * Author URI: https://pixelgrade.com
 * Author Email: contact@pixelgrade.com
 * Text Domain: pixproof
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /lang
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once( plugin_dir_path( __FILE__ ) . 'includes/lib/abstracts/class-Pixproof_Singleton_Registry.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/lib/abstracts/class-Pixproof_Plugin_Init.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/lib/class-Pixproof_Array.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/lib/class-Pixproof_Create_Archive.php' );
require_once( plugin_dir_path( __FILE__ ) . 'extras.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/deprecated.php' );

/**
 * Returns the main instance of Pixproof_Plugin to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return Pixproof_Plugin Pixproof_Plugin instance.
 */
function Pixproof_Plugin() {
	require_once( plugin_dir_path( __FILE__ ) . 'includes/class-Pixproof_Plugin.php' );

	return Pixproof_Plugin::getInstance( __FILE__, '2.0.0' );
}

global $pixproof_plugin;
$pixproof_plugin = Pixproof_Plugin();
