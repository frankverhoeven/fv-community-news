<?php

use FvCommunityNews\Post\PostType;

/**
 * fvcn_is_anonymous()
 *
 * @version 20120229
 * @return bool
 */
function fvcn_is_anonymous() {
    if (!is_user_logged_in()) {
        $is_anonymous = true;
    } else {
        $is_anonymous = false;
    }

    return apply_filters('fvcn_is_anonymous', $is_anonymous);
}

/**
 * fvcn_get_current_author_ip()
 *
 * @version 20120229
 * @return string
 */
function fvcn_get_current_author_ip() {
    $ip = preg_replace('/[^0-9a-fA-F:., ]/', '', $_SERVER['REMOTE_ADDR']);

    return apply_filters('fvcn_get_current_author_ip', $ip);
}

/**
 * fvcn_get_current_author_ua()
 *
 * @version 20120229
 * @return string
 */
function fvcn_get_current_author_ua() {
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
 * @version 20120307
 * @param int $userId
 */
function fvcn_user_id($userId = 0) {
    echo fvcn_get_user_id($userId);
}

    /**
     * fvcn_get_user_id()
     *
     * @version 20120307
                 * @param int $userId
     * @return int
     */
    function fvcn_get_user_id($userId = 0) {
        if (!empty($userId) && is_numeric($userId)) {
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
 * @version 20120229
 */
function fvcn_current_user_id() {
    echo fvcn_get_current_user_id();
}

    /**
     * fvcn_get_current_user_id()
     *
     * @version 20120229
             * @return int
     */
    function fvcn_get_current_user_id() {
        $current_user = wp_get_current_user();

        return apply_filters('fvcn_get_current_user_id', $current_user->ID);
    }


/**
 * fvcn_current_user_name()
 *
 * @version 20120307
 */
function fvcn_current_user_name() {
    echo fvcn_get_current_user_name();
}

    /**
     * fvcn_get_current_user_name()
     *
     * @version 20120307
             * @return string
     */
    function fvcn_get_current_user_name() {
        global $userIdentity;

        return apply_filters('fvcn_get_current_user_name', $userIdentity);
    }


/**
 * fvcn_has_user_posts()
 *
 * @version 20120323
 * @param int $userId
 * @param string $post_status
 * @return bool
 */
function fvcn_has_user_posts($userId=0, $post_status='') {
    $id = fvcn_get_user_id($userId);

    if (0 == $id) {
        $retval = false;
    } else {
        if (empty($post_status)) {
            $post_status = PostType::STATUS_PUBLISH;
        }

        $args = [
            'author' => $id,
            'post_status' => $post_status
        ];

        $retval = fvcn_has_posts($args);
    }

    return apply_filters('fvcn_has_user_posts', (bool) $retval);
}
