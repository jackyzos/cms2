<?php
/*
Plugin Name: CMS 2 Labb 1 Widget
Description: YouTube Widget by url
Author: Jan Gorges
Version: 1.0
*/
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

 /**
  * Adds Youtube_Widget widget.
  */
  function register_youtubeVideo(){
  register_widget('Youtube_Embed_Widget');
}
add_action('widgets_init', 'register_youtubeVideo');
 class Youtube_Embed_Widget extends WP_Widget {

     /**
      * Register widget with WordPress.
      */
     public function __construct() {

         parent::__construct(
             'youtube_embed_widget', // Base ID
             'Youtube Embed Widget', // Name
             array( 'description' => __( 'Youtube Widget by ID', 'yew_domain' ), ) // Args
         );
     }

     /**
      * Front-end display of widget.
      *
      * @see WP_Widget::widget()
      *
      * @param array $args     Widget arguments.
      * @param array $instance Saved values from database.
      */
     public function widget( $args, $instance ) {
         echo $before_widget;

         if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
          }

          //om man klistrar en hela youtube URL då kommer REGEX välja bara ID av VIDEON
          preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $instance['video'], $videoid);

          //om man klistrar in själva Youtube ID så behöver man inte REGEX
          if ($videoid[1] == NULL) {
            $videoid[1] = $instance['video'];
          }
          // $zero == '0' returnerar empty string  på php och inte själva '0'
          //här tilldelar jag till nollan "värde" för att använda så här controls=0&autoplay=0
          if ($instance['autoplay'] == '') {
            $instance['autoplay'] = 0;
          }
          if ($instance['controls'] == '') {
            $instance['controls'] = 0;
          }

         //visa koden på frontend
         echo '<iframe src="https://www.youtube.com/embed/'.$videoid[1].'?rel=0&modestbranding=1&autohide=1&mute=1&showinfo=0&controls='.$instance['controls'].'&autoplay='.$instance['autoplay'].'"  width="100%" height="auto"  frameborder="0" allowfullscreen></iframe>
         ';

         echo $after_widget;
     }

     /**
      * Back-end widget form.
      *
      * @see WP_Widget::form()
      *
      * @param array $instance Previously saved values from database.
      */
     public function form( $instance ) {
         $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'YouTube Video', 'yew_domain' );

         $video = ! empty( $instance['video'] ) ? $instance['video'] : esc_html__( '83PaFAAxYe0', 'yew_domain' );

         $autoplay = ! empty( $instance['autoplay'] ) ? $instance['autoplay'] : esc_html__( '2', 'yts_domain' );

         $controls = ! empty( $instance['controls'] ) ? $instance['controls'] : esc_html__( '0', 'yts_domain' );


         ?>
         <!-- TITTLE -->
         <p>
             <label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>

             <input
             class="widefat"
             id="<?php echo $this->get_field_id( 'title' ); ?>"
             name="<?php echo $this->get_field_name( 'title' ); ?>"
             type="text"
             value="<?php echo esc_attr( $title ); ?>" />
          </p>
          <!-- VIDEOID -->
          <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'video' ) ); ?>">
              <?php esc_attr_e( 'VIDEO: ID eller URL', 'yew_domain' ); ?>
            </label>

            <input
              class="widefat"
              id="<?php echo esc_attr( $this->get_field_id( 'video' ) ); ?>"
              name="<?php echo esc_attr( $this->get_field_name( 'video' ) ); ?>"
              type="text"
              value="<?php echo esc_attr( $video ); ?>">
          </p>
          <!-- AUTOPLAY -->
          <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'autoplay' ) ); ?>">
              <?php esc_attr_e( 'Autoplay:', 'yts_domain' ); ?>
            </label>

            <select
              class="widefat"
              id="<?php echo esc_attr( $this->get_field_id( 'autoplay' ) ); ?>"
              name="<?php echo esc_attr( $this->get_field_name( 'autoplay' ) ); ?>">
              <option value="0" <?php echo ($autoplay == '0') ? 'selected' : ''; ?>>
                OFF
              </option>
              <option value="1" <?php echo ($autoplay == '1') ? 'selected' : ''; ?>>
                ON
              </option>
            </select>
          </p>

          <!-- CONTROLS -->
          <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'controls' ) ); ?>">
              <?php esc_attr_e( 'Controls:', 'yts_domain' ); ?>
            </label>

            <select
              class="widefat"
              id="<?php echo esc_attr( $this->get_field_id( 'controls' ) ); ?>"
              name="<?php echo esc_attr( $this->get_field_name( 'controls' ) ); ?>">
              <option value="0" <?php echo ($controls == intval(0)) ? 'selected' : ''; ?>>
                OFF
              </option>
              <option value="1" <?php echo ($controls == '1') ? 'selected' : ''; ?>>
                ON
              </option>
            </select>
          </p>
     <?php
     }

     /**
      * Sanitize widget form values as they are saved.
      *
      * @see WP_Widget::update()
      *
      * @param array $new_instance Values just sent to be saved.
      * @param array $old_instance Previously saved values from database.
      *
      * @return array Updated safe values to be saved.
      */
     public function update( $new_instance, $old_instance ) {
         $instance = array();
         $instance['title'] = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

         $instance['video'] = ( ! empty( $new_instance['video'] ) ) ? strip_tags( $new_instance['video'] ) : '';

         $instance['autoplay'] = ( ! empty( $new_instance['autoplay'] ) ) ? strip_tags( $new_instance['autoplay'] ) : '';

         $instance['controls'] = ( ! empty( $new_instance['controls'] ) ) ? strip_tags( $new_instance['controls'] ) : '';

         return $instance;
     }

 } // class end


$MyYoutubeVideo = new Youtube_Embed_Widget();
