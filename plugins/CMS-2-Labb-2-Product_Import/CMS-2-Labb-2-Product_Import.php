<?php
/*
Plugin Name: CMS 2 Labb 2 Product Import
Description: import woocommerce products via cvs file
Author: Jan Gorges
Version: 1.0
*/
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 *
 */
 class ProductImport
 {
   function __construct()
   {
     add_action("admin_menu", array( $this,'cookie_admin_menu' ) );


   }

   function cookie_admin_menu()
   {
      add_menu_page("Cookie Notice Setting", "Import Product","manage_options", "import-product-admin", array( $this,"importer_form"),'dashicons-update-alt');
       add_filter('plugin_action_links_'.plugin_basename(__FILE__), array( $this,'zip_import_link'));
   }


   function zip_import_link( $links ) {
   	$links[] = '<a href="' .
   		admin_url( 'admin.php?page=import-product-admin' ) .
   		'">' . __('Settings') . '</a>';
   	return $links;
   }

 function importer_form() {
   echo '<h1>Import zip File</h1>';

   if(isset($_POST["Import"])){
 $filename=$_FILES["file"]["tmp_name"];
  if($_FILES["file"]["size"] > 0)
  {
    $unzip = new ZipArchive;
    $out = $unzip->open($filename);
    if ($out === TRUE) {
      for($i = 0; $i < $unzip->numFiles; $i++) {
        $file = $unzip->getNameIndex($i);
        $filecsv = fopen($file, "r");
       while (($getData = fgetcsv($filecsv, 10000, ",")) !== FALSE)
        {
          $post = array(
            'post_content'   => $getData[2],
            'post_excerpt'   => $getData[2],
            'post_title'     => $getData[1],
            'post_status'    => 'publish',
            'post_type'      => 'product'
        );
        // $getSUK = get_post_meta($post->ID,'_sku',true);
        // args to query for your key
         $args = array(
           'post_type' => 'product',
           'meta_query' => array(
               array(
                   'key' => '_sku',
                   'value' => $getData[0]
               )
           ),
           'fields' => 'ids'
         );

         $query = new WP_Query( $args );
         $checkSKU = $query->posts;

         // om SKU finns redan på databasen
         if ( ! empty( $checkSKU ) ) {
             update_post_meta( $checkSKU, '_sku', $getData[0] );
             update_post_meta( $checkSKU, '_product_version', $getData[2] );
             update_post_meta( $checkSKU, '_width', $getData[3] );
             update_post_meta( $checkSKU, '_regular_price', $getData[4]);
             update_post_meta( $checkSKU, '_sale_price', $getData[5] );
             update_post_meta( $checkSKU, '_sale_price_dates_to', $getData[6] );
             update_post_meta( $checkSKU, '_sale_price_dates_from', $getData[7] );

             echo '<h2 style="color:orange">Successfully Updated EXISTS Product ID--->'.$checkSKU[0].'</h2>';
             echo var_dump($checkSKU);
         } else {
            // set
            $thisid = wp_insert_post( $post, true );
                      add_post_meta( $thisid, '_sku', $getData[0] );
                      add_post_meta( $thisid, '_product_version', $getData[2] );
                      add_post_meta( $thisid, '_width', $getData[3] );
                      add_post_meta( $thisid, '_regular_price', $getData[4]);
                      add_post_meta( $thisid, '_sale_price', $getData[5] );
                      add_post_meta( $thisid, '_sale_price_dates_to', $getData[6] );
                      add_post_meta( $thisid, '_sale_price_dates_from', $getData[7] );

                      echo '<h2 style="color:green">Successfully Added NEW Product ID--->'.$thisid.'</h2>';
                      echo var_dump($getData);

                    }

        }
        fclose($filecsv);

  }
      } else {
        echo '<h3 style="color:red">Error: The file Must by .zip format</h3>';
      }


  }else {
    echo '<div style="color:red">no file was selected</div>';
  }
}
   ?>
   <form class="form-horizontal" action="<?php echo esc_url( admin_url( 'admin.php?page=import-product-admin' ) ); ?>" method="post" name="upload_excel" enctype="multipart/form-data">
       <fieldset>
           <!-- File Button -->
           <div class="form-group">
               <label class="col-md-4 control-label" for="filebutton">Select File</label>
               <div class="col-md-4">
                   <input type="file" name="file" id="file" class="input-large">
               </div>
           </div>
           <!-- Button -->
           <div class="form-group">
               <label class="col-md-4 control-label" for="singlebutton">Import data</label>
               <div class="col-md-4">
                   <input type="submit" id="submit" name="Import" class="" value="Import">
               </div>
           </div>
       </fieldset>
   </form>
   <?php
 }




 }
$Importera = new ProductImport();
