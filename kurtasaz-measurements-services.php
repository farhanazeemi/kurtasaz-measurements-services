<?php
/**
 * Plugin Name:       KurtaSaz Measurements & Services
 * Plugin URI:        https://github.com/wpacademy/wordpress-dev-skills
 * Description:       Premium custom tailoring measurements and services form featuring a multi-step design, validation, and an interactive SVG Body Diagram.
 * Version:           1.0.0
 * Author:            KurtaSaz
 * Author URI:        https://msrbuilds.com
 * License:           GPL-2.0-or-later
 * Text Domain:       kurtasaz-measurements-services
 * Domain Path:       /languages
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define core constants.
define( 'KSMS_VERSION', '1.0.0' );
define( 'KSMS_PATH', plugin_dir_path( __FILE__ ) );
define( 'KSMS_URL', plugin_dir_url( __FILE__ ) );

/**
 * Autoload classes or manually require modular components.
 */
require_once KSMS_PATH . 'includes/class-ksms-cpt.php';
require_once KSMS_PATH . 'admin/class-ksms-admin.php';
require_once KSMS_PATH . 'public/class-ksms-public.php';

/**
 * The main plugin class.
 */
class KurtaSaz_Measurements_Services {

	/**
	 * Instance of this class.
	 *
	 * @var KurtaSaz_Measurements_Services
	 */
	private static $instance = null;

	/**
	 * Get class instance.
	 *
	 * @return KurtaSaz_Measurements_Services
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		// Initialize the Custom Post Type.
		if ( class_exists( 'KSMS_CPT' ) ) {
			KSMS_CPT::get_instance();
		}

		// Initialize the Admin component.
		if ( class_exists( 'KSMS_Admin' ) && is_admin() ) {
			KSMS_Admin::get_instance();
		}

		// Initialize the Public/Frontend component.
		if ( class_exists( 'KSMS_Public' ) ) {
			KSMS_Public::get_instance();
		}
	}
}

/**
 * Run the plugin.
 */
function ksms_run_plugin() {
	return KurtaSaz_Measurements_Services::get_instance();
}
add_action( 'plugins_loaded', 'ksms_run_plugin' );
