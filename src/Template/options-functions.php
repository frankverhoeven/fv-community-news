<?php

use FvCommunityNews\Options;

/**
 * fvcn_admin_moderation()
 *
 * @return bool
 * @version 20120524
 */
function fvcn_admin_moderation()
{
    return apply_filters('fvcn_admin_moderation', (bool) Options::fvcnGetOption('_fvcn_admin_moderation'));
}

/**
 * fvcn_user_moderation()
 *
 * @return bool
 * @version 20120524
 */
function fvcn_user_moderation()
{
    return apply_filters('fvcn_user_moderation', (bool) Options::fvcnGetOption('_fvcn_user_moderation'));
}

/**
 * fvcn_mail_on_submission()
 *
 * @return bool
 * @version 20120524
 */
function fvcn_mail_on_submission()
{
    return apply_filters('fvcn_mail_on_submission', (bool) Options::fvcnGetOption('_fvcn_mail_on_submission'));
}

/**
 * fvcn_mail_on_moderation()
 *
 * @return bool
 * @version 20120524
 */
function fvcn_mail_on_moderation()
{
    return apply_filters('fvcn_mail_on_moderation', (bool) Options::fvcnGetOption('_fvcn_mail_on_moderation'));
}

/**
 * fvcn_is_anonymous_allowed()
 *
 * @return bool
 * @version 20120524
 */
function fvcn_is_anonymous_allowed()
{
    return apply_filters('fvcn_is_anonymous_allowed', (bool) Options::fvcnGetOption('_fvcn_is_anonymous_allowed'));
}
