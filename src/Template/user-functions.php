<?php

use FvCommunityNews\Post\PostType;

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
function fvcn_user_id(int $userId = 0): void
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
function fvcn_current_user_id(): void
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
function fvcn_current_user_name(): void
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
            $postStatus = PostType::STATUS_PUBLISH;
        }

        $retval = fvcn_has_posts([
            'author' => $id,
            'post_status' => $postStatus
        ]);
    }

    return apply_filters('fvcn_has_user_posts', $retval);
}
