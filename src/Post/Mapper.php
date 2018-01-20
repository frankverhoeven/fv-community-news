<?php

namespace FvCommunityNews\Post;

/**
 * Mapper
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Mapper
{
    /**
     * changePostStatus()
     *
     * @param int $postId
     * @param string $status
     * @return int
     */
    protected function changePostStatus($postId, $status)
    {
        $post = [];
        $post['ID'] = $postId;
        $post['post_status'] = $status;

        return wp_update_post($post);
    }

    /**
     * publishPost()
     *
     * @param int $postId
     * @return int
     */
    public function publishPost($postId)
    {
        do_action('fvcn_publish_post', $postId);
        return $this->changePostStatus($postId, PostType::STATUS_PUBLISH);
    }

    /**
     * unpublishPost()
     *
     * @param int $postId
     * @return int
     */
    public function unpublishPost($postId)
    {
        do_action('fvcn_unpublish_post', $postId);
        return $this->changePostStatus($postId, PostType::STATUS_PENDING);
    }

    /**
     * spamPost()
     *
     * @param int $postId
     * @return int
     */
    public function spamPost($postId)
    {
        do_action('fvcn_spam_post', $postId);
        return $this->changePostStatus($postId, PostType::STATUS_SPAM);
    }

    /**
     * increasePostRating()
     *
     * @param int $postId
     */
    public function increasePostRating($postId)
    {
        do_action('fvcn_increase_post_rating', $postId);
        update_post_meta($postId, '_fvcn_post_rating', fvcn_get_post_rating($postId)+1);
    }

    /**
     * decreasePostRating()
     *
     * @param int $postId
     */
    public function decreasePostRating($postId)
    {
        do_action('fvcn_decrease_post_rating', $postId);
        update_post_meta($postId, '_fvcn_post_rating', fvcn_get_post_rating($postId)-1);
    }

    /**
     * increasePostViewCount()
     *
     * @param int $postId
     */
    public function increasePostViewCount($postId)
    {
        do_action('fvcn_increase_post_view_count', $postId);
        update_post_meta($postId, '_fvcn_post_views', fvcn_get_post_views($postId)+1);
    }
}
