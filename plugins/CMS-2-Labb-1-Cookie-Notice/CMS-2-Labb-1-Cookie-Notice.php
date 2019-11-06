<?php

/*
Plugin Name: CMS 2 Labb 1 Cookie Notice
Description: add Cookie notification for visitors with setting
Author: Jan Gorges
Version: 1.0
*/
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

include( plugin_dir_path( __FILE__ ) . 'admin/cookies-notice-admin-class.php');
function cookie_load_plugin_css() {
    $plugin_url = plugin_dir_url( __FILE__ );

    wp_enqueue_style( 'cookies-plugin-css', $plugin_url . 'css/style.css' );
}
add_action( 'wp_enqueue_scripts', 'cookie_load_plugin_css' );
add_action( 'init', 'setcookie_agree');
add_action( 'wp_footer', 'display_cookie_bar' );

function setcookie_agree() {

  $options = get_option( 'cookie_option_name' );
  $getDays = $options['cookie_save_days'];
  $cookie_name = 'accept_cookies';
  $cookie_value = true;

    if (isset($_GET['accept_cookies'])) {

      setcookie($cookie_name, $cookie_value, time() + (86400 * $getDays));
      $_COOKIE[$cookie_name] = $cookie_value;
      // echo  $_COOKIE[$cookie_name];
      header('Location: ./');
    }


}


function display_cookie_bar() {
  $options = get_option( 'cookie_option_name' );
  $getOptions = $options['cookie_text'];
  $getBtnText = $options['cookie_btn_text'];
      // $getOptions = get_option('cookies-field-1');

      if (!isset($_COOKIE['accept_cookies'])) {
        ?>
        <div id="cookie-plugin">

          <div class="container">
            <p class="cookie-plugin-text"><?php echo $getOptions; ?></p>
            <a href="?accept_cookies" class="cookie-plugin-button"><?php echo $getBtnText; ?></a>
          </div>

      </div>
        <?php

  }



}
