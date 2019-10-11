<?php
/**
 * Extras File
 *
 * Contains extra (helper) functions.
 *
 * @package Pixproof
 */

/**
 * Helper function to prefix options, metas IDs.
 *
 * @param string $option The option ID to prefix.
 * @param string $separator Optional. The separator to use between prefix and the rest.
 * @return string
 */
function pixproof_prefix( $option, $separator = '_' ) {
	return Pixproof_Metaboxes::getInstance()->prefix( $option, $separator );
}

/**
 * Helper function to get/return the Pixproof_Settings object
 *
 * @return Pixproof_Settings
 */
function pixproof_settings() {
	return Pixproof_Settings::getInstance();
}

/**
 * Wrapper function around cmb2_get_option.
 *
 * @since 1.0.0
 *
 * @param  string  $setting Option key without any prefixing.
 * @param mixed $default Optional. The default value to retrieve in case the option was not found.
 * @return mixed        Option value
 */
function pixproof_get_setting( $setting, $default = false ) {
	return Pixproof_Plugin()->settings->get_option( $setting, $default );
}

function pixproof_array_sort( $array, $on, $order = SORT_ASC ) {
	$new_array = array();
    $sortable_array = array();

    if ( count( $array ) > 0 ) {
	    foreach ( $array as $k => $v ) {
		    if ( is_array( $v ) ) {
			    foreach ( $v as $k2 => $v2 ) {
				    if ( $k2 == $on ) {
					    $sortable_array[ $k ] = $v2;
				    }
			    }
		    } else {
			    $sortable_array[ $k ] = $v;
		    }
	    }

	    switch ( $order ) {
		    case SORT_ASC:
			    asort( $sortable_array );
			    break;
		    case SORT_DESC:
			    arsort( $sortable_array );
			    break;
	    }

	    foreach ( $sortable_array as $k => $v ) {
		    $new_array[ $k ] = $array[ $k ];
	    }
    }

    return $new_array;
}

function pixproof_get_string_between( $string, $start, $end = null ){
	$string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);

    // If we couldn't find the end marker or it is null we will return everything 'till the end
    if ( null === $end || false === strpos($string, $end, $ini) ) {
		return substr($string, $ini);
    } else {
	    $len = strpos( $string, $end, $ini ) - $ini;

	    return substr( $string, $ini, $len );
    }
}

/**
 * Get the complete current URL including query args
 * @return string
 */
function pixproof_get_current_url() {
	//@todo we should do this is more standard WordPress way
	$url  = @( $_SERVER["HTTPS"] != 'on' ) ? 'http://'.$_SERVER["SERVER_NAME"] :  'https://'.$_SERVER["SERVER_NAME"];
	$url .= $_SERVER["REQUEST_URI"];
	return $url;
}

function pixproof_to_bool( $value ) {
	if ( empty( $value ) ) {
		return false;
	}

	//see this for more info: http://stackoverflow.com/questions/7336861/how-to-convert-string-to-boolean-php
	return filter_var( $value, FILTER_VALIDATE_BOOLEAN );
}

/**
 * Return a boolean value for the current state of a checkbox (it usually has yes or no value)
 *
 * @param $post_id    int
 * @param $meta_key   string
 *
 * @return boolean
 */
function pixproof_meta_to_bool( $post_id, $meta_key ) {

	$result = get_post_meta( $post_id, $meta_key, true );

	return pixproof_to_bool( $result );
}

/**
 * Check if the $haystack contains any of the needles.
 *
 * @param string $haystack
 * @param array $needles
 *
 * @return bool
 */
function pixproof_string_contains_any( $haystack, $needles ) {
	foreach ( $needles as $needle ) {
		if ( false !== strpos( $haystack, $needle ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Wrapper for _doing_it_wrong.
 *
 * Taken from WooCommerce - see wc_doing_it_wrong().
 *
 * @since  1.0.0
 * @param string $function Function used.
 * @param string $message Message to log.
 * @param string $version Version the message was added in.
 */
function pixproof_doing_it_wrong( $function, $message, $version ) {
	// @codingStandardsIgnoreStart
	$message .= ' Backtrace: ' . wp_debug_backtrace_summary();

	if ( is_ajax() ) {
		do_action( 'doing_it_wrong_run', $function, $message, $version );
		error_log( "{$function} was called incorrectly. {$message}. This message was added in version {$version}." );
	} else {
		_doing_it_wrong( $function, $message, $version );
	}
	// @codingStandardsIgnoreEnd
}

/**
 * Checks whether an array is associative or not
 *
 * @param array $array
 *
 * @return bool
 */
function pixproof_is_assoc( $array ) {

	if ( ! is_array( $array ) ) {
		return false;
	}

	// Keys of the array
	$keys = array_keys( $array );

	// If the array keys of the keys match the keys, then the array must
	// not be associative (e.g. the keys array looked like {0:0, 1:1...}).
	return array_keys( $keys ) !== $keys;
}

/**
 * Attempt to split a string by whitespaces and return the parts as an array.
 * If not a string or no whitespaces present, just returns the value.
 *
 * @param mixed $value
 *
 * @return array|false|string[]
 */
function pixproof_maybe_split_by_whitespace( $value ) {
	if ( ! is_string( $value ) ) {
		return $value;
	}

	return preg_split( '#[\s][\s]*#', $value, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE );
}

/**
 * Given a string, treat it as a (comma separated by default) list and return the array with the items
 *
 * @param mixed $str
 * @param string $delimiter Optional. The delimiter to user.
 *
 * @return array
 */
function pixproof_maybe_explode_list( $str, $delimiter = ',' ) {
	// If by any chance we are given an array, just return it
	if ( is_array( $str ) ) {
		return $str;
	}

	// Anything else we coerce to a string
	if ( ! is_string( $str ) ) {
		$str = (string) $str;
	}

	// Make sure we trim it
	$str = trim( $str );

	// Bail on empty string
	if ( empty( $str ) ) {
		return array();
	}

	// Return the whole string as an element if the delimiter is missing
	if ( false === strpos( $str, $delimiter ) ) {
		return array( $str );
	}

	// Explode it and return it
	return explode( $delimiter, str_replace( ' ', '', $str ) );
}

/**
 * Retrive the class attribute given some classes.
 *
 * @param string|array $class Optional. One or more classes to add to the class list.
 * @param string|array $location Optional. The place (template) where the classes are displayed. This is a hint for filters.
 * @param string       $prefix Optional. Prefix to prepend to all of the provided classes
 * @param string       $suffix Optional. Suffix to append to all of the provided classes
 * @return string
 */
function pixproof_css_class( $class = '', $location = '', $prefix = '', $suffix = '' ) {
	// Separates classes with a single space, collates classes for element
	return 'class="' . esc_attr( join( ' ', pixproof_get_css_class( $class, $location ) ) ) . '"';
}

/**
 * Retrieve the classes for a element as an array.
 *
 * @param string|array $class Optional. One or more classes to add to the class list.
 * @param string|array $location Optional. The place (template) where the classes are displayed. This is a hint for filters.
 * @param string       $prefix Optional. Prefix to prepend to all of the provided classes
 * @param string       $suffix Optional. Suffix to append to all of the provided classes
 *
 * @return array Array of classes.
 */
function pixproof_get_css_class( $class = '', $location = '', $prefix = '', $suffix = '' ) {
	$classes = array();

	if ( ! empty( $class ) ) {
		$class = pixproof_maybe_split_by_whitespace( $class );

		// If we have a prefix then we need to add it to every class
		if ( ! empty( $prefix ) && is_string( $prefix ) ) {
			foreach ( $class as $key => $value ) {
				$class[ $key ] = $prefix . $value;
			}
		}

		// If we have a suffix then we need to add it to every class
		if ( ! empty( $suffix ) && is_string( $suffix ) ) {
			foreach ( $class as $key => $value ) {
				$class[ $key ] = $value . $suffix;
			}
		}

		$classes = array_merge( $classes, $class );
	} else {
		// Ensure that we always coerce class to being an array.
		$class = array();
	}

	$classes = array_map( 'esc_attr', $classes );

	return array_unique( $classes );
} // function

function pixproof_comments_match_callback( $matches ) {
	$the_id = substr( trim( $matches[ 0 ] ), 1 );

	$matches[ 0 ] = '<span class="pixproof_photo_ref" data-href="#item-' . $the_id . '">#' . $the_id . '</span>';

	return $matches[ 0 ];

}
