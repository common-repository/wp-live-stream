<?php
/**
 * @package WP Live Stream
 * @version 1.2.4.4
 */
/*
Plugin Name: WP Live Stream
Plugin URI: http://www.efrogthemes.com/wordpress-plugins/wp-live-stream/
Description: Add a widget that allows the wp-admin to create a twitter like stream just for the site via the Stream post type.
Author: eFrog Digital Design
Author URI: http://www.efrogthemes.co.za
Version: 1.2.4.4
Tested up to: 3.5.1
*/

define(LS_URL, plugins_url('',__FILE__));

include "controller/stream.php";
include "controller/archive.php";
include "controller/mail.php";
include "controller/social-networks.php";
include "controller/url-shortening.php";

include "view/admin/stream.php";
include "view/frontend/stream.php";
include "view/frontend/shortcodes/stream.php";
include "view/frontend/widgets/stream.php";

?>
