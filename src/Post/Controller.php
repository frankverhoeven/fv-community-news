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
     * @var Validator
     */
    private $postValidator;

    /**
     * @param Mapper $postMapper
     * @param Validator $postValidator
     */
    public function __construct(Mapper $postMapper, Validator $postValidator)
    {
        $this->postMapper = $postMapper;
        $this->postValidator = $postValidator;
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

        $data = array_merge([
            'fvcn_post_form_author_name' => null,
            'fvcn_post_form_author_email' => null,
            'fvcn_post_form_title' => null,
            'fvcn_post_form_link' => null,
            'fvcn_post_form_content' => null,
            'fvcn_post_form_tags' => null,
            'fvcn_post_form_thumbnail' => null,
        ], $_POST, $_FILES);

        do_action('fvcn_new_post_pre_extras');

        if ($this->postValidator->isValid($data)) {
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

            if (false !== strpos($data['fvcn_post_form_tags'], ',')) {
                $data['fvcn_post_form_tags'] = explode(',', $data['fvcn_post_form_tags']);
            }
            $data['fvcn_post_form_tags'] = [fvcn_get_post_tag_id() => $data['fvcn_post_form_tags']];

            $post_data = apply_filters('fvcn_new_post_data_pre_insert', [
                'post_author' => fvcn_is_anonymous() ? 0 : fvcn_get_current_user_id(),
                'post_title' => $data['fvcn_post_form_title'],
                'post_content' => $data['fvcn_post_form_content'],
                'tax_input' => $data['fvcn_post_form_tags'],
                'post_status' => $status,
                'post_type' => PostType::POST_TYPE_KEY
            ]);
            foreach ($post_data as $key => $value) {
                $post_data[$key] = apply_filters('fvcn_new_post_pre_' . $key, $value);
            }

            $post_meta = apply_filters('fvcn_new_post_meta_pre_insert', [
                '_fvcn_anonymous_author_name' => fvcn_is_anonymous() ? $data['fvcn_post_form_author_name'] : '',
                '_fvcn_anonymous_author_email' => fvcn_is_anonymous() ? $data['fvcn_post_form_author_email'] : '',
                '_fvcn_post_url' => $data['fvcn_post_form_link']
            ]);
            foreach ($post_meta as $key => $value) {
                $filter = str_replace('_fvcn_', '', $key);
                $post_meta[$key] = apply_filters('fvcn_new_post_pre_' . $filter, $value);
            }

            do_action('fvcn_new_post_pre_insert', $post_data, $post_meta);

            $postId = $this->postMapper->insertPost($post_data, $post_meta);

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
