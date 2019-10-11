<?php
/**
 * Deprecated stuff.
 *
 * @package Pixproof
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'PixProofPlugin' ) ) {
	/**
	 * @deprecated Use Pixproof or Pixproof_Plugin class instead.
	 */
	class PixProofPlugin {
		static function get_attachment_class( $attachment ) {
			_deprecated_function( __METHOD__, '2.0.0', 'Use Pixproof::get_attachment_class() instead.' );
			return Pixproof::get_attachment_class( $attachment );
		}

		static function attachment_class( $attachment ) {
			_deprecated_function( __METHOD__, '2.0.0', 'Use Pixproof::attachment_class() instead.' );
			echo Pixproof::attachment_class( $attachment );
		}

		static function attachment_data( $attachment ) {
			_deprecated_function( __METHOD__, '2.0.0', 'Use Pixproof::attachment_data() instead.' );
			echo Pixproof::attachment_data( $attachment );
		}

		static function set_number_of_images( $number_of_images ) {
			_deprecated_function( __METHOD__, '2.0.0', 'Use Pixproof::set_number_of_images() instead.' );
			return Pixproof::set_number_of_images( $number_of_images );
		}

		static function get_number_of_images() {
			_deprecated_function( __METHOD__, '2.0.0', 'Use Pixproof::get_number_of_images() instead.' );
			return Pixproof::get_number_of_images();
		}

		static function get_thumbnails_size() {
			_deprecated_function( __METHOD__, '2.0.0', 'Use Pixproof::get_thumbnails_size() instead.' );
			return Pixproof::get_thumbnails_size();
		}

		static function get_gallery_grid_size() {
			_deprecated_function( __METHOD__, '2.0.0', 'Use Pixproof::get_gallery_grid_size() instead.' );
			return Pixproof::get_gallery_grid_size();
		}

		static function has_global_style() {
			_deprecated_function( __METHOD__, '2.0.0', 'Use Pixproof::has_global_style() instead.' );
			return Pixproof::has_global_style();
		}
	}

}

