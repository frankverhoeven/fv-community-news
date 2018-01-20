<?php

/**
 * fvcn_admin_moderation()
 *
 * @return bool
 */
function fvcn_admin_moderation(): bool
{
    return apply_filters('fvcn_admin_moderation',
        FvCommunityNews::$container->get('Config')['_fvcn_admin_moderation']
    );
}

/**
 * fvcn_user_moderation()
 *
 * @return bool
 */
function fvcn_user_moderation(): bool
{
    return apply_filters('fvcn_user_moderation',
        FvCommunityNews::$container->get('Config')['_fvcn_user_moderation']
    );
}

/**
 * fvcn_mail_on_submission()
 *
 * @return bool
 */
function fvcn_mail_on_submission(): bool
{
    return apply_filters('fvcn_mail_on_submission',
        FvCommunityNews::$container->get('Config')['_fvcn_mail_on_submission']
    );
}

/**
 * fvcn_mail_on_moderation()
 *
 * @return bool
 */
function fvcn_mail_on_moderation(): bool
{
    return apply_filters('fvcn_mail_on_moderation',
        FvCommunityNews::$container->get('Config')['_fvcn_mail_on_moderation']
    );
}

/**
 * fvcn_is_anonymous_allowed()
 *
 * @return bool
 */
function fvcn_is_anonymous_allowed(): bool
{
    return apply_filters('fvcn_is_anonymous_allowed',
        FvCommunityNews::$container->get('Config')['_fvcn_is_anonymous_allowed']
    );
}
