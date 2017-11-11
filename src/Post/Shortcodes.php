<?php

namespace FvCommunityNews\Post;

/**
 * Shortcodes
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Shortcodes
{
    /**
     * @var array
     */
    private $codes = [];

    /**
     * __construct()
     *
     * @version 20120315
     */
    public function __construct()
    {
        $this->addCodes()
            ->registerCodes();
    }

    /**
     * addCodes()
     *
     * @version 20120716
     * @return Shortcodes
     */
    private function addCodes()
    {
        $codes = [
            'fvcn-recent-posts' => [$this, 'displayRecentPosts'],
            'fvcn-post-form' => [$this, 'displayPostForm'],
            'fvcn-tag-cloud' => [$this, 'displayTagCloud']
        ];

        $codes = apply_filters('fvcn_shortcodes', $codes);

        foreach ($codes as $code=>$callback) {
            $this->addCode($code, $callback);
        }

        return $this;
    }

    /**
     * addCode()
     *
     * @version 20120716
     * @param string $code
     * @param callback $callback
     * @return Shortcodes
     */
    public function addCode($code, $callback)
    {
        if (!isset($this->codes[ $code ])) {
            $this->codes[ $code ] = $callback;
        }

        return $this;
    }

    /**
     * removeCode()
     *
     * @version 20120315
     * @param string $code
     * @return Shortcodes
     */
    public function removeCode($code)
    {
        unset($this->codes[ $code ]);
        return $this;
    }

    /**
     * getCodes()
     *
     * @version 20120315
     * @return array
     */
    public function getCodes()
    {
        return $this->codes;
    }

    /**
     * registerCodes()
     *
     * @version 20120315
     * @return Shortcodes
     */
    private function registerCodes()
    {
        foreach ($this->getCodes() as $code=>$callback) {
            add_shortcode($code, $callback);
        }

        do_action('fvcn_register_shortcodes');

        return $this;
    }

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
     * displayRecentPosts()
     *
     * @version 20120315
     * @return string
     */
    public function displayRecentPosts()
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

    /**
     * displayPostForm()
     *
     * @version 20120315
     * @return string
     */
    public function displayPostForm()
    {
        $this->obStart();

        fvcn_get_template_part('fvcn/form', 'post');

        return $this->obEnd();
    }

    /**
     * displayTagCloud()
     *
     * @version 20120716
     * @return string
     */
    public function displayTagCloud()
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
