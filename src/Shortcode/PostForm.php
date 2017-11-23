<?php

namespace FvCommunityNews\Shortcode;

/**
 * PostForm
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class PostForm
{
    /**
     * @var string
     */
    const SHORTCODE_TAG = 'fvcn-post-form';

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
     * Display the post form.
     *
     * @return string
     */
    public function __invoke()
    {
        $this->obStart();

        fvcn_get_template_part('fvcn/form', 'post');

        return $this->obEnd();
    }
}
