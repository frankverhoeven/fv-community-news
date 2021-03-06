<?php

namespace FvCommunityNews\Admin;

use FvCommunityNews\Admin\Dashboard\Dashboard;
use FvCommunityNews\Admin\Post\Edit;
use FvCommunityNews\Admin\Post\Moderation;
use FvCommunityNews\Admin\Settings\Form;
use FvCommunityNews\Admin\Settings\Settings;
use FvCommunityNews\Container\Container;
use FvCommunityNews\Post\Type as PostType;

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
     * @var Container
     */
    protected $container;

    /**
     * __construct()
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->setupActions();
    }

    /**
     * setupActions()
     *
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

        $postType = PostType::post();

        switch ($pageId) {
            case 'dashboard' :
            case 'admin-ajax' :
                $this->container->get(Dashboard::class);
                break;

            case 'edit-' . $postType :
                $this->container->get(Moderation::class);
                break;

            case 'post' :
            case 'post-new-' . $postType :
                $this->container->get(Edit::class);
                break;

            case 'admin-fvcn-settings' :
            case 'edit-' . $postType . '-fvcn-settings' :
                $this->container->get(Settings::class);
                break;

            case 'admin-fvcn-form' :
            case 'edit-' . $postType . '-fvcn-form' :
                $this->container->get(Form::class);
                break;

            case 'options' :
                $this->container->get(Settings::class);
                $this->container->get(Form::class);
                break;
        }

        do_action('fvcn_adminfactory', $pageId);
    }

    /**
     * init()
     *
     */
    public function init()
    {
        do_action('fvcn_admin_init');
    }

    /**
     * adminHead()
     *
     */
    public function adminHead()
    {
        $registry = fvcn_container_get('Registry');
        $menuIconUrl = $registry['pluginUrl'] . 'public/images/menu.png';
        $menuIconUrl2x = $registry['pluginUrl'] . 'public/images/menu@2x.png';
        $postClass = sanitize_html_class(PostType::post());

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
     */
    public function enqueueScripts()
    {
        do_action('fvcn_admin_enqueue_scripts');
    }

    /**
     * adminMenu()
     *
     */
    public function adminMenu()
    {
        $adminFormPage = add_submenu_page(
            'edit.php?post_type=' . PostType::post(),
            __('FV Community News Form', 'fvcn'),
            __('Form', 'fvcn'),
            'manage_options',
            'fvcn-form',
            [$this->container->get(Form::class), 'fvcn_admin_form']
        );
        add_action('load-' . $adminFormPage, [$this->container->get(Form::class), 'fvcn_admin_form_help']);

        add_submenu_page(
            'edit.php?post_type=' . PostType::post(),
            __('FV Community News Settings', 'fvcn'),
            __('Settings', 'fvcn'),
            'manage_options',
            'fvcn-settings',
            [$this->container->get(Settings::class), 'fvcn_admin_settings']
        );

        do_action('fvcn_admin_menu');
    }
}
