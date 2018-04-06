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
     * Store a post to the database.
     *
     * @param array $data
     * @param array $meta
     * @return int
     */
    public function insertPost(array $data, array $meta): int
    {
        $defaultPost = [
            'post_author' => 0,
            'post_title' => '',
            'post_content' => '',
            'post_status' => Status::pending(),
            'post_type' => Type::post(),
            'post_password' => '',
            'tax_input' => ''
        ];
        $data = wp_parse_args($data, $defaultPost);

        $postId = wp_insert_post($data);

        // Anonymous tags fix
        $tagType = Type::tag()->getType();
        if (!empty($data['tax_input']) && is_array($data['tax_input'])
            && !empty($data['tax_input'][$tagType]))
        {
            wp_set_post_terms($postId, $data['tax_input'][$tagType], $tagType);
        }

        $defaultMeta = [
            '_fvcn_anonymous_author_name' => '',
            '_fvcn_anonymous_author_email' => '',
            '_fvcn_post_url' => '',
            '_fvcn_post_likes' => 0,
            '_fvcn_author_ip' => fvcn_get_current_author_ip(),
            '_fvcn_author_au' => fvcn_get_current_author_ua()
        ];
        $meta = wp_parse_args($meta, $defaultMeta);

        foreach ($meta as $meta_key => $meta_value) {
            update_post_meta($postId, $meta_key, $meta_value);
        }

        do_action('fvcn_insert_post', $postId, $data, $meta);

        return $postId;
    }

    /**
     * Process the thumbnail uploaded with a post.
     * 
     * @param int $postId
     * @return int
     */
    public function insertPostThumbnail(int $postId): int
    {
        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';

        $attachId = media_handle_upload('fvcn_post_form_thumbnail', $postId);
        update_post_meta($postId, '_thumbnail_id', $attachId);

        return $attachId;
    }

    /**
     * publishPost()
     *
     * @param int $postId
     * @return int
     */
    public function publishPost(int $postId): int
    {
        do_action('fvcn_publish_post', $postId);
        return $this->changePostStatus($postId, Status::publish());
    }

    /**
     * unpublishPost()
     *
     * @param int $postId
     * @return int
     */
    public function unpublishPost(int $postId): int
    {
        do_action('fvcn_unpublish_post', $postId);
        return $this->changePostStatus($postId, Status::pending());
    }

    /**
     * spamPost()
     *
     * @param int $postId
     * @return int
     */
    public function spamPost(int $postId): int
    {
        do_action('fvcn_spam_post', $postId);
        return $this->changePostStatus($postId, Status::spam());
    }

    /**
     * changePostStatus()
     *
     * @param int $postId
     * @param Status $status
     * @return int
     */
    protected function changePostStatus(int $postId, Status $status): int
    {
        $post = [
            'ID' => $postId,
            'post_status' => $status->getStatus(),
        ];

        return wp_update_post($post);
    }

    /**
     * likePost()
     *
     * @param int $postId
     */
    public function likePost(int $postId)
    {
        do_action('fvcn_like_post', $postId);
        update_post_meta($postId, '_fvcn_post_likes', fvcn_get_post_likes($postId) + 1);
    }

    /**
     * unlikePost()
     *
     * @param int $postId
     */
    public function unlikePost(int $postId)
    {
        do_action('fvcn_unlike_post', $postId);
        update_post_meta($postId, '_fvcn_post_likes', max(0, fvcn_get_post_likes($postId) - 1));
    }

    /**
     * increasePostViewCount()
     *
     * @param int $postId
     */
    public function increasePostViewCount(int $postId)
    {
        do_action('fvcn_increase_post_view_count', $postId);
        update_post_meta($postId, '_fvcn_post_views', fvcn_get_post_views($postId) + 1);
    }
}
