=== FV Community News ===

Contributors:       frankverhoeven
Donate link:        https://www.paypal.me/FrankVerhoeven
Tags:               Community News, Community, News, Widget, Custom Post, Custom Posting, Post, Form, Akismet, Ajax
Requires at least:  4.8
Tested up to:       4.9
Stable tag:         3.2

Give the visitors of your site the ability to submit their news to you, and list it in a nice news feed.



== Description ==

Many blogs out there can only produce a couple of articles a week, while their visitors
ask for loads more. It is a good idea to add related articles from other blogs to your site.
Adding them manually, however, takes lots of time we don't have. With this Community News
plugin you allow your visitors to add articles to your blog. Complete with moderation panel
and a settings page, you can sit back while you have full control about the look and articles
that are being posted.


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

* PHP 7.0 or higher
* WordPress 4.8 or higher


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
1. Modify the files you just copied to match your theme. By default the files are setup for the default WordPress theme (Twenty Seventeen).

And thats all! The plugin will automatically detect the new files and use those instead
of the default files.


= Updating From Version 2 =

Version 2 and older are not compatible with version 3 and later. To convert your posts
to work with the new version, download the [FV Community News Upgrader](https://frankverhoeven.me/fv-community-news-upgrader/).

If you're still using version 1.x, you will have to update to version 2 first.



== Screenshots ==

1. The Moderation page, 100% integrated with WordPress.
2. Edit Community News, just like any other post.
3. Moderate the latest posts right on your dashboard.
4. General settings page.
5. Customise the form.
6. Widget displaying recent posts.
7. Widget displaying the form.



== FAQ ==

Below is a list of frequently asked questions.


Q: I have a great idea for this plugin, could I make a suggestion?
A: Sure you can! [Let me know about it](https://frankverhoeven.me/wordpress-plugin-fv-community-news/).

Q: How can I contribute to the plugin?
A: You can open a pull request at [GitHub](https://github.com/frankverhoeven/fv-community-news/pulls).

Q: What to do if I found a bug?
A: Please report the bug to me as soon as possible. This way I can solve the problem and make the plugin better for everyone.
Open a new issue at [https://github.com/frankverhoeven/fv-community-news/issues](https://github.com/frankverhoeven/fv-community-news/issues).



== Changelog ==

For more details on changes, please visit the [WordPress Trac](http://plugins.trac.wordpress.org/log/fv-community-news/).


= 3.2 =

* New: The link and tags field can now be disabled.
* New: Advanced form options right in your wp-admin.
* Improvement: Theme compat improvements.
* Improvement: General code improvements & cleanup.


= 3.1.2 =

* Change: Now requires PHP >= 7.0.
* Fix: Links in the notification mail now correctly point to wp-admin if wp is located in a custom directory.
* Improvement: No longer replaces jQuery.Form shipped with WP.
* Improvement: New/minified javascript, moved to footer.
* Improvement: Minified css.
* Improvement: New dependency injection.
* Improvement: General code improvements & cleanup.


= 3.1.1 =

* Fix: Correct (de)actiovation hooks


= 3.1 =

* Fix: WordPress 4.9 compatibility
* Fix: Post info can be edited again
* Improvement: Code cleanup
* Improvement: Various bug fixes and enhancements
* Improvement: Relaxed the very strict form validators
