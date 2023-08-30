<?php
/**
 * DBC_Footer_Splash class.
 *
 * Version: 1.0.0
 * Author: Bruno Laferrière
 * Author Email: blaferriere@dbc.ca
 *
 * This class is responsible for displaying the splash.
 */

// Subpackage namespace
namespace dbccomm\dbcSplash\core;


// Block direct calls
if ( ! function_exists( 'add_action' ) ) {
        die;
}

class DBC_Footer_Splash {


    private static $instance;

    /**
     * Singleton pattern - instance initialization
     * @return DBC_Footer_Splash
     */
    final public static function instance() {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * DBC_Footer_Splash constructor
     */
    final private function __construct() {
        add_action('wp_footer', [ $this, 'splash' ]);
//        add_action('init', [ $this, 'set_cookie' ]);
    }

    public function set_cookie() {

        $prefix = \dbccomm\dbcSplash\PREFIX;
        $options = get_option($prefix . '_options', array());
        $splash_name = $options['splash_name'] ?? '';
        $enable_splash = $options['enable_splash'] ?? '';

        if (!$splash_name || !$enable_splash) {
            return;
        }

        setcookie( $splash_name, 'true', time() + ( 86400 * 30 ), '/' );

    }


    /**
     * Convert HEX color to RGBA
     *
     * This function takes a HEX color value and a transparency percentage,
     * and returns an RGBA color value.
     *
     * @param string $hex HEX color value
     * @param int $transparency Transparency level (0-100)
     *
     * @return string RGBA color value
     */
    public static function hex_to_rgba($hex, $transparency) {
        // Remove "#" if it exists in the $hex value
        $hex = str_replace("#", "", $hex);

        // Convert HEX to RGB
        $r = intval(substr($hex, 0, 2), 16);
        $g = intval(substr($hex, 2, 2), 16);
        $b = intval(substr($hex, 4, 2), 16);

        // Convert the 0-100% transparency to a float value between 0 and 1
        $transparency = $transparency / 100;

        // Return the RGBA value
        return "rgba($r, $g, $b, $transparency)";
    }


    public function splash() {

        $prefix = \dbccomm\dbcSplash\PREFIX;

        $options = get_option($prefix . '_options', array());

        $enable_splash = $options['enable_splash'] ?? '';
        $splash_name = $options['splash_name'] ?? '';
        $overlay_bg_color = $options['overlay_bg_color'] ?? '#000000';
        $overlay_transparency = $options['overlay_transparency'] ?? '50';
        $modal_header_bg = $options['modal_header_bg'] ?? '#000000';
        $modal_header_fg = $options['modal_header_fg'] ?? '#ffffff';
        $modal_body_bg = $options['modal_body_bg'] ?? '#000000';
        $modal_body_fg = $options['modal_body_fg'] ?? '#000000';
        $custom_button_bg = $options['custom_button_bg'] ?? '#000000';
        $custom_button_fg = $options['custom_button_fg'] ?? '#ffffff';
        $close_button_bg = $options['close_button_bg'] ?? '#000000';
        $close_button_fg = $options['close_button_fg'] ?? '#ffffff';
        $close_button_label = $options['close_button_label'] ?? 'CLOSE';
        $modal_header_title = $options['modal_header_title'] ?? 'TITLE';
        $modal_body_text = $options['modal_body_text'] ?? 'TEXT';
        $custom_button_label = $options['custom_button_label'] ?? 'BUTTON';
        $custom_button_link = $options['custom_button_link'] ?? '#';

        if (!$splash_name || !$enable_splash) {
            return;
        }

    ?>
    <style>
      .dbc-splash-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: <?php echo self::hex_to_rgba($overlay_bg_color, $overlay_transparency); ?>;
        z-index: 9999;
        align-items: center;
        justify-content: center;
      }

      .dbc-splash-modal-card {
        background: <?php echo $modal_body_bg; ?>;
        color: <?php echo $modal_body_fg; ?>;
        border-radius: 15px;
        overflow: hidden;
        width: 700px;
      }

      .dbc-splash-modal-card-head {
        padding: 12px;
        background-color: <?php echo $modal_header_bg; ?>;
        color: <?php echo $modal_header_fg; ?>;
      }

      .dbc-splash-modal-card-foot {
        padding: 20px 15px;
        text-align: right;
      }

      .dbc-splash-modal-card-title {
        margin: 0;
        font-size: 1.5em;
      }

      .dbc-splash-modal-card-body {
        padding: 12px;
      }

      .dbc-splash-button {
        padding: 10px 20px;
        cursor: pointer;
        font-size: 1rem;
        text-align: center;
        border: none;
        border-radius: 4px;
        text-decoration: none;
      }

      .dbc-splash-is-success,
      .dbc-splash-is-success:visited,
      .dbc-splash-is-success:hover,
      .dbc-splash-is-success:active,
      .dbc-splash-is-success:focus {
        background-color: <?php echo $custom_button_bg; ?>;
        color: <?php echo $custom_button_fg; ?>;
      }

      .dbc-splash-is-close,
      .dbc-splash-is-close:visited,
      .dbc-splash-is-close:hover,
      .dbc-splash-is-close:active,
      .dbc-splash-is-close:focus  {
        background-color: <?php echo $close_button_bg; ?>;
        color: <?php echo $close_button_fg; ?>;
      }

      @media only screen and (max-width: 768px) {
        .dbc-splash-modal-card {
          margin-left: 20px;
          margin-right: 20px;
          width: calc(100% - 40px);
        }
      }

     </style>


     <div id="newsletter_modal" class="dbc-splash-modal">
       <div class="dbc-splash-modal-background"></div>
       <div class="dbc-splash-modal-card">
         <header class="dbc-splash-modal-card-head">
           <p class="dbc-splash-modal-card-title"><?php echo esc_html( $modal_header_title ); ?></p>
         </header>
         <section class="dbc-splash-modal-card-body">
           <?php echo wp_kses_post( $modal_body_text ); ?>
         </section>
         <footer class="dbc-splash-modal-card-foot">
           <a href="<?php echo esc_url( $custom_button_link ); ?>" class="dbc-splash-button dbc-splash-is-success"><?php echo esc_html( $custom_button_label ); ?></a>
           <a href="#" id="newsletter_modal_close" class="dbc-splash-button dbc-splash-is-close"><?php echo esc_html( $close_button_label ); ?></a>
         </footer>
       </div>
     </div>

     <script>

      function getCookie( name ) {
         let value = "; " + document.cookie;
         let parts = value.split( "; " + name + "=" );
         if ( parts.length === 2 ) return parts.pop().split(";").shift();
       }

       function setCookie(name, value, days) {
         let expires = "";
         if (days) {
           let date = new Date();
           date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
           expires = "; expires=" + date.toUTCString();
         }
         document.cookie = name + "=" + (value || "") + expires + "; path=/";
      }


       document.addEventListener("DOMContentLoaded", function() {

         const modal = document.getElementById("newsletter_modal");
         const closeModalBtn = document.getElementById("newsletter_modal_close");

         closeModalBtn.addEventListener("click", function(e) {
           e.preventDefault();
           modal.style.display = "none";
         });

         modal.addEventListener("click", function(e) {
           if (e.target === modal) {
             modal.style.display = "none";
           }
         });

         if (getCookie('<?php echo $splash_name; ?>') !== 'true') {
           document.querySelector('.dbc-splash-modal').style.display = 'flex';
           setCookie('<?php echo $splash_name; ?>', 'true', 3650);
         }

       });
     </script>




    <?php
    }

}


\dbccomm\dbcSplash\core\DBC_Footer_Splash::instance();
