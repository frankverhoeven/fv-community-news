<?php

use FvCommunityNews\Post\PostType;

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
    function fvcn_get_post_slug()
    {
        $registry = \FvCommunityNews::$container->get('Registry');
        return apply_filters('fvcn_get_post_slug', $registry['postSlug']);
    }


/**
 * fvcn_has_posts()
 *
 * @param mixed $args
 * @return object
 */
function fvcn_has_posts($args='')
{
    $defaults = [
        'post_type' => PostType::POST_TYPE_KEY,
        'post_status' => PostType::STATUS_PUBLISH,
        'posts_per_page'=> 15,
        'order' => 'DESC'
    ];

    $options = wp_parse_args($args, $defaults);
    $options = apply_filters('fvcn_has_posts_query', $options);

    $registry = \FvCommunityNews::$container->get('Registry');
    $registry['wpQuery'] = new WP_Query($options);

    return apply_filters('fvcn_has_posts', $registry['wpQuery']->have_posts(), $registry['wpQuery']);
}


/**
 * fvcn_posts()
 *
 * @return object
 */
function fvcn_posts()
{
    $registry = \FvCommunityNews::$container->get('Registry');
    $have_posts = $registry['wpQuery']->have_posts();

    if (empty($have_posts)) {
        wp_reset_postdata();
    }

    return $have_posts;
}


/**
 * fvcn_the_post()
 *
 * @return object
 */
function fvcn_the_post()
{
    $registry = \FvCommunityNews::$container->get('Registry');
    return $registry['wpQuery']->the_post();
}


/**
 * fvcn_post_id()
 *
 * @param int $postId
 */
function fvcn_post_id($postId = 0)
{
    echo fvcn_get_post_id($postId);
}

    /**
     * fvcn_get_post_id()
     *
     * @param int $postId
     * @return int
     */
    function fvcn_get_post_id($postId = 0)
    {
        global $wp_query, $post;
        $registry = \FvCommunityNews::$container->get('Registry');

        if (!empty($postId) && is_numeric($postId)) {
            $id = $postId;

        } elseif (!empty($registry['wpQuery']->in_the_loop) && isset($registry['wpQuery']->post->ID)) {
            $id = $registry['wpQuery']->post->ID;

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
 * @return object
 */
function fvcn_get_post($postId = 0)
{
    $id = fvcn_get_post_id($postId);

    if (empty($id)) {
        return null;
    }

    $post = get_post($id, OBJECT);

    if (!$post || $post->post_type != PostType::POST_TYPE_KEY) {
        return null;
    }

    return apply_filters('fvcn_get_post', $post);
}


/**
 * fvcn_post_permalink()
 *
 * @param int $postId
 */
function fvcn_post_permalink($postId = 0)
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
    function fvcn_get_post_permalink($postId = 0, $redirect='')
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
function fvcn_has_post_link($postId = 0)
{
    $link = fvcn_get_post_link($postId);

    return !empty($link);
}


/**
 * fvcn_post_link()
 *
 * @param int $postId
 */
function fvcn_post_link($postId = 0)
{
    echo fvcn_get_post_link($postId);
}

    /**
     * fvcn_get_post_link()
     *
     * @param int $postId
     * @return string
     */
    function fvcn_get_post_link($postId = 0)
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
function fvcn_post_title($postId = 0)
{
    echo fvcn_get_post_title($postId);
}

    /**
     * fvcn_get_post_title()
     *
     * @param int $postId
     * @return string
     */
    function fvcn_get_post_title($postId = 0)
    {
        $id = fvcn_get_post_id($postId);

        return apply_filters('fvcn_get_post_title', get_the_title($id), $id);
    }


/**
 * fvcn_post_content()
 *
 * @param int $postId
 */
function fvcn_post_content($postId = 0)
{
    echo fvcn_get_post_content($postId);
}

    /**
     * fvcn_get_post_content()
     *
     * @param int $postId
     * @return string
     */
    function fvcn_get_post_content($postId = 0)
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
function fvcn_post_excerpt($postId = 0, $length = 100) {
    echo fvcn_get_post_excerpt($postId, $length);
}

    /**
     * fvcn_get_post_excerpt()
     *
     * @param int $postId
     * @param int $length
     * @return string
     */
    function fvcn_get_post_excerpt($postId = 0, $length = 100) {
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
function fvcn_post_date($postId = 0, $format = '') {
    echo fvcn_get_post_date($postId, $format);
}

    /**
     * fvcn_get_post_date()
     *
     * @param int $postId
     * @param string $format
     * @return string
     */
    function fvcn_get_post_date($postId = 0, $format = '') {
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
function fvcn_post_time($postId = 0, $format = '', $gmt = false) {
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
    function fvcn_get_post_time($postId = 0, $format='', $gmt=false) {
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
function fvcn_has_post_thumbnail($postId = 0)
{
    $id = fvcn_get_post_id($postId);
    $registry = \FvCommunityNews::$container->get('Registry');

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
function fvcn_post_thumbnail($postId = 0, $size = 'thumbnail', $attributes = [])
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
    function fvcn_get_post_thumbnail($postId = 0, $size = 'thumbnail', $attributes= [])
    {
        $id = fvcn_get_post_id($postId);
        return apply_filters('fvcn_get_post_thumbnail', get_the_post_thumbnail($id, $size, $attributes), $id);
    }


/**
 * fvcn_post_rating()
 *
 * @param int $postId
 */
function fvcn_post_rating($postId = 0)
{
    echo fvcn_get_post_rating($postId);
}

    /**
     * fvcn_get_post_rating()
     *
     * @param int $postId
     * @return int
     */
    function fvcn_get_post_rating($postId = 0)
    {
        $id = fvcn_get_post_id($postId);
        $rating = get_post_meta($id, '_fvcn_post_rating', true);

        if (!is_numeric($rating)) {
            $rating = 0;
        }

        return apply_filters('fvcn_get_post_rating', $rating, $id);
    }


/**
 * fvcn_post_rating_increment_link()
 *
 * @param int $postId
 */
function fvcn_post_rating_increment_link($postId = 0)
{
    echo fvcn_get_post_rating_increment_link($postId);
}

    /**
     * fvcn_get_post_rating_increment_link()
     *
     * @param int $postId
     * @return string
     */
    function fvcn_get_post_rating_increment_link($postId = 0)
    {
        $id = fvcn_get_post_id($postId);

        $link = wp_nonce_url(add_query_arg(
            [
                'post_id' => $id,
                'fvcn_post_rating_action' => 'increase'
            ],
            fvcn_get_post_permalink($id)
), 'fvcn-post-rating');

        return apply_filters('fvcn_get_post_rating_increment_link', $link, $id);
    }


/**
 * fvcn_post_rating_decrement_link()
 *
 * @param int $postId
 */
function fvcn_post_rating_decrement_link($postId = 0)
{
    echo fvcn_get_post_rating_decrement_link($postId);
}

    /**
     * fvcn_get_post_rating_decrement_link()
     *
     * @param int $postId
     * @return string
     */
    function fvcn_get_post_rating_decrement_link($postId = 0)
    {
        $id = fvcn_get_post_id($postId);

        $link = wp_nonce_url(add_query_arg(
            [
                'post_id' => $id,
                'fvcn_post_rating_action' => 'decrease'
            ],
            fvcn_get_post_permalink($id)
), 'fvcn-post-rating');

        return apply_filters('fvcn_get_post_rating_decrement_link', $link, $id);
    }


/**
 * fvcn_is_post_rated_by_current_user()
 *
 * @param int $postId
 * @return bool
 */
function fvcn_is_post_rated_by_current_user($postId = 0)
{
    $id = fvcn_get_post_id($postId);

    return apply_filters('fvcn_is_post_rated_by_current_user', isset($_COOKIE['fvcn_post_rated_' . $id . '_' . COOKIEHASH]));
}


/**
 * fvcn_post_views()
 *
 * @param int $postId
 */
function fvcn_post_views($postId = 0)
{
    echo fvcn_get_post_views($postId);
}

    /**
     * fvcn_get_post_views()
     *
     * @param int $postId
     * @return int
     */
    function fvcn_get_post_views($postId = 0)
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
function fvcn_post_status($postId = 0)
{
    echo fvcn_get_post_status($postId);
}

    /**
     * fvcn_get_post_status()
     *
     * @param int $postId
     * @return string
     */
    function fvcn_get_post_status($postId = 0)
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
        $link = get_post_type_archive_link(PostType::POST_TYPE_KEY);

        return apply_filters('fvcn_get_post_archive_link', $link);
    }


/**
 * fvcn_is_post()
 *
 * @param int $postId
 * @return bool
 */
function fvcn_is_post($postId = 0): bool
{
    $is_post = false;

    if (!empty($postId) && PostType::POST_TYPE_KEY == get_post_type($postId)) {
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
function fvcn_is_post_published($postId = 0)
{
    return PostType::STATUS_PUBLISH == fvcn_get_post_status(fvcn_get_post_id($postId));
}


/**
 * fvcn_is_post_pending()
 *
 * @param int $postId
 * @return bool
 */
function fvcn_is_post_pending($postId = 0)
{
    return PostType::STATUS_PENDING == fvcn_get_post_status(fvcn_get_post_id($postId));
}


/**
 * fvcn_is_post_trash()
 *
 * @param int $postId
 * @return bool
 */
function fvcn_is_post_trash($postId = 0)
{
    return PostType::STATUS_TRASH == fvcn_get_post_status(fvcn_get_post_id($postId));
}


/**
 * fvcn_is_post_spam()
 *
 * @param int $postId
 * @return bool
 */
function fvcn_is_post_spam($postId = 0)
{
    return PostType::STATUS_SPAM == fvcn_get_post_status(fvcn_get_post_id($postId));
}


/**
 * fvcn_is_post_private()
 *
 * @param int $postId
 * @return bool
 */
function fvcn_is_post_private($postId = 0)
{
    return PostType::STATUS_PRIVATE == fvcn_get_post_status(fvcn_get_post_id($postId));
}


/**
 * fvcn_is_post_anonymous()
 *
 * @param int $postId
 * @return bool
 */
function fvcn_is_post_anonymous($postId = 0)
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

    if (is_singular(PostType::POST_TYPE_KEY)) {
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

    if (is_post_type_archive(PostType::POST_TYPE_KEY)) {
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

    if (is_tax(fvcn_get_post_tag_id())) {
        $retval = true;
    }

    return apply_filters('fvcn_is_post_tag_archive', $retval);
}


/**
 * fvcn_post_author()
 *
 * @param int $postId
 */
function fvcn_post_author($postId = 0)
{
    echo fvcn_get_post_author($postId);
}

    /**
     * fvcn_get_post_author()
     *
     * @param int $postId
     * @return string
     */
    function fvcn_get_post_author($postId = 0)
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
function fvcn_post_author_id($postId = 0)
{
    echo fvcn_get_post_author_id($postId);
}

    /**
     * fvcn_get_post_author_id()
     *
     * @param int $postId
     * @return string
     */
    function fvcn_get_post_author_id($postId = 0)
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
function fvcn_post_author_display_name($postId = 0)
{
    echo fvcn_get_post_author_display_name($postId);
}

    /**
     * fvcn_get_post_author_display_name()
     *
     * @param int $postId
     * @return string
     */
    function fvcn_get_post_author_display_name($postId = 0)
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
function fvcn_post_author_email($postId = 0)
{
    echo fvcn_get_post_author_email($postId);
}

    /**
     * fvcn_get_post_author_email()
     *
     * @param int $postId
     * @return string
     */
    function fvcn_get_post_author_email($postId = 0)
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
function fvcn_post_author_avatar($postId = 0, $size=40)
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
    function fvcn_get_post_author_avatar($postId = 0, $size=40)
    {
        $avatar = get_avatar(fvcn_get_post_author_email($postId), $size);

        return apply_filters('fvcn_get_post_author_avatar', $avatar, $postId);
    }


/**
 * fvcn_post_author_website()
 *
 * @param int $postId
 */
function fvcn_post_author_website($postId = 0)
{
    echo fvcn_get_post_author_website($postId);
}

    /**
     * fvcn_get_post_author_website()
     *
     * @param int $postId
     * @return string
     */
    function fvcn_get_post_author_website($postId = 0)
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
function fvcn_post_author_link($postId = 0)
{
    echo fvcn_get_post_author_link($postId);
}

    /**
     * fvcn_get_post_author_link()
     *
     * @param int $postId
     * @return string
     */
    function fvcn_get_post_author_link($postId = 0)
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
function fvcn_post_author_ip($postId = 0)
{
    echo fvcn_get_post_author_ip($postId);
}

    /**
     * fvcn_get_post_author_ip()
     *
     * @param int $postId
     * @return string
     */
    function fvcn_get_post_author_ip($postId = 0)
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
function fvcn_post_author_ua($postId = 0)
{
    echo fvcn_get_post_author_ua($postId);
}

    /**
     * fvcn_get_post_author_ua()
     *
     * @param int $postId
     * @return string
     */
    function fvcn_get_post_author_ua($postId = 0)
    {
        $id = fvcn_get_post_id($postId);

        $ua = get_post_meta($id, '_fvcn_author_ua', true);

        return apply_filters('fvcn_get_post_author_ua', $ua, $id);
    }


/**
 * fvcn_post_tag_id()
 *
 */
function fvcn_post_tag_id()
{
    echo fvcn_get_post_tag_id();
}

    /**
     * fvcn_get_post_tag_id()
     *
     * @return string
     */
    function fvcn_get_post_tag_id()
    {
        return PostType::TAG_TYPE_KEY;
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
        $registry = \FvCommunityNews::$container->get('Registry');
        return apply_filters('fvcn_get_post_tag_slug', $registry['postTagSlug']);
    }


/**
 * fvcn_post_tag_list()
 *
 * @param int $postId
 * @param string|array $args
 */
function fvcn_post_tag_list($postId = 0, $args='')
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
    function fvcn_get_post_tag_list($postId = 0, $args='')
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

        $tag_list = get_the_term_list($id, fvcn_get_post_tag_id(), $before, $sep, $after);

        return apply_filters('fvcn_get_post_tag_list', $tag_list, $id);
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
function fvcn_post_form_field_error($field)
{
    $errors = FvCommunityNews::$container->get(WP_Error::class)->get_error_messages($field);

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
        $label = esc_attr(FvCommunityNews::$container->get('Config')['_fvcn_post_form_author_name_label']);
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
        $label = esc_attr(FvCommunityNews::$container->get('Config')['_fvcn_post_form_author_email_label']);
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
        $label = esc_attr(FvCommunityNews::$container->get('Config')['_fvcn_post_form_title_label']);
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
        $label = esc_attr(FvCommunityNews::$container->get('Config')['_fvcn_post_form_link_label']);
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
 * fvcn_is_post_form_link_required()
 *
 * @return bool
 */
function fvcn_is_post_form_link_required(): bool
{
    return apply_filters('fvcn_is_post_form_link_required',
        FvCommunityNews::$container->get('Config')['_fvcn_post_form_link_required']
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
        $label = esc_attr(FvCommunityNews::$container->get('Config')['_fvcn_post_form_content_label']);
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
        $label = esc_attr(FvCommunityNews::$container->get('Config')['_fvcn_post_form_tags_label']);
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
 * fvcn_is_post_form_tags_required()
 *
 * @return bool
 */
function fvcn_is_post_form_tags_required(): bool
{
    return apply_filters('fvcn_is_post_form_tags_required',
        FvCommunityNews::$container->get('Config')['_fvcn_post_form_tags_required']
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
        $label = esc_attr(FvCommunityNews::$container->get('Config')['_fvcn_post_form_thumbnail_label']);
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
        FvCommunityNews::$container->get('Config')['_fvcn_post_form_thumbnail_enabled']
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
        FvCommunityNews::$container->get('Config')['_fvcn_post_form_thumbnail_required']
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

        return PostType::STATUS_PUBLISH == fvcn_get_post_status($_GET['fvcn_added']);
    }
