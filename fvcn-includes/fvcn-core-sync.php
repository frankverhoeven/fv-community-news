<?php

/**
 * fvcn-core-sync.php
 *
 * Sync Functions
 *
 * @package FV Community News
 * @subpackage Sync
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */

if (!defined('ABSPATH')) {
    die('Direct access is not allowed!');
}


/**
 * FvCommunityNews_Sync
 *
 * Synchronisation
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class FvCommunityNews_Sync
{
    const API_REGISTER = 'https://api.frankverhoeven.me/fvcn/1.0/register-site/';
    const API_SUBMIT_POST = 'https://api.frankverhoeven.me/fvcn/1.0/submit-post/';
    const API_INC_POST_VIEW_COUNT = 'https://api.frankverhoeven.me/fvcn/1.0/increase-post-view-count/';
    const API_INC_POST_RATING = 'https://api.frankverhoeven.me/fvcn/1.0/increase-post-rating/';
    const API_DEC_POST_RATING = 'https://api.frankverhoeven.me/fvcn/1.0/decrease-post-rating/';

    /**
     * @var array
     */
    protected $_options = [];

    /**
     * @var bool
     */
    protected $_enabled = true;

    /**
     * @var bool
     */
    protected $_registered = false;

    /**
     * __construct()
     *
     * @version 20120716
     */
    public function __construct()
    {
        $this->_setupOptions();

        if (!$this->isSiteRegistered()) {
            $this->registerSite();
        }
    }

    /**
     * _setupOptions()
     *
     * @version 20120701
     * @return FvCommunityNews_Sync
     */
    protected function _setupOptions()
    {
        $this->_options = [
            'method' => 'POST',
            'timeout' => 10,
            'redirection' => 1,
            'user-agent' => 'WordPress/' . get_bloginfo('version') . ';FvCommunityNews/' . fvcn_get_version() . ';' . home_url('/'),
            'blocking' => true,
            'compress' => true,
            'decompress' => true,
            'headers' => [],
            'body' => [],
            'cookies' => []
        ];

        return $this;
    }

    /**
     * isEnabled()
     *
     * @version 20120701
     * @return bool
     */
    public function isEnabled()
    {
        return $this->_enabled;
    }

    /**
     * isSiteRegistered()
     *
     * @version 20120701
     * @return bool
     */
    public function isSiteRegistered()
    {
        $key = fvcn_get_option('_fvcn_sync_key');

        if (false !== strpos($key, 'inactive') || false !== strpos($key, 'invallid')) {
            $this->_enabled = false;
        }

        return (bool)$key;
    }

    /**
     * _encryptData()
     *
     * @version 20120701
     * @param array $data
     * @param bool $root
     * @return array
     */
    protected function _encryptData(array $data, $root=true)
    {
        return $data;
    }

    /**
     * _makeApiCall()
     *
     * @version 20120716
     * @param string $uri
     * @param array $data
     * @param array $options
     * @param bool $encrypt
     * @return bool|string
     */
    protected function _makeApiCall($uri, array $data, array $options= [], $encrypt=true)
    {
        if (!$this->isEnabled()) {
            return false;
        }

        $options = wp_parse_args($options, $this->_options);

        if (true === $encrypt) {
            $options['body'] = $this->_encryptData( $data);
        } else {
            $options['body'] = $data;
        }

        $response = wp_remote_post($uri, $options);

        if (is_wp_error($response)) {
            return false;
        }

        return $response['body'];
    }

    /**
     * registerSite()
     *
     * @version 20120716
     * @return FvCommunityNews_Sync
     */
    public function registerSite()
    {
        if (true === $this->_registered) {
            return $this;
        }

        $data = [
            'blog_name' => get_bloginfo('name'),
            'blog_description' => get_bloginfo('description'),
            'blog_url' => home_url('/'),
            'blog_language' => get_bloginfo('language')
        ];

        if (false === ($key = $this->_makeApiCall(self::API_REGISTER, $data, [], false))) {
            $this->_enabled = false;
        } else {
            update_option('_fvcn_sync_key', $key);
            $this->_registered = true;
        }

        return $this;
    }

    /**
     * submitPost()
     *
     * @version 20120712
     * @param array $postData
     * @return FvCommunityNews_Sync
     */
    public function submitPost(array $postData)
    {
        $this->_makeApiCall(self::API_SUBMIT_POST, $postData, ['blocking' => false]);

        return $this;
    }

    /**
     * increasePostViewCount()
     *
     * @version 20120712
     * @param int $postId
     * @return FvCommunityNews_Sync
     */
    public function increasePostViewCount($postId)
    {
        $data = [
            'post_link' => fvcn_get_post_link($postId),
            'post_title' => fvcn_get_post_title($postId)
        ];

        $this->_makeApiCall(self::API_INC_POST_VIEW_COUNT, $data, ['blocking' => false]);

        return $this;
    }

    /**
     * increasePostRating()
     *
     * @version 20120712
     * @param int $postId
     * @return FvCommunityNews_Sync
     */
    public function increasePostRating($postId)
    {
        $data = [
            'post_link' => fvcn_get_post_link($postId),
            'post_title' => fvcn_get_post_title($postId)
        ];

        $this->_makeApiCall(self::API_INC_POST_RATING, $data, ['blocking' => false]);

        return $this;
    }

    /**
     * decreasePostRating()
     *
     * @version 20120712
     * @param int $postId
     * @return FvCommunityNews_Sync
     */
    public function decreasePostRating($postId)
    {
        $data = [
            'post_link' => fvcn_get_post_link($postId),
            'post_title' => fvcn_get_post_title($postId)
        ];

        $this->_makeApiCall(self::API_DEC_POST_RATING, $data, ['blocking' => false]);

        return $this;
    }
}


/**
 * fvcn_sync_submit_post()
 *
 * @version 20120729
 * @param int $postId
 * @return void
 */
function fvcn_sync_submit_post($postId)
{
    if (fvcn_get_public_post_status() != fvcn_get_post_status($postId) && 'fvcn_publish_post' != current_filter()) {
        return;
    }

    $data = [
        'post_id' => $postId,
        'post_title' => fvcn_get_post_title($postId),
        'post_content' => strip_tags( fvcn_get_post_content($postId)),
        'post_url' => fvcn_get_post_link($postId),
        'post_tags' => strip_tags( fvcn_get_post_tag_list($postId, ['before'=>'', 'sep'=>';', 'after'=>''])),
        'post_rating' => fvcn_get_post_rating($postId),
        'post_views' => fvcn_get_post_views($postId),
        'post_status' => fvcn_get_public_post_status(),
        'post_author' => [
            'author_name' => fvcn_get_post_author_display_name($postId),
            'author_email' => fvcn_get_post_author_email($postId)
        ]
    ];

    if (fvcn_has_post_thumbnail($postId)) {
        $data['post_thumbnail'] = wp_get_attachment_url( get_post_thumbnail_id($postId));
    } else {
        $data['post_thumbnail'] = '';
    }

    FvCommunityNews_Container::getInstance()->getSync()->submitPost($data);
}

/**
 * fvcn_sync_increase_post_view_count()
 *
 * @version 20120702
 * @param int $postId
 * @return void
 */
function fvcn_sync_increase_post_view_count($postId)
{
    FvCommunityNews_Container::getInstance()->getSync()->increasePostViewCount($postId);
}

/**
 * fvcn_sync_increase_post_rating()
 * 
 * @version 20120712
 * @param int $postId
 * @return void
 */
function fvcn_sync_increase_post_rating($postId)
{
    FvCommunityNews_Container::getInstance()->getSync()->increasePostRating($postId);
}

/**
 * fvcn_sync_decrease_post_rating()
 * 
 * @version 20120712
 * @param int $postId
 * @return void
 */
function fvcn_sync_decrease_post_rating($postId)
{
    FvCommunityNews_Container::getInstance()->getSync()->decreasePostRating($postId);
}

