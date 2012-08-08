=== FV Community News ===

Contributors:		frankverhoeven
Donate link:		https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=SB62B7H867Y4C&lc=US&item_name=Frank%20Verhoeven&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_LG%2egif%3aNonHosted
Tags:				Community News, Community, News, Widget, Custom Post, Custom Posting, Post, Form, Akismet, Ajax
Requires at least:	3.3
Tested up to:		3.4
Stable tag:			3.0.2

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

* Custom post type, with featured images (thumbnails) and tags support.
* Widget support, with three different widgets to display posts, the post form or a tag cloud.
* Shortcodes to add a list of recent posts, the post form or a tag cloud to a page.
* Moderation page, with the ability to modify posts.
* Akismet spam protection.
* Settings page, including moderation and mail notification options.
* Customisable post form.
* AJAX form handling.
* Multi-language support.
* Seamless integration with your current theme (knowledge of WordPress theming required).



== Installation ==

This section describes how to install the plugin and get it up & running.


= Requirements =

In order to successfully use this plugin, you will need the following:

* PHP 5 or higher
* WordPress 3.3 or higher


= Installation Steps =

1. Download the files.
1. Upload the files to `/wp-content/plugins/`.
1. Activate the plugin (WP-admin > Plugins > FV Community News).
1. Enable the widgets, or use the shortcodes.


= Widgets =

There are three different widgets available:

* Form: Displays the form to submit a post.
* Posts: Displays a list of the most recent approved posts.
* Tag Cloud: Displays a tag cloud of community post tags.


= Shortcodes =

Shortcodes can be used in a regular post or page. There are three different
shortcodes available:

* `[fvcn-post-form]`: Displays the form to submit a post.
* `[fvcn-recent-posts]`: Displays a list of the most recent approved posts.
* `[fvcn-tag-cloud]`: Displays a tag cloud of community post tags.


= Theme Integration =

It is possible to seamlessly integrate the plugin with your current theme. To do this
follow the next steps:

1. Copy the contents of `/fv-community-news/fvcn-theme/` to the directory of your current theme.
1. Modify the files you just copied to match your theme. By default the files are setup for the default WordPress theme (Twenty Eleven).

And thats all! The plugin will automatically detect the new files and use those instead
of the default files.


= Updating From Version 2 =

Version 2 and older are not compatible with version 3 and later. To convert your posts
to work with the new version, download the [FV Community News Upgrader](http://www.frank-verhoeven.com/fv-community-news-upgrader/ "FV Community News Upgrader").

If you're still using version 1.x, you will have to update to version 2 first.



== Screenshots ==

1. The Moderation page, 100% integrated with WordPress.
2. Edit Community News, just like any other post.
3. Moderate the latest posts right on your dashboard.
4. General settings page.
5. Customise the form.
6. The post form on DivitoDesign.com



== FAQ ==

Below is a list of frequently asked questions.


= The plugin always gives a timeout error =

You probably have a caching plugin installed. The easiest way to fix this is by creating
a page for the post form and exclude it from the cache.



== Changelog ==

For more details on changes, please visit the [WordPress Trac](http://plugins.trac.wordpress.org/log/fv-community-news/ "FV Community News on WordPress Trac").


= 3.0.2 =

* Fixed featured image metabox on normal posts/pages.
* Fixed non latin text validation.
* Improved theme compatibility.
* Other minor improvements.


= 3.0.1 =

* Fixed an issue that caused post thumbnails to display twice on certain themes.


= 3.0 =

3.0 is a complete rewrite of the plugin. It now uses  the custom post type, and is NOT backwards
compatible with version 2.x or 1.x!

* Conversion to the custom post type.


