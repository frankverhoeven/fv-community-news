<?php

/**
 * FvCommunityNews_Config_Default
 *
 * @author Frank Verhoeven <info@frank-verhoeven.com>
 */
final class FvCommunityNews_Config_Default
{
    /**
     * Configuration.
     * @var array
     */
    private $_config = array(
        'admin_moderation'     => false,
        'user_moderation'      => true,
        'mail_on_submission'   => false,
        'mail_on_moderation'   => true,
        'is_anonymous_allowed' => true,

        'sync_key'             => false,
        'dashboard_rp_num'     => 5,

        'theme_dir'            => 'fvcn-theme',
        'theme_url'            => 'fvcn-theme',

        'lang_dir'             => 'fvcn_languages',

        'post' => array(
            'type' => array(
                'post' => 'fvcn-post',
                'tag'  => 'fvcn-tag',
            ),
            'status' => array(
                'public'  => 'publish',
                'trash'   => 'trash',
                'private' => 'private',
                'pending' => 'pending',
                'spam'    => 'spam',
            ),
            'slug' => array(
                'base'    => 'fv-community-news',
                'post'    => 'post',
                'tag'     => 'tag',
                'archive' => 'archive',
            ),
            'form' => array(
                'author_name_label'  => 'Author Name',
                'author_email_label' => 'Author Email',
                'title_label'        => 'Title',
                'link_label'         => 'Link',
                'link_required'      => true,
                'content_label'      => 'Description',
                'tags_label'         => 'Tags',
                'tags_required'      => true,
                'thumbnail_enabled'  => true,
                'thumbnail_label'    => 'Thumbnail',
                'thumbnail_required' => false,
            ),
        ),
    );

    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct ()
    {}

    /**
     * Get configuration.
     *
     * @return array Configuration.
     */
    public function getConfig()
    {
        return array('_fvcn' => $this->_config);
    }
}

