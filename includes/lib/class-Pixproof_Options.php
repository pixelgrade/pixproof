<?php
/**
 * Document for class Pixproof_Options.
 *
 * @package Pixproof
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to manage values saved as options (in the wp_options DB table).
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @since       1.0.0
 */
class Pixproof_Options extends Pixproof_Singleton_Registry {

	/**
	 * The token used for prefixing.
	 *
	 * @var     string
	 * @access  private
	 * @since   1.0.0
	 */
	private $_token;

	protected function __construct( $prefixToken ) {
		$this->_token = $prefixToken;
	}

	/**
	 * Get the prefixed version input $name suitable for storing in WP options
	 * Idempotent: if $optionName is already prefixed, it is not prefixed again, it is returned without change
	 *
	 * @param  string $name option name to prefix.
	 * @return string
	 */
	public function prefix( $name ) {
		$optionNamePrefix = $this->token();
		if ( strpos( $name, $optionNamePrefix ) === 0 ) { // 0 but not false
			return $name; // already prefixed
		}

		return $optionNamePrefix . $name;
	}

	/**
	 * Get the token.
	 *
	 * @access  public
	 * @since   1.0.0
	 *
	 * @return  string
	 */
	public function token() {
		return $this->_token;
	}

	/**
	 * A wrapper function delegating to WP get_option() but it prefixes the input $optionName
	 * to enforce "scoping" the options in the WP options table thereby avoiding name conflicts
	 *
	 * @access  public
	 * @since   1.0.0
	 *
	 * @param string $optionName Option to get.
	 * @param string $default default value to return if the option is not set.
	 * @return string the value from delegated call to get_option(), or optional default value
	 * if option is not set.
	 */
	public function get_option( $optionName, $default = null ) {
		$prefixedOptionName = $this->prefix( $optionName );
		$retVal             = get_option( $prefixedOptionName );
		if ( ! $retVal && $default ) {
			$retVal = $default;
		}

		return $retVal;
	}

	/**
	 * A wrapper function delegating to WP add_option() but it prefixes the input $optionName
	 * to enforce "scoping" the options in the WP options table thereby avoiding name conflicts.
	 *
	 * @access  public
	 * @since   1.0.0
	 *
	 * @param  string $optionName Defined in settings.php and set as keys of $this->optionMetaData.
	 * @param  mixed $value the new value.
	 * @return null from delegated call to delete_option()
	 */
	public function add_option( $optionName, $value ) {
		$prefixedOptionName = $this->prefix( $optionName );

		return add_option( $prefixedOptionName, $value );
	}

	/**
	 * A wrapper function delegating to WP add_option() but it prefixes the input $optionName
	 * to enforce "scoping" the options in the WP options table thereby avoiding name conflicts
	 *
	 * @access  public
	 * @since   1.0.0
	 *
	 * @param string $optionName Name of option.
	 * @param mixed $value The new value.
	 * @return null from delegated call to delete_option()
	 */
	public function update_option( $optionName, $value ) {
		$prefixedOptionName = $this->prefix( $optionName );

		return update_option( $prefixedOptionName, $value );
	}
}
