<?php

use FvCommunityNews\Post\PostType;

/**
 * fvcn_insert_post()
 *
 * 
 * @param array $post_data
 * @param array $post_meta
 * @return int
 */
function fvcn_insert_post(array $post_data, array $post_meta)
{
    $default_post = [
        'post_author' => 0,
        'post_title' => '',
        'post_content' => '',
        'post_status' => PostType::STATUS_PENDING,
        'post_type' => PostType::POST_TYPE_KEY,
        'post_password' => '',
        'tax_input' => ''
    ];
    $post_data = wp_parse_args($post_data, $default_post);

    $postId = wp_insert_post($post_data);

    // Anonymous tags fix
    if (!empty($post_data['tax_input']) && is_array($post_data['tax_input']) && !empty($post_data['tax_input'][ fvcn_get_post_tag_id() ])) {
        wp_set_post_terms($postId, $post_data['tax_input'][ fvcn_get_post_tag_id() ], fvcn_get_post_tag_id());
    }


    $default_meta = [
        '_fvcn_anonymous_author_name' => '',
        '_fvcn_anonymous_author_email' => '',
        '_fvcn_post_url' => '',
        '_fvcn_post_rating' => 0,
        '_fvcn_author_ip' => fvcn_get_current_author_ip(),
        '_fvcn_author_au' => fvcn_get_current_author_ua()
    ];

    $post_meta = wp_parse_args($post_meta, $default_meta);

    foreach ($post_meta as $meta_key=>$meta_value) {
        update_post_meta($postId, $meta_key, $meta_value);
    }

    do_action('fvcn_insert_post', $postId, $post_data, $post_meta);

    return $postId;
}


/**
 * fvcn_insert_post_thumbnail()
 *
 * @param int $postId
 * @return int
 */
function fvcn_insert_post_thumbnail($postId)
{
    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';

    $attach_id = media_handle_upload('fvcn_post_form_thumbnail', $postId);

    update_post_meta($postId, '_thumbnail_id', $attach_id);

    return $attach_id;
}


/**
 * fvcn_new_post_handler()
 *
 * @return int|null
 */
function fvcn_new_post_handler()
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
    /* @var \FvCommunityNews\Post\Validator $validator */
    $validator = FvCommunityNews::$container->get(\FvCommunityNews\Post\Validator::class);

    do_action('fvcn_new_post_pre_extras');

    if ($validator->isValid($data)) {
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
        $post_meta = apply_filters('fvcn_new_post_meta_pre_insert', [
            '_fvcn_anonymous_author_name' => fvcn_is_anonymous() ? $data['fvcn_post_form_author_name'] : '',
            '_fvcn_anonymous_author_email' => fvcn_is_anonymous() ? $data['fvcn_post_form_author_email'] : '',
            '_fvcn_post_url' => $data['fvcn_post_form_link']
        ]);

        do_action('fvcn_new_post_pre_insert', $post_data, $post_meta);

        $postId = fvcn_insert_post($post_data, $post_meta);

        if ('template_redirect' == current_filter()) {
            if (PostType::STATUS_PUBLISH == fvcn_get_post_status($postId)) {
                wp_redirect(add_query_arg(['fvcn_added'=>$postId], fvcn_get_post_permalink($postId)));
            } else {
                wp_redirect(add_query_arg(['fvcn_added'=>$postId], home_url('/')));
            }
        } else {
            return $postId;
        }
    }

    return null;
}


/**
 * fvcn_filter_new_post_data()
 *
 * @param array $data
 * @return array
 */
function fvcn_filter_new_post_data(array $data)
{
    foreach ($data as $key=>$value) {
        $filter = str_replace('_fvcn_', '', $key);
        $data[ $key ] = apply_filters('fvcn_new_post_pre_' . $filter, $value);
    }

    return $data;
}


/**
 * fvcn_increase_post_view_count()
 *
 * @param string $template
 * @return string
 */
function fvcn_increase_post_view_count($template)
{
    if (!fvcn_is_single_post()) {
        return $template;
    }

    $id = (int) fvcn_get_post_id();
    if (isset($_COOKIE['fvcn_post_viewed_' . $id . '_' . COOKIEHASH])) {
        return $template;
    }

    $postMapper = new \FvCommunityNews\Post\Mapper();
    $postMapper->increasePostViewCount($id);

    setcookie('fvcn_post_viewed_' . $id . '_' . COOKIEHASH, 'true', 0, COOKIEPATH, COOKIE_DOMAIN);

    return $template;
}


/**
 * fvcn_post_rating_handler()
 *
 */
function fvcn_post_rating_handler()
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

    $postMapper = new \FvCommunityNews\Post\Mapper();
    if ('increase' == $_REQUEST['fvcn_post_rating_action']) {
        $postMapper->increasePostRating($id);
    } else {
        $postMapper->decreasePostRating($id);
    }

    setcookie('fvcn_post_rated_' . $id . '_' . COOKIEHASH, 'true', time() + 30000000, COOKIEPATH, COOKIE_DOMAIN);

    wp_redirect(fvcn_get_post_permalink($id));
}
