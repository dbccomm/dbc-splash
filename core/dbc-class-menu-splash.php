<?php
/**
 * DBC_Menu_Splash class.
 *
 * Version: 1.0.0
 * Author: Bruno Laferrière
 * Author Email: blaferriere@dbc.ca
 *
 * This class is responsible handling the settings.
 */

// Subpackage namespace
namespace dbccomm\dbcSplash\core;


// Block direct calls
if ( ! function_exists( 'add_action' ) ) {
        die;
}

class DBC_Menu_Splash {


    private static $instance;

    /**
     * Singleton pattern - instance initialization
     * @return DBC_Menu_Splash
     */
    final public static function instance() {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * DBC_Menu_Splash constructor
     */
    final private function __construct() {
        add_action('admin_init', [$this, 'register_settings']);
    }


    /**
     * Registers settings for the plugin
     */
     function register_settings() {
         $prefix = \dbccomm\dbcSplash\PREFIX;
         register_setting($prefix . '_options_group', $prefix . '_options');

         // Check if settings were saved
         if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST[$prefix . '_options'])) {
             set_transient('settings_updated', true, 5); // Set transient for 5 seconds
         }
     }


     public static function show() {
    // Register and enqueue necessary scripts
    wp_enqueue_script('wp-color-picker');
    wp_enqueue_style('wp-color-picker');

    $prefix = \dbccomm\dbcSplash\PREFIX;


    // Retrieve options
    $options = get_option($prefix . '_options', array());

    ?>
    <div class="wrap">
        <h2><?php echo _x('DBC Splash Settings', 'page title', 'dbcSplash'); ?></h2>
        <form method="post" action="options.php">
            <?php
            settings_fields($prefix . '_options_group');
            do_settings_sections($prefix . '_options_group');
            ?>

            <table class="form-table">
                <!-- Enable Splash Screen -->
                <tr>
                    <th scope="row"><?php echo _x('Enable Splash Screen', 'setting', 'dbcSplash'); ?></th>
                    <td>
                        <input type="checkbox" name="<?php echo $prefix; ?>_options[enable_splash]" value="1" <?php checked(1, $options['enable_splash'] ?? ''); ?> />
                    </td>
                </tr>

                <!-- Other fields here -->
                <!-- Please replace the placeholders in $options['your_field_here'] with your actual option keys -->

                <!-- Splash Name -->
                <tr>
                    <th scope="row"><?php echo _x('Splash Name', 'setting', 'dbcSplash'); ?></th>
                    <td>
                        <input type="text" name="<?php echo $prefix; ?>_options[splash_name]" value="<?php echo esc_attr($options['splash_name'] ?? ''); ?>" pattern="^[a-zA-Z0-9_-]+$"/>
                    </td>
                </tr>

                <!-- Overlay Background Color -->
                <tr>
                    <th scope="row"><?php echo _x('Overlay Background Color', 'setting', 'dbcSplash'); ?></th>
                    <td>
                        <input type="text" name="<?php echo $prefix; ?>_options[overlay_bg_color]" class="color-picker" value="<?php echo esc_attr($options['overlay_bg_color'] ?? ''); ?>"/>
                    </td>
                </tr>
                <tr>
                <th scope="row"><?php echo _x('Overlay Background Transparency', 'setting', 'dbcSplash'); ?></th>
                    <td>
                         <input type="number" name="<?php echo $prefix; ?>_options[overlay_transparency]" min="0" max="100" value="<?php echo esc_attr($options['overlay_transparency'] ?? ''); ?>"/>
                    </td>
                </tr>

                <!-- Modal Header Background Color -->
                <tr>
                    <th scope="row"><?php echo _x('Modal Header Background Color', 'setting', 'dbcSplash'); ?></th>
                    <td>
                        <input type="text" name="<?php echo $prefix; ?>_options[modal_header_bg]" class="color-picker" value="<?php echo esc_attr($options['modal_header_bg'] ?? ''); ?>"/>
                    </td>
                </tr>

                <!-- Modal Header Foreground Color -->
                <tr>
                    <th scope="row"><?php echo _x('Modal Header Foreground Color', 'setting', 'dbcSplash'); ?></th>
                    <td>
                        <input type="text" name="<?php echo $prefix; ?>_options[modal_header_fg]" class="color-picker" value="<?php echo esc_attr($options['modal_header_fg'] ?? ''); ?>"/>
                    </td>
                </tr>

                <!-- Modal Header Title -->
                <tr>
                    <th scope="row"><?php echo _x('Modal Header Title', 'setting', 'dbcSplash'); ?></th>
                    <td>
                        <input type="text" name="<?php echo $prefix; ?>_options[modal_header_title]" value="<?php echo esc_attr($options['modal_header_title'] ?? ''); ?>"/>
                    </td>
                </tr>

                <!-- Modal Body Background Color -->
                <tr>
                    <th scope="row"><?php echo _x('Modal Body Background Color', 'setting', 'dbcSplash'); ?></th>
                    <td>
                        <input type="text" name="<?php echo $prefix; ?>_options[modal_body_bg]" class="color-picker" value="<?php echo esc_attr($options['modal_body_bg'] ?? ''); ?>"/>
                    </td>
                </tr>

                <!-- Modal Body Foreground Color -->
                <tr>
                    <th scope="row"><?php echo _x('Modal Body Foreground Color', 'setting', 'dbcSplash'); ?></th>
                    <td>
                        <input type="text" name="<?php echo $prefix; ?>_options[modal_body_fg]" class="color-picker" value="<?php echo esc_attr($options['modal_body_fg'] ?? ''); ?>"/>
                    </td>
                </tr>

                <!-- Modal Body Text -->
                <tr>
                    <th scope="row"><?php echo _x('Modal Body Text', 'setting', 'dbcSplash'); ?></th>
                    <td>
                        <textarea name="<?php echo $prefix; ?>_options[modal_body_text]" rows="4" cols="50"><?php echo esc_textarea($options['modal_body_text'] ?? ''); ?></textarea>
                    </td>
                </tr>

                <!-- Custom Button Label -->
                <tr>
                    <th scope="row"><?php echo _x('Custom Button Label', 'setting', 'dbcSplash'); ?></th>
                    <td>
                        <input type="text" name="<?php echo $prefix; ?>_options[custom_button_label]" value="<?php echo esc_attr($options['custom_button_label'] ?? ''); ?>"/>
                    </td>
                </tr>

                <!-- Custom Button Link -->
                <tr>
                    <th scope="row"><?php echo _x('Custom Button Link', 'setting', 'dbcSplash'); ?></th>
                    <td>
                        <input type="text" name="<?php echo $prefix; ?>_options[custom_button_link]" value="<?php echo esc_attr($options['custom_button_link'] ?? ''); ?>"/>
                    </td>
                </tr>

                <!-- Custom Button Background Color -->
                <tr>
                    <th scope="row"><?php echo _x('Custom Button Background Color', 'setting', 'dbcSplash'); ?></th>
                    <td>
                        <input type="text" name="<?php echo $prefix; ?>_options[custom_button_bg]" class="color-picker" value="<?php echo esc_attr($options['custom_button_bg'] ?? ''); ?>"/>
                    </td>
                </tr>

                <!-- Custom Button Foreground Color -->
                <tr>
                    <th scope="row"><?php echo _x('Custom Button Foreground Color', 'setting', 'dbcSplash'); ?></th>
                    <td>
                        <input type="text" name="<?php echo $prefix; ?>_options[custom_button_fg]" class="color-picker" value="<?php echo esc_attr($options['custom_button_fg'] ?? ''); ?>"/>
                    </td>
                </tr>

                <!-- Close Button Label -->
                <tr>
                    <th scope="row"><?php echo _x('Close Button Label', 'setting', 'dbcSplash'); ?></th>
                    <td>
                        <input type="text" name="<?php echo $prefix; ?>_options[close_button_label]" value="<?php echo esc_attr($options['close_button_label'] ?? ''); ?>"/>
                    </td>
                </tr>


                <!-- Close Button Background Color -->
                <tr>
                    <th scope="row"><?php echo _x('Close Button Background Color', 'setting', 'dbcSplash'); ?></th>
                    <td>
                        <input type="text" name="<?php echo $prefix; ?>_options[close_button_bg]" class="color-picker" value="<?php echo esc_attr($options['close_button_bg'] ?? ''); ?>"/>
                    </td>
                </tr>

                <!-- Close Button Foreground Color -->
                <tr>
                    <th scope="row"><?php echo _x('Close Button Foreground Color', 'setting', 'dbcSplash'); ?></th>
                    <td>
                        <input type="text" name="<?php echo $prefix; ?>_options[close_button_fg]" class="color-picker" value="<?php echo esc_attr($options['close_button_fg'] ?? ''); ?>"/>
                    </td>
                </tr>


                <!-- And so on for each of your fields -->
                <!-- For example: Title, Text, Custom Button Label, Custom Button Link, Colors etc. -->
            </table>

            <?php submit_button(_x('Save Changes', 'button', 'dbcSplash')); ?>
        </form>
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function($) {
            // Initialize color picker
            $('.color-picker').wpColorPicker();
        });
    </script>
    <style>
        .color-picker {
            max-width: 100px;
        }
    </style>
    <?php
}



}


\dbccomm\dbcSplash\core\DBC_Menu_Splash::instance();
