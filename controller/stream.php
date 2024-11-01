<?php
    //! @file: this part registers all the functionality of the streaming protocol
    
    /**
     * register the live stream post type
     *
     * @since 1.0
     * @author shannon
     */
    function liveStream_register_stream_type() {
        register_post_type( 'livestream', 
            array(
                'label' => 'Live Streams',
                'labels' => array(
                    'name' => 'Live Streams',
                    'singular_name' => 'Live Stream',
                    'add_new' => 'Update Stream',
                    'all_items' => 'All Live Streams',
                    'add_new_item' => 'Update Stream',
                    'edit_item' => 'Edit Stream',
                    'new_item' => 'New Stream',
                    'view_item' => 'View Stream',
                    'search_item' => 'Search Streams',
                    'not_found' => 'No Streams Found',
                    'not_found_in_trash' => 'No Streams Found in Trash',
                    'parent_item_colon' => 'Parent Stream',
                    'menu_name' => 'Live Streams',
                ),
                'description' => 'Twitter like stream displayed on the frontend via a widget.',
                'exclude_from_search' => true,
                'capability_type' => 'post',
                'public' => true,
                'publicly_queryable' => false,
                'supports' => array( 'title'),
            )
        );
    }
    add_action( 'init', 'liveStream_register_stream_type' );
    
    /**
     * adjust the columns used to display in the admin section
     *
     * @since 1.0
     * @author shannon
     */
    function liveStream_post_type_columns($cols) { 
        $cols = array();
        $cols['cb'] = '<input type="checkbox">';
		$cols['streamid'] = __('ID'); 
        $cols['title'] = __('Stream');
		$cols['date'] = __('Date'); 
		return $cols;
	}
    add_filter( 'manage_edit-livestream_columns', 'liveStream_post_type_columns' );
	
    /**
     * adjust how the column is displayed
     *
     * @since 1.0
     * @author shannon
     */
    function liveStream_column_display( $column_name, $post_id ) {
	    if ( 'streamid' != $column_name )
		    return;
     
	    $streamid = get_post_meta($post_id, 'live_stream_id', true);
	    if ( !$streamid )
		    $streamid = '<em>' . __( 'main' ) . '</em>';
     
	    echo $streamid;
    }
    add_action( 'manage_posts_custom_column', 'liveStream_column_display', 10, 2 );
    
    /**
     * register the column 'streamid' as sortable column
     *
     * @since 1.0
     * @author shannon
     */
    function liveStream_column_register_sortable( $columns ) {
	    $columns['streamid'] = 'streamid';
     
	    return $columns;
    }
    add_filter( 'manage_edit-livestream_sortable_columns', 'liveStream_column_register_sortable' );

    /**
     * sort the request if 'streamid' is the sorted column
     *
     * @since 1.0
     * @author shannon
     */
    function liveStream_column_orderby( $vars ) {
	    if ( isset( $vars['orderby'] ) && 'streamid' == $vars['orderby'] ) {
		    $vars = array_merge( $vars, array(
			    'meta_key' => 'live_stream_id',
			    'orderby' => 'meta_value'
		    ));
	    }
     
	    return $vars;
    }
    add_filter( 'request', 'liveStream_column_orderby' );    

    /**
     * helper function that checks if there is a new liveStream update
     *
     * @param lasttimesstamp when the last livestream update you checked for was (int)
     * @return whether or not the livestream was updated (bool)
     * @since 1.0
     * @author shannon
     */
    function liveStream_is_updated($lasttimestamp) {
        return liveStream_last_update() > $lasttimestamp;
    }
    
    /**
     * helper function that checks when the last liveStream update was
     *
     * @return timestamp for the new (int)
     * @since 1.0
     * @author shannon
     */
    function liveStream_last_update() {
        global $post, $livestream_last_update;
        if($livestream_last_update)
            return $livestream_last_update;
        query_posts("post_type=livestream&posts_per_page=1");
        if(!have_posts()) return 0;
        the_post();
	    $livestream_last_update = strtotime($post->post_date);
        wp_reset_query();
        return $livestream_last_update;
    }
    
    /**
     * helper function that performs the query for all livestreams with a certain id
     *
     * @param feedcount how many feeds to return (int)
     * @param offset how many posts to skip (int)
     * @param the stream id you want to query for (string)
     * @return all stream feeds for the given stream (array)
     * @since 1.0
     * @author shannon
     */
    function liveStream_query($feedcount, $offset = 0, $streamid = NULL) {
        $posts = get_posts("post_type=livestream&posts_per_page=-1");
        $return_posts = array();
        
        $offseted = 0;
        foreach($posts as $p) :
            if(get_post_meta($p->ID, 'live_stream_id', true) == $streamid) :
                ++$offseted;
                if($offset == 0 || $feedcount == -1 || $offseted > $offset*$feedcount) :
                    $return_posts[] = $p;
                endif;
                if(count($return_posts) >= $feedcount && $feedcount != -1) return $return_posts;
            endif;
        endforeach;
        return $return_posts;
        //return get_posts("post_type=livestream&posts_per_page=".$feedcount."&offset=".($feedcount*$offset));
    }
    
    /**
     * helper function that prints a complete feed, used by the widget and shortcode
     *
     * @param how many posts to display (int)
     * @param all the overriding options for the feed (array)
     * @since 1.0
     * @author shannon
     */
    function liveStream_print_feed($feedcount, &$options = array()) {
        global $post;
        
        $bubble_style = '';
        $date_style = '';
        $streamid = NULL;
        
        if($options['bubble_background_color']||$options['bubblebackgroundcolor']) {
            $bubble_style .= 'background-color:'.($options['bubble_background_color']?$options['bubble_background_color']:$options['bubblebackgroundcolor']).';';
        }
        if($options['bubble_border_color']||$options['bubblebordercolor']) {
            $bubble_style .= 'border-color:'.($options['bubble_border_color']?$options['bubble_border_color']:$options['bubblebordercolor']).';';
        }
        
        if($options['date_background_color']||$options['datebackgroundcolor']) {
            $date_style .= 'background-color:'.($options['date_background_color']?$options['date_background_color']:$options['datebackgroundcolor']).';';
        }
        if($options['date_border_color']||$options['datebordercolor']) {
            $date_style .= 'border-color:'.($options['date_border_color']?$options['date_border_color']:$options['datebordercolor']).';';
        }
        
        if($bubble_style) $bubble_style = ' style="'.$bubble_style.'"';
        if($date_style) $date_style = ' style="'.$date_style.'"';
        
        if($options['streamid'])
            $streamid = $options['streamid'];
        
        $posts = liveStream_query($feedcount, 0, $streamid);
        
        $displayed = count($posts);
        
        echo '<dl class="livestream_feed">';
        foreach($posts as $p): $post = $p;
            $title = make_clickable( get_the_title() );
            if( is_numeric($options['charlimit']) && $options['charlimit'] > 0 && strlen(get_the_title()) > $options['charlimit']) 
                $title = substr(get_the_title(),0, $options['charlimit']). ' ...';

	        echo '<dt id="'.get_the_ID().'" '.$bubble_style.'>';
	        echo str_replace('rel="nofollow"', 'rel="nofollow" target="_blank"', $title);
	        liveStream_print_attachment();
	        echo '</dt>';
	        echo '<dd'.$date_style.'>';
	        echo get_the_date(); echo ' - '; the_time();
	        echo '</dd>';
        endforeach;
        
        $offset = 1;
        $count = count(liveStream_query(-1, 0, $streamid));
        $posts = liveStream_query($feedcount, $offset, $streamid);
        $all_posts = array();
        
        if(count($posts) && ($options['showolder'] == 'true' || is_numeric($options['showold']))) :
            $all_posts = array_merge($posts,array());
            
            $last_post = end($posts);
            
            if(!is_numeric($options['showold']))
                $options['showold'] = get_the_ID();
            
            while($options['showold'] <= $last_post->ID):
                /* Show in groups of "feedcount" if user clicked showolder */
                if($options['showolder']) :
                    $posts = liveStream_query($feedcount, ++$offset, $streamid);
                /* Show all the way to the last viewed post if this is just an update */
                else :
                    if($options['showold'] == $last_post->ID) break; // only show what's been shown before
                    $posts = liveStream_query(1, ++$offset+$feedcount, $streamid);
                endif;
                $all_posts = array_merge($all_posts,$posts);
                if(!count($posts)):
                    $last_post = false;
                    break;
                endif;
                $last_post = end($posts);
            endwhile;
            
            foreach($all_posts as $p):
                $post = $p;
                $title = make_clickable( get_the_title() );
                if( is_numeric($options['charlimit']) && $options['charlimit'] > 0 && strlen(get_the_title()) > $options['charlimit']) 
                    $title = substr(get_the_title(),0, $options['charlimit']). ' ...';

                echo '<dt id="'.get_the_ID().'" '.$bubble_style.'>';
	            echo str_replace('rel="nofollow"', 'rel="nofollow" target="_blank"', $title);
	            liveStream_print_attachment();
	            echo '</dt>';
	            echo '<dd'.$date_style.'>';
	            echo get_the_date(); echo ' - '; the_time();
	            echo '</dd>';
            endforeach;
        
            $options['showold'] = get_the_ID();
        endif;
        
        if($count > count($all_posts) + $displayed):
            echo '<dd class="more">';
            echo '<a href="javascript:;">View Older</a>';
            echo '</dd>';
        endif;
        
        echo '</dl>';
    }
    
    /**
     * helper function to print the feeds attachments if any
     *
     * @since 1.0
     * @author shannon
     */
    function liveStream_print_attachment() {
        global $post;
        
        if($external_media = get_post_meta($post->ID, 'external_media', true)) {

            $width = get_post_meta($post->ID, 'external_media_width', true);
            if(!$width) :
                $data = @liveStream_get_contents($external_media);

                if(!$data)
                    return;
            
                $resource = @imagecreatefromstring($data); 

                if(!$resource)
                    return;

                $width = imagesx($resource); 
                update_post_meta($post->ID, 'external_media_width', $width);
            endif;
        ?>
            <br/><img src="" style="display:none" maxwidth="<?php echo $width; ?>" unsizedsrc="<?php echo $external_media; ?>" alt="<?php echo basename($external_media); ?>" />
        <?php 
            return;
        }
        
        
        $images = get_children(array(
            'post_type' => 'attachment',
            'post_status' => null,
            'post_parent' => $post->ID,
            'post_mime_type' => 'image',
            'order' => 'ASC',
            'orderby' => 'menu_order ID'));                
        $uploads = wp_upload_dir();
        foreach($images as $image) {
            list($width, $height, $type, $attr) = getimagesize(str_replace($uploads['baseurl'],$uploads['basedir'], $image->guid));
        ?>
            <br/><img src="" style="display:none" maxwidth="<?php echo $width; ?>" unsizedsrc="<?php echo $image->guid; ?>" alt="<?php echo get_the_title(); ?>" />
        <?php 
        }
    }
    
    /**
     * helper function that inserts a live_stream post and attaches any attachments
     *
     * @param the feed content (string)
     * @param either a base64 encoded attachment list for any new uploads or
     *      an external media being attached (string)
     * @param options that can effect how the strinng is stored, e.g chararacter limit for the stream (array)
     * @since 1.0
     * @author shannon
     */
    function liveStream_update_feed($feed, $attachments, $options) {
        // Create post object
          $feed = array(
             'post_title' => is_numeric($options['charlimit']) && $options['charlimit']>0 ? substr($feed, 0, $options['charlimit']) : $feed,
             'post_status' => 'publish',
             'post_author' => get_current_user_ID(),
             'post_type' => 'livestream',
          );

        // Insert the post into the database
          $e = wp_insert_post(apply_filters('livestream_insert', $feed));
          
          if($options['streamid'])
              update_post_meta($e, 'live_stream_id', $options['streamid']);
              
          if($attachments) {
              $attachment_decoded = (json_decode(base64_decode($attachments)));
              if($attachment_decoded) :
                  $attachments = $attachment_decoded;
                  foreach($attachments as $attachment) {
                    $oldname = $name = basename($attachment->file);
                    $oldurl = $url = basename($attachment->url);
                    $changed = false;
                    
                    if($tmppos !== false && $tmppos == 0) {
                        $name = substr($name,3);
                        $newname = str_replace('/'.$oldname,'/'.$name,$attachment->file);
                        if(rename($attachment->file, $newname)) {
                            $attachment->file =  $newname;
                            $changed = true;
                        }
                    }
                    
                    $tmppos = strpos($oldurl, 'tmp');
                    if($tmppos !== false && $tmppos == 0 && $changed) {
                        $url = substr($url,3);
                        $attachment->url = str_replace('/'.$oldurl,'/'.$url,$attachment->url);
                    }
                  
                    $wp_filetype = wp_check_filetype(basename($name), null );
                    
                    $wp_attachment = array(
                         'post_mime_type' => $wp_filetype['type'],
                         'post_title' => preg_replace('/\.[^.]+$/', '', basename($attachment->file)),
                         'post_content' => '',
                         'post_status' => 'inherit',
                         'guid' => $attachment->url
                      );
                    $attach_id = wp_insert_attachment( $wp_attachment, $attachment->file, $e );

                    // you must first include the image.php file
                    // for the function wp_generate_attachment_metadata() to work

                    require_once(ABSPATH . 'wp-admin/includes/image.php');        
                    $attach_data = wp_generate_attachment_metadata( $attach_id, $attachment->file );
                    wp_update_attachment_metadata( $attach_id, $attach_data );  
                  }
              else :
                update_post_meta($e, 'external_media', $attachments);
              endif;
          }
    }
    
    /**
     * helper function that prints an option list as an html tags attribute list
     *
     * @param options to be output (array)
     *      currently only outputs the following options:
     *          datebordercolor / date_border_color
     *          datebackgroundcolor / date_background_color
     *          bubblebordercolor / bubble_border_color
     *          bubblebackgroundcolor / bubble_background_color
     *          interval
     *          showold
     *          streamid
     *          charlimit
     * @since 1.0
     * @author shannon
     */
    function liveStream_print_post_options($options) {
        $all_options = array(
            'datebordercolor' => 'date_border_color',
            'datebackgroundcolor' => 'date_background_color',
            'bubblebordercolor' => 'bubble_border_color',
            'bubblebackgroundcolor' => 'bubble_background_color',
            'interval' => 'interval',
            'showold' => 'showold',
            'streamid' => 'streamid',
            'charlimit' => 'charlimit',
        );
        foreach($all_options as $k => $o) {
            if($options[$k]) echo " $k='{$options[$k]}'";
            else if($options[$o]) echo " $k='{$options[$o]}'";
        }
    }
    
    /**
     * helper function that returns all the options that were posted via ajax
     *
     * @return options that were posted (array)
     *      currently only outputs the following options:
     *          datebordercolor
     *          datebackgroundcolor
     *          bubblebordercolor
     *          bubblebackgroundcolor
     *          interval
     *          showold
     *          streamid
     *          charlimit
     * @since 1.0
     * @author shannon
     */
    function liveStream_get_post_options() {
        $options = array();
        $all_options = array(
            'datebordercolor',
            'datebackgroundcolor',
            'bubblebordercolor',
            'bubblebackgroundcolor',
            'showold',
            'streamid',
            'charlimit',
        );
        foreach($all_options as $o)
            if($_POST[$o]) $options[$o] = $_POST[$o];
        return $options;
    }
    
    /**
     * helper function that handles all ajax requests, at the moment 4 
     * requests are handled
     *      - livestream_update 
     *          posting a live stream update via the livestream frontend form
     *      - livestream_fetchfeed
     *          fetching the latest feed or history
     *      - livestream_upload
     *          handling a livestream upload, outputs the base64 encoded list of attachments
     *      - livestream_bitly
     *          request to get bitly credentials, keep it seperate from the static html produced
     *
     * @since 1.0
     * @author shannon
     */
    function liveStream_ajax_handles() {
        $livestream_options = get_option('live_stream');
        $options = liveStream_get_post_options();
        
        if(isset($_POST['livestream_update'])) {
            liveStream_update_feed($_POST['livestream_update'], $_POST['attachment'], $options);
            liveStream_print_feed($_POST['feedcount'], $options);
            if(!$_POST['updatemessage'])
                $_POST['updatemessage'] = $livestream_options['social_network_update'];
            liveStream_update_social_networks(stripslashes($_POST['updatemessage']),$_POST['networks']);
            exit;
        }
        
        if(isset($_POST['livestream_fetchfeed'])) {
            if($_POST['showolder'] == 'true' || (liveStream_is_updated($_POST['livestream_fetchfeed']) && liveStream_last_update())) {
                $widgets = get_option('widget_livestream_widget');
                if($_POST['title'])
                    echo $_POST['title']; 
                else
                    echo $widgets[$_POST['widgetid']]['title']; 
                    
                if($_POST['showolder'] == 'true') {
                    if(!$options) $options = array();
                    $options['showolder'] = 'true';
                    $options['showold'] = $_POST['showold'];
                }
                    
                echo "<!-!>";
                echo liveStream_last_update();
                echo "<!-!>";
                liveStream_print_feed($_POST['feedcount'], $options);
                echo "<!-!>";
                liveStream_print_network_authed();
                echo "<!-!>";
                echo $options['showold'];
            } else {
                $widgets = get_option('widget_livestream_widget');
                $title = $_POST['title'];
                if($title)
                    echo $title; 
                else {
                    $title = $widgets[$_POST['widgetid']]['title']; 
                    if(!$title) $title = "Live Stream";
                    echo $title;
                }
                echo "<!-!>";
                liveStream_print_network_authed();
            }
            exit;
        }
        
        if(isset($_POST['livestream_upload'])) {
            $uploads = array();
            foreach($_FILES as $v) {
                $uploads[] = wp_upload_bits('tmp'.$v["name"], null, file_get_contents($v["tmp_name"]));
            }
            echo base64_encode(json_encode($uploads));
            exit;
        }
        
        if(isset($_GET['livestream_bitly'])) {
            echo stripslashes(json_encode(array(
                'api_key' => @$livestream_options['bitly_api_key'],
                'api_login' => @$livestream_options['bitly_api_login'],
            )));
            exit;
        }
    }
    add_action( 'init', 'liveStream_ajax_handles' );
    
    /**
     * helper function that prints out the network checkboxes below the live stream form
     *
     * @since 1.0
     * @author shannon
     */
    function liveStream_print_network_checkboxes() {
		 global $live_stream_social_network;
		 ?>
		            <div class="update_networks">
			            <div class="networks">
			                <div class="twitter network">
			                    <div class="icon <?php if(!$live_stream_social_network['twitter']->is_authed()): ?>noauth<?php endif; ?>"></div>
			                    <input type="checkbox" value="twitter" <?php if(!$live_stream_social_network['twitter']->is_authed()): ?>class="noauth"<?php endif; ?> />
			                    <div style="clear:both;"></div>
			                </div>
			            </div>
			            Update Twitter
			            <div style="clear:both;"></div>
			        </div>
		 <?php
    }
    
    /**
     * helper function that prints out a list of authorized networks, so if a network
     * is authorized on another page or in the admin the frontend won't nag for 
     * authorization
     *
     * @since 1.0
     * @author shannon
     */
    function liveStream_print_network_authed() {
		 global $live_stream_social_network;
		 $return = false;
		 foreach($live_stream_social_network as $k => $v) {
		    if($return) echo ',';
		    $return = true;
		    echo $k.':';
		    if(!$v->is_authed()) echo 'noauth';
		 }
    } 
    
    /**
     * a replacement function for file_get_contents that reads external urls via
     * cURL if necessary
     *
     * @param the url of content to read for (string)
     * @since 1.0
     * @author shannon
     */
    function liveStream_get_contents($url) {
        if(!function_exists('curl_init') && ini_get('allow_url_fopen'))
            throw "allow_url_fopen disabled and cURL extensions not installed";
        if(ini_get('allow_url_fopen')) :
            return file_get_contents($url);
        else:
            $ch = @curl_init ($url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
            $rawdata=curl_exec($ch);
            curl_close ($ch);
            return $rawdata;
        endif;
    }
    
    /**
     * a function that sets the default settings for live stream if none are saved. use
     * apply_filters('livestream_defaults', 'setting_name') to get the content and hook into
     * it to apply your own
     *
     * @param the url of content to read for (string)
     * @since 1.0
     * @author shannon
     */
    function liveStream_get_defaults($setting) {
        $livestream_options = get_option('live_stream');
        if($livestream_options[$setting])
            return $livestream_options[$setting];
        
        $defaults = array(
            'default_interval' => 60,
            'default_feedcount' => 3,
            'default_title' => 'Live Stream',
            'default_showold' => 'false',
            'default_charlimit' => isset($livestream_options[$setting]) ? $livestream_options[$setting] : 120
        );

        return @$defaults[$setting];
    }
    add_filter('livestream_defaults', 'liveStream_get_defaults');
    
    /**
     * function that handles the admin form saving
     *
     * @param the url of content to read for (string)
     * @since 1.0
     * @author shannon
     */
    function liveStream_global_settings_handle_forms() {
        if($_POST['live_stream_global_settings']) {
            $livestream_options = get_option('live_stream');
            $livestream_options['default_interval'] = $_POST['update_interval'];
            $livestream_options['default_title'] = $_POST['update_title'];
            $livestream_options['default_feedcount'] = $_POST['update_feedcount'];
            $livestream_options['default_charlimit'] = $_POST['update_charlimit'];
            $livestream_options['default_backgroundcolor'] = $_POST['update_backgroundcolor'];
            $livestream_options['default_bubblebackgroundcolor'] = $_POST['update_bubblebackgroundcolor'];
            $livestream_options['default_bubblebordercolor'] = $_POST['update_bubblebordercolor'];
            $livestream_options['default_datebackgroundcolor'] = $_POST['update_datebackgroundcolor'];
            $livestream_options['default_datebordercolor'] = $_POST['update_datebordercolor'];
            $livestream_options['default_fontcolor'] = $_POST['update_fontcolor'];
            $livestream_options['default_showold'] = $_POST['update_showold'];
            update_option('live_stream', $livestream_options);
        }
    }
    add_action('init', 'liveStream_global_settings_handle_forms');