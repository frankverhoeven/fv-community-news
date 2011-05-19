=== FV Community News ===

Contributors:		frankverhoeven
Donate link:		https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=SB62B7H867Y4C&lc=US&item_name=Frank%20Verhoeven&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_LG%2egif%3aNonHosted
Tags:			Community, News, Widget, Custom Posting, Post, Form, Akismet, Ajax
Requires at least:	3.0
Tested up to:		3.1.2
Stable tag:		2.0.2

Give the visitors of your site the ability to submit their news to you, and list it in a nice news feed.



== Description ==

Many blogs out there can only produce a couple articles a week while their visitors
ask for loads more. It sometimes is a good idea to add related articles from other
blogs to your sidebar. Adding them manually takes lots of time we don't have. With
this Community News plugin you allow your visitors to add to your blog. Complete with
moderation panel and a settings page, you can sit back while you have full control
about the look and articles that being posted.


= Features =

Currently, the Community News plugin has the following features:

* Widget Ready (Different form/results widget)
* Tags for use in posts/pages
* AJAX Form Handling
* Multi-Language Support
* Moderation panel (with email option) - Make sure you approve the right articles.
* Edit Submissions
* "My Community News" Page - Allow registered users to view their submitted articles.
* Customizable templates
* Akismet Spam protection (API key required)
* Build-in RSS 2.0 Feed
* Settings page
* Uninstallation - Not the right plugin for you? You can easily remove it completely.



== Installation ==

This section describes how to install the plugin and get it working.


= Requirements =

In order to successfully use this plugin, you will need the following:

* PHP 5 or higher
* WordPress 3.0 or higher


= Installation Steps =

1. Download the files
1. Upload the files to `/wp-content/plugins/`
1. Activate the plugin (WP-admin > Plugins > FV Community News)
1. Browse Community News > Settings to select the settings you prefer.
1. Go to Design > Widgets to add the Community News widgets to your sidebar; or use the tags in your post/page.



== Screenshots ==

1. The Moderation page, 100% integrated with WordPress.
2. Edit Community News, just like a comment.
3. The plugin has a build-in widget to moderate the articles right on your dashboard.
4. Settings page.
5. Uninstalling is just as easy as installing.
6. The plugin in action on DivitoDesign.com.



== Custom Templates ==

Every blog out there has different requests and needs. With version 2.0 and up, the plugin supports custom
templates so it can meet all of those needs. Creating your custom templates is simple:

1. First go to the plugin directory, usually `/wp-content/plugins/fv-community-news/`.
1. Copy the `Template` directory.
1. Go to your themes directory, usually `/wp-content/themes/`.
1. Paste the `Template` directory here, and rename it to `fvcn`
1. You can start editing the template files, located in the `fvcn` directory, now.



== Template Tags ==

With version 2.0 and up, the plugin has build-in template tags, very similar to the WordPress ones you
use in your themes. You can use these functions to customize the default templates, or even create your
own unique templates.


= Post Functions =

These functions can be used to render anything related to a submitted post.

`fvcn_has_posts($args)`

`fvcn_posts()`

`fvcn_the_post()`

`fvcn_get_post_id()`

`fvcn_post_id()`

`fvcn_get_post_author()`

`fvcn_post_author()`

`fvcn_get_post_author_email()`

`fvcn_post_author_email()`

`fvcn_get_post_status()`

`fvcn_post_status()`

`fvcn_get_post_author_ip()`

`fvcn_post_author_ip()`

`fvcn_get_post_title()`

`fvcn_post_title()`

`fvcn_get_post_content()`

`fvcn_post_content()`

`fvcn_get_post_excerpt()`

`fvcn_post_excerpt()`

`fvcn_get_post_url()`

`fvcn_has_post_url()`

`fvcn_post_url()`

`fvcn_get_post_link($text)`

`fvcn_post_link($text)`

`fvcn_get_post_title_link()`

`fvcn_get_post_date($format)`

`fvcn_post_date($format)`

`fvcn_get_post_views()`

`fvcn_post_views()`

`fvcn_get_post_approved()`

`fvcn_post_approved()`

`fvcn_get_post_approve_link()`

`fvcn_post_approve_link()`

`fvcn_get_post_unapprove_link()`

`fvcn_post_unapprove_link()`

`fvcn_get_post_edit_link()`

`fvcn_post_edit_link()`

`fvcn_get_post_spam_link()`

`fvcn_post_spam_link()`

`fvcn_get_post_unspam_link()`

`fvcn_post_unspam_link()`

`fvcn_get_post_delete_link()`

`fvcn_post_delete_link()`


= Form Functions =

Use these functions to create the submission form.

`fvcn_get_form()`

`fvcn_form()`

`fvcn_get_form_message()`

`fvcn_form_message()`

`fvcn_form_processed()`


= Render Functions =

These functions render a complete section.

`fvcn_list_posts()`

`fvcn_post_archives()`


= Various Functions =

Other functions.

`fvcn_get_rss_url()`

`fvcn_rss_url()`



== Changelog ==

For more details on changes, please visit the [WordPress Trac](http://plugins.trac.wordpress.org/log/fv-community-news/ "FV Community News on WordPress Trac").


= 2.0.2 =

* Removed alnum validator on the title field.


= 2.0.1 =

* Fixed a bug that prevented the use of custom templates.


= 2.0 =

* Complete revision of the plugin.










