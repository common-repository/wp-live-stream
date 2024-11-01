<?php
    //! @file: this part enables all admin functionality

	/**
	 * enqueue scripts and css styles for the admin section
	 *
	 * @since 1.0
	 * @author shannon
	 */
    function liveStream_enqueue_scripts() {
        if( is_admin() ) {
            wp_deregister_script( 'farbtastic' );
            wp_register_script( 'farbtastic', LS_URL.'/view/admin/js/farbtastic.js');
            wp_enqueue_script( 'farbtastic' );
            
            
            //wp_deregister_script( 'jquery-ui' );
            //wp_register_script( 'jquery-ui', LS_URL.'/view/admin/js/jquery-ui-1.8.18.custom.min.js');
            wp_enqueue_script( 'jquery-ui' );
            
            wp_deregister_style( 'farbtastic' );
            wp_register_style( 'farbtastic', LS_URL.'/view/admin/css/farbtastic.css');
            wp_enqueue_style( 'farbtastic' );
            
            //wp_deregister_style( 'jquery-ui' );
            //wp_register_style( 'jquery-ui', LS_URL.'/view/admin/css/redmond/jquery-ui-1.8.18.custom.css');
            wp_enqueue_style( 'jquery-ui' );
        }
    }
    add_action('admin_enqueue_scripts', 'liveStream_enqueue_scripts');
    
    /**
	 * adds javascript variables that the livestream scripts depend on
	 *
	 * @since 1.0
	 * @author shannon
	 */
    function liveStream_add_js() {
        
        global $post;
        
        if($post->post_type != 'livestream' && $_GET['post_type'] != 'livestream' && strpos($_SERVER['PHP_SELF'], '/widgets.php') === false && $_GET['page'] != 'live-stream') return;
?>        
<script type='text/javascript' src='<?php echo LS_URL; ?>/view/admin/js/stream.js'></script>
<script>
    var livestream_url = "<?php echo LS_URL; ?>";
    var livestream_site_url = "<?php echo site_url(); ?>";
    var livestream_max_lengths = {
    	"default" : "<?php echo apply_filters('livestream_defaults', 'default_charlimit'); ?>"
    };
</script>
<?php
    }
    add_action('admin_head', 'liveStream_add_js');
    
    
    /**
	 * register the admin options page
	 *
	 * @since 1.0
	 * @author shannon
	 */
    function liveStream_admin_options() {
	    add_options_page('Live Stream', 'Live Stream', 'manage_options', 'live-stream', 'liveStream_admin_options_page');
    }
    add_action('admin_menu', 'liveStream_admin_options');
    
    /**
	 * display the options page. sections are divided by tabs.
	 * Tabs:
	 *		- main : main tab, basically shows the shortcode 
	 *		- global : tab that allows admin to adjust default live stream settings
	 *		- social network : tab that allows admin to connect live stream to their 
	 *			social network
	 *		- url shortening : tab that allows the admin to supply url shortening 
	 *			api credentials such as bit.ly
	 *		- archives : tab that allows the admin to archive live streams / clear 
	 *			archives
	 * options are stored under get_option('live_stream')
	 * @see liveStream_global_settings_handle_forms() at /controller/stream.php to see 
	 *	adjust the form handling
	 * 
	 *
	 * @since 1.0
	 * @author shannon
	 */
    function liveStream_admin_options_page() {
        $livestream_options = get_option('live_stream');
?>
<div class="wrap">
<div id="icon-options-general" class="icon32"><br></div>
<h2 class="nav-tab-wrapper">
<a href="#main" onclick="return liveStream_tab(this)" class="nav-tab nav-tab-active" id="main">Live Stream</a>
<a href="#global" onclick="return liveStream_tab(this)" class="nav-tab" id="global">Global Settings</a>
<a href="#social" onclick="return liveStream_tab(this)" class="nav-tab" id="social">Social Networks</a>
<a href="#url" onclick="return liveStream_tab(this)" class="nav-tab" id="url">URL Shortening</a>
<a href="#archive" onclick="return liveStream_tab(this)" class="nav-tab" id="archive">Archives</a>
</h2>
<div class="tab" id="tab-main">
<h3>Shortcodes</h3>
<p>Copy and paste the following shortcodes into your pages to add a stream inside your article or below your pages:</p>
<p>
    <code>[livestream]</code> This adds the live stream with its default settings.<br>
    <code>[livestream streamid="Some Stream"]</code> This allows you to create a live stream seperate from the main live stream.<br>
    <code>[livestream title="My Feed"]</code> This adds the live stream with mostly default settings except the title will be "My Feed".<br>
    <code>[livestream feedcount=4]</code> This adds the live stream with mostly default settings except it should show the latest 4 feeds.<br>
    <code>[livestream charlimit=120]</code> This limits how long the feeds can be.<br>
    <code>[livestream width=200]</code> This adds the live stream with mostly default settings except it should 200px wide.<br>
    <code>[livestream credit=0]</code> This adds the live stream with mostly default settings except it should show no eFrog Digital Design credit.<br>
    <code>[livestream fontcolor=#600]</code> This adds the live stream with mostly default settings except the font color should be #600.<br>
    <code>[livestream backgroundcolor=#600]</code> This adds the live stream with mostly default settings except the background color should be #600.<br>
    <code>[livestream bubblebackgroundcolor=#600]</code> This adds the live stream with mostly default settings except the background color of each feed should be #600.<br>
    <code>[livestream bubblebordercolor=#600]</code> This adds the live stream with mostly default settings except the border color of each feed should be #600.<br>
    <code>[livestream datebackgroundcolor=#600]</code> This adds the live stream with mostly default settings except the background color of each feed's date should be #600.<br>
    <code>[livestream datebordercolor=#600]</code> This adds the live stream with mostly default settings except the border color of each feed's date should be #600.<br>
    <code>[livestream socialmessage="I just updated my Live Stream at URL #livestream"]</code> This adds the live stream with mostly default settings except making any social broadcasts during an update (e.g Twitter post) post this message.  The word URL will substitute for the address of the post / page.<br>
</p>
<p>Feel free to use as many of the settings in one short code param like <code>[livestream bubblebordercolor=#600 feedcount=4 /]</code></p>

</div>
<div class="tab" id="tab-global" style="display:none;min-height:200px;">
<h3>Global Settings</h3>
<p>Here are all the settings you can change that effect the defaults of all livestream widgets where your own custom options aren't set.</p>
<form name="form" action="" method="post">
 <input type="hidden" name="live_stream_global_settings" value=1 />
<table class="form-table">
	<tbody>
	<tr>
		<th><label for="update_interval">Update Interval</label></th>
		<td>
		    <input name="update_interval" id="update_interval" type="text" value="<?php echo apply_filters('livestream_defaults', 'default_interval'); ?>" class="regular-text code"><br><em>This is the amount of seconds between each refresh so that your viewers stream can retrieve the latest feeds. (Note: For high traffic sites if the interval is too short then you may experience high server loads.)</em>
		</td>
	</tr>
	<tr>
		<th><label for="update_title">Title</label></th>
		<td>
		    <input name="update_title" id="update_title" type="text" value="<?php echo apply_filters('livestream_defaults', 'default_title'); ?>" class="regular-text code"><br><em>This is the default title of all your widgets or shortcoded Live Streams.</em>
		</td>
	</tr>
	<tr>
		<th><label for="update_feedcount">Feedcount</label></th>
		<td>
		    <input name="update_feedcount" id="update_feedcount" type="text" value="<?php echo apply_filters('livestream_defaults', 'default_feedcount'); ?>" class="regular-text code"><br><em>This is the default feedcount of all your widgets or shortcoded Live Streams.</em>
		</td>
	</tr>
	<tr>
		<th><label for="update_charlimit">Character Limit</label></th>
		<td>
		    <input name="update_charlimit" id="update_charlimit" type="text" value="<?php echo apply_filters('livestream_defaults', 'default_charlimit'); ?>" class="regular-text code"><br><em>This is the default character limit for all your streams.</em> <code>Leave blank for no limit</code>
		</td>
	</tr>
	<tr>
		<th><label for="update_backgroundcolor">Background Color</label></th>
		<td>
		    <input class="farbtastic-input" name="update_backgroundcolor" id="update_backgroundcolor" type="text" value="<?php echo apply_filters('livestream_defaults', 'default_backgroundcolor'); ?>" class="regular-text code"><br><em>This is the default background color of all your widgets or shortcoded Live Streams.</em>
		    <div class="farbtastic-picker" rel="update_backgroundcolor"></div>
		</td>
	</tr>
	<tr>
		<th><label for="update_bubblebackgroundcolor">Bubble Background Color</label></th>
		<td>
		    <input class="farbtastic-input" name="update_bubblebackgroundcolor" id="update_bubblebackgroundcolor" type="text" value="<?php echo apply_filters('livestream_defaults', 'default_bubblebackgroundcolor'); ?>" class="regular-text code"><br><em>This is the default background color of the Live Stream widget's bubbles of all your widgets or shortcoded Live Streams.</em>
		    <div class="farbtastic-picker" rel="update_bubblebackgroundcolor"></div>
		</td>
	</tr>
	<tr>
		<th><label for="update_bubblebordercolor">Bubble Border Color</label></th>
		<td>
		    <input class="farbtastic-input" name="update_bubblebordercolor" id="update_bubblebordercolor" type="text" value="<?php echo apply_filters('livestream_defaults', 'default_bubblebordercolor'); ?>" class="regular-text code"><br><em>This is the default border color of the Live Stream widget's bubbles of all your widgets or shortcoded Live Streams.</em>
		    <div class="farbtastic-picker" rel="update_bubblebordercolor"></div>
		</td>
	</tr>
	<tr>
		<th><label for="update_datebackgroundcolor">Date Background Color</label></th>
		<td>
		    <input class="farbtastic-input" name="update_datebackgroundcolor" id="update_datebackgroundcolor" type="text" value="<?php echo apply_filters('livestream_defaults', 'default_datebackgroundcolor'); ?>" class="regular-text code"><br><em>This is the default background color of the Live Stream widget's date areas of all your widgets or shortcoded Live Streams.</em>
		    <div class="farbtastic-picker" rel="update_datebackgroundcolor"></div>
		</td>
	</tr>
	<tr>
		<th><label for="update_datebordercolor">Date Border Color</label></th>
		<td>
		    <input class="farbtastic-input" name="update_datebordercolor" id="update_datebordercolor" type="text" value="<?php echo apply_filters('livestream_defaults', 'default_datebordercolor'); ?>" class="regular-text code"><br><em>This is the default border color of the Live Stream widget's date areas of all your widgets or shortcoded Live Streams.</em>
		    <div class="farbtastic-picker" rel="update_datebordercolor"></div>
		</td>
	</tr>
	<tr>
		<th><label for="update_fontcolor">Font Color</label></th>
		<td>
		    <input class="farbtastic-input" name="update_fontcolor" id="update_fontcolor" type="text" value="<?php echo apply_filters('livestream_defaults', 'default_fontcolor'); ?>" class="regular-text code"><br><em>This is the default font color of all your widgets or shortcoded Live Streams</em>
		    <div class="farbtastic-picker" rel="update_fontcolor"></div>
		</td>
	</tr>
	</tbody></table>
	<script>//reset_farbtastic();</script>
<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="Update Message"></p>  </form>
</div>
<div class="tab" id="tab-social" style="display:none;min-height:200px;">
<h3>Social Networks</h3>
<p>This section is used for linking Live Stream to your social networks so that you can notify your followers when you update your Live Stream. <em>Note: Live Stream will only notify the networks that you tick before each update.</em></p>

<?php 
    global $live_stream_social_network;
?>
<table class="form-table">
	<tbody><tr>
		<th><label for="twitter">Twitter</label></th>
		<td>
		<?php
		    if($live_stream_social_network['twitter']->is_authed()):
		?>
		    <input name="twitter" id="twitter" type="text" value="<?php echo $live_stream_social_network['twitter']->username(); ?>" class="regular-text code" readonly>
		    <a href="<?php echo $live_stream_social_network['twitter']->unlink(); ?>">Click here</a> to unlink your account
		<?php
		    else:  
		?>
		    <a href="<?php echo $live_stream_social_network['twitter']->link(); ?>">Click here</a> to link your account
		<?php
		    endif;
		?>
		</td>
	</tr>
	</tbody></table>
<?php 
    $social_network_update = $livestream_options['social_network_update'];
    if(!strlen(trim($social_network_update))) {
        $social_network_update = $livestream_options['social_network_update'] = 'I just updated my Live Stream at '.get_option('siteurl').' #livestream';
        update_option('live_stream', $livestream_options);        
    }
?>
<p>Change the message to be sent to your networks for each update.</em></p>
<form name="form" action="" method="post">
 <input type="hidden" name="live_stream_social_network_message" value=1 />
<table class="form-table">
	<tbody><tr>
		<th><label for="update_message">Update Message</label></th>
		<td>
		    <input name="update_message" id="update_message" type="text" value="<?php echo $social_network_update; ?>" class="regular-text code">
		</td>
	</tr>
	</tbody></table>
<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="Update Message"></p>  </form>
</div>
<div class="tab" id="tab-url" style="display:none;">
<h3>URL Shortening</h3>
<p>Setup your bit.ly settings if you wish to enable URL shortening. You can get your API details at <a href="http://bitly.com/a/your_api_key" target="_blank">http://bitly.com/a/your_api_key</a>.</p>
<form name="form" action="" method="post">
 <input type="hidden" name="live_stream_url_shortening" value=1 />
<table class="form-table">
	<tbody><tr>
		<th><label for="bitly_api_login">Bit.ly API Login</label></th>
		<td>
		    <input name="bitly_api_login" id="bitly_api_login" type="text" value="<?php echo $livestream_options['bitly_api_login']; ?>" class="regular-text code">
		</td>
	</tr><tr>
		<th><label for="bitly_api_key">Bit.ly API Key</label></th>
		<td>
		    <input name="bitly_api_key" id="bitly_api_key" type="text" value="<?php echo $livestream_options['bitly_api_key']; ?>" class="regular-text code">
		</td>
	</tr>
	</tbody></table>
<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="Update URL Shortening"></p>  </form>
</div>
<div class="tab" id="tab-archive" style="display:none;">
<h3>Archive Your Old Streams</h3>
<form name="form" action="" method="post">
 <input type="hidden" name="live_stream_archive" value=1 />
<p>This form allows you to archive all your old streams or clear them. Just select the furthest date you want to keep and click the appropriate button.</p>

    <table class="form-table">
	<tbody><tr>
		<th><label for="furthest_date">Furthest Date</label></th>
		<td> <input autocomplete="off" name="furthest_date" id="furthest_date" type="text" value="<?php echo date("Y-m-d", time()-24*3600*7); ?>" class="regular-text code"></td>
	</tr>
	</tbody></table>
	
<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="Archive Old"><input type="submit" name="submit" id="submit" class="button-primary" value="Clear Old"></p>  </form>
<h3>Stored Archives</h3>
<form name="form" action="" method="post">
 <input type="hidden" name="live_stream_archive_clear" value=1 />
<ul>
    <?php 
        $archives = $livestream_options['archives'];
        if(!count($archives)) echo "<li>No Archives</li>";
        else
        foreach($archives as $a) {
            if(is_wp_error($a) || !$a['realname'])
                continue;
    ?>
    <li><a href="<?php echo $a['url']; ?>"><?php echo $a['realname']; ?></a></li>
    <?php 
        }
    ?>
</ul>
<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="Clear Archives"></p>  </form>
</div>
<br/>
<br/>
<p><em><a href="http://wordpress.org/extend/plugins/wp-live-stream/" target="_blank">Live Stream</a> by <a href="http://www.efrogthemes.com/" target="_blank">eFrog Digital Design</a></em>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="XHKEEB3K9SPWN">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>

</p>
</div>

<script>
    jQuery( "#furthest_date" ).datepicker({ dateFormat: 'yy-mm-dd' }); 
</script>
<?php
    }
