<?php
/*
Plugin Name: CMS 2 Labb 1 Shortcode
Description: shordcode a button with attribute
Author: Jan Gorges
Version: 1.0
*/
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 *
 */
class ShortCode
{

  function __construct()
  {
    add_action( 'wp_enqueue_scripts', array($this,'shortcode_load_plugin_css' ) );
    add_shortcode( 'button', array($this,'button_shortcode') );
  }


function shortcode_load_plugin_css() {
    $plugin_url = plugin_dir_url( __FILE__ );

    wp_enqueue_style( 'shortcode-plugin-css', $plugin_url . 'css/style.css' );
}
  function button_shortcode( $atts, $content = null ) {
    $value = shortcode_atts( array(
      'url'    => '',
      'text'  => 'knapp',
      'width' => '',
      'background'   => 'grey',
      'style'   => '',
    ), $atts );
?>
<style>
  .mybutton {
    width:<?php echo $value['width']; ?>px !important;
    background-color:<?php echo $value['background']; ?> !important;
  }
</style>
<?php
    $error = '<div class="error-plugin"> Error!! please check if your shortcode attributes are empty, the WIDTH==INT</div>';
    $button = '
          <div id="shortcode-plugin">
            <a href="'. $value['url'] . '">
            <button type="button" class="mybutton" style="' . $value['style'] . '">'. $value['text'] . '</button>
             </a>
         </div>';
         // vi gör en kontrol innan vi skriver ut html coden
         if (!empty($value['url']) && !empty($value['background']) && !empty($value['text']) && intval($value['width']) && !empty($value['style'])) {
           return $button;
         }
         return $error;
}
}
$MyShortCode = new ShortCode();
