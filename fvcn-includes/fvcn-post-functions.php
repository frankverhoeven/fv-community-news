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
