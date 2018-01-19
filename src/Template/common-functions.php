<?php

/**
 * fvcn_version()
 *
 * @version 20120322
 */
function fvcn_version()
{
    echo fvcn_get_version();
}
    /**
     * fvcn_get_version()
     *
     * @version 20120711
     * @return string
     */
    function fvcn_get_version()
    {
        return FvCommunityNews::VERSION;
    }

/**
 * fvcn_template_notices()
 *
 * @version 20120713
 */
function fvcn_template_notices()
{
    if (!fvcn_has_errors()) {
        return;
    }

    $errors = $messages = [];

    $wpError = FvCommunityNews::$container->get(WP_Error::class);
    foreach ($wpError->get_error_codes() as $code) {
        $severity = $wpError->get_error_data($code);

        foreach ($wpError->get_error_messages($code) as $error) {
            if ('message' == $severity) {
                $messages[] = $error;
            } else {
                $errors[] = $error;
            }
        }
    }

    if (!empty($errors)) : ?>

        <div class="fvcn-template-notice error">
            <span>
                <?= implode("</span><br>\n<span>", $errors); ?>
            </span>
        </div>

    <?php else : ?>

        <div class="fvcn-template-notice">
            <span>
                <?= implode("</span><br>\n<span>", $messages); ?>
            </span>
        </div>

    <?php endif;
}

/**
 * is_fvcn()
 *
 * @version 20120622
 * @return bool
 */
function is_fvcn()
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
 * @version 20120710
 * @return bool
 */
function fvcn_show_widget_thumbnail(): bool
{
    $registry = \FvCommunityNews::$container->get('Registry');
    return $registry['widgetShowThumbnail'];
}

/**
 * fvcn_show_widget_view_all()
 *
 * @version 20120710
 * @return bool
 */
function fvcn_show_widget_view_all(): bool
{
    $registry = \FvCommunityNews::$container->get('Registry');
    return $registry['widgetShowViewAll'];
}
