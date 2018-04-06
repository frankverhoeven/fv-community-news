<?php

use FvCommunityNews\Post\Status as PostStatus;
use FvCommunityNews\Post\Type as PostType;

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
        add_theme_support('post-thumbnails', [PostType::post(), fvcn_get_post_slug()]);
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
    if ($data['post_type'] != PostType::post()) {
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
    /* @var \FvCommunityNews\Config\AbstractConfig $config */
    $config = fvcn_container_get('Config');

    if (!$config['_fvcn_mail_on_submission'] && !$config['_fvcn_mail_on_moderation']) {
        return false;
    }
    if (PostStatus::spam() == fvcn_get_post_status($postId)) {
        return false;
    }

    if (PostStatus::pending() == fvcn_get_post_status($postId) && ($config['_fvcn_mail_on_submission'] || $config['_fvcn_mail_on_moderation'])) {
        $subject =    '[' . get_option('blogname') . '] ' . __('New Community Post Awaiting Moderation', 'fvcn');
        $moderationPage = add_query_arg(['post_type' => PostType::post(), 'post_status' => PostStatus::pending()], site_url('/wp-admin/edit.php'));
    } else if ($config['_fvcn_mail_on_submission']) {
        $subject =    '[' . get_option('blogname') . '] ' . __('New Community Post', 'fvcn');
        $moderationPage = add_query_arg(['post_type' => PostType::post()], site_url('/wp-admin/edit.php'));
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

    return apply_filters('fvcn_get_form_option', $value);
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

    $postMapper = fvcn_container_get(\FvCommunityNews\Post\Mapper::class);
    $postMapper->increasePostViewCount($id);

    return $template;
}

/**
 * fvcn_is_anonymous()
 *
 * @return bool
 */
function fvcn_is_anonymous(): bool
{
    if (!is_user_logged_in()) {
        $isAnonymous = true;
    } else {
        $isAnonymous = false;
    }

    return apply_filters('fvcn_is_anonymous', $isAnonymous);
}

/**
 * fvcn_get_current_author_ip()
 *
 * @return string
 */
function fvcn_get_current_author_ip(): string
{
    $ip = preg_replace('/[^0-9a-fA-F:., ]/', '', $_SERVER['REMOTE_ADDR']);
    return apply_filters('fvcn_get_current_author_ip', $ip);
}

/**
 * fvcn_get_current_author_ua()
 *
 * @return string
 */
function fvcn_get_current_author_ua(): string
{
    if (!empty($_SERVER['HTTP_USER_AGENT'])) {
        $ua = substr($_SERVER['HTTP_USER_AGENT'], 0, 254);
    } else {
        $ua = '';
    }

    return apply_filters('fvcn_get_current_author_ua', $ua);
}

/**
 * fvcn_user_id()
 *
 * @param int $userId
 */
function fvcn_user_id(int $userId = 0)
{
    echo fvcn_get_user_id($userId);
}

/**
 * fvcn_get_user_id()
 *
 * @param int $userId
 * @return int
 */
function fvcn_get_user_id(int $userId = 0): int
{
    if (0 < $userId) {
        $id = $userId;
    } elseif (!fvcn_is_anonymous()) {
        $id = fvcn_get_current_user_id();
    } else {
        $id = 0;
    }

    return apply_filters('fvcn_get_user_id', $id);
}


/**
 * fvcn_current_user_id()
 *
 */
function fvcn_current_user_id()
{
    echo fvcn_get_current_user_id();
}

/**
 * fvcn_get_current_user_id()
 *
 * @return int
 */
function fvcn_get_current_user_id(): int
{
    $currentUser = wp_get_current_user();
    return apply_filters('fvcn_get_current_user_id', $currentUser->ID);
}


/**
 * fvcn_current_user_name()
 *
 */
function fvcn_current_user_name()
{
    echo fvcn_get_current_user_name();
}

/**
 * fvcn_get_current_user_name()
 *
 * @return string
 */
function fvcn_get_current_user_name(): string
{
    $currentUser = wp_get_current_user();
    return apply_filters('fvcn_get_current_user_name', $currentUser->display_name);
}


/**
 * fvcn_has_user_posts()
 *
 * @param int $userId
 * @param string|null $postStatus
 * @return bool
 */
function fvcn_has_user_posts(int $userId = 0, string $postStatus = ''): bool
{
    $id = fvcn_get_user_id($userId);

    if (0 == $id) {
        $retval = false;
    } else {
        if (empty($postStatus)) {
            $postStatus = PostStatus::publish();
        }

        $retval = fvcn_has_posts([
            'author' => $id,
            'post_status' => $postStatus
        ]);
    }

    return apply_filters('fvcn_has_user_posts', $retval);
}

/**
 * fvcn_post_slug()
 *
 */
function fvcn_post_slug()
{
    echo fvcn_get_post_slug();
}

/**
 * fvcn_get_post_slug()
 *
 * @return string
 */
function fvcn_get_post_slug(): string
{
    return apply_filters('fvcn_get_post_slug', fvcn_container_get('Registry')['postSlug']);
}


/**
 * fvcn_has_posts()
 *
 * @param array|string $args
 * @return bool
 */
function fvcn_has_posts($args = ''): bool
{
    $defaults = [
        'post_type' => PostType::post(),
        'post_status' => PostStatus::publish(),
        'posts_per_page'=> 15,
        'order' => 'DESC'
    ];

    $options = wp_parse_args($args, $defaults);
    $options = apply_filters('fvcn_has_posts_query', $options);

    $registry = fvcn_container_get('Registry');
    $registry['wpQuery'] = new WP_Query($options);

    return apply_filters('fvcn_has_posts', $registry['wpQuery']->have_posts(), $registry['wpQuery']);
}


/**
 * fvcn_posts()
 *
 * @return bool
 */
function fvcn_posts(): bool
{
    /* @var WP_Query $wpQuery */
    $wpQuery = fvcn_container_get('Registry')['wpQuery'];
    $havePosts = $wpQuery->have_posts();

    if (!$havePosts) {
        $wpQuery->reset_postdata();
    }

    return $havePosts;
}


/**
 * fvcn_the_post()
 *
 * @return void
 */
function fvcn_the_post()
{
    /* @var WP_Query $wpQuery */
    $wpQuery = fvcn_container_get('Registry')['wpQuery'];
    $wpQuery->the_post();
}


/**
 * fvcn_post_id()
 *
 * @param int $postId
 */
function fvcn_post_id(int $postId = 0)
{
    echo fvcn_get_post_id($postId);
}

/**
 * fvcn_get_post_id()
 *
 * @param int $postId
 * @return int
 */
function fvcn_get_post_id(int $postId = 0): int
{
    global $wp_query, $post;
    /* @var WP_Query $wpQuery */
    $wpQuery = fvcn_container_get('Registry')['wpQuery'];

    if (0 < $postId) {
        $id = $postId;
    } elseif (null !== $wpQuery && $wpQuery->in_the_loop && isset($wpQuery->post->ID)) {
        $id = $wpQuery->post->ID;
    } elseif (fvcn_is_single_post() && isset($wp_query->post->ID)) {
        $id = $wp_query->post->ID;
    } elseif (isset($post->ID)) {
        $id = $post->ID;
    } else {
        $id = 0;
    }

    return apply_filters('fvcn_get_post_id', $id, $postId);
}


/**
 * fvcn_get_post()
 *
 * @param int $postId
 * @return WP_Post|null
 */
function fvcn_get_post(int $postId = 0)
{
    $id = fvcn_get_post_id($postId);

    if (empty($id)) {
        return null;
    }

    $post = get_post($id, OBJECT);

    if (!$post || $post->post_type != PostType::post()) {
        return null;
    }

    return apply_filters('fvcn_get_post', $post);
}


/**
 * fvcn_post_permalink()
 *
 * @param int $postId
 */
function fvcn_post_permalink(int $postId = 0)
{
    echo fvcn_get_post_permalink($postId);
}

/**
 * fvcn_get_post_permalink()
 *
 * @param int $postId
 * @param string $redirect
 * @return string
 */
function fvcn_get_post_permalink(int $postId = 0, string $redirect = ''): string
{
    $id = fvcn_get_post_id($postId);

    if (!empty($redirect)) {
        $permalink = esc_url($redirect);
    } else {
        $permalink = get_permalink($id);
    }

    return apply_filters('fvcn_get_post_permalink', $permalink, $id);
}


/**
 * fvcn_has_post_link()
 *
 * @param int $postId
 * @return bool
 */
function fvcn_has_post_link(int $postId = 0): bool
{
    $link = fvcn_get_post_link($postId);
    return !empty($link);
}


/**
 * fvcn_post_link()
 *
 * @param int $postId
 */
function fvcn_post_link(int $postId = 0)
{
    echo fvcn_get_post_link($postId);
}

/**
 * fvcn_get_post_link()
 *
 * @param int $postId
 * @return string|null
 */
function fvcn_get_post_link(int $postId = 0)
{
    $id = fvcn_get_post_id($postId);
    $link = esc_url(get_post_meta($id, '_fvcn_post_url', true));

    return apply_filters('fvcn_get_post_link', $link, $id);
}


/**
 * fvcn_post_title()
 *
 * @param int $postId
 */
function fvcn_post_title(int $postId = 0)
{
    echo fvcn_get_post_title($postId);
}

/**
 * fvcn_get_post_title()
 *
 * @param int $postId
 * @return string
 */
function fvcn_get_post_title(int $postId = 0): string
{
    $id = fvcn_get_post_id($postId);
    return apply_filters('fvcn_get_post_title', get_the_title($id), $id);
}


/**
 * fvcn_post_content()
 *
 * @param int $postId
 */
function fvcn_post_content(int $postId = 0)
{
    echo fvcn_get_post_content($postId);
}

/**
 * fvcn_get_post_content()
 *
 * @param int $postId
 * @return string
 */
function fvcn_get_post_content(int $postId = 0): string
{
    $id = fvcn_get_post_id($postId);

    if (post_password_required($id)) {
        return get_the_password_form();
    }

    $content = get_post_field('post_content', $id);

    return apply_filters('fvcn_get_post_content', $content, $id);
}


/**
 * fvcn_post_excerpt()
 *
 * @param int $postId
 * @param int $length
 */
function fvcn_post_excerpt(int $postId = 0, $length = 100) {
    echo fvcn_get_post_excerpt($postId, $length);
}

/**
 * fvcn_get_post_excerpt()
 *
 * @param int $postId
 * @param int $length
 * @return string
 */
function fvcn_get_post_excerpt(int $postId = 0, $length = 100) {
    $id = fvcn_get_post_id($postId);
    $length = abs((int)$length);

    if (post_password_required($id)) {
        return apply_filters('fvcn_get_post_excerpt', '');
    }

    $excerpt = get_post_field('post_excerpt', $id);

    if (empty($excerpt)) {
        $excerpt = get_post_field('post_content', $id);
    }

    $excerpt = trim(strip_tags($excerpt));

    if (!empty($length) && strlen($excerpt) > $length) {
        $string = ''; $i = 0;
        $array = explode(' ', $excerpt);

        while (strlen($string) < $length) {
            $string .= $array[ $i ] . ' ';
            $i++;
        }

        if (trim($string) != $excerpt) {
            $excerpt = trim($string) . '&hellip;';
        }
    }

    return apply_filters('fvcn_get_post_excerpt', $excerpt, $id);
}


/**
 * fvcn_post_date()
 *
 * @param int $postId
 * @param string $format
 */
function fvcn_post_date(int $postId = 0, string $format = '') {
    echo fvcn_get_post_date($postId, $format);
}

/**
 * fvcn_get_post_date()
 *
 * @param int $postId
 * @param string $format
 * @return string
 */
function fvcn_get_post_date(int $postId = 0, string $format = '') {
    $id = fvcn_get_post_id($postId);

    if (empty($format)) {
        $date = mysql2date(get_option('date_format'), get_post_field('post_date', $id));
    } else {
        $date = mysql2date($format, get_post_field('post_date', $id));
    }

    return apply_filters('fvcn_get_post_date', $date, $id);
}


/**
 * fvcn_post_time()
 *
 * @param int $postId
 * @param string $format
 * @param bool $gmt
 */
function fvcn_post_time(int $postId = 0, string $format = '', $gmt = false) {
    echo fvcn_get_post_time($postId, $format, $gmt);
}

/**
 * fvcn_get_post_time()
 *
 * @param int $postId
 * @param string $format
 * @param bool $gmt
 * @return string
 */
function fvcn_get_post_time(int $postId = 0, string $format = '', $gmt = false) {
    $id = fvcn_get_post_id($postId);

    if ($gmt) {
        $date = get_post_field('post_date_gmt', $id);
    } else {
        $date = get_post_field('post_date', $id);
    }

    if (empty($format)) {
        $time = mysql2date(get_option('time_format'), $date);
    } else {
        $time = mysql2date($format, $date);
    }

    return apply_filters('fvcn_get_post_time', $time, $id);
}


/**
 * fvcn_has_post_thumbnail()
 *
 * @param int $postId
 * @return bool
 */
function fvcn_has_post_thumbnail(int $postId = 0)
{
    $id = fvcn_get_post_id($postId);
    $registry = fvcn_container_get('Registry');

    // Double thumbnail display fix.
    if ('the_content' != current_filter() || false === $registry['nativeThumbnailSupport'] || is_archive()) {
        return has_post_thumbnail($id);
    } else {
        return false;
    }
}


/**
 * fvcn_post_thumbnail()
 *
 * @param int $postId
 * @param string|array $size
 * @param string|array $attributes
 */
function fvcn_post_thumbnail(int $postId = 0, $size = 'thumbnail', $attributes = [])
{
    echo fvcn_get_post_thumbnail($postId, $size, $attributes);
}

/**
 * fvcn_get_post_thumbnail()
 *
 * @param int $postId
 * @param string|array $size
 * @param string|array $attributes
 * @return string
 */
function fvcn_get_post_thumbnail(int $postId = 0, $size = 'thumbnail', $attributes= [])
{
    $id = fvcn_get_post_id($postId);
    return apply_filters('fvcn_get_post_thumbnail', get_the_post_thumbnail($id, $size, $attributes), $id);
}


/**
 * fvcn_post_likes()
 *
 * @param int $postId
 */
function fvcn_post_likes(int $postId = 0)
{
    echo fvcn_get_post_likes($postId);
}

/**
 * fvcn_get_post_likes()
 *
 * @param int $postId
 * @return int
 */
function fvcn_get_post_likes(int $postId = 0)
{
    $id = fvcn_get_post_id($postId);
    $likes = get_post_meta($id, '_fvcn_post_likes', true);

    if (!is_numeric($likes)) {
        $likes = 0;
    }

    return apply_filters('fvcn_get_post_likes', $likes, $id);
}

/**
 * fvcn_post_like_button()
 *
 * @param int $postId
 * @return void
 */
function fvcn_post_like_button(int $postId = 0)
{
    echo fvcn_get_post_like_button($postId);
}

/**
 * fvcn_get_post_like_button()
 *
 * @param int $postId
 * @return string
 */
function fvcn_get_post_like_button(int $postId = 0): string
{
    $id = fvcn_get_post_id($postId);

    $button = '<form method="post" action="' . fvcn_get_post_permalink($id) . '" class="fvcn-like-form">';

    $button .= '<button type="submit" class="fvcn-like-button" title="' . __('Like', 'fvcn') . '">';
    $button .= '<svg aria-hidden="true" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" style="height: 1em"><path fill="currentColor" d="M257.3 475.4L92.5 313.6C85.4 307 24 248.1 24 174.8 24 84.1 80.8 24 176 24c41.4 0 80.6 22.8 112 49.8 31.3-27 70.6-49.8 112-49.8 91.7 0 152 56.5 152 150.8 0 52-31.8 103.5-68.1 138.7l-.4.4-164.8 161.5a43.7 43.7 0 0 1-61.4 0zM125.9 279.1L288 438.3l161.8-158.7c27.3-27 54.2-66.3 54.2-104.8C504 107.9 465.8 72 400 72c-47.2 0-92.8 49.3-112 68.4-17-17-64-68.4-112-68.4-65.9 0-104 35.9-104 102.8 0 37.3 26.7 78.9 53.9 104.3z"></path></svg> ';
    $button .= fvcn_get_post_likes($id);
    $button .= '</button>';
    $button .= '<input type="hidden" name="fvcn-post-like-action" value="like">';
    $button .= '<input type="hidden" name="fvcn-post-id" value="' . $id . '">';
    $button .= wp_nonce_field('fvcn-post-like', '_wpnonce', true, false);
    $button .= '</form>';

    return $button;
}

/**
 * fvcn_post_unlike_button()
 *
 * @param int $postId
 * @return void
 */
function fvcn_post_unlike_button(int $postId = 0)
{
    echo fvcn_get_post_unlike_button($postId);
}

/**
 * fvcn_get_post_unlike_button()
 *
 * @param int $postId
 * @return string
 */
function fvcn_get_post_unlike_button(int $postId = 0): string
{
    $id = fvcn_get_post_id($postId);

    $button = '<form method="post" action="' . fvcn_get_post_permalink($id) . '" class="fvcn-like-form">';

    $button .= '<button type="submit" class="fvcn-unlike-button" title="' . __('Unlike', 'fvcn') . '">';
    $button .= '<svg aria-hidden="true" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" style="height: 1em"><path fill="currentColor" d="M414.9 24C361.8 24 312 65.7 288 89.3 264 65.7 214.2 24 161.1 24 70.3 24 16 76.9 16 165.5c0 72.6 66.8 133.3 69.2 135.4l187 180.8c8.8 8.5 22.8 8.5 31.6 0l186.7-180.2c2.7-2.7 69.5-63.5 69.5-136C560 76.9 505.7 24 414.9 24z"></path></svg> ';
    $button .= fvcn_get_post_likes($id);
    $button .= '</button>';
    $button .= '<input type="hidden" name="fvcn-post-like-action" value="unlike">';
    $button .= '<input type="hidden" name="fvcn-post-id" value="' . $id . '">';
    $button .= wp_nonce_field('fvcn-post-like', '_wpnonce', true, false);
    $button .= '</form>';

    return $button;
}

/**
 * fvcn_is_post_liked_by_current_user()
 *
 * @param int $postId
 * @return bool
 */
function fvcn_is_post_liked_by_current_user(int $postId = 0): bool
{
    $id = fvcn_get_post_id($postId);
    return apply_filters('fvcn_is_post_liked_by_current_user', isset($_COOKIE['fvcn_post_liked_' . $id . '_' . COOKIEHASH]));
}


/**
 * fvcn_post_views()
 *
 * @param int $postId
 */
function fvcn_post_views(int $postId = 0)
{
    echo fvcn_get_post_views($postId);
}

/**
 * fvcn_get_post_views()
 *
 * @param int $postId
 * @return int
 */
function fvcn_get_post_views(int $postId = 0)
{
    $postId = fvcn_get_post_id($postId);

    $views = get_post_meta($postId, '_fvcn_post_views', true);

    if (!is_numeric($views)) {
        $views = 0;
    }

    return apply_filters('fvcn_get_post_views', $views, $postId);
}


/**
 * fvcn_post_status()
 *
 * @param int $postId
 */
function fvcn_post_status(int $postId = 0)
{
    echo fvcn_get_post_status($postId);
}

/**
 * fvcn_get_post_status()
 *
 * @param int $postId
 * @return string
 */
function fvcn_get_post_status(int $postId = 0)
{
    $id = fvcn_get_post_id($postId);

    return apply_filters('fvcn_get_post_status', get_post_status($id), $id);
}


/**
 * fvcn_post_archive_link()
 *
 */
function fvcn_post_archive_link()
{
    echo fvcn_get_post_archive_link();
}

/**
 * fvcn_get_post_archive_link()
 *
 * @return string
 */
function fvcn_get_post_archive_link()
{
    $link = get_post_type_archive_link(PostType::post()->getType());

    return apply_filters('fvcn_get_post_archive_link', $link);
}

/**
 * fvcn_tag_cloud()
 *
 * @param array|string $args
 * @return void
 */
function fvcn_tag_cloud($args = '')
{
    $default = ['taxonomy' => PostType::tag()];
    $args = wp_parse_args($args, $default);

    wp_tag_cloud($args);
}

/**
 * fvcn_is_post()
 *
 * @param int $postId
 * @return bool
 */
function fvcn_is_post(int $postId = 0): bool
{
    $is_post = false;

    if (!empty($postId) && PostType::post() == get_post_type($postId)) {
        $is_post = true;
    }

    return apply_filters('fvcn_is_post', $is_post, $postId);
}


/**
 * fvcn_is_post_published()
 *
 * @param int $postId
 * @return bool
 */
function fvcn_is_post_published(int $postId = 0)
{
    return PostStatus::publish() == fvcn_get_post_status(fvcn_get_post_id($postId));
}


/**
 * fvcn_is_post_pending()
 *
 * @param int $postId
 * @return bool
 */
function fvcn_is_post_pending(int $postId = 0)
{
    return PostStatus::pending() == fvcn_get_post_status(fvcn_get_post_id($postId));
}


/**
 * fvcn_is_post_trash()
 *
 * @param int $postId
 * @return bool
 */
function fvcn_is_post_trash(int $postId = 0)
{
    return PostStatus::trash() == fvcn_get_post_status(fvcn_get_post_id($postId));
}


/**
 * fvcn_is_post_spam()
 *
 * @param int $postId
 * @return bool
 */
function fvcn_is_post_spam(int $postId = 0)
{
    return PostStatus::trash() == fvcn_get_post_status(fvcn_get_post_id($postId));
}


/**
 * fvcn_is_post_private()
 *
 * @param int $postId
 * @return bool
 */
function fvcn_is_post_private(int $postId = 0)
{
    return PostStatus::private() == fvcn_get_post_status(fvcn_get_post_id($postId));
}


/**
 * fvcn_is_post_anonymous()
 *
 * @param int $postId
 * @return bool
 */
function fvcn_is_post_anonymous(int $postId = 0)
{
    $id = fvcn_get_post_id($postId);

    if (0 !== fvcn_get_post_author_id($id)) {
        return false;
    }
    if (false == get_post_meta($id, '_fvcn_anonymous_author_name', true)) {
        return false;
    }
    if (false == get_post_meta($id, '_fvcn_anonymous_author_email', true)) {
        return false;
    }

    return true;
}


/**
 * fvcn_is_single_post()
 *
 * @return bool
 */
function fvcn_is_single_post()
{
    $retval = false;

    if (is_singular(PostType::post())) {
        $retval = true;
    }

    return apply_filters('fvcn_is_single_post', $retval);
}


/**
 * fvcn_is_post_archive()
 *
 * @return bool
 */
function fvcn_is_post_archive()
{
    $retval = false;

    if (is_post_type_archive(PostType::post())) {
        $retval = true;
    }

    return apply_filters('fvcn_is_post_archive', $retval);
}


/**
 * fv_is_post_tag_archive()
 *
 * @return bool
 */
function fvcn_is_post_tag_archive()
{
    $retval = false;

    if (is_tax(PostType::tag())) {
        $retval = true;
    }

    return apply_filters('fvcn_is_post_tag_archive', $retval);
}


/**
 * fvcn_post_author()
 *
 * @param int $postId
 */
function fvcn_post_author(int $postId = 0)
{
    echo fvcn_get_post_author($postId);
}

/**
 * fvcn_get_post_author()
 *
 * @param int $postId
 * @return string
 */
function fvcn_get_post_author(int $postId = 0)
{
    $id = fvcn_get_post_id($postId);

    if (!fvcn_is_post_anonymous($id)) {
        $author = get_the_author_meta('display_name', fvcn_get_post_author_id($id));
    } else {
        $author = get_post_meta($id, '_fvcn_anonymous_author_name', true);
    }

    return apply_filters('fvcn_get_post_author', $author, $id);
}


/**
 * fvcn_post_author_id()
 *
 * @param int $postId
 */
function fvcn_post_author_id(int $postId = 0)
{
    echo fvcn_get_post_author_id($postId);
}

/**
 * fvcn_get_post_author_id()
 *
 * @param int $postId
 * @return string
 */
function fvcn_get_post_author_id(int $postId = 0)
{
    $id = fvcn_get_post_id($postId);
    $author = get_post_field('post_author', $id);

    return apply_filters('fvcn_get_post_author_id', (int)$author, $id);
}


/**
 * fvcn_post_author_display_name()
 *
 * @param int $postId
 */
function fvcn_post_author_display_name(int $postId = 0)
{
    echo fvcn_get_post_author_display_name($postId);
}

/**
 * fvcn_get_post_author_display_name()
 *
 * @param int $postId
 * @return string
 */
function fvcn_get_post_author_display_name(int $postId = 0)
{
    $id = fvcn_get_post_id($postId);

    if (!fvcn_is_post_anonymous($id)) {
        $author_display_name = get_the_author_meta('display_name', fvcn_get_post_author_id($id));
    } else {
        $author_display_name = get_post_meta($id, '_fvcn_anonymous_author_name', true);
    }

    return apply_filters('fvcn_get_post_author_display_name', $author_display_name, $id);
}


/**
 * fvcn_post_author_email()
 *
 * @param int $postId
 */
function fvcn_post_author_email(int $postId = 0)
{
    echo fvcn_get_post_author_email($postId);
}

/**
 * fvcn_get_post_author_email()
 *
 * @param int $postId
 * @return string
 */
function fvcn_get_post_author_email(int $postId = 0)
{
    $id = fvcn_get_post_id($postId);

    if (!fvcn_is_post_anonymous($id)) {
        $email = get_the_author_meta('user_email', fvcn_get_post_author_id($id));
    } else {
        $email = get_post_meta($id, '_fvcn_anonymous_author_email', true);
    }

    return apply_filters('fvcn_get_post_author_email', $email, $id);
}


/**
 * fvcn_post_author_avatar()
 *
 * @param int $postId
 * @param int $size
 */
function fvcn_post_author_avatar(int $postId = 0, $size=40)
{
    echo fvcn_get_post_author_avatar($postId, $size);
}

/**
 * fvcn_get_post_author_avatar()
 *
 * @param int $postId
 * @param int $size
 * @return string
 */
function fvcn_get_post_author_avatar(int $postId = 0, $size=40)
{
    $avatar = get_avatar(fvcn_get_post_author_email($postId), $size);

    return apply_filters('fvcn_get_post_author_avatar', $avatar, $postId);
}


/**
 * fvcn_post_author_website()
 *
 * @param int $postId
 */
function fvcn_post_author_website(int $postId = 0)
{
    echo fvcn_get_post_author_website($postId);
}

/**
 * fvcn_get_post_author_website()
 *
 * @param int $postId
 * @return string
 */
function fvcn_get_post_author_website(int $postId = 0)
{
    $id = fvcn_get_post_id($postId);

    if (!fvcn_is_post_anonymous($id)) {
        $website = get_the_author_meta('user_url', fvcn_get_post_author_id($id));
    } else {
        $website = '';
    }

    return apply_filters('fvcn_get_post_author_website', $website, $id);
}


/**
 * fvcn_post_author_link()
 *
 * @param int $postId
 */
function fvcn_post_author_link(int $postId = 0)
{
    echo fvcn_get_post_author_link($postId);
}

/**
 * fvcn_get_post_author_link()
 *
 * @param int $postId
 * @return string
 */
function fvcn_get_post_author_link(int $postId = 0)
{
    $id = fvcn_get_post_id($postId);

    if ('' != fvcn_get_post_author_website($postId)) {
        $link = '<a href="' . fvcn_get_post_author_website($id) . '">' . fvcn_get_post_author_display_name($id) . '</a>';
    } else {
        $link = fvcn_get_post_author_display_name($id);
    }

    return apply_filters('fvcn_get_post_author_link', $link, $id);
}


/**
 * fvcn_post_author_ip()
 *
 * @param int $postId
 */
function fvcn_post_author_ip(int $postId = 0)
{
    echo fvcn_get_post_author_ip($postId);
}

/**
 * fvcn_get_post_author_ip()
 *
 * @param int $postId
 * @return string
 */
function fvcn_get_post_author_ip(int $postId = 0)
{
    $id = fvcn_get_post_id($postId);

    $ip = get_post_meta($id, '_fvcn_author_ip', true);

    return apply_filters('fvcn_get_post_author_ip', $ip, $id);
}


/**
 * fvcn_post_author_ua()
 *
 * @param int $postId
 */
function fvcn_post_author_ua(int $postId = 0)
{
    echo fvcn_get_post_author_ua($postId);
}

/**
 * fvcn_get_post_author_ua()
 *
 * @param int $postId
 * @return string
 */
function fvcn_get_post_author_ua(int $postId = 0)
{
    $id = fvcn_get_post_id($postId);

    $ua = get_post_meta($id, '_fvcn_author_ua', true);

    return apply_filters('fvcn_get_post_author_ua', $ua, $id);
}

/**
 * fvcn_post_tag_slug()
 *
 */
function fvcn_post_tag_slug()
{
    echo fvcn_get_post_tag_slug();
}

/**
 * fvcn_get_post_tag_slug()
 *
 * @return string
 */
function fvcn_get_post_tag_slug()
{
    $registry = fvcn_container_get('Registry');
    return apply_filters('fvcn_get_post_tag_slug', $registry['postTagSlug']);
}


/**
 * fvcn_post_tag_list()
 *
 * @param int $postId
 * @param string|array $args
 */
function fvcn_post_tag_list(int $postId = 0, $args = '')
{
    echo fvcn_get_post_tag_list($postId, $args);
}

/**
 * fvcn_get_post_tag_list()
 *
 * @param int $postId
 * @param string|array $args
 * @return string
 */
function fvcn_get_post_tag_list(int $postId = 0, $args = '')
{
    $id = fvcn_get_post_id($postId);

    $default = [
        'before' => '<div class="fvcn-post-tags"><p>' . __('Tags:', 'fvcn') . ' ',
        'sep' => ', ',
        'after' => '</p></div>'
    ];

    $args = wp_parse_args($args, $default);
    $before = $sep = $after = '';
    extract($args);

    $tagList = get_the_term_list($id, PostType::tag()->getType(), $before, $sep, $after);

    return apply_filters('fvcn_get_post_tag_list', $tagList, $id);
}


/**
 * fvcn_post_form_fields()
 *
 */
function fvcn_post_form_fields()
{
    ?>

    <input type="hidden" name="fvcn_post_form_action" id="fvcn_post_form_action" value="fvcn-new-post">
    <?php wp_nonce_field('fvcn-new-post', 'fvcn_post_form_nonce'); ?>

    <?php
}


/**
 * fvcn_post_form_field_error()
 *
 * @param string $field
 */
function fvcn_post_form_field_error(string $field)
{
    $errors = fvcn_container_get(WP_Error::class)->get_error_messages($field);

    if (empty($errors)) {
        return;
    }

    echo '<ul class="fvcn-template-notice error">';

    foreach ($errors as $error) {
        echo '<li>' . $error . '</li>';
    }

    echo '</ul>';
}


/**
 * fvcn_post_form_author_name_label()
 *
 */
function fvcn_post_form_author_name_label()
{
    echo fvcn_get_post_form_author_name_label();
}

/**
 * fvcn_get_post_form_author_name_label()
 *
 * @return string
 */
function fvcn_get_post_form_author_name_label(): string
{
    $label = esc_attr(fvcn_container_get('Config')['_fvcn_post_form_author_name_label']);
    return apply_filters('fvcn_get_post_form_author_name_label', $label);
}

/**
 * fvcn_post_form_author_name()
 *
 */
function fvcn_post_form_author_name()
{
    echo fvcn_get_post_form_author_name();
}

/**
 * fvcn_get_post_form_author_name()
 *
 * @return string
 */
function fvcn_get_post_form_author_name()
{
    if ('post' == strtolower($_SERVER['REQUEST_METHOD'])) {
        $value = $_POST['fvcn_post_form_author_name'];
    } else {
        $value = '';
    }

    return apply_filters('fvcn_get_post_form_author_name', esc_attr($value));
}


/**
 * fvcn_post_form_author_email_label()
 *
 */
function fvcn_post_form_author_email_label()
{
    echo fvcn_get_post_form_author_email_label();
}

/**
 * fvcn_get_post_form_author_email_label()
 *
 * @return string
 */
function fvcn_get_post_form_author_email_label(): string
{
    $label = esc_attr(fvcn_container_get('Config')['_fvcn_post_form_author_email_label']);
    return apply_filters('fvcn_get_post_form_author_email_label', $label);
}

/**
 * fvcn_post_form_author_email()
 *
 */
function fvcn_post_form_author_email()
{
    echo fvcn_get_post_form_author_email();
}

/**
 * fvcn_get_post_form_author_email()
 *
 * @return string
 */
function fvcn_get_post_form_author_email()
{
    if ('post' == strtolower($_SERVER['REQUEST_METHOD'])) {
        $value = $_POST['fvcn_post_form_author_email'];
    } else {
        $value = '';
    }

    return apply_filters('fvcn_get_post_form_author_email', esc_attr($value));
}


/**
 * fvcn_post_form_title_label()
 *
 */
function fvcn_post_form_title_label()
{
    echo fvcn_get_post_form_title_label();
}

/**
 * fvcn_get_post_form_title_label()
 *
 * @return string
 */
function fvcn_get_post_form_title_label(): string
{
    $label = esc_attr(fvcn_container_get('Config')['_fvcn_post_form_title_label']);
    return apply_filters('fvcn_get_post_form_title_label', $label);
}

/**
 * fvcn_post_form_title()
 *
 */
function fvcn_post_form_title()
{
    echo fvcn_get_post_form_title();
}

/**
 * fvcn_get_post_form_title()
 *
 * @return string
 */
function fvcn_get_post_form_title()
{
    if ('post' == strtolower($_SERVER['REQUEST_METHOD'])) {
        $value = $_POST['fvcn_post_form_title'];
    } else {
        $value = '';
    }

    return apply_filters('fvcn_get_post_form_title', esc_attr($value));
}


/**
 * fvcn_post_form_link_label()
 *
 */
function fvcn_post_form_link_label()
{
    echo fvcn_get_post_form_link_label();
}

/**
 * fvcn_get_post_form_link_label()
 *
 * @return string
 */
function fvcn_get_post_form_link_label(): string
{
    $label = esc_attr(fvcn_container_get('Config')['_fvcn_post_form_link_label']);
    return apply_filters('fvcn_get_post_form_link_label', $label);
}

/**
 * fvcn_post_form_link()
 *
 */
function fvcn_post_form_link()
{
    echo fvcn_get_post_form_link();
}

/**
 * fvcn_get_post_form_link()
 *
 * @return string
 */
function fvcn_get_post_form_link()
{
    if ('post' == strtolower($_SERVER['REQUEST_METHOD'])) {
        $value = $_POST['fvcn_post_form_link'];
    } else {
        $value = '';
    }

    return apply_filters('fvcn_get_post_form_link', esc_attr($value));
}

/**
 * Whether the link field is enabled.
 *  True if it is enabled.
 *
 * @return bool
 */
function fvcn_is_post_form_link_enabled(): bool
{
    return apply_filters('fvcn_is_post_form_link_enabled',
        fvcn_container_get('Config')['_fvcn_post_form_link_enabled']
    );
}

/**
 * fvcn_is_post_form_link_required()
 *
 * @return bool
 */
function fvcn_is_post_form_link_required(): bool
{
    return apply_filters('fvcn_is_post_form_link_required',
        fvcn_container_get('Config')['_fvcn_post_form_link_required']
    );
}


/**
 * fvcn_post_form_content_label()
 *
 */
function fvcn_post_form_content_label()
{
    echo fvcn_get_post_form_content_label();
}

/**
 * fvcn_get_post_form_content_label()
 *
 * @return string
 */
function fvcn_get_post_form_content_label(): string
{
    $label = esc_attr(fvcn_container_get('Config')['_fvcn_post_form_content_label']);
    return apply_filters('fvcn_get_post_form_content_label', $label);
}

/**
 * fvcn_post_form_content()
 *
 */
function fvcn_post_form_content()
{
    echo fvcn_get_post_form_content();
}

/**
 * fvcn_get_post_form_content()
 *
 * @return string
 */
function fvcn_get_post_form_content()
{
    if ('post' == strtolower($_SERVER['REQUEST_METHOD'])) {
        $value = $_POST['fvcn_post_form_content'];
    } else {
        $value = '';
    }

    return apply_filters('fvcn_get_post_form_content', esc_attr($value));
}


/**
 * fvcn_post_form_tags_label()
 *
 */
function fvcn_post_form_tags_label()
{
    echo fvcn_get_post_form_tags_label();
}

/**
 * fvcn_get_post_form_tags_label()
 *
 * @return string
 */
function fvcn_get_post_form_tags_label(): string
{
    $label = esc_attr(fvcn_container_get('Config')['_fvcn_post_form_tags_label']);
    return apply_filters('fvcn_get_post_form_tags_label', $label);
}

/**
 * fvcn_post_form_tags()
 *
 */
function fvcn_post_form_tags()
{
    echo fvcn_get_post_form_tags();
}

/**
 * fvcn_get_post_form_tags()
 *
 * @return string
 */
function fvcn_get_post_form_tags()
{
    if ('post' == strtolower($_SERVER['REQUEST_METHOD'])) {
        $value = $_POST['fvcn_post_form_tags'];
    } else {
        $value = '';
    }

    return apply_filters('fvcn_get_post_form_tags', esc_attr($value));
}

/**
 * Whether the tags field is enabled.
 *  True if it is enabled.
 *
 * @return bool
 */
function fvcn_is_post_form_tags_enabled(): bool
{
    return apply_filters('fvcn_is_post_form_tags_enabled',
        fvcn_container_get('Config')['_fvcn_post_form_tags_enabled']
    );
}

/**
 * fvcn_is_post_form_tags_required()
 *
 * @return bool
 */
function fvcn_is_post_form_tags_required(): bool
{
    return apply_filters('fvcn_is_post_form_tags_required',
        fvcn_container_get('Config')['_fvcn_post_form_tags_required']
    );
}


/**
 * fvcn_post_form_thumbnail_label()
 *
 */
function fvcn_post_form_thumbnail_label()
{
    echo fvcn_get_post_form_thumbnail_label();
}

/**
 * fvcn_get_post_form_thumbnail_label()
 *
 * @return string
 */
function fvcn_get_post_form_thumbnail_label(): string
{
    $label = esc_attr(fvcn_container_get('Config')['_fvcn_post_form_thumbnail_label']);
    return apply_filters('fvcn_get_post_form_thumbnail_label', $label);
}

/**
 * fvcn_is_post_form_thumbnail_enabled()
 *
 * @return bool
 */
function fvcn_is_post_form_thumbnail_enabled(): bool
{
    return apply_filters('fvcn_is_post_form_thumbnail_enabled',
        fvcn_container_get('Config')['_fvcn_post_form_thumbnail_enabled']
    );
}

/**
 * fvcn_is_post_form_thumbnail_required()
 *
 * @return bool
 */
function fvcn_is_post_form_thumbnail_required(): bool
{
    return apply_filters('fvcn_is_post_form_thumbnail_required',
        fvcn_container_get('Config')['_fvcn_post_form_thumbnail_required']
    );
}


/**
 * fvcn_is_post_added()
 *
 * @return bool
 */
function fvcn_is_post_added()
{
    if (isset($_GET['fvcn_added'])) {
        return true;
    }

    return false;
}

/**
 * fvcn_is_post_added_approved()
 *
 * @return bool
 */
function fvcn_is_post_added_approved()
{
    if (!fvcn_is_post_added()) {
        return false;
    }

    return PostStatus::publish() == fvcn_get_post_status($_GET['fvcn_added']);
}




/**
 * fvcn_get_theme_dir()
 *
 * @return string
 */
function fvcn_get_theme_dir(): string
{
    $reg = fvcn_container_get('Registry');
    return apply_filters('fvcn_get_theme_dir', $reg['themeDir']);
}


/**
 * fvcn_get_theme_url()
 *
 * @return string
 */
function fvcn_get_theme_url(): string
{
    $reg = fvcn_container_get('Registry');
    return apply_filters('fvcn_get_theme_url', $reg['themeUrl']);
}

/**
 * fvcn_theme_get_template_part()
 *
 * @param string $slug
 * @param string $name
 */
function fvcn_get_template_part($slug, $name = null)
{
    if (null === $name) {
        $file = $slug . '.php';
    } else {
        $file = $slug . '-' . $name . '.php';
    }

    if (!file_exists(get_stylesheet_directory() . '/' . $file)) {
        load_template(fvcn_get_theme_dir() . '/' . $file, false);
    } else {
        get_template_part($slug, $name);
    }
}


/**
 * fvcn_get_query_template()
 *
 * @param string $type
 * @param array $templates
 * @return string
 */
function fvcn_get_query_template($type, $templates)
{
    $reg = fvcn_container_get('Registry');
    $templates = apply_filters('fvcn_get_' . $type . '_template', $templates);

    if ('' == ($template = locate_template($templates))) {
        $reg['themeCompatActive'] = true;
    } else {
        $reg['themeCompatActive'] = false;
    }

    return apply_filters('fvcn_' . $type . '_template', $template);
}


/**
 * fvcn_theme_get_single_post_template()
 *
 * @return string
 */
function fvcn_theme_get_single_post_template(): string
{
    return \fvcn_get_query_template('single_post', [
        'single-' . PostType::post() . '.php',
        'single-fvcn.php'
    ]);
}

/**
 * fvcn_theme_get_post_archive_template()
 *
 * @return string
 */
function fvcn_theme_get_post_archive_template(): string
{
    return \fvcn_get_query_template('post_archive', [
        'archive-' . PostType::post() . '.php',
        'archive-fvcn.php'
    ]);
}

/**
 * fvcn_theme_get_post_tag_archive_template()
 *
 * @return string
 */
function fvcn_theme_get_post_tag_archive_template(): string
{
    return \fvcn_get_query_template('post_tag', [
        'taxonomy-' . PostType::tag() . '.php',
        'taxonomy-fvcn.php'
    ]);
}


/**
 * fvcn_theme_compat_active()
 *
 * @return bool
 */
function fvcn_theme_is_compat_active(): bool
{
    $active = true;
    $reg = fvcn_container_get('Registry');

    if (false === $reg['themeCompatActive']) {
        $active = false;
    }

    return apply_filters('fvcn_theme_is_compat_active', $active);
}


/**
 * fvcn_theme_compat_template_include()
 *
 * @param string $template
 * @return string
 */
function fvcn_theme_compat_template_include($template)
{
    if (!is_fvcn()) {
        return $template;
    }

    if (fvcn_is_single_post()) {
        $newTemplate = fvcn_theme_get_single_post_template();
    } elseif (fvcn_is_post_archive()) {
        $newTemplate = fvcn_theme_get_post_archive_template();
    } else {
        $newTemplate = fvcn_theme_get_post_tag_archive_template();
    }

    if (fvcn_theme_is_compat_active()) {
        add_filter('the_content', 'fvcn_theme_compat_replace_the_content');
    } else {
        $template = $newTemplate;
    }

    return apply_filters('fvcn_theme_compat_template_include', $template);
}


/**
 * fvcn_theme_compat_replace_the_content()
 *
 * @param string $content
 * @return string
 */
function fvcn_theme_compat_replace_the_content(string $content): string
{
    if (fvcn_theme_is_compat_active()) {
        if (fvcn_is_single_post()) {
            ob_start();

            fvcn_get_template_part('fvcn/content', 'single-post');
            $newContent = ob_get_contents();

            ob_end_clean();
        } elseif (fvcn_is_post_archive() || fvcn_is_post_tag_archive()) {
            ob_start();

            fvcn_get_template_part('fvcn/content', 'archive-post');
            $newContent = ob_get_contents();

            ob_end_clean();
        }

        if (isset($newContent)) {
            $content = apply_filters('fvcn_theme_compat_replace_the_content', $newContent, $content);
        }
    }

    return $content;
}