<?php defined('ABSPATH') or die;

	function pixproof_validate_not_empty($fieldvalue, $processor) {
		return ! empty($fieldvalue);
	}
