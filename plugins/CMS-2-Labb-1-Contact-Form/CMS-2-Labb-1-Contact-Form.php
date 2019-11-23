<?php
/*
Plugin Name: CMS 2 Labb 1 Contact Form
Description: contact form shortcode with attributes
Author: Jan Gorges
Version: 1.0
*/
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 *
 */
 class ContactFormShort
 {

   function __construct()
   {
     add_action( 'wp_enqueue_scripts', array($this,'form_load_plugin_css' ) );
     add_shortcode( 'myform', array($this,'contact_form') );
   }
   function form_load_plugin_css() {
       $plugin_url = plugin_dir_url( __FILE__ );

       wp_enqueue_style( 'form-plugin-css', $plugin_url . 'css/style.css' );
   }


   function contact_form( $atts = [], $content = null, $tag = '' ) {
     $num1 = 5;
     $num2 = 4;
     $value = shortcode_atts( array(
       'receiver'    => 'test@test.com',
       'placeholder'  => 'type your message here ...',
       'success-text'   => 'Your message sent successfully',
       'num1'   => $num1,
       'num2'   => $num2,
       'sum'   => $num1 + $num2,
     ), $atts );
 ?>

 <?php

     $form = '
     <h1><h1>
     <form method="post" action="" id="myform-plugin">
       <label for="email">Email:</label>
       <input type="email" name="email" value="">
       <label for="sender-subject">Subject:</label>
       <input type="text" name="sender-subject" value="">
       <label for="message">Message:</label>
       <textarea name="message" rows="4" cols="50" placeholder="'.$value['placeholder'].'"></textarea>
       <label for="captcha">Captcha: '.$value['num1'].'+'.$value['num2'].'?</label>
       <input type="number" name="captcha" value="">
       <input type="submit" value="Send">
       <input type="hidden" name="submitted" id="submitted" value="true" />

     </form>
     ';

            $rightAnswer = $value['sum']; //hämtar från shortcoden
            $sum = $value['num1'] + $value['num2']; //hämtar från shortcoden
          if(isset($_POST['submitted'])) {
            $userAnswer = $_POST['captcha'];
            //om captcha är inte lika med sum i shortcoden
            if( $userAnswer != $rightAnswer){
              $hasError = true;
            }
            //om sum är inte lika med summan av num1 + num2 i shortcoden
            if( $rightAnswer != $sum ){
              $hasError = true;
            }

            if(trim($_POST['email']) === '') {
              $hasError = true;
            } else {
              $email = trim($_POST['email']);
            }

           if(trim($_POST['sender-subject']) === '') {
             $hasError = true;
           } else {
             $senderSubject = trim($_POST['sender-subject']);
           }

           if(trim($_POST['message']) === '') {
             $hasError = true;
           } else {
             if(function_exists('stripslashes')) {
               $message = stripslashes(trim($_POST['message']));
             } else {
               $message = trim($_POST['message']);
             }
           }

       if(!isset($hasError)) {
         $emailTo = $value['receiver'];
         if (!isset($emailTo) || ($emailTo == '') ){
           $emailTo = get_option('admin_email');
         }
         $subject = 'Subject: '.$senderSubject;
         $body = 'Message:'. $message;
         $headers = 'From: '.$email.' <'.$emailTo.'>' . "\r\n" . 'Sent-To: ' . $emailTo;

         wp_mail($emailTo, $subject, $body, $headers);
         $emailSent = true;
         echo '<h1 class="sent-success">' . $value['success-text'] . '</h1>';
       }else {
          echo '<h1 class="no-sent-success" style="">Somthing went Wrong check empty fields Or Captcha</h1>';
       }

     }
          return $form;
 }
}
$My_contact_form = new ContactFormShort();
