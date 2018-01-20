<?php

namespace FvCommunityNews\Hook;

use FvCommunityNews\Container\Container;
use FvCommunityNews\Version;
use FvCommunityNews\View\AjaxForm;

/**
 * EnqueueScripts
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class EnqueueScripts implements HookInterface
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Execute the hook
     *
     * @return void
     */
    public function doHook(): void
    {
        $this->enqueueScripts();
        $this->enqueueStyles();
    }

    /**
     * Enqueue scripts
     *
     * @return void
     */
    protected function enqueueScripts(): void
    {
        $ajaxForm = $this->container->get(AjaxForm::class);
        $ajaxForm->enqueueScripts();

        do_action('fvcn_enqueue_scripts');
    }

    /**
     * Enqueue stylesheets
     *
     * @return void
     */
    protected function enqueueStyles(): void
    {
        if (!fvcn_theme_is_compat_active() && file_exists(get_stylesheet_directory() . '/fvcn/css/fvcn-theme.css')) {
            $src = get_stylesheet_directory_uri() . '/fvcn/css/fvcn-theme.css';
        } else {
            $src = fvcn_get_theme_url() . '/fvcn/css/fvcn-theme.min.css';
        }

        wp_enqueue_style('fvcn-theme', $src, '', Version::getCurrentVersion(), 'all');

        do_action('fvcn_enqueue_styles');
    }
}
