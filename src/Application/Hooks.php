<?php

namespace FvCommunityNews\Application;

use FvCommunityNews;
use FvCommunityNews\Akismet\Akismet;
use FvCommunityNews\Container;
use FvCommunityNews\Options;

/**
 * Hooks
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Hooks
{
    /**
     * @var Bootstrap
     */
    private $bootstrap;

    /**
     * @param Bootstrap $bootstrap
     */
    public function __construct(Bootstrap $bootstrap)
    {
        $this->bootstrap = $bootstrap;

        register_activation_hook(FvCommunityNews::FILE, [static::class, 'fvcn_activation']);
        register_deactivation_hook(FvCommunityNews::FILE, [static::class, 'fvcn_deactivation']);
    }

    /**
     * Register hooks
     *
     */
    public function register()
    {
        add_action('plugins_loaded',    [static::class, 'fvcn_loaded'], 10);
        add_action('init',              [static::class, 'fvcn_init'], 10);
        add_action('widgets_init',      [static::class, 'fvcn_widgets_init'], 10);
        add_action('wp_enqueue_scripts',[static::class, 'fvcn_enqueue_scripts'], 10);
        add_action('after_setup_theme', 'fvcn_add_thumbnail_theme_support', 999);
        add_filter('template_include',  [static::class, 'fvcn_template_include'], 10);

        /**
         * fvcn_init
         */
        add_action('fvcn_init', [static::class, 'fvcn_load_textdomain'], 2);
        add_action('fvcn_init', [static::class, 'fvcn_register_post_type'], 10);
        add_action('fvcn_init', [static::class, 'fvcn_register_shortcodes'], 16);
        add_action('fvcn_init', [static::class, 'fvcn_ready'], 999);

        add_action('fvcn_ready', [Akismet::class, 'fvcn_akismet']);

        add_action('fvcn_register_post_type', [$this->bootstrap, 'registerPostType']);
        add_action('fvcn_register_shortcodes', [$this->bootstrap, 'registerShortcodes']);

        /**
         * fvcn_widgets_init
         */
        add_action('fvcn_widgets_init', [$this->bootstrap, 'registerWidgets'], 10);

        add_action('wp_head', 'fvcn_head');

        add_action('fvcn_enqueue_scripts', 'fvcn_theme_enqueue_css', 10);
        add_action('fvcn_init', [$this->bootstrap, 'enqueueScripts']);

        add_action('fvcn_load_text_domain', [$this->bootstrap, 'loadTextdomain'], 5);

        /**
         * fvcn_(de)activation
         */
        add_action('fvcn_init', [$this->bootstrap, 'install']);
        add_action('fvcn_activation', [$this->bootstrap, 'install']);
        add_action('fvcn_deactivation', 'flush_rewrite_rules');
        add_action('fvcn_uninstall', [Options::class, 'fvcnDeleteOptions']);

        add_action('template_redirect', 'fvcn_new_post_handler');
        add_action('template_redirect', 'fvcn_post_rating_handler');


        add_action('fvcn_insert_post', 'fvcn_send_notification_mail', 999);


        add_filter('single_template', 'fvcn_increase_post_view_count');


        add_filter('fvcn_get_form_option', 'stripslashes');


        /**
         * fvcn_template_include
         */
        add_filter('fvcn_template_include', 'fvcn_theme_compat_template_include', 4, 2);


        /**
         * fvcn_get_post_form
         */
        add_filter('fvcn_get_post_form_author_name', 'stripslashes');
        add_filter('fvcn_get_post_form_author_email', 'stripslashes');
        add_filter('fvcn_get_post_form_title', 'stripslashes');
        add_filter('fvcn_get_post_form_link', 'stripslashes');
        add_filter('fvcn_get_post_form_content', 'stripslashes');
        add_filter('fvcn_get_post_form_tags', 'stripslashes');


        /**
         * fvcn_new_post_filters
         */
        add_filter('fvcn_new_post_data_pre_insert', 'fvcn_filter_new_post_data');
        add_filter('fvcn_new_post_meta_pre_insert', 'fvcn_filter_new_post_data');


        add_filter('fvcn_new_post_pre_anonymous_author_name', 'sanitize_text_field', 10);
        add_filter('fvcn_new_post_pre_anonymous_author_name', '_wp_specialchars', 30);

        add_filter('fvcn_new_post_pre_anonymous_author_email', 'trim', 10);
        add_filter('fvcn_new_post_pre_anonymous_author_email', 'sanitize_email', 10);
        add_filter('fvcn_new_post_pre_anonymous_author_email', 'wp_filter_kses', 10);

        add_filter('fvcn_new_post_pre_post_title', 'trim', 10);
        add_filter('fvcn_new_post_pre_post_title', 'wp_strip_all_tags', 10);
        add_filter('fvcn_new_post_pre_post_title', 'wp_filter_kses', 10);

        add_filter('fvcn_new_post_pre_post_url', 'trim', 10);
        add_filter('fvcn_new_post_pre_post_url', 'wp_strip_all_tags', 10);
        add_filter('fvcn_new_post_pre_post_url', 'esc_url_raw', 10);
        add_filter('fvcn_new_post_pre_post_url', 'wp_filter_kses', 10);

        add_filter('fvcn_new_post_pre_post_content', 'trim', 10);
        add_filter('fvcn_new_post_pre_post_content', 'balanceTags', 10);
        add_filter('fvcn_new_post_pre_post_content', 'wp_rel_nofollow', 10);
        add_filter('fvcn_new_post_pre_post_content', 'wp_filter_kses', 10);


        /**
         * fvcn_get_post
         */
        add_filter('fvcn_get_post_author_link', 'wp_rel_nofollow');
        add_filter('fvcn_get_post_author_link', 'stripslashes');

        add_filter('fvcn_get_post_content', 'capital_P_dangit');
        add_filter('fvcn_get_post_content', 'wptexturize', 3);
        add_filter('fvcn_get_post_content', 'convert_chars', 5);
        add_filter('fvcn_get_post_content', 'make_clickable', 9);
        add_filter('fvcn_get_post_content', 'force_balance_tags', 25);
        add_filter('fvcn_get_post_content', 'convert_smilies', 20);
        add_filter('fvcn_get_post_content', 'wpautop', 30);


        add_filter('wp_insert_post_data', 'fvcn_fix_post_author', 30, 2);


        /**
         * fvcn_admin
         */
        if (is_admin()) {
            add_action('fvcn_init', function() {
                Container::getInstance()->getAdmin();
            });
        }
    }

    /**
     * fvcn_activation()
     *
     * @version 20120229
     */
    public static function fvcn_activation()
    {
        register_uninstall_hook(__FILE__, 'fvcn_uninstall');
        do_action('fvcn_activation');
    }

    /**
     * fvcn_deactivation()
     *
     * @version 20120229
     */
    public static function fvcn_deactivation()
    {
        do_action('fvcn_deactivation');
    }

    /**
     * fvcn_uninstall()
     *
     * @version 20120229
     */
    public static function fvcn_uninstall()
    {
        do_action('fvcn_uninstall');
    }

    /**
     * fvcn_loaded()
     *
     * @version 20120229
     */
    public static function fvcn_loaded()
    {
        do_action('fvcn_loaded');
    }

    /**
     * fvcn_init()
     *
     * @version 20120229
     */
    public static function fvcn_init()
    {
        do_action('fvcn_init');
    }

    /**
     * fvcn_widgets_init()
     *
     * @version 20120305
     */
    public static function fvcn_widgets_init()
    {
        do_action('fvcn_widgets_init');
    }

    /**
     * fvcn_load_textdomain()
     *
     * @version 20120229
     */
    public static function fvcn_load_textdomain()
    {
        do_action('fvcn_load_textdomain');
    }

    /**
     * fvcn_register_post_type()
     *
     * @version 20120229
     */
    public static function fvcn_register_post_type()
    {
        do_action('fvcn_register_post_type');
    }

    /**
     * fvcn_register_shortcodes()
     *
     * @version 20171112
     */
    public static function fvcn_register_shortcodes()
    {
        do_action('fvcn_register_shortcodes');
    }

    /**
     * fvcn_enqueue_scripts()
     *
     * @version 20120314
     */
    public static function fvcn_enqueue_scripts()
    {
        do_action('fvcn_enqueue_scripts');
    }

    /**
     * fvcn_ready()
     *
     * @version 20120229
     */
    public static function fvcn_ready()
    {
        do_action('fvcn_ready');
    }

    /**
     * fvcn_template_include()
     *
     * @version 20120319
     * @param string $template
     * @return string
     */
    public static function fvcn_template_include($template = '')
    {
        return apply_filters('fvcn_template_include', $template);
    }
}
