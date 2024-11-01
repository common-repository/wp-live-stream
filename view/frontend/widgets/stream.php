<?php
    //! @file: stream widget

    add_action( 'widgets_init', create_function( '', 'register_widget( "LiveStream_Widget" );' ) );
    
    class LiveStream_Widget extends WP_Widget {

	    public function __construct() {
		    parent::__construct(
	     		'livestream_widget', // Base ID
			    'Live Stream', // Name
			    array( 'description' => __( 'A Live Stream that allows the wp-admin to create a twitter like stream just for the site via the Stream post type.', 'text_domain' ), ) // Args
		    );
	    }

     	public function form( $instance ) {
		    if ( $instance ) {
			    $title = $instance[ 'title' ]?esc_attr( $instance[ 'title' ] ):'';
			    $streamid = $instance[ 'streamid' ]?esc_attr( $instance[ 'streamid' ] ):'';
			    $feedcount = $instance[ 'feedcount' ]?esc_attr( $instance[ 'feedcount' ] ):'';
			    $charlimit = $instance[ 'charlimit' ]?esc_attr( $instance[ 'charlimit' ] ):'';
			    $width = $instance[ 'width' ]?esc_attr( $instance[ 'width' ] ):'';
			    $background_color = $instance[ 'background_color' ]?esc_attr( $instance[ 'background_color' ] ):'';
			    $bubble_background_color = $instance[ 'bubble_background_color' ]?esc_attr( $instance[ 'bubble_background_color' ] ):'';
			    $bubble_border_color = $instance[ 'bubble_border_color' ]?esc_attr( $instance[ 'bubble_border_color' ] ):'';
			    $date_background_color = $instance[ 'date_background_color' ]?esc_attr( $instance[ 'date_background_color' ] ):'';
			    $date_border_color = $instance[ 'date_border_color' ]?esc_attr( $instance[ 'date_border_color' ] ):'';
			    $font_color = $instance[ 'font_color' ]?esc_attr( $instance[ 'font_color' ] ):'';
			    //$border = is_string($instance[ 'border' ])?esc_attr( $instance[ 'border' ] ):true;
			    //$border_color = $instance[ 'border_color' ]?esc_attr( $instance[ 'border_color' ] ):'';
			    $credit = is_string($instance[ 'credit' ])?esc_attr( $instance[ 'credit' ] ):true;
		    }
		    ?>
		    <p>
		    <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		    <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
		    </p>
		    <p>
		    <label for="<?php echo $this->get_field_id( 'streamid' ); ?>"><?php _e( 'Stream ID:' ); ?></label> 
		    <input class="widefat" id="<?php echo $this->get_field_id( 'streamid' ); ?>" name="<?php echo $this->get_field_name( 'streamid' ); ?>" type="text" value="<?php echo $streamid; ?>" />
		    </p>
		    <p>
		    <label for="<?php echo $this->get_field_id( 'feedcount' ); ?>"><?php _e( 'Feedcount:' ); ?></label> 
		    <input class="widefat" id="<?php echo $this->get_field_id( 'feedcount' ); ?>" name="<?php echo $this->get_field_name( 'feedcount' ); ?>" type="text" value="<?php echo $feedcount; ?>" />
		    </p>
		    <p>
		    <label for="<?php echo $this->get_field_id( 'charlimit' ); ?>"><?php _e( 'Character Limit:' ); ?></label> 
		    <input class="widefat" id="<?php echo $this->get_field_id( 'charlimit' ); ?>" name="<?php echo $this->get_field_name( 'charlimit' ); ?>" type="text" value="<?php echo $charlimit; ?>" />
		    </p>
		    <p>
		    <label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e( 'Width:' ); ?></label> 
		    <input class="widefat" id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>" type="text" value="<?php echo $width; ?>" />
		    </p>
		    <p>
		    <label for="<?php echo $this->get_field_id( 'background_color' ); ?>"><?php _e( 'Background Color:' ); ?></label> 
		    <input class="widefat farbtastic-input" id="<?php echo $this->get_field_id( 'background_color' ); ?>" name="<?php echo $this->get_field_name( 'background_color' ); ?>" type="text" value="<?php echo $background_color; ?>" />
            <div class="farbtastic-picker" rel="<?php echo $this->get_field_id('background_color'); ?>"></div>
		    </p>
		    <p>
		    <label for="<?php echo $this->get_field_id( 'bubble_background_color' ); ?>"><?php _e( 'Bubble Background Color:' ); ?></label> 
		    <input class="widefat farbtastic-input" id="<?php echo $this->get_field_id( 'bubble_background_color' ); ?>" name="<?php echo $this->get_field_name( 'bubble_background_color' ); ?>" type="text" value="<?php echo $bubble_background_color; ?>" />
            <div class="farbtastic-picker" rel="<?php echo $this->get_field_id('bubble_background_color'); ?>"></div>
		    </p>
		    <p>
		    <label for="<?php echo $this->get_field_id( 'bubble_border_color' ); ?>"><?php _e( 'Bubble Border Color:' ); ?></label> 
		    <input class="widefat farbtastic-input" id="<?php echo $this->get_field_id( 'bubble_border_color' ); ?>" name="<?php echo $this->get_field_name( 'bubble_border_color' ); ?>" type="text" value="<?php echo $bubble_border_color; ?>" />
            <div class="farbtastic-picker" rel="<?php echo $this->get_field_id('bubble_border_color'); ?>"></div>
		    </p>
		    <p>
		    <label for="<?php echo $this->get_field_id( 'date_background_color' ); ?>"><?php _e( 'Date Background Color:' ); ?></label> 
		    <input class="widefat farbtastic-input" id="<?php echo $this->get_field_id( 'date_background_color' ); ?>" name="<?php echo $this->get_field_name( 'date_background_color' ); ?>" type="text" value="<?php echo $date_background_color; ?>" />
            <div class="farbtastic-picker" rel="<?php echo $this->get_field_id('date_background_color'); ?>"></div>
		    </p>
		    <p>
		    <label for="<?php echo $this->get_field_id( 'date_border_color' ); ?>"><?php _e( 'Date Border Color:' ); ?></label> 
		    <input class="widefat farbtastic-input" id="<?php echo $this->get_field_id( 'date_border_color' ); ?>" name="<?php echo $this->get_field_name( 'date_border_color' ); ?>" type="text" value="<?php echo $date_border_color; ?>" />
            <div class="farbtastic-picker" rel="<?php echo $this->get_field_id('date_border_color'); ?>"></div>
		    </p>
		    <p>
		    <label for="<?php echo $this->get_field_id( 'font_color' ); ?>"><?php _e( 'Font Color:' ); ?></label> 
		    <input class="widefat farbtastic-input" id="<?php echo $this->get_field_id( 'font_color' ); ?>" name="<?php echo $this->get_field_name( 'font_color' ); ?>" type="text" value="<?php echo $font_color; ?>" />
            <div class="farbtastic-picker" rel="<?php echo $this->get_field_id('font_color'); ?>"></div>
		    </p>
		    <p>
		    <?php /*
		    <input id="<?php echo $this->get_field_id( 'border' ); ?>" name="<?php echo $this->get_field_name( 'border' ); ?>" type="checkbox" value="1" <?php if(strlen($border)) echo "checked=checked"; ?>>
	<label for="<?php echo $this->get_field_id( 'border' ); ?>">Display a border?</label></p>
		    <p>
		    <label for="<?php echo $this->get_field_id( 'border_color' ); ?>"><?php _e( 'Border Color:' ); ?></label> 
		    <input class="widefat farbtastic-input" id="<?php echo $this->get_field_id( 'border_color' ); ?>" name="<?php echo $this->get_field_name( 'border_color' ); ?>" type="text" value="<?php echo $border_color; ?>" />
            <div class="farbtastic-picker" rel="<?php echo $this->get_field_id('border_color'); ?>"></div>
		    </p> */ ?>
		    <p>
		    <input id="<?php echo $this->get_field_id( 'credit' ); ?>" name="<?php echo $this->get_field_name( 'credit' ); ?>" type="checkbox" value="1" <?php if(strlen($credit) || !isset($credit)) echo "checked=checked"; ?>>
	<label for="<?php echo $this->get_field_id( 'credit' ); ?>">Display the eFrog Digital Design credits?</label></p>
	
            <script>
            	//jQuery('.farbtastic-picker').hide();
                //reset_farbtastic();
            </script>
		    <?php
	    }

	    public function update( $new_instance, $old_instance ) {
		    $instance = $old_instance;
		    $instance['title'] = strip_tags( $new_instance['title'] );
		    $instance['streamid'] = strip_tags( $new_instance['streamid'] );
		    $instance['feedcount'] = strip_tags( $new_instance['feedcount'] );
		    $instance['width'] = strip_tags( $new_instance['width'] );
		    $instance['background_color'] = strip_tags( $new_instance['background_color'] );
		    $instance['bubble_background_color'] = strip_tags( $new_instance['bubble_background_color'] );
		    $instance['date_background_color'] = strip_tags( $new_instance['date_background_color'] );
		    $instance['bubble_border_color'] = strip_tags( $new_instance['bubble_border_color'] );
		    $instance['date_border_color'] = strip_tags( $new_instance['date_border_color'] );
		    $instance['font_color'] = strip_tags( $new_instance['font_color'] );
		    $instance['charlimit'] = strip_tags( $new_instance['charlimit'] );
		    //$instance['border'] = strip_tags( $new_instance['border'] );
		    //$instance['border_color'] = strip_tags( $new_instance['border_color'] );
		    $instance['credit'] = strip_tags( $new_instance['credit'] );
		    return $instance;
	    }

	    public function widget( $args, $instance ) {		    
            $livestream_options = get_option('live_stream');           
		    extract( $args );
		    
		    if(!$instance['interval'])
		        $instance['interval'] = apply_filters('livestream_defaults', 'default_interval');
		    if(!$instance['feedcount'])
		        $instance['feedcount'] = apply_filters('livestream_defaults', 'default_feedcount');
		    if(!$instance['title'])
		        $instance['title'] = apply_filters('livestream_defaults', 'default_title');
		    if(!$instance['background_color'])
		        $instance['background_color'] = apply_filters('livestream_defaults', 'default_backgroundcolor');
		    if(!$instance['width'])
		        $instance['width'] = apply_filters('livestream_defaults', 'default_width');
		    if(!$instance['bubblebackgroundcolor'])
		        $instance['bubblebackgroundcolor'] = apply_filters('livestream_defaults', 'default_bubblebackgroundcolor');
		    if(!$instance['bubblebordercolor'])
		        $instance['bubblebordercolor'] = apply_filters('livestream_defaults', 'default_bubblebordercolor');
		    if(!$instance['datebackgroundcolor'])
		        $instance['datebackgroundcolor'] = apply_filters('livestream_defaults', 'default_datebackgroundcolor');
		    if(!$instance['datebordercolor'])
		        $instance['datebordercolor'] = apply_filters('livestream_defaults', 'default_datebordercolor');
		    if(!$instance['font_color'])
		        $instance['font_color'] = apply_filters('livestream_defaults', 'default_fontcolor');
		    if(!$instance['showold'])
		        $instance['showold'] = apply_filters('livestream_defaults', 'default_showold');
		    if(!$instance['streamid'])
		        $instance['streamid'] = NULL;
		    if(!$instance['charlimit'])
		        $instance['charlimit'] = apply_filters('livestream_defaults', 'default_charlimit');
		        
		    
		    $title = apply_filters( 'widget_title', $instance['title'] );
		    
		    $border_div_style = apply_filters( 'livestream_widget_border_style', $instance['border_style'] );
		    
		    $feedcount = $instance['feedcount'];
            
            $credit = is_string($instance[ 'credit' ])?esc_attr( $instance[ 'credit' ] ):true;
            
            if($width = trim($instance['width'])) {
                if(strpos($width,'px') === false || strpos($width,'%') === false)
                    $width .= 'px';
                $border_div_style .= ';width:'.$width;
            }
            if($instance['background_color'])
                $border_div_style .= 'padding:5px;;background-color:'.$instance['background_color'];
            if($instance['font_color'])
                $border_div_style .= ';color:'.$instance['font_color'];
            //if(!$instance['border'])
                $border_div_style .= ';border:none';
            //if($instance['border_color'])
            //    $border_div_style .= ';border-color:'.$instance['border_color'];
		    echo $before_widget;
		    
		    
		        
?>
                <div class="livestream_widget_border" style="<?php echo $border_div_style; ?>" feedcount="<?php echo $feedcount; ?>" lastupdate="<?php echo liveStream_last_update(); ?>"<?php liveStream_print_post_options($instance); ?>>
<?php
            if($instance['border']) echo "<div style='padding:4px;'>";

		    if ( ! empty( $title ) )
			    echo $before_title . $title . $after_title;
			    liveStream_print_feed($feedcount, $instance);
			    if(current_user_can('edit_post') || current_user_can('administrator')):
			        liveStream_swfupload_render();
			        ?>
			        <div class="updator">
			            <textarea>type your message here</textarea>
			            <input type="button" class="submit" value="submit" />
			            <div style="clear:both"></div>
			        </div>
			        <?php
		            liveStream_print_network_checkboxes();
			    endif;
		    ?>
		    
		        <?php if($credit || !isset($credit)): ?><div class="credit"><a href="http://wordpress.org/extend/plugins/wp-live-stream/" target="_blank">Live Stream</a> by <a href="http://www.efrogthemes.com/" target="_blank">eFrog Digital Design</a></div><?php endif; ?>
		        </div>
		    <?php
		    if($instance['border']) echo "</div>";
		    echo $after_widget;
	    }

    }
   
