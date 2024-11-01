<?php
    add_action('init', 'liveStream_url_shortening_handle_forms');
    function liveStream_url_shortening_handle_forms() {
        if($_POST['live_stream_url_shortening']) {
            $livestream_options = get_option('live_stream');
            $livestream_options['bitly_api_login'] = $_POST['bitly_api_login'];
            $livestream_options['bitly_api_key'] = $_POST['bitly_api_key'];
            update_option('live_stream', $livestream_options);  
        }    
    }
    
    function _livestream_url_shorten_cb($matches) {
        $url = $matches[2];
        $prefix = '';
        
        $url = esc_url($url);
        if ( empty($url) )
            return $matches[0];
        
        global $bitly_key, $bitly_login;
        if(strlen($url) > 20) :
            $rawdata = liveStream_get_contents ("http://api.bitly.com/v3/shorten?longUrl=".urlencode($url)."&login=".$bitly_login."&apiKey=".$bitly_key."&callback=?");
            
            $json = @json_decode($rawdata);
            if(!$json)
                $json = @json_decode(substr($rawdata,2,strlen($rawdata)-4));

            if($json)
                $url = $json->data->url;
        endif;
            
        return $matches[1].$url;
    }
    
    function _livestream_url_shorten_from_text($ret) {
        $ret = ' ' . $ret;
        // in testing, using arrays here was found to be faster
        $save = @ini_set('pcre.recursion_limit', 10000);
        $retval = preg_replace_callback('#(?<!=[\'"])(?<=[*\')+.,;:!&$\s>])(\()?([\w]+?://(?:[\w\\x80-\\xff\#%~/?@\[\]-]{1,2000}|[\'*(+.,;:!=&$](?![\b\)]|(\))?([\s]|$))|(?(1)\)(?![\s<.,;:]|$)|\)))+)#is', '_livestream_url_shorten_cb', $ret);
        if (null !== $retval )
            $ret = $retval;
        @ini_set('pcre.recursion_limit', $save);
        $ret = preg_replace_callback('#([\s>])((www)\.[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]+)#is', '_livestream_url_shorten_cb', $ret);
        //$ret = preg_replace_callback('#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})#i', '_make_email_clickable_cb', $ret);
        // this one is not in an array because we need it to run last, for cleanup of accidental links within links
        $ret = preg_replace("#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i", "$1$3</a>", $ret);
        $ret = trim($ret);
        return $ret;
	}
    
    add_filter('livestream_insert', 'liveStream_shorten_urls');
    function liveStream_shorten_urls($feed) {
        global $bitly_key, $bitly_login;
        $livestream_options = get_option('live_stream');
        $bitly_key = @$livestream_options['bitly_api_key'];
        $bitly_login = @$livestream_options['bitly_api_login'];
        
        if(!$bitly_key || !$bitly_login) return $feed;
        if(is_object($feed))
            $feed->post_title = _livestream_url_shorten_from_text($feed->post_title);
        if(is_array($feed))
            $feed['post_title'] = _livestream_url_shorten_from_text($feed['post_title']);
        return $feed;
    }
    
    add_action( 'save_post', 'liveStream_shorten_urls_on_save', 10, 2 );
    function liveStream_shorten_urls_on_save( $post_id, $post) {
        global $livestream_in_short;
        $post = (object)$post;
        if($post->post_status != 'publish' || $livestream_in_short) return;
        $post = liveStream_shorten_urls($post);
        $livestream_in_short = true;
        wp_update_post($post);
        $livestream_in_short = false;
    }
?>
