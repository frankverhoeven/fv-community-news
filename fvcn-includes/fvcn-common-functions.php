<?php

use FvCommunityNews\Container;
use FvCommunityNews\Options;
use FvCommunityNews\Post\PostType;
use FvCommunityNews\Registry;

/**
 * fvcn_add_error()
 *
 * @version 20120229
 * @param string $code
 * @param string $message
 * @param string $data
 */
function fvcn_add_error($code='', $message='', $data='')
{
    Container::getInstance()->getWpError()->add($code, $message, $data);
}

/**
 * fvcn_has_errors()
 *
 * @version 20120229
 * @return bool
 */
function fvcn_has_errors()
{
    $hasErrors = false;

    if (Container::getInstance()->getWpError()->get_error_codes()) {
        $hasErrors = true;
    }

    return apply_filters('fvcn_has_errors', $hasErrors, Container::getInstance()->getWpError());
}

/**
 * fvcn_add_featured_image_theme_support()
 *
 * @version 20120805
 */
function fvcn_add_thumbnail_theme_support()
{
    if (true === get_theme_support('post-thumbnails')) {
        Registry::set('nativeThumbnailSupport', true);
    } else {
        Registry::set('nativeThumbnailSupport', false);
        add_theme_support('post-thumbnails', [PostType::POST_TYPE_KEY, fvcn_get_post_slug()]);
    }
}

/**
 * fvcn_fix_post_author()
 *
 * @version 20120321
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
 * @version 20120712
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
        $moderationPage = add_query_arg(['post_type' => PostType::POST_TYPE_KEY, 'post_status' => PostType::STATUS_PENDING], home_url('/wp-admin/edit.php'));
    } else if (fvcn_mail_on_submission()) {
        $subject =    '[' . get_option('blogname') . '] ' . __('New Community Post', 'fvcn');
        $moderationPage = add_query_arg(['post_type' => PostType::POST_TYPE_KEY], home_url('/wp-admin/edit.php'));
    } else {
        return false;
    }

    $to = get_option('admin_email');
    if (!is_email($to)) {
        return false;
    }

    $message = '<html><head><style type="text/css">*,html{padding:0;margin:0}body{font:11px/17px"Lucida Grande","Lucida Sans Unicode",Helvetica,Arial,Verdana;color:#333}a{color:#08c;text-decoration:underline}#container{margin:10px auto;width:450px}.column{display:inline-block;vertical-align:top}#post-thumbnail{width:90px;padding:5px}#post-info{width:350px;padding:2px 0 0}#post-info p{margin:2px 0 0}#post-info p span{float:left;width:85px;text-align:right;margin-right:8px;font-weight:700}#post-content{padding:3px 5px 0}#post-actions{padding:5px}</style></head><body><div id="container"><div id="post-details"><div id="post-thumbnail" class="column">' . (fvcn_has_post_thumbnail($postId) ? fvcn_get_post_thumbnail($postId, [90, 90]) : fvcn_get_post_author_avatar($postId, 90)) . '</div><div id="post-info" class="column"><p><span>' . __('Author Name', 'fvcn') . '</span>' . fvcn_get_post_author_display_name($postId) . ' (<a href="http://whois.arin.net/rest/ip/' . fvcn_get_post_author_ip($postId) . '">' . fvcn_get_post_author_ip($postId) . '</a>)</p><p><span>' . __('Author Email', 'fvcn') . '</span><a href="mailto:' . fvcn_get_post_author_email($postId) . '">' . fvcn_get_post_author_email($postId) . '</a></p><p><span>' . __('Title', 'fvcn') . '</span>' . fvcn_get_post_title($postId) . '</p><p><span>' . __('Link', 'fvcn') . '</span>' . (fvcn_has_post_link($postId) ? '<a href="' . fvcn_get_post_link($postId) . '">' . parse_url(fvcn_get_post_link($postId), PHP_URL_HOST) . '</a>' : __('No Link Added', 'fvcn')) . '</p><p><span>' . __('Tags', 'fvcn') . '</span>' . fvcn_get_post_tag_list($postId, ['before'=>'', 'after'=>'']) . '</p></div></div><div id="post-content">' . fvcn_get_post_content($postId) . '</div><div id="post-actions"><a href="' . $moderationPage . '">' . __('Moderation Page', 'fvcn') . '</a> | <a href="' . add_query_arg(['post' => $postId, 'action' => 'edit'], home_url('/wp-admin/post.php')) . '">' . __('Edit Post', 'fvcn') . '</a> | <a href="' . fvcn_get_post_permalink($postId) . '">' . __('Permalink', 'fvcn') . '</a></div></div></body></html>';

    $headers = [
        'Content-Type: text/html',
    ];

    return wp_mail($to, $subject, $message, $headers);
}


///////// Admin Functions

/**
 * fvcn_form_option()
 *
 * @version 20120524
 * @param string $option
 * @param bool $slug
 */
function fvcn_form_option($option, $slug=false)
{
    echo fvcn_get_form_option($option, $slug);
}

/**
 * fvcn_get_form_option()
 *
 * @version 20120524
 * @param string $option
 * @param bool $slug
 * @return mixed
 */
function fvcn_get_form_option($option, $slug=false)
{
    $value = Options::fvcnGetOption($option);

    if (true === $slug) {
        $value = apply_filters('editable_slug', $value);
    }

    return apply_filters('fvcn_get_form_option', esc_attr($value));
}
