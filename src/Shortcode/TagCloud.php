<?php

namespace FvCommunityNews\Shortcode;

/**
 * TagCloud
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class TagCloud
{
    /**
     * @var string
     */
    const SHORTCODE_TAG = 'fvcn-tag-cloud';

    /**
     * obStart()
     *
     */
    private function obStart()
    {
        ob_start();
    }

    /**
     * obEnd()
     *
     * @return string
     */
    private function obEnd()
    {
        $output = ob_get_contents();

        ob_end_clean();

        return $output;
    }

    /**
     *
     * @return string
     */
    public function __invoke()
    {
        $this->obStart();

        ?>
        <div class="fvcn-tag-cloud">
            <?php fvcn_tag_cloud(); ?>
        </div>
        <?php

        return $this->obEnd();
    }
}
