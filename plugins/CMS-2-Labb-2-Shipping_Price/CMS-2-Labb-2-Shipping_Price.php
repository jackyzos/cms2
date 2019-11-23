<?php  session_start();
/*
Plugin Name: CMS 2 Labb 2 Shipping weight
Description: count totalt weight shipping
Author: Jan Gorges
Version: 1.0
*/
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
      global $distance;

     add_filter('woocommerce_package_rates', 'shipping_cost_based_on_weight' , 10, 2 );

   add_filter( 'woocommerce_locate_template', 'woo_adon_plugin_template', 1, 3 );
   function woo_adon_plugin_template( $template, $template_name, $template_path ) {
     global $woocommerce;
     $_template = $template;
     if ( ! $template_path )
        $template_path = $woocommerce->template_url;

     $plugin_path  = untrailingslashit( plugin_dir_path( __FILE__ ) )  . '/templates/woocommerce/';

    $template = locate_template(
    array(
      $template_path . $template_name,
      $template_name
    )
   );

   if( ! $template && file_exists( $plugin_path . $template_name ) )
    $template = $plugin_path . $template_name;

   if ( ! $template )
    $template = $_template;

   return $template;
}

    add_action('wp_enqueue_scripts', 'plugin_style');
   function plugin_style(){
     wp_enqueue_style('form-style', plugin_dir_url(__FILE__) . 'css/style.css');
     wp_enqueue_script('form-script', plugin_dir_url(__FILE__) . 'js/script.js');
     wp_register_script( 'jQuery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js', null, null, true );
     wp_enqueue_script('jQuery');
   }

   function shipping_cost_based_on_weight( $rates, $package ){
     // global      $time,$street,$husNummer,$ort,$country;
     if (isset($_POST['calc_shipping']) || isset($_POST['update_cart'])) {
         WC()->session->set( 'shipping_distance_data', null );

         $street = urlencode($_POST['calc_shipping_adress']);
         $gethusNummer = (int) filter_var($street, FILTER_SANITIZE_NUMBER_INT);
         $husNummer = $gethusNummer;
         $ort = urlencode($_POST['calc_shipping_city']);
         $country = $_POST['calc_shipping_state'];

        $hereID = 'emILWwst6MjlSnaIIiPo';
        $hereCODE = 'lB_yXId6Kwd-YDHaD9acaw';

        // get the adress from input into API
        $getAdress = file_get_contents('https://geocoder.api.here.com/6.2/geocode.json?housenumber='.$husNummer.'&street='.$street.'&city='.$ort.'&country='.$country.'&app_id='.$hereID.'&app_code='.$hereCODE.'');
        $getLitLong = json_decode($getAdress);

        // min butik adress
        $storeLocationLatitude = 57.746874;
        $storeLocationLongitude = 12.031675;
        // kunden adress
        $latitude = 0;
        $longitude = 0;

        foreach ($getLitLong as $key=>$value){
         $latitude = $value->View[0]->Result[0]->Location->DisplayPosition->Latitude;
         $longitude = $value->View[0]->Result[0]->Location->DisplayPosition->Longitude;
        }

        // get LAT and LONG from the adress
        $getdata = file_get_contents('https://route.api.here.com/routing/7.2/calculateroute.json?app_id='.$hereID.'&app_code='.$hereCODE.'&waypoint0=geo!'.$storeLocationLatitude.','.$storeLocationLongitude.'&waypoint1=geo!'.$latitude.','.$longitude.'&mode=fastest;car;traffic:disabled');

        $data = json_decode($getdata);

        foreach ($data as $key=>$val){
         $distance += $val->route[0]->summary->distance;
         $time += $val->route[0]->summary->baseTime;
        }

      }
       $sDistance = round($distance/1000);
       // det här session är extra grej
       WC()->session->set( 'shipping_distance_data' , $sDistance );
       $distanceKM = WC()->session->get( 'shipping_distance_data' );

       $baseKM = 10;
       $frakt = 0;
       $totalCost = 0;

       // Hämta total cart vikt
       $total_weight = WC()->cart->get_cart_contents_weight();

       foreach ( $rates as $rate_key => $rate ){

           $has_taxes = false;
           // "flat rate" module some är activerat i shipping option i woocommerce admin
           if( 'flat_rate' === $rate->method_id ){

               $initial_cost = $new_cost = $rates[$rate_key]->cost;

               // räkna nya priset beroende av vikt
               if( $total_weight < 1 ) {
                 $frakt += 30;
                 if ($distanceKM > $baseKM) {
                   $new_cost = $frakt  * ($distanceKM / $baseKM);
                 }else {
                   $new_cost = $frakt;
                 }
                   // $new_cost = $cost;
               }
               elseif( $total_weight < 5 && $total_weight > 1 ) {
                 $frakt += 60;
                 if ($distanceKM > $baseKM) {
                   $new_cost = $frakt * ($distanceKM / $baseKM);

                 }else {
                   $new_cost = $frakt;

                 }
                    // $new_cost = $cost;
               }
               elseif( $total_weight > 5 && $total_weight < 10 ) {
                 $frakt += 100;
                 if ($distanceKM > $baseKM) {
                   $new_cost = ($distanceKM / $baseKM) * $frakt ;
                 }else {
                   $new_cost = $frakt ;
                 }
               }
               elseif( $total_weight > 10 && $total_weight < 20 ) {
                 $frakt += 200;
                 if ($distanceKM > $baseKM) {
                   $new_cost = ($distanceKM / $baseKM) * $frakt ;
                 }else {
                   $new_cost = $frakt ;
                 }

               }
               else {
                 $frakt += 10;
                 if ($distanceKM > $baseKM) {
                   $new_cost = ($frakt * $total_weight) * ($distanceKM / $baseKM);

                 }else {

                   $new_cost = $frakt * $total_weight;
                 }
                   // $new_cost = $total_weight * $cost4;
               }

               $rates[$rate_key]->cost = $new_cost;
           }
       }

       return $rates;
   }
