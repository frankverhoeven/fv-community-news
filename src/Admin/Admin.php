<?php

namespace FvCommunityNews\Admin;

use FvCommunityNews\Post\PostType;
use FvCommunityNews\Registry;

/**
 * Admin
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Admin
{
    /**
     * @var object
     */
    public $posts;
    /**
     * @var AdminFactory
     */
    protected $factory;

    /**
     * __construct()
     *
     * @param AdminFactory $factory
     * @version 20171112
     */
    public function __construct(AdminFactory $factory)
    {
        $this->factory = $factory;
        $this->setupActions();
    }

    /**
     * setupActions()
     *
     * @version 20120720
     */
    private function setupActions()
    {
        add_action('admin_init', [$this, 'init']);
        add_action('admin_head', [$this, 'adminHead']);
        add_action('admin_menu', [$this, 'adminMenu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueScripts']);

        add_action('fvcn_admin_init', [$this, 'factory']);
    }

    /**
     * factory()
     *
     * @version 20120808
     */
    public function factory()
    {
        $pageId = substr(basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)), 0, -4);

        if (isset($_GET['post_type'])) {
            $pageId .= '-' . $_GET['post_type'];
        }
        if (isset($_GET['page'])) {
            $pageId .= '-' . $_GET['page'];
        }
        if ('index' == $pageId || 'wp-a' == $pageId) {
            $pageId = 'dashboard';
        }

        $postType = PostType::POST_TYPE_KEY;

        switch ($pageId) {
            case 'dashboard' :
            case 'admin-ajax' :
                $this->factory->getDashboard();
                break;

            case 'edit-' . $postType :
                $this->factory->getPostModeration();
                break;

            case 'post' :
            case 'post-new-' . $postType :
                $this->factory->getPostEdit();
                break;

            case 'admin-fvcn-settings' :
            case 'edit-' . $postType . '-fvcn-settings' :
                $this->factory->getSettings();
                break;

            case 'admin-fvcn-form' :
            case 'edit-' . $postType . '-fvcn-form' :
                $this->factory->getForm();
                break;

            case 'options' :
                $this->factory->getSettings();
                $this->factory->getForm();
                break;
        }

        do_action('fvcn_adminfactory', $pageId);
    }

    /**
     * init()
     *
     * @version 20120129
     */
    public function init()
    {
        do_action('fvcn_admin_init');
    }

    /**
     * adminHead()
     *
     * @version 20120721
     */
    public function adminHead()
    {
        $menuIconUrl = Registry::get('pluginUrl') . 'public/images/menu.png';
        $menuIconUrl2x = Registry::get('pluginUrl') . 'public/images/menu@2x.png';
        $postClass = sanitize_html_class(PostType::POST_TYPE_KEY);

        ?>
        <style type="text/css">
            #menu-posts-<?= $postClass; ?> .wp-menu-image {
                background: url(<?= $menuIconUrl; ?>) no-repeat 0 0;
            }
            #menu-posts-<?= $postClass; ?>:hover .wp-menu-image,
            #menu-posts-<?= $postClass; ?>.wp-has-current-submenu .wp-menu-image {
                background-position: 0 -34px;
            }

            #menu-posts-<?= $postClass; ?>.wp-menu-open .wp-menu-image {
                background-position: 0 -68px;
            }

            @media only screen and (-moz-min-device-pixel-ratio: 1.5),
            only screen and (-o-min-device-pixel-ratio: 3/2),
            only screen and (-webkit-min-device-pixel-ratio: 1.5),
            only screen and (min-devicepixel-ratio: 1.5),
            only screen and (min-resolution: 1.5dppx) {
                #menu-posts-<?= $postClass; ?> .wp-menu-image {
                    background-image: url(<?= $menuIconUrl2x; ?>);
                    background-size: 36px 102px;
                }
            }

            .column-fvcn_tags {
                width: 15%;
            }
            .column-fvcn_post_details {
                width: 35%;
                clear: both;
            }
            .fvcn-post-thumbnail {
                float: left;
                margin-right: 8px;
            }
        </style>
        <?php

        do_action('fvcn_admin_head');
    }

    /**
     * enqueueScripts()
     *
     * @version 20120721
     */
    public function enqueueScripts()
    {
        do_action('fvcn_admin_enqueue_scripts');
    }

    /**
     * adminMenu()
     *
     * @version 20120721
     */
    public function adminMenu()
    {
        $adminFormPage = add_submenu_page(
            'edit.php?post_type=' . PostType::POST_TYPE_KEY,
            __('FV Community News Form', 'fvcn'),
            __('Form', 'fvcn'),
            'manage_options',
            'fvcn-form',
            [$this->factory->getForm(), 'fvcn_admin_form']
        );
        add_action('load-' . $adminFormPage, [$this->factory->getForm(), 'fvcn_admin_form_help']);

        add_submenu_page(
            'edit.php?post_type=' . PostType::POST_TYPE_KEY,
            __('FV Community News Settings', 'fvcn'),
            __('Settings', 'fvcn'),
            'manage_options',
            'fvcn-settings',
            [$this->factory->getSettings(), 'fvcn_admin_settings']
        );

        do_action('fvcn_admin_menu');
    }
}
