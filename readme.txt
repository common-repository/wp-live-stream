=== Plugin Name ===
Contributors: efrog-themes
Donate link: efrogthemes.com
Tags: live stream, events, realtime, streaming, updates, widget, shortcode, blogging, email
Requires at least: 3.1
Tested up to: 3.5.1
Stable tag: 1.3

The WP Live Stream plugin provides a widget for live or event blogging updates into a Twitter-like stream on your WordPress website.

== Description ==

The WP Live Stream plugin provides a widget for posting updates into a Twitter-like stream on your WordPress website. It allows you to keep the updates separate from your blog content, and your Twitter timeline un-cluttered.

It’s dead simple to use, and we’ve made it highly configurable. It even has automagical real-time refresh. You post an update, and people following the event on your blog see the update refresh automatically. We’ve even built in support for short codes with configuration settings, so that you can place the widget on a WordPress page if you prefer.

Here’s a listing of the awesome features that we’ve built in to WP Live Stream:

*   Twitter Notification – Connect your Twitter account to WP Live Stream and send a tweet out with a link to your live stream when you start live blogging.
*   Clear & Archive – Clear your database of previous tweets or archive them.
*   Widgetized - Place your Live Stream anywhere on your blog that is widget-ready.
*   Shortcodes - Configure WP Live Stream on a dedicated page on your website.
*   Customize - Choose font colours, widths, background colours, update colours and much more.
*   Rich media - Attach images from your computer or by placing a image URL in your stream
*   URL Shortening - Using your bitly credentials you can now utilize URL shortening for URL's longer than 20 characters as you type
*   Live Updates & Simple Resource Management - manage how often your users live streams are updated in realtime to find a balance between server load and keeping their feeds current
*   View Older - Allow your visitors to browse the streams history all the way back to when it began right inside the stream
*   Multiple Streams - Want to store or display multiple streams / event streams? Now just give your stream a unique ID of your choice and keep your streams seperate
*   Email Streaming - Email from your phone to your wordpress blogging address with "#livestream" in the subject line and stream from wherever you are

== Installation ==

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Under the 'Settings' menu in WordPress will be a new menu called 'Live Stream'. Find out how to use shortcodes there.
4. Under the 'Appearances' menu in WordPress view the 'Widgets' page and add a 'WP Live Stream' widget to any sidebar.

== Frequently Asked Questions ==

No FAQ available

== Screenshots ==

1. The default Live Stream without customized options.

== Changelog ==

= 1.3 =
* Add ability to donate towards the development of WP Live Stream
* Email to livestream via built-in wordpress Post-to-Email setup
* Email to livestream via postie plugin - supports image attachments
* Add ability to view older entries
* Add ability to make seperate streams
* Add ability to customize how many characters in each stream / stream input
* [Fix] external images use cURL to get image width if allow_url_fopen disabled
* Increased code documentation and seperating the shortcode code into a frontend view script
* [Fix] no longer pulls in own jquery-ui for wp 3.5+ compatibility
* [Fix] allow the frontend to resize the textarea

= 1.2 =
* Improved admin settings
* Option to increase intervals to improve site traffic
* Add external media (images) via URL's
* Edit default settings in the "GLobal Settings" admin section
* [Fix] Firefox twitter authentication popup in the admin

= 1.1 =
* URL Shortening via bitly
* Attach images to your live streams
* [experimental] Email to livestream via built-in wordpress Post-to-Email setup
* [fix] IE issues and styling
* [fix] FireFox text area scrolling issue when you type

= 1.0 =
* Release

== Upgrade Notice ==

= 1.0 =
* Release
