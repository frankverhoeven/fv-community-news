<?php

use FvCommunityNews\Post\PostType;

/**
 * fvcn_tag_cloud()
 *
 * @param array|string $args
 * @return void
 */
function fvcn_tag_cloud($args = '')
{
    $default = ['taxonomy' => PostType::TAG_TYPE_KEY];
    $args = wp_parse_args($args, $default);

    wp_tag_cloud($args);
}
