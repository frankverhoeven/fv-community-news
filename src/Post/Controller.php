<?php

namespace FvCommunityNews\Post;

/**
 * Controller
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Controller
{
    /**
     * @var Mapper
     */
    private $postMapper;
    /**
     * @var Form
     */
    private $postForm;

    /**
     * @param Mapper $postMapper
     * @param Form $postForm
     */
    public function __construct(Mapper $postMapper, Form $postForm)
    {
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
        if (fvcn_is_anonymous() && !fvcn_is_anonymous_allowed()) {
            return null;
        }
        if (!wp_verify_nonce($_POST['fvcn_post_form_nonce'], 'fvcn-new-post')) {
            return null;
        }

        $data = array_merge($_POST, $_FILES);

        do_action('fvcn_new_post_pre_extras');

        if ($this->postForm->isValid($data)) {
            $data = $this->postForm->getData();

            $status = PostType::STATUS_PENDING;
            if (!fvcn_admin_moderation()) {
                if (fvcn_user_moderation()) {
                    if (fvcn_has_user_posts()) {
                        $status = PostType::STATUS_PUBLISH;
                    }
                } else {
                    $status = PostType::STATUS_PUBLISH;
                }
            }

            if (isset($data['fvcn_post_form_tags'])) {
                if (false !== strpos($data['fvcn_post_form_tags'], ',')) {
                    $data['fvcn_post_form_tags'] = explode(',', $data['fvcn_post_form_tags']);
                }
                $data['fvcn_post_form_tags'] = [fvcn_get_post_tag_id() => $data['fvcn_post_form_tags']];
            }

            $postData = apply_filters('fvcn_new_post_data_pre_insert', [
                'post_author' => fvcn_is_anonymous() ? 0 : fvcn_get_current_user_id(),
                'post_title' => $data['fvcn_post_form_title'],
                'post_content' => $data['fvcn_post_form_content'],
                'tax_input' => $data['fvcn_post_form_tags'],
                'post_status' => $status,
                'post_type' => PostType::POST_TYPE_KEY
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
                if (PostType::STATUS_PUBLISH == fvcn_get_post_status($postId)) {
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
    public function adjustPostRating(): void
    {
        $actions = [
            'increase',
            'decrease'
        ];

        if (!isset($_REQUEST['fvcn_post_rating_action'], $_REQUEST['post_id']) || !in_array($_REQUEST['fvcn_post_rating_action'], $actions)) {
            return;
        }
        if (0 === ($id = fvcn_get_post_id($_REQUEST['post_id']))) {
            return;
        }
        if (fvcn_is_post_rated_by_current_user($id)) {
            return;
        }

        check_admin_referer('fvcn-post-rating');

        if ('increase' == $_REQUEST['fvcn_post_rating_action']) {
            $this->postMapper->increasePostRating($id);
        } else {
            $this->postMapper->decreasePostRating($id);
        }

        setcookie('fvcn_post_rated_' . $id . '_' . COOKIEHASH, 'true', time() + 30000000, COOKIEPATH, COOKIE_DOMAIN);

        wp_redirect(fvcn_get_post_permalink($id));
    }
}
