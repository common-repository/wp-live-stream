<?php
	//! @file file adds various mail support for live stream

	/**
     * adds wp-mail support for live stream
     *
     * @since 1.0
     * @author shannon
     */
    function liveStream_mail_stream($post_id) {
        $post = get_post($post_id, ARRAY_A);
        if(strpos($post['post_title'], '#livestream') !== false) {
            $post['post_title'] = trim(str_replace('#livestream','',$post['post_title']));
            if(!strlen($post['post_title']))
                $post['post_title'] = trim($post['post_content']);
            $post['post_name'] = sanitize_title($post['post_title']);
            $post['post_type'] = 'livestream';
        } else return;
         wp_update_post( $post );
    }
    add_action('publish_phone', 'liveStream_mail_stream');
    
    /**
     * adds postie mail support for live stream
     *
     * @since 1.0
     * @author shannon
     */
    function liveStream_postie_post($post) {
        if(strpos($post['post_title'], '#livestream') !== false) {
            $post['post_title'] = trim(str_replace('#livestream','',$post['post_title']));
            if(!strlen($post['post_title']))
                $post['post_title'] = trim(strip_tags($post['post_content']));
            $post['post_name'] = sanitize_title($post['post_title']);
            $post['post_type'] = 'livestream';
            $post['post_content'] = '';
        } 
        return $post;
    }
    add_filter('postie_post', 'liveStream_postie_post');
?>