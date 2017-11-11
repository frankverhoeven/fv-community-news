<?php

/**
 * fvcn_tag_cloud()
 *
 * @version 20120716
 * @param string|array $args
 */
function fvcn_tag_cloud($args='')
{
    $default = ['taxonomy' => fvcn_get_post_tag_id()];
    $args = wp_parse_args($args, $default);

    wp_tag_cloud($args);
}
