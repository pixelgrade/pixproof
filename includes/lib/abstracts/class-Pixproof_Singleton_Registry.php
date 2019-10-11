<?php
/**
 * Document for abstract class Pixproof_Singleton_Registry.
 *
 * @package Pixproof
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This is the abstract class for singletons (a singleton registry).
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @since       1.0.0
 */
abstract class Pixproof_Singleton_Registry {

	/**
	 * The instances list.
	 *
	 * @var array
	 */
	public static $_instances = array();

	/**
	 * Get an instance of a class.
	 *
	 * @param string $arg
	 *
	 * @return mixed
	 */
	public static function getInstance( $arg = '' ) {

		// Get classname
		$class = get_called_class();

		// Grab the args in order to pass to the $class constructor
		$args = func_get_args();

		// Get the unique hash based on the classname and the passed args
		$key = $class::get_singleton_key( $class, $args );

		// If the $class instance does not exist...
		if ( ! array_key_exists( $key, self::$_instances ) ) {

			// If there are args to pass...
			if ( count( $args ) > 0 ) {

				// Create and store a new instance... also pass the args
				try {
					$reflect = new ReflectionClass( $class );
				} catch ( ReflectionException $exception ) {
					_doing_it_wrong( __METHOD__, esc_html__( 'Trying to get instance of nonexistent class.', 'pixproof' ), null );
				}

				// If the class has a constructor
				if ( method_exists( $class, '__construct' ) ) {

					// Create a new instance without invoking the constructor
					$instance = $reflect->newInstanceWithoutConstructor();

					// Get the constructor method
					$constructor = $reflect->getConstructor();

					// If the constructor is private, set it as public temporarily
					if ( $protected = ( $constructor->isPrivate() || $constructor->isProtected() ) ) {
						$constructor->setAccessible( true );
					}

					// Call the constructor
					$constructor->invokeArgs( $instance, $args );

					// Return original constructor visibility
					if ( $protected ) {
						$constructor->setAccessible( false );
					}

					// If the class has no set constructor
				} else {
					$instance = $reflect->newInstanceArgs( $args );
				}

				// Store the instance
				self::$_instances[ $key ] = $instance;

				// Otherwise create and store a new instance normally.
			} else {
				self::$_instances[ $key ] = new $class;
			}
		}

		// Return the stored singleton instance
		return self::$_instances[ $key ];
	}

	/**
	 * Get the singleton key of a certain class.
	 *
	 * @param $class
	 * @param $args
	 *
	 * @return mixed
	 */
	public static function get_singleton_key( $class, $args ) {

		// Only return the classname. This behavior can be modified in child classes
		return $class;
	}

	protected function __construct() {}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		pixproof_doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning is forbidden.', 'pixproof' ), '1.0.0' );
	} // End __clone ()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		pixproof_doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing instances of this class is forbidden.', 'pixproof' ), '1.0.0' );
	} // End __wakeup ()
}

// This one is for pre-PHP 5.3 PHP versions.
if ( ! function_exists( 'get_called_class' ) ) {

	/**
	 * Gets the name of the class the static method is called in.
	 *
	 * @see http://php.net/manual/ro/function.get-called-class.php
	 *
	 * @return mixed
	 */
	function get_called_class() {
		$bt    = debug_backtrace();
		$lines = file( $bt[1]['file'] );
		preg_match(
			'/([a-zA-Z0-9\_]+)::' . $bt[1]['function'] . '/',
			$lines[ $bt[1]['line'] - 1 ],
			$matches
		);
		return $matches[1];
	}
}
