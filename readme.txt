=== FV Community News ===
Contributors:		frankverhoeven
Donate link:		https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=SB62B7H867Y4C&lc=US&item_name=Frank%20Verhoeven&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_LG%2egif%3aNonHosted
Tags:			Community, News, Widget, Posts, Comments, Anonymous, Posting, Users, Post, Form, Admin, Submit, Submissions, Unregistered Users, Uploads, Captcha, Custom Posting, Plugin, Custom, Widget, Akismet, Ajax
Requires at least:	3.0
Tested up to:		3.1.2
Stable tag:		1.3.1

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

1. Download the files
1. Upload the files to `/wp-content/plugins/`
1. Activate the plugin (WP-admin > Plugins > FV Community News)
1. Browse Community News > Settings to select the settings you prefer.
1. Go to Design > Widgets to add the Community News widgets to your sidebar; or use the tags in your post/page.



== Frequently Asked Questions ==

Q: Which Wordpress version support this plugin?
A: This Wordpress plugin requires WordPress 3.0 and has been tested up to version 3.1.2

Q: Is this plugin widget ready?
A: Yes, this plugin has build-in widgets for both displaying the list of news and for the form.

Q: Is there a live example around?
A: My friend Stefan Vervoort over at `http://www.divitodesign.com/` is using the plugin for some time now.

Q: What to do if I found a bug?
A: Please report the bug to me as soon as possible. This way I can solve the problem and make the
plugin better for everyone. Visit `http://www.frank-verhoeven.com/contact/` for bug reporting.

Q: Is there an other way to display the form or submissions?
A: Yes you could use the tags in your posts or page, or you can manually edit the template files.

Q: How to add this to a page?
A: The easiest way to do this is to use the build-in tags. You can put <!--fvCommunityNews:Submissions-->,
<!--fvCommunityNews:Form--> or <!--fvCommunityNews:Archive--> in your page/post to display a list of recent
submissions, the form to add news, or a complete archive of all the news ever submitted.



== Screenshots ==

1. The Moderation Page, 100% integrated with WordPress.
2. The Uninstall page.
3. The plugin has a build-in widget for the Dashboard.
4. A lot of settings for everyone.
5. Editing submissions just as comments.
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
use in your themes.


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

= 2.0 =
Complete revision of the plugin.










