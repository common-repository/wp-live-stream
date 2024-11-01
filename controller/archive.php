<?php

    function liveStream_archive_old($date) {
        global $post;
        query_posts('post_type=livestream&posts_per_page=-1');
        $multiple_archives = array();
        while(have_posts()): the_post();
	        if(strtotime($post->post_date) < strtotime($date)) {
	            $stream_id = get_post_meta($post->ID, 'live_stream_id', true);
	            
	            if(!$stream_id) $stream_id = 'main_livestream';
	            if(!isset($multiple_archives[$stream_id])) $multiple_archives[$stream_id] = '';
	            
	            $multiple_archives[$stream_id]['archive'] .= $post->post_date.': '.trim($post->post_title)."\n";
	            if(!$multiple_archives[$stream_id]['earliest_date']) {
	                $multiple_archives[$stream_id]['earliest_date'] = $multiple_archives[$stream_id]['last_date'] = strtotime($post->post_date);
	            }
	            if($multiple_archives[$stream_id]['earliest_date'] > strtotime($post->post_date))
	                $multiple_archives[$stream_id]['earliest_date'] = strtotime($post->post_date);
	            if($multiple_archives[$stream_id]['last_date'] < strtotime($post->post_date))
	                $multiple_archives[$stream_id]['last_date'] = strtotime($post->post_date);
	        }
        endwhile;
        wp_reset_query();
        foreach($multiple_archives as $k => $archive) :
            if(!$archive) return;
            
            $filename = "Live Stream Archive - ".date("Y-m-d H:i:s", $archive['earliest_date'])." to ".date("Y-m-d H:i:s", $archive['last_date']).".txt";
            if($k != 'main_livestream') $filename = '['.$k.'] '.$filename;
            $upload = wp_upload_bits($filename , null, $archive['archive']);
            $upload['realname'] = $filename;
            
            $livestream_options = get_option('live_stream');
            $archives = $livestream_options['archives'];
            $archives[] = $upload;
            $livestream_options['archives'] = $archives;
            update_option('live_stream', $livestream_options);
        endforeach;
    }
    
    function liveStream_clear($date) {
        query_posts('post_type=livestream&posts_per_page=-1');
        while(have_posts()): the_post();
	        if(strtotime($post->post_date) < strtotime($date)) {
	            wp_delete_post( get_the_ID(), true );
	        }
        endwhile;
        wp_reset_query();
    }
    
    function liveStream_archive_clear() {
        $livestream_options = get_option('live_stream');
        $archives = $livestream_options['archives'];
        foreach($archives as $a) {
            if(is_wp_error($a))
                continue;
            unlink($a['file']);
        }
        $livestream_options['archives'] = array();
        update_option('live_stream', $livestream_options);
    }
    
    add_action('init', 'liveStream_archive_handle_forms');
    function liveStream_archive_handle_forms() {
        if($_POST['live_stream_archive']) {
            if($_POST['submit'] == 'Archive Old')
                liveStream_archive_old($_POST['furthest_date']);
            liveStream_clear($_POST['furthest_date']);
        }    
        if($_POST['live_stream_archive_clear']) {
            liveStream_archive_clear();
        }
    }
    
