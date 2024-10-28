<?php
/**
 * Avasize setup
 *
 * @package Avasize
 */

defined( 'ABSPATH' ) || exit;

/**
 * Main Avasize Class.
 *
 * @class Avasize
 */
final class Avasize {

	/**
	 * Avasize version.
	 *
	 * @var string
	 */
	public $version = '1.0.0';

	/**
	 * The single instance of the class.
	 *
	 * @var Avasize
	 */
	protected static $_instance = null;

	/**
	 * Main Avasize Instance.
	 *
	 * Ensures only one instance of Avasize is loaded or can be loaded.
	 *
	 * @static
	 * @see AV()
	 * @return Avasize - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 */
	public function __clone() {
		die( __FUNCTION__. ' ' . __( 'Cloning is forbidden.', 'avasize' ) );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 */
	public function __wakeup() {
		die(  __FUNCTION__. ' ' . __( 'Unserializing instances of this class is forbidden.', 'avasize' ) );
	}

	/**
	 * Avasize Constructor.
	 */
	public function __construct() {
		$this->define_constants();
		$this->includes();
	}


	/**
	 * Define AV Constants.
	 */
	private function define_constants() {
		$this->define( 'AVSZ_ABSPATH', dirname( AVSZ_PLUGIN_FILE ) . '/' );
		$this->define( 'AVSZ_PLUGIN_BASENAME', plugin_basename( AVSZ_PLUGIN_FILE ) );
		$this->define( 'AVSZ_VERSION', $this->version );
	}

	/**
	 * Define constant if not already set.
	 *
	 * @param string      $name  Constant name.
	 * @param string|bool $value Constant value.
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes() {
		/**
		 * Widgets
		 */
		include_once AVSZ_ABSPATH . 'includes/av-widget-functions.php';
		include_once AVSZ_ABSPATH . 'includes/av-action-functions.php';
	}
}
