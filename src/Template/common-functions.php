<?php

use FvCommunityNews\Post\PostType;

/**
 * Finds an entry of the container by its identifier and returns it.
 *  Convenience function for use in template functions.
 *
 * @param string $id Identifier of the entry to look for.
 * @return mixed Entry.
 */
function fvcn_container_get(string $id)
{
    return FvCommunityNews::$container->get($id);
}

/**
 * fvcn_add_error()
 *
 * @param string $code
 * @param string $message
 * @param string $data
 */
function fvcn_add_error($code = '', $message = '', $data = '')
{
    fvcn_container_get(WP_Error::class)->add($code, $message, $data);
}

/**
 * fvcn_has_errors()
 *
 * @return bool
 */
function fvcn_has_errors()
{
    $hasErrors = false;

    if (fvcn_container_get(WP_Error::class)->get_error_codes()) {
        $hasErrors = true;
    }

    return apply_filters('fvcn_has_errors', $hasErrors, fvcn_container_get(WP_Error::class));
}

/**
 * fvcn_add_featured_image_theme_support()
 *
 */
function fvcn_add_thumbnail_theme_support()
{
    $reg = fvcn_container_get('Registry');
    if (true === get_theme_support('post-thumbnails')) {
        $reg['nativeThumbnailSupport'] = true;
    } else {
        $reg['nativeThumbnailSupport'] = false;
        add_theme_support('post-thumbnails', [PostType::POST_TYPE_KEY, fvcn_get_post_slug()]);
    }
}

/**
 * fvcn_fix_post_author()
 *
 * @param array $data
 * @param array $postarr
 * @return array
 */
function fvcn_fix_post_author($data = [], $postarr = [])
{
    if (empty($postarr['ID']) || empty($data['post_author'])) {
        return $data;
    }
    if ($data['post_type'] != PostType::POST_TYPE_KEY) {
        return $data;
    }
    if (!fvcn_is_post_anonymous($postarr['ID'])) {
        return $data;
    }

    $data['post_author'] = 0;

    return $data;
}

/**
 * fvcn_send_notification_mail()
 *
 * @param int $postId
 * @return bool
 */
function fvcn_send_notification_mail($postId)
{
    if (!fvcn_mail_on_submission() && !fvcn_mail_on_moderation()) {
        return false;
    }
    if (PostType::STATUS_SPAM == fvcn_get_post_status($postId)) {
        return false;
    }

    if (PostType::STATUS_PENDING == fvcn_get_post_status($postId) && (fvcn_mail_on_submission() || fvcn_mail_on_moderation())) {
        $subject =    '[' . get_option('blogname') . '] ' . __('New Community Post Awaiting Moderation', 'fvcn');
        $moderationPage = add_query_arg(['post_type' => PostType::POST_TYPE_KEY, 'post_status' => PostType::STATUS_PENDING], site_url('/wp-admin/edit.php'));
    } else if (fvcn_mail_on_submission()) {
        $subject =    '[' . get_option('blogname') . '] ' . __('New Community Post', 'fvcn');
        $moderationPage = add_query_arg(['post_type' => PostType::POST_TYPE_KEY], site_url('/wp-admin/edit.php'));
    } else {
        return false;
    }

    $to = get_option('admin_email');
    if (!is_email($to)) {
        return false;
    }

    $message = '<html><head><style type="text/css">*,html{padding:0;margin:0}body{font:11px/17px"Lucida Grande","Lucida Sans Unicode",Helvetica,Arial,Verdana;color:#333}a{color:#08c;text-decoration:underline}#container{margin:10px auto;width:450px}.column{display:inline-block;vertical-align:top}#post-thumbnail{width:90px;padding:5px}#post-info{width:350px;padding:2px 0 0}#post-info p{margin:2px 0 0}#post-info p span{float:left;width:85px;text-align:right;margin-right:8px;font-weight:700}#post-content{padding:3px 5px 0}#post-actions{padding:5px}</style></head><body><div id="container"><div id="post-details"><div id="post-thumbnail" class="column">' . (fvcn_has_post_thumbnail($postId) ? fvcn_get_post_thumbnail($postId, [90, 90]) : fvcn_get_post_author_avatar($postId, 90)) . '</div><div id="post-info" class="column"><p><span>' . __('Author Name', 'fvcn') . '</span>' . fvcn_get_post_author_display_name($postId) . ' (<a href="http://whois.arin.net/rest/ip/' . fvcn_get_post_author_ip($postId) . '">' . fvcn_get_post_author_ip($postId) . '</a>)</p><p><span>' . __('Author Email', 'fvcn') . '</span><a href="mailto:' . fvcn_get_post_author_email($postId) . '">' . fvcn_get_post_author_email($postId) . '</a></p><p><span>' . __('Title', 'fvcn') . '</span>' . fvcn_get_post_title($postId) . '</p><p><span>' . __('Link', 'fvcn') . '</span>' . (fvcn_has_post_link($postId) ? '<a href="' . fvcn_get_post_link($postId) . '">' . parse_url(fvcn_get_post_link($postId), PHP_URL_HOST) . '</a>' : __('No Link Added', 'fvcn')) . '</p><p><span>' . __('Tags', 'fvcn') . '</span>' . fvcn_get_post_tag_list($postId, ['before'=>'', 'after'=>'']) . '</p></div></div><div id="post-content">' . fvcn_get_post_content($postId) . '</div><div id="post-actions"><a href="' . $moderationPage . '">' . __('Moderation Page', 'fvcn') . '</a> | <a href="' . add_query_arg(['post' => $postId, 'action' => 'edit'], site_url('/wp-admin/post.php')) . '">' . __('Edit Post', 'fvcn') . '</a> | <a href="' . fvcn_get_post_permalink($postId) . '">' . __('Permalink', 'fvcn') . '</a></div></div></body></html>';

    $headers = [
        'Content-Type: text/html',
    ];

    return wp_mail($to, $subject, $message, $headers);
}


///////// Admin Functions

/**
 * fvcn_get_form_option()
 *
 * @param string $option
 * @param bool $slug
 * @return mixed
 */
function fvcn_get_form_option($option, $slug = false)
{
    $value = fvcn_container_get('Config')[$option];

    if (true === $slug) {
        $value = apply_filters('editable_slug', $value);
    }

    return apply_filters('fvcn_get_form_option', esc_attr($value));
}


/**
 * is_fvcn()
 *
 * @return bool
 */
function is_fvcn(): bool
{
    if (fvcn_is_single_post()) {
        return true;
    }
    if (fvcn_is_post_archive()) {
        return true;
    }
    if (fvcn_is_post_tag_archive()) {
        return true;
    }

    return false;
}

/**
 * fvcn_show_widget_thumbnail()
 *
 * @return bool
 */
function fvcn_show_widget_thumbnail(): bool
{
    $registry = fvcn_container_get('Registry');
    return $registry['widgetShowThumbnail'];
}

/**
 * fvcn_show_widget_view_all()
 *
 * @return bool
 */
function fvcn_show_widget_view_all(): bool
{
    $registry = fvcn_container_get('Registry');
    return $registry['widgetShowViewAll'];
}

/**
 * fvcn_increase_post_view_count()
 *
 * @param string $template
 * @return string
 */
function fvcn_increase_post_view_count(string $template): string
{
    if (!fvcn_is_single_post()) {
        return $template;
    }

    $id = (int) fvcn_get_post_id();
    if (isset($_COOKIE['fvcn_post_viewed_' . $id . '_' . COOKIEHASH])) {
        return $template;
    }

    $postMapper = fvcn_container_get(\FvCommunityNews\Post\Mapper::class);
    $postMapper->increasePostViewCount($id);

    setcookie('fvcn_post_viewed_' . $id . '_' . COOKIEHASH, 'true', 0, COOKIEPATH, COOKIE_DOMAIN);

    return $template;
}
