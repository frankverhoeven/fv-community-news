<?php

namespace FvCommunityNews\Syncer;

use FvCommunityNews\Syncer\Api\Api;
use FvCommunityNews\Syncer\Api\Exception;
use FvCommunityNews\Syncer\Api\Request;

/**
 * Syncer
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Syncer
{
    /**
     * Submit a post to the API.
     *
     * @param int $postId
     * @return int|null
     */
    public function submitPost(int $postId)
    {
        $apiId = \get_post_meta($postId, '_fvcn_post_synced', true);

        if (!\ctype_digit($apiId)) {
            if (\fvcn_has_post_thumbnail($postId)) {
                $thumbnail = \explode('"', \explode('src="', \fvcn_get_post_thumbnail())[1])[0];
            } else {
                $thumbnail = null;
            }

            $data = [
                'title'	=> \fvcn_get_post_title($postId),
                'content' => \strip_tags(\fvcn_get_post_content($postId)),
                'url' => \fvcn_get_post_link($postId),
                'tags' => \explode(';', \strip_tags(\fvcn_get_post_tag_list($postId, ['before'=>'', 'sep'=>';', 'after'=>'']))),
                'likes' => \fvcn_get_post_likes($postId),
                'views' => \fvcn_get_post_views($postId),
                'thumbnail' => $thumbnail,
                'author'	=> [
                    'name' => \fvcn_get_post_author_display_name($postId),
                    'email' => \fvcn_get_post_author_email($postId),
                ],
                'blog' => [
                    'url' => \get_bloginfo('url'),
                ],
            ];

            $response = $this->executeRequest(Api::submitPost(), $data);

            if (!empty($response)) {
                \update_post_meta($postId, '_fvcn_post_synced', $response['post']['id']);
                return $response['post']['id'];
            }

            return null;
        }

        return $apiId;
    }

    /**
     * Submit a post view to the API.
     *
     * @param int $postId
     * @return void
     */
    public function increasePostView(int $postId)
    {
        $apiId = $this->submitPost($postId);

        if (null === $apiId) {
            return;
        }

        $this->executeRequest(Api::viewPost($apiId), [], ['blocking' => false]);
    }

    /**
     * Submit a like to the API.
     *
     * @param int $postId
     * @return void
     */
    public function likePost(int $postId)
    {
        $apiId = $this->submitPost($postId);

        if (null === $apiId) {
            return;
        }

        $this->executeRequest(Api::likePost($apiId), [], ['blocking' => false]);
    }

    /**
     * Submit an unlike to the API.
     *
     * @param int $postId
     * @return void
     */
    public function unlikePost(int $postId)
    {
        $apiId = $this->submitPost($postId);

        if (null === $apiId) {
            return;
        }

        $this->executeRequest(Api::unlikePost($apiId), [], ['blocking' => false]);
    }

    /**
     * Perform an API Request
     *
     * @param Api $api
     * @param array $data
     * @param array $options
     * @return array|null
     */
    protected function executeRequest(Api $api, array $data = [], array $options = [])
    {
        $apiRequest = new Request($api);

        try {
            $response = $apiRequest->execute($data, $options);
            $response = \json_decode($response['body'], true);
        } catch (Exception $e) {
            $response = [];
        }

        return $response;
    }
}
