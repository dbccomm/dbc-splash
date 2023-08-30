<?php
/**
 * Bootstrapping functionality for DBC Splashplugin.
 *
 * Handles the initialization and setup of the plugin.
 *
 * @package dbccomm\dbcSplash\core
 */

namespace dbccomm\dbcSplash\core;

// Block direct calls for security.
if ( ! function_exists( 'add_action' ) ) {
	die( esc_html_x( 'Direct access is not allowed.', 'security message', 'dbcsplash' ) );
}

/**
 * Boot class.
 *
 * Handles the bootstrapping of the plugin, including activation, deactivation,
 * text domain loading, and class includes.
 *
 * @final
 */
final class Boot {

	/**
	 * @var Boot $instance The single instance of the class.
	 */
	private static $instance;

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @return Boot A single instance of this class.
	 */
	final public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Boot constructor.
	 *
	 * Initializes the plugin by registering hooks and including required files.
	 *
	 * @final
	 */
	final private function __construct() {

		$plugin_dir = dirname( \dbccomm\dbcSplash\FILE );

		// Register Activation and Deactivation methods.
		register_activation_hook( \dbccomm\dbcSplash\FILE, [ $this, 'activate' ] );
		register_deactivation_hook( \dbccomm\dbcSplash\FILE, [ $this, 'deactivate' ] );

		// Load Translation.
		add_action( 'init', [ $this, 'load_textdomain' ] );

		// Include Plugin Updater and classes.
		require_once $plugin_dir . '/plugin-updater.php';
		new \dbccomm\dbcSplash\DBC_Plugin_Updater(
			\dbccomm\dbcSplash\REPO,
			array(
				'tested'       => \dbccomm\dbcSplash\TESTED,
				'requires'     => \dbccomm\dbcSplash\REQUIRES,
				'requires_php' => \dbccomm\dbcSplash\REQUIRES_PHP,
			)
		);

		// Conditionally load admin or frontend classes.
		if ( is_admin() ) {

        require_once $plugin_dir . '/core/dbc-class-register-menu.php';
				require_once $plugin_dir . '/core/dbc-class-menu-splash.php';


		} else {
			  // Include any frontend-specific files here.
 			  require_once $plugin_dir . '/core/dbc-class-footer-splash.php';

		}
	}

	/**
	 * Activation method.
	 *
	 * Handles tasks to be run when the plugin is activated.
	 *
	 * @final
	 */
	final public function activate() {
		// Activation logic.
	}

	/**
	 * Deactivation method.
	 *
	 * Handles tasks to be run when the plugin is deactivated.
	 *
	 * @final
	 */
	final public function deactivate() {
		// Deactivation logic.
	}

	/**
	 * Loads the text domain for translation.
	 *
	 * @final
	 */
	final public function load_textdomain() {
		load_plugin_textdomain( 'dbcSplash', false, basename( dirname( \dbccomm\dbcSplash\FILE ) ) . '/languages/' );
	}
}
