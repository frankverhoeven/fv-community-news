<?php

namespace FvCommunityNews\Application;

use FvCommunityNews;
use FvCommunityNews\Admin\Admin;
use FvCommunityNews\Admin\AdminFactory;
use FvCommunityNews\Container;
use FvCommunityNews\Installer;
use FvCommunityNews\Post\PostType;

/**
 * Bootstrap
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Bootstrap
{
    public function registerHooks()
    {
        add_action('fvcn_activation', [$this, 'installation']);
        add_action('fvcn_init', [$this, 'registerShortcodes'], 16);

    }

    public function installation()
    {
        $installer = new Installer(Container::getInstance()->getOptions());

        $installer->hasUpdate();

        if ($installer->isInstall()) {
            $installer->install();
        } elseif ($installer->isUpdate()) {
            $installer->update();
        }

        PostType::register();

        flush_rewrite_rules();
    }

    public function registerShortcodes()
    {
        Container::getInstance()->getShortcodes();
    }
}
