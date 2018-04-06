<?php

namespace FvCommunityNews\Akismet;

use FvCommunityNews\Post\Mapper;
use FvCommunityNews\Post\Status;

/**
 * Handler
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Handler
{
    /**
     * @var int
     */
    private $currentPostId;
    /**
     * @var Akismet
     */
    private $akismet;
    /**
     * @var Mapper
     */
    private $postMapper;

    /**
     * @param Akismet $akismet
     * @param Mapper $postMapper
     */
    public function __construct(Akismet $akismet, Mapper $postMapper)
    {
        $this->akismet = $akismet;
        $this->postMapper = $postMapper;
    }

    /**
     * _getParams()
     *
     * @param int $postId
     * @return array
     */
    protected function _getParams($postId)
    {
        $params = [
            'user_ip' => fvcn_get_post_author_ip($postId),
            'user_agent' => fvcn_get_post_author_ua($postId),
            'referer' => $_SERVER['HTTP_REFERER'],
            'permalink' => fvcn_get_post_permalink($postId),
            'comment_type' => 'fv-community-news',
            'comment_author' => fvcn_get_post_author_display_name($postId),
            'comment_author_email' => fvcn_get_post_author_email($postId),
            'comment_author_url' => fvcn_get_post_link($postId),
            'comment_content' => fvcn_get_post_content($postId),
            'blog_charset' => get_option('blog_charset'),
            'blog_lang' => get_locale()
        ];

        $ignore = ['HTTP_COOKIE', 'HTTP_COOKIE2', 'PHP_AUTH_PW'];
        foreach ($_SERVER as $key=>$value) {
            if (!in_array($key, $ignore) && is_string($value)) {
                $params[ $key ] = $value;
            }
        }

        return $params;
    }

    /**
     * checkPost()
     *
     * @param int $postId
     * @return Handler
     */
    public function checkPost($postId)
    {
        try {
            if ($this->akismet->isSpam($this->_getParams($postId))) {
                $this->currentPostId = $postId;
                $this->postMapper->spamPost($postId);
            }
        } catch (\Exception $e) {}

        return $this;
    }

    /**
     * submitPost()
     *
     * @param int $postId
     * @return Handler
     */
    public function submitPost($postId)
    {
        $filter = current_filter();

        if ('fvcn_spam_post' == $filter) {
            if ($this->currentPostId == $postId) {
                return $this;
            }

            $method = 'submitSpam';
        } elseif ('fvcn_publish_post' == $filter) {
            if (Status::spam() != fvcn_get_post_status($postId)) {
                return $this;
            }

            $method = 'submitHam';
        } else {
            return $this;
        }

        $this->akismet->$method($this->_getParams($postId));

        return $this;
    }

    /**
     * registerSettings()
     *
     */
    public function registerSettings()
    {
        add_settings_section('fvcn_settingsakismet', __('Akismet', 'fvcn'), [$this, 'settingsCallbackSection'], 'fvcn-settings');

        add_settings_field('_fvcnakismet_enabled', __('Enabled', 'fvcn'), [$this, 'settingsCallbackEnabled'], 'fvcn-settings', 'fvcn_settingsakismet');
        register_setting('fvcn-settings', '_fvcnakismet_enabled', 'intval');
    }

    /**
     * settingsCallbackSection()
     *
     */
    public function settingsCallbackSection()
    {
        ?>

        <p><?php _e('Keep in mind that you must have/keep the Akismet plugin enabled with a valid API key.', 'fvcn'); ?></p>

        <?php
    }

    /**
     * settingsCallbackEnabled()
     *
     */
    public function settingsCallbackEnabled()
    {
        ?>

        <input type="checkbox" name="_fvcnakismet_enabled" id="_fvcnakismet_enabled" value="1" <?php checked(get_option('_fvcnakismet_enabled', false)); ?>>
        <label for="_fvcnakismet_enabled"><?php _e('Enable Akismet spam protection for community posts.', 'fvcn'); ?></label>

        <?php if ($this->akismet->verifyKey()): ?>

            <p class="description"><?php _e('Your current API key appears to be <strong>valid</strong>.', 'fvcn'); ?></p>

        <?php else : ?>

            <p class="description"><?php _e('Your current API key appears to be <strong>invalid</strong>.', 'fvcn'); ?></p>

        <?php endif;
    }
}
