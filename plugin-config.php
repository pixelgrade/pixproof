<?php defined('ABSPATH') or die;

$basepath = dirname(__FILE__).DIRECTORY_SEPARATOR;

$debug = false;
if ( isset( $_GET['debug'] ) && $_GET['debug'] == 'true' ) {
	$debug = true;
}
$debug = true;
$options = get_option('pixproof_settings');

$display_settings = false;

if ( isset( $options['display_settings'] ) ){
	$display_settings = $options['display_settings'];
}

return array
	(
		'plugin-name' => 'pixproof',

		'settings-key' => 'pixproof_settings',

		'textdomain' => 'pixproof_txtd',

		'template-paths' => array
			(
				$basepath.'core/views/form-partials/',
				$basepath.'views/form-partials/',
			),

		'fields' => array
			(
				'hiddens'
					=> include 'settings/hiddens'.EXT,
				'general'
					=> include 'settings/general'.EXT,
				'post_types'
					=> include 'settings/post_types'.EXT,
//				'taxonomies'
//					=> include 'settings/taxonomies'.EXT,
			),

		'processor' => array
			(
				// callback signature: (array $input, PixtypesProcessor $processor)

				'preupdate' => array
				(
					// callbacks to run before update process
					// cleanup and validation has been performed on data
				),
				'postupdate' => array
				(
					'save_settings'
				),
			),

		'cleanup' => array
			(
				'switch' => array('switch_not_available'),
			),

		'checks' => array
			(
				'counter' => array('is_numeric', 'not_empty'),
			),

		'errors' => array
			(
				'not_empty' => __('Invalid Value.', pixproof::textdomain()),
			),

		'callbacks' => array
			(
				'save_settings' => 'save_proof_settings'
			),

		// shows exception traces on error
		'debug' => $debug,

	); # config
