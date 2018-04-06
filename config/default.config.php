<?php

use FvCommunityNews\Version;

return [
    /**
     * @var int Current plugin version
     */
    '_fvcn_version' => Version::getCurrentVersion(),

    /**
     * @var bool Require admin approval of posts
     */
    '_fvcn_admin_moderation' => false,

    /**
     * @var bool Automatically approve posts from trusted users
     */
    '_fvcn_user_moderation' => true,

    /**
     * @var bool Send notification mail after a post is added
     */
    '_fvcn_mail_on_submission' => false,

    /**
     * @var bool Send notification mail if a post is held for moderation
     */
    '_fvcn_mail_on_moderation' => true,

    /**
     * @var bool Allow anonymous user to add posts
     */
    '_fvcn_is_anonymous_allowed' => true,

    /**
     * @var bool Whether syncing posts to the API is enabled
     */
    '_fvcn_syncer_enabled' => true,

    /**
     * @var string Base slug
     */
    '_fvcn_base_slug' => 'fv-community-news',

    /**
     * @var string Post slug
     */
    '_fvcn_post_slug' => 'post',

    /**
     * @var string Tag slug
     */
    '_fvcn_post_tag_slug' => 'tag',

    /**
     * @var string Archive slug
     */
    '_fvcn_post_archive_slug' => 'archive',

    /**
     * @var string Author name form field label
     */
    '_fvcn_post_form_author_name_label' => __('Author Name', 'fvcn'),

    /**
     * @var int Minimum author name length
     */
    '_fvcn_post_form_author_name_length_min' => 2,

    /**
     * @var int Maximum author name length
     */
    '_fvcn_post_form_author_name_length_max' => 40,

    /**
     * @var string Author email form field label
     */
    '_fvcn_post_form_author_email_label' => __('Author Email', 'fvcn'),

    /**
     * @var string Title form field label
     */
    '_fvcn_post_form_title_label' => __('Title', 'fvcn'),

    /**
     * @var int Minimum title length
     */
    '_fvcn_post_form_title_length_min' => 8,

    /**
     * @var int Maximum title length
     */
    '_fvcn_post_form_title_length_max' => 70,

    /**
     * @var bool Enable the use of link
     */
    '_fvcn_post_form_link_enabled' => true,

    /**
     * @var bool Require a link to be posted
     */
    '_fvcn_post_form_link_required' => true,

    /**
     * @var string Link form field label
     */
    '_fvcn_post_form_link_label' => __('Link', 'fvcn'),

    /**
     * @var int Minimum link length
     */
    '_fvcn_post_form_link_length_min' => 6,

    /**
     * @var int Maximum link length
     */
    '_fvcn_post_form_link_length_max' => 1000,

    /**
     * @var string Description from field label
     */
    '_fvcn_post_form_content_label' => __('Description', 'fvcn'),

    /**
     * @var int Minimum content length
     */
    '_fvcn_post_form_content_length_min' => 20,

    /**
     * @var int Maximum content length
     */
    '_fvcn_post_form_content_length_max' => 5000,

    /**
     * @var bool Enable the use of tags
     */
    '_fvcn_post_form_tags_enabled' => true,

    /**
     * @var bool Require tags to be added
     */
    '_fvcn_post_form_tags_required' => true,

    /**
     * @var string Tags form field label
     */
    '_fvcn_post_form_tags_label' => __('Tags', 'fvcn'),

    /**
     * @var int Minimum tags length
     */
    '_fvcn_post_form_tags_length_min' => 2,

    /**
     * @var int Maximum tags length
     */
    '_fvcn_post_form_tags_length_max' => 1000,

    /**
     * @var bool Enable the use of thumbnails
     */
    '_fvcn_post_form_thumbnail_enabled' => true,

    /**
     * @var bool Require a thumbnail
     */
    '_fvcn_post_form_thumbnail_required' => false,

    /**
     * @var string Thumbnail form field label
     */
    '_fvcn_post_form_thumbnail_label' => __('Thumbnail', 'fvcn'),

    /**
     * @var int Number of posts to show on the admin dashboard
     */
    '_fvcn_dashboard_rp_num' => 5
];
