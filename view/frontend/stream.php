<?php
    //! @file: the frontend overview file
    
    add_action('wp_head', 'liveStream_frontend_css');
    function liveStream_frontend_css() {
?>        
<link rel="stylesheet" type="text/css" media="all" href='<?php echo LS_URL; ?>/view/frontend/css/stream.css' />
<?php
    }
    
    add_action('wp_head', 'liveStream_frontend_js');
    function liveStream_frontend_js() {
    
    $max_upload = (int)(ini_get('upload_max_filesize'));
    $max_post = (int)(ini_get('post_max_size'));
    $memory_limit = (int)(ini_get('memory_limit'));
    $upload_mb = min($max_upload, $max_post, $memory_limit);
?>        
<script type='text/javascript' src='<?php echo LS_URL; ?>/view/frontend/js/stream.js'></script>
<script>
    var livestream_url = "<?php echo LS_URL; ?>";
    var livestream_site_url = "<?php echo site_url(); ?>";
    var livestream_max_upload = "<?php echo $upload_mb; ?>";
</script>
<?php
    }
    
    add_action( 'wp_enqueue_scripts',  'liveStream_frontend_scripts' );
    function liveStream_frontend_scripts() {
        wp_enqueue_script( "slimScroll", LS_URL."/view/frontend/js/slimScroll.min.js", array('jquery','jquery-ui-core','jquery-ui-draggable') );
        wp_enqueue_script("jquery");
        wp_enqueue_script("jquery-ui-core");
        wp_enqueue_script("jquery-ui-draggable");
        wp_enqueue_script('swfupload');
        wp_enqueue_script('swfupload-swfobject');
        wp_enqueue_script('swfupload-queue');
        //wp_enqueue_script('swfupload-handlers');
    }
    
    function liveStream_swfupload_render() {
        global $swfs;
        ++$swfs;
        ?>
        <div class="media-upload">
            <input type="hidden" name="attachment" value="" />
            <div class="swfupload-progress" id="swfupload-progress-<?php echo $swfs; ?>">0%</div>
            <span class="swfupload-placeholder" id="swfupload-placeholder-<?php echo $swfs; ?>"></span>
            <div class="deattachment-placeholder" id="deattachment-placeholder-<?php echo $swfs; ?>" title="Deattach Media"></div>
            <div style="clear:both;"></div>
            <!--<a href="#" onclick="return false;">
	            <img src="<?php echo site_url(); ?>/wp-admin/images/media-button.png" width="15" height="15">
	        </a>-->
        </div>
        <?php
    }
