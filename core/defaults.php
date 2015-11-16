<?php return array
	(
		'cleanup' => array
			(
				'switch' => array('switch_not_available'),
			),

		'checks' => array
			(
				'counter' => array('is_numeric', 'not_empty'),
			),

		'processor' => array
			(
				// callback signature: (array $input, PixproofProcessor $processor)

				'preupdate' => array
					(
						// callbacks to run before update process
						// cleanup and validation has been performed on data
					),
				'postupdate' => array
					(
						// callbacks to run post update
					),
			),

		'errors' => array
			(
				'is_numeric' => esc_html__('Numberic value required.', 'pixproof'),
				'not_empty' => esc_html__('Field is required.', 'pixproof'),
			),

		'callbacks' => array
			(
			// cleanup callbacks
				'switch_not_available' => 'pixproof_cleanup_switch_not_available',

			// validation callbacks
				'is_numeric' => 'pixproof_validate_is_numeric',
				'not_empty' => 'pixproof_validate_not_empty'
			)

	); # config
