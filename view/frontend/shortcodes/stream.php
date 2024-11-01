<?php
	//! @file this file adds the shortcode for livestream

	 /**
     * shortcode hooked function that outputs the livestream in posts / pages, shares code with 
     * widgets where possible
     *
     * @since 1.0
     * @author shannon
     */
    function liveStream_shortcode( $atts ){
        $atts = shortcode_atts( array(
		    'title' => apply_filters('livestream_defaults', 'default_title'),
		    'feedcount' => apply_filters('livestream_defaults', 'default_feedcount'),
		    'border' => 1,
		    'credit' => 1,
		    'fontcolor' => apply_filters('livestream_defaults', 'default_fontcolor'),
		    'bordercolor' => apply_filters('livestream_defaults', 'default_bordercolor'),
		    'width' => 'auto',
		    'socialmessage' => '',
		    'backgroundcolor' => apply_filters('livestream_defaults', 'default_backgroundcolor'),
		    'bubblebackgroundcolor' => apply_filters('livestream_defaults', 'default_bubblebackgroundcolor'),
		    'datebackgroundcolor' => apply_filters('livestream_defaults', 'default_datebackgroundcolor'),
		    'bubblebordercolor' => apply_filters('livestream_defaults', 'default_bubblebordercolor'),
		    'datebordercolor' => apply_filters('livestream_defaults', 'default_datebordercolor'),
		    'interval' => apply_filters('livestream_defaults', 'default_interval'),
		    'showold' => apply_filters('livestream_defaults', 'default_showold'),
		    'streamid' => NULL,
            'charlimit' => apply_filters('livestream_defaults', 'default_charlimit')
	    ), $atts );
        
        extract( $atts );
	    ob_start();
	    
	    $title = apply_filters( 'widget_title', $title );
	    $border_div_style = apply_filters( 'livestream_widget_border_style', '' );
	    
	    if(trim($width)) {
            if(strpos($width,'px') === false || strpos($width,'%') === false)
                $width .= 'px';
            $border_div_style .= ';width:'.$width;
        }

        if($backgroundcolor)
            $border_div_style .= ';background-color:'.$backgroundcolor;
        if($fontcolor)
            $border_div_style .= ';color:'.$fontcolor;

        //if(!($border && $border != 'false' && $border != 'no'))
            $border_div_style .= ';border:none';
        //if($bordercolor)
        //    $border_div_style .= ';border-color:'.$bordercolor;
            
        ?>
        <aside id="livestream_widget-shortcoded" class="widget widget_livestream_widget">
            <div class="livestream_widget_border" style="<?php echo $border_div_style; ?>" feedcount="<?php echo $feedcount; ?>" lastupdate="<?php echo liveStream_last_update(); ?>" streamtitle="<?php echo $title; ?>"<?php liveStream_print_post_options($atts); ?>>
            
            <?php if($socialmessage) : 
                $socialmessage = str_replace("URL", get_permalink(), $socialmessage);
            ?>
            <div id="update_message" style="display:none">
                <?php echo $socialmessage; ?>
            </div>
            <?php endif; ?>
            
            <?php
            if($border) echo "<div style='padding:5px;'>";
                if ( ! empty( $title ) )
    			    echo "<h3 class='widget-title'>" . $title . "</h3>";
    	            liveStream_print_feed($feedcount, $atts);
			    if(current_user_can('edit_post')):
			        
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
		        <?php if($credit && $credit != 'false' && $credit != 'no'): ?><div class="credit"><a href="http://wordpress.org/extend/plugins/wp-live-stream/" target="_blank">Live Stream</a> by <a href="http://www.efrogthemes.com/" target="_blank">eFrog Digital Design</a></div><?php endif; ?>
            </div>
            <?php if($border) echo "</div>"; ?>
        </aside>
        <?php
        return ob_get_clean();
    }
    add_shortcode( 'livestream', 'liveStream_shortcode' );  
    
?>