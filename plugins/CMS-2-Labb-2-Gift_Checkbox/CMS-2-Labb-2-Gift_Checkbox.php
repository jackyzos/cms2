<?php
/*
Plugin Name: CMS 2 Labb 2 Checkbox Gift
Description: display extra field for if gift or not
Author: Jan Gorges
Version: 1.0
*/
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 *
 */
 class GiftCheckbox
 {
   function __construct()
   {
     add_filter('woocommerce_checkout_fields', array($this,'hook_multiaction_fields') );

     add_action( 'woocommerce_checkout_after_customer_details' , array($this,'gift_extra_checkout_fields' ) ); // group fields add

     add_action('woocommerce_checkout_update_order_meta', array($this,'custom_checkout_gift_update') );

     add_action( 'woocommerce_admin_order_data_after_order_details', array($this,'display_custom_field_on_order_backend') );

   }
   function hook_multiaction_fields($fields){
            $fields['gift_extra_fields'] = array(
              'custom_field_gift' => array(
                  'type' => 'checkbox',
                  'label' => __( 'Marked as Gift' )
                ),
                'custom_field_gift_note' => array(
                    'type' => 'text',
                    'label' => __( 'Some extra Note for Admin' )
                )

                    );
            return $fields;
            }

      function gift_extra_checkout_fields(){

                $checkout = WC()->checkout; ?>
                <div class="extra-fields">
                <h3><?php _e( 'Gift Packing Optional' ); ?></h3>
                <?php
                // echo var_dump($checkout->checkout_fields['gift_extra_fields']);
                   foreach ( $checkout->checkout_fields['gift_extra_fields'] as $key => $field ) : ?>
                        <?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
                    <?php endforeach; ?>
                </div>
            <?php }


          // spara data om checkboxen är checkade
          function custom_checkout_gift_update( $order_id ) {
            if( isset( $_POST['custom_field_gift_note'] ) ) {
              update_post_meta( $order_id, '_custom_field_gift_note', sanitize_text_field( $_POST['custom_field_gift_note'] ) );
          }
            if( isset( $_POST['custom_field_gift'] ) ) {
                update_post_meta( $order_id, '_custom_field_gift', $_POST['custom_field_gift'] );
                }
          }


        function display_custom_field_on_order_backend( $order ){
            $checkbox = get_post_meta( $order->id, '_custom_field_gift', true );
            $checkboxNote = get_post_meta( $order->id, '_custom_field_gift_note', true );
             echo '<div class="gift-order" style="padding:5px;margin-top:5px;" ><h4>' . __('Additional Gift Note') . '</h4>';
            if ($checkbox == 1) {
              echo '<p><strong>' . __( 'GIFT?' ) . ':</strong> <span style="color:green;">This Order is marked as a GIFT</span>'.$checkbox.'</p>';
              echo '<p>' . __( 'Customer Notifation' ) . ': '.$checkboxNote.'</p>';
            }else {
              echo '<p><strong>' . __( 'GIFT?' ) . ':</strong> <span style="color:red;">This Order is NOT marked as a GIFT</span>'.$checkbox.'</p>';
              echo '<p>' . __( 'Customer Notifation' ) . ': '.$checkboxNote.'</p>';
            }
            echo '</div>';
        }



 }
$checkBox_Gift = new GiftCheckbox();
