<?php

namespace FvCommunityNews\Hook;

use FvCommunityNews;
use FvCommunityNews\Config\AbstractConfig as Config;
use FvCommunityNews\Container\Container;
use FvCommunityNews\Installer;
use FvCommunityNews\Post\PostType;
use FvCommunityNews\Shortcode\PostForm as PostFormShortcode;
use FvCommunityNews\Shortcode\RecentPosts as RecentPostsShortcode;
use FvCommunityNews\Shortcode\TagCloud as TagCloudShortcode;

/**
 * Init
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Init implements HookInterface
{
    /**
     * @var Config
     */
    private $config;
    /**
     * @var Container
     */
    private $container;

    /**
     * @param Config $config
     * @param Container $container
     */
    public function __construct(Config $config, Container $container)
    {
        $this->config = $config;
        $this->container = $container;
    }

    /**
     * Execute the hook
     *
     * @return void
     */
    public function doHook(): void
    {
        $this->setupVariables();

        do_action('fvcn_init');

        $this->install();
        $this->loadTextDomain();
        $this->registerPostType();
        $this->registerShortCodes();
        $this->setupAjaxForm();

        if (is_admin()) {
            $this->setupAdmin();
        }

        do_action('fvcn_ready');
    }

    /**
     * Setup variables.
     * 
     * @return void
     */
    protected function setupVariables(): void
    {
        $pluginDir = plugin_dir_path(FvCommunityNews::FILE);
        $pluginUrl = plugin_dir_url(FvCommunityNews::FILE);
        $baseSlug = $this->config['_fvcn_base_slug'];

        // @todo: deprecate Registry
        $registry = $this->container->get('Registry');
        $registry['pluginDir'] = $pluginDir;
        $registry['pluginUrl'] = $pluginUrl;

        $registry['themeDir'] = $pluginDir . 'fvcn-theme';
        $registry['themeUrl'] = $pluginUrl . 'fvcn-theme';

        $registry['postSlug'] = $baseSlug . '/' . $this->config['_fvcn_post_slug'];
        $registry['postTagSlug'] = $baseSlug . '/' . $this->config['_fvcn_post_tag_slug'];
        $registry['postArchiveSlug'] = $baseSlug . '/' . $this->config['_fvcn_post_archive_slug'];
    }

    /**
     * Install the plugin if needed.
     *
     * @return void
     */
    protected function install(): void
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

        do_action('fvcn_install');
    }

    /**
     * Load `fvcn` textdomain
     *
     * @return void
     */
    protected function loadTextDomain(): void
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

        do_action('fvcn_load_textdomain');
    }

    /**
     * Register custom post type
     *
     * @return void
     */
    protected function registerPostType(): void
    {
        $postType = new PostType();
        $postType->registerPostType(
            $this->config['_fvcn_base_slug'] . '/' . $this->config['_fvcn_post_slug'],
            $this->config['_fvcn_base_slug'] . '/' . $this->config['_fvcn_post_archive_slug']
        );
        $postType->registerPostStatuses();
        $postType->registerTaxonomy($this->config['_fvcn_base_slug'] . '/' . $this->config['_fvcn_post_tag_slug']);

        do_action('fvcn_register_post_type');
    }

    /**
     * Register shortcodes
     *
     * @return void
     */
    protected function registerShortCodes(): void
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

        do_action('fvcn_register_shortcodes');
    }

    /**
     * Register shortcodes
     *
     * @return void
     */
    protected function setupAjaxForm(): void
    {
        $this->container->get(FvCommunityNews\View\AjaxForm::class);
    }

    /**
     * Load admin files
     *
     * @return void
     */
    protected function setupAdmin(): void
    {
        $this->container->get(FvCommunityNews\Admin\Admin::class);
    }
}
