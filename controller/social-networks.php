<?php

    class SocialNetwork {
        function link() {
        
        }
        
        function unlink() {
        
        }
        
        function is_authed() {
            return false;
        }
        
        function username() {
        
        }
        
        function update($post) {
        
        }
    }

    function liveStream_register_social_network($name, $obj) {
        global $live_stream_social_network;
        $live_stream_social_network[$name] = $obj;
    }
    
    function liveStream_update_social_networks($update, $includestr = NULL) {
        global $live_stream_social_network;
        foreach ($live_stream_social_network as $k => $v) {
            if(strpos($includestr, $k) !== false)
                $v->update($update);
        }
    }

    include_once "social-networks/twitter.php";
    
    add_action('init', 'liveStream_social_network_handle_forms');
    function liveStream_social_network_handle_forms() {
        if($_POST['live_stream_social_network_message']) {
            $livestream_options = get_option('live_stream');
            $livestream_options['social_network_update'] = $_POST['update_message'];
            update_option('live_stream', $livestream_options);
        }
    }
?>
