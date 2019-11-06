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
class CookiesSettingsPage{
  private $options;

  public function __construct()
  {
    add_action("admin_menu", array( $this,'cookie_admin_menu' ) );
    add_action( 'admin_init', array( $this, 'cookie_page_init' ) );
  }//end of construct

  // add admin page to menu
 function cookie_admin_menu()
 {
    add_menu_page("Cookie Notice Setting", "Cookie Notice","manage_options", "cookie-setting-admin", array( $this,"cookie_admin_option"),'dashicons-buddicons-community');
 }

  // add page init
 public function cookie_page_init()
 {
     register_setting(
         'cookie_option_group', // Option group
         'cookie_option_name', // Option name
         array( $this, 'sanitize' ) // Sanitize
     );
     add_settings_section(
     		'cookies_settings_section_id', // id
     		'Cookies plugin Setting', //title
        array( $this, 'display_cookies_section_info' ), // Callback
        'cookie-setting-admin'//page

     	);
      add_settings_field(
        'cookie_save_days', // ID
        'Days To Save Cookies', // Title
        array( $this, 'cookie_save_days_callback' ), // Callback
        'cookie-setting-admin', // Page
        'cookies_settings_section_id' // Section
    );

    add_settings_field(
        'cookie_text',
        'Cookies Notice Text',
        array( $this, 'cookie_text_callback' ),
        'cookie-setting-admin',
        'cookies_settings_section_id'
    );

    add_settings_field(
        'cookie_btn_text',
        'Cookies Notice Button Text',
        array( $this, 'cookie_btn_text_callback' ),
        'cookie-setting-admin',
        'cookies_settings_section_id'
    );
}

// visa på admin option sida
function cookie_admin_option() {
  $this->options = get_option( 'cookie_option_name' );
    ?>

    <h2>My Settings</h2>
    <form action='options.php' method='post'>

        <?php
        settings_fields( 'cookie_option_group' );
        do_settings_sections( 'cookie-setting-admin' );
        submit_button();

        ?>

    </form>
    <?php
}

public function sanitize( $input )
{
    if( !is_numeric( $input['cookie_save_days'] ) )
        $input['cookie_save_days'] = '';

    if( !empty( $input['cookie_text'] ) )
        $input['cookie_text'] = sanitize_text_field( $input['cookie_text'] );

    if( !empty( $input['cookie_btn_text'] ) )
        $input['cookie_btn_text'] = sanitize_text_field( $input['cookie_btn_text'] );

    return $input;
}

public function display_cookies_section_info()
{
    print 'Cookies Notification Setting:';
}

public function cookie_save_days_callback()
{
    echo '<input type="text" id="cookie_save_days" name="cookie_option_name[cookie_save_days]" value="'.esc_attr( $this->options['cookie_save_days']).'" />' ;
}

 public function cookie_text_callback()
 {
    echo '<textarea type="textarea" cols="40" rows="5" id="cookie_text" name="cookie_option_name[cookie_text]" >'.esc_attr( $this->options['cookie_text']).'</textarea>';
 }

 public function cookie_btn_text_callback()
 {
     echo '<input type="text" id="cookie_btn_text" name="cookie_option_name[cookie_btn_text]" value="'.esc_attr( $this->options['cookie_btn_text']).'" />' ;
 }

}//end of class

if( is_admin() )
    $my_settings_page = new CookiesSettingsPage();
