<?php

namespace FvCommunityNews\Shortcode;

/**
 * RecentPosts
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class RecentPosts
{
    /**
     * @var string
     */
    const SHORTCODE_TAG = 'fvcn-recent-posts';

    /**
     * obStart()
     *
     * @version 20120315
     */
    private function obStart()
    {
        ob_start();
    }

    /**
     * obEnd()
     *
     * @version 20120315
     * @return string
     */
    private function obEnd()
    {
        $output = ob_get_contents();

        ob_end_clean();

        return $output;
    }

    /**
     * Display a list of recent posts.
     *
     * @return string
     */
    public function __invoke()
    {
        $this->obStart();

        $options = ['posts_per_page' => 10];

        if (fvcn_has_posts($options)) {
            fvcn_get_template_part('fvcn/loop', 'posts');
        } else {
            fvcn_get_template_part('fvcn/feedback', 'no-posts');
        }

        return $this->obEnd();
    }
}
