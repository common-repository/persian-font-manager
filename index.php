<?php
/*
Plugin Name: Persian Font Manager
Plugin URI: https://wpfm.ir
Description: Change wordpress dashboard and theme font family
Author: DJ
Version: 1.0.
Author URI: https://codeinwp.ir
Text Domain: wpfm-persian
Domain Path: /languages/
*/
namespace WPFM_Persian;

class WPFMBootstrap {

    private static $_instance;
    public static $path;
    public static $url;
	/**
	 * Bootstrap constructor.
	 */
	public function __construct() {
        self::$url = plugin_dir_url( __FILE__ );
        self::$path = plugin_dir_path( __FILE__ );

		include_once( self::$path . 'includes/main.php' );
		WPFMMain::instance();
	}


	/**
	 * Main Class Instance.
	 *
	 * Ensures only one instance of this class is loaded or can be loaded.
	 *
	 * @static
	 * @return Bootstrap - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}
WPFMBootstrap::instance();
?>
