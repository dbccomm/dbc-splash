<?php
/**
 * Plugin Name: DBC Splash Screen
 * Plugin URI: https://lecourrier.qc.ca
 * Description: Show user a splash screen on page load then set a cookie so it is loaded only once.
 * Version: 1.0.1
 * Author: Bruno Laferrière
 * Author URI: https://lecourrier.qc.ca
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Requires at least: 6.3
 * Requires PHP: 8.0
 * Domain Path: /languages
 * Text Domain: dbcSplash
 *
 * @package dbccomm\dbcSplash
 */

// Plugin namespace.
namespace dbccomm\dbcSplash;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Define plugin constants.
 */
const FILE         = __FILE__;
const PREFIX       = 'dbcsplash';
const VERSION      = '1.0.1';
const TESTED       = '6.3';
const REQUIRES     = '6.3';
const REQUIRES_PHP = '8.0';
const REPO         = 'dbccomm/dbc-splash';

/**
 * Boot the plugin by requiring the main bootstrap file.
 */
require_once dirname( FILE ) . '/core/boot.php';

/**
 * Create an instance of the boot class to initialize the plugin.
 *
 * @see \dbccomm\dbcSplash\core\boot::instance()
 */
\dbccomm\dbcSplash\core\boot::instance();
