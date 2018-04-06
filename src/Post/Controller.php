<?php

namespace FvCommunityNews\Post;

use FvCommunityNews\Config\AbstractConfig as Config;

/**
 * Controller
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Controller
{
    /**
     * @var Config
     */
    private $config;
    /**
     * @var Mapper
     */
    private $postMapper;
    /**
     * @var Form
     */
    private $postForm;

    /**
     * @param Config $config
     * @param Mapper $postMapper
     * @param Form $postForm
     */
    public function __construct(Config $config, Mapper $postMapper, Form $postForm)
    {
        $this->config = $config;
        $this->postMapper = $postMapper;
        $this->postForm = $postForm;
    }

    /**
     * Create a new post.
     *
     * @return int|null
     */
    public function createPost()
    {
        if ('post' != strtolower($_SERVER['REQUEST_METHOD'])) {
            return null;
        }
        if (!isset($_POST['fvcn_post_form_action']) || 'fvcn-new-post' != $_POST['fvcn_post_form_action']) {
            return null;
        }
        if (fvcn_is_anonymous() && !$this->config['_fvcn_is_anonymous_allowed']) {
            return null;
        }
        if (!wp_verify_nonce($_POST['fvcn_post_form_nonce'], 'fvcn-new-post')) {
            return null;
        }

        $data = array_merge($_POST, $_FILES);

        do_action('fvcn_new_post_pre_extras');

        if ($this->postForm->isValid($data)) {
            $data = $this->postForm->getData();

            $status = Status::pending();
            if (!$this->config['_fvcn_admin_moderation']) {
                if ($this->config['_fvcn_user_moderation']) {
                    if (fvcn_has_user_posts()) {
                        $status = Status::publish();
                    }
                } else {
                    $status = Status::publish();
                }
            }

            if (isset($data['fvcn_post_form_tags'])) {
                if (false !== strpos($data['fvcn_post_form_tags'], ',')) {
                    $data['fvcn_post_form_tags'] = explode(',', $data['fvcn_post_form_tags']);
                }
                $data['fvcn_post_form_tags'] = [Type::tag()->getType() => $data['fvcn_post_form_tags']];
            }

            $postData = apply_filters('fvcn_new_post_data_pre_insert', [
                'post_author' => fvcn_is_anonymous() ? 0 : fvcn_get_current_user_id(),
                'post_title' => $data['fvcn_post_form_title'],
                'post_content' => $data['fvcn_post_form_content'],
                'tax_input' => $data['fvcn_post_form_tags'],
                'post_status' => $status,
                'post_type' => Type::post()
            ]);
            $postMeta = apply_filters('fvcn_new_post_meta_pre_insert', [
                '_fvcn_anonymous_author_name' => fvcn_is_anonymous() ? $data['fvcn_post_form_author_name'] : '',
                '_fvcn_anonymous_author_email' => fvcn_is_anonymous() ? $data['fvcn_post_form_author_email'] : '',
                '_fvcn_post_url' => $data['fvcn_post_form_link']
            ]);

            do_action('fvcn_new_post_pre_insert', $postData, $postMeta);

            $postId = $this->postMapper->insertPost($postData, $postMeta);
            if (isset($data['fvcn_post_form_thumbnail'])) {
                $this->postMapper->insertPostThumbnail($postId);
            }

            if ('template_redirect' == current_filter()) {
                if (Status::publish() == fvcn_get_post_status($postId)) {
                    wp_redirect(add_query_arg(['fvcn_added' => $postId], fvcn_get_post_permalink($postId)));
                } else {
                    wp_redirect(add_query_arg(['fvcn_added' => $postId], home_url('/')));
                }
            } else {
                return $postId;
            }
        }

        return null;
    }

    /**
     * Increase or decrease the post rating
     *
     * @return void
     */
    public function likeUnlikePost()
    {
        $actions = [
            'like',
            'unlike'
        ];

        if (!isset($_REQUEST['fvcn-post-like-action'], $_REQUEST['fvcn-post-id']) || !in_array($_REQUEST['fvcn-post-like-action'], $actions)) {
            return;
        }
        if (0 === ($id = fvcn_get_post_id($_REQUEST['fvcn-post-id']))) {
            return;
        }

        check_admin_referer('fvcn-post-like');

        if ('like' == $_REQUEST['fvcn-post-like-action']) {
            if (\fvcn_is_post_liked_by_current_user($id)) {
                return;
            }
            $this->postMapper->likePost($id);
            setcookie('fvcn_post_liked_' . $id . '_' . COOKIEHASH, 'true', time() + 30000000, COOKIEPATH, COOKIE_DOMAIN);
        } else {
            if (!\fvcn_is_post_liked_by_current_user($id)) {
                return;
            }
            $this->postMapper->unlikePost($id);
            setcookie('fvcn_post_liked_' . $id . '_' . COOKIEHASH, 'true', time() - 30000000, COOKIEPATH, COOKIE_DOMAIN);
        }

        wp_redirect(fvcn_get_post_permalink($id));
    }
}
