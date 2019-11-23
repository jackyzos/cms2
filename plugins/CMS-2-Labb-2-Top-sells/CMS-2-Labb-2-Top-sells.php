<?php
/*
Plugin Name: CMS 2 Labb 2 Top Selling Products
Description: display 10 best selling products via shorcode in any page
Author: Jan Gorges
Version: 1.0
*/
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 *
 */
 class TopProducts
 {
   function __construct()
   {
     add_shortcode( 'topSelling', array($this,'top_selling_shortcode') );
   }
   function top_products( $args ) {
     $args = array(
         'post_type' => 'product',
         'meta_key' => 'total_sales',
         'orderby' => 'meta_value_num',
         'posts_per_page' => 10,
         'order' => 'DESC',
     );
     $postsTops = new WP_Query( $args );
      while ( $postsTops->have_posts() ) : $postsTops->the_post();
      global $product;
      $count = get_post_meta(get_the_id(),'total_sales', true); // hur många gånger har vi såld denna produkten

      if ($count > 0) { // om produkten är såld minst 1 engång då visar
      ?>

      <div>
      <a href="<?php the_permalink(); ?>" id="id-<?php the_id(); ?>" title="<?php the_title(); ?>">

      <h3><?php the_title(); ?> <strong>(<?php echo $count; ?>)</strong>  </h3>
      </a>
      <h3><?php echo $product->get_price() ?>kr</h3>
      <p class="sku"><?php echo $postsTops->post->post_excerpt; ?></p>
      </div>
    <?php       }?>
      <?php endwhile; ?>

<?php
        }
        function top_selling_shortcode() {
            return $this->top_products($args);
        }

 }
$top_sell = new TopProducts();
