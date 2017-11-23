<?php

namespace FvCommunityNews\Application;

use FvCommunityNews;
use FvCommunityNews\Config;
use FvCommunityNews\Container;
use FvCommunityNews\Installer;
use FvCommunityNews\Post\PostType;
use FvCommunityNews\Shortcode\PostForm as PostFormShortcode;
use FvCommunityNews\Shortcode\RecentPosts as RecentPostsShortcode;
use FvCommunityNews\Shortcode\TagCloud as TagCloudShortcode;
use FvCommunityNews\Widget\Form as FormWidget;
use FvCommunityNews\Widget\ListPosts as ListPostsWidget;
use FvCommunityNews\Widget\TagCloud as TagCloudWidget;

/**
 * Bootstrap
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Bootstrap
{
    /**
     * @var Config
     */
    private $config;

    /**
     * __construct()
     *
     * @param Config $config
     * @version 20171112
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Install the plugin
     *
     * @version 20171112
     */
    public function install()
    {
        $installer = new Installer($this->config);

        $installer->hasUpdate();

        if ($installer->isInstall()) {
            $installer->install();
            $this->registerPostType();
            flush_rewrite_rules();
        } elseif ($installer->isUpdate()) {
            $installer->update();
        }
    }

    /**
     * Register the custom post type
     *
     * @version 20171112
     */
    public function registerPostType()
    {
        $postType = new PostType();
        $postType->registerPostType(
            $this->config['_fvcn_base_slug'] . '/' . $this->config['_fvcn_post_slug'],
            $this->config['_fvcn_base_slug'] . '/' . $this->config['_fvcn_post_archive_slug']
        );
        $postType->registerPostStatuses();
        $postType->registerTaxonomy($this->config['_fvcn_base_slug'] . '/' . $this->config['_fvcn_post_tag_slug']);
    }

    /**
     * Register widgets
     *
     * @version 20171112
     */
    public function registerWidgets()
    {
        FormWidget::register();
        ListPostsWidget::register();
        TagCloudWidget::register();
    }

    /**
     * Register shortcodes
     *
     * @version 20171112
     */
    public function registerShortcodes()
    {
        add_shortcode(PostFormShortcode::SHORTCODE_TAG, function() {
            return (new PostFormShortcode())();
        });
        add_shortcode(RecentPostsShortcode::SHORTCODE_TAG, function() {
            return (new RecentPostsShortcode())();
        });
        add_shortcode(TagCloudShortcode::SHORTCODE_TAG, function() {
            return (new TagCloudShortcode())();
        });
    }

    /**
     * Load Textdomain
     *
     * @version 20171112
     */
    public function loadTextdomain()
    {
        $locale = apply_filters('fvcn_locale', get_locale());
        $mofile = sprintf('fvcn-%s.mo', $locale);

        $mofile_local = FvCommunityNews::DIR . '/languages/' . $mofile;
        $mofile_global = WP_LANG_DIR . '/fv-community-news/' . $mofile;

        // /wp-content/plugins/fv-community-news/languages/
        if (file_exists($mofile_local)) {
            load_textdomain('fvcn', $mofile_local);

            // /wp-content/languages/fv-community-news/
        } elseif (file_exists($mofile_global)) {
            load_textdomain('fvcn', $mofile_global);
        }
    }

    public static function enqueueScripts()
    {
        Container::getInstance()->getJavascript()
            ->enqueueScripts();
    }
}
