<?php
/**
 * DBC_Register_Menu class.
 *
 * Version: 1.0.0
 * Author: Bruno Laferrière
 * Author Email: blaferriere@dbc.ca
 *
 * This class is responsible for registering admin menus in WordPress.
 */

// Subpackage namespace
namespace dbccomm\dbcSplash\core;


// Block direct calls
if ( ! function_exists( 'add_action' ) ) {
        die;
}

class DBC_Register_Menu {
    /**
     * Singleton instance.
     *
     * @var DBC_Register_Menu
     */
    private static $instance;

    /**
     * DBC_Register_Menu constructor.
     *
     * Initializes actions and filters.
     */
    private function __construct() {
        add_action( 'admin_menu', [ $this, 'register_menus' ] );
    }

    /**
     * Get the singleton instance of the class.
     *
     * @return DBC_Register_Menu The singleton instance of the class.
     */
    final public static function instance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Register admin menus.
     *
     * This method should contain the add_menu_page() calls.
     */
    public function register_menus() {

            add_menu_page(
                    __( 'DBC Splash', 'dbcSplash'),
                    __( 'DBC Splash', 'dbcSplash'),
                    'manage_options',
                    'dbc_splash',
                    [ 'dbccomm\dbcSplash\core\DBC_Menu_Splash', 'show' ],
                    'dashicons-welcome-view-site',
                    5
            );

    }
}

// Self init;
 \dbccomm\dbcSplash\core\DBC_Register_Menu::instance();
