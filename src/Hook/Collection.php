<?php

namespace FvCommunityNews\Hook;

use FvCommunityNews\Container\Container;
use InvalidArgumentException;

/**
 * Collection
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Collection
{
    /**
     * @var Container
     */
    private $container;
    /**
     * @var array Actions to setup
     */
    private $actions = [
        'init' => [
            Init::class
        ],
        'widgets_init' => [
            WidgetsInit::class,
        ],
        'wp_enqueue_scripts' => [
            EnqueueScripts::class,
        ],
        'wp_head' => [
            Head::class,
        ],

        'template_redirect' => [
            Controller::class,
        ],

        'fvcn_insert_post' => [
            ['function' => 'fvcn_send_notification_mail', 'priority' => 90],
        ],

        'after_setup_theme' => [
            ['function' => 'fvcn_add_thumbnail_theme_support', 'priority' => 90],
        ],

        'fvcn_deactivation' => [
            ['function' => 'flush_rewrite_rules'],
        ],

        'fvcn_ready' => [
            Akismet::class,
        ],
    ];
    /**
     * @var array Filters to setup
     */
    private $filters = [
        'fvcn_get_post_form_author_name' => [
            ['function' => 'stripslashes'],
        ],
        'fvcn_get_post_form_author_email' => [
            ['function' => 'stripslashes'],
        ],
        'fvcn_get_post_form_title' => [
            ['function' => 'stripslashes'],
        ],
        'fvcn_get_post_form_link' => [
            ['function' => 'stripslashes'],
        ],
        'fvcn_get_post_form_content' => [
            ['function' => 'stripslashes'],
        ],
        'fvcn_get_post_form_tags' => [
            ['function' => 'stripslashes'],
        ],

        'fvcn_new_post_pre_anonymous_author_name' => [
            ['function' => 'sanitize_text_field'],
            ['function' => '_wp_specialchars', 'priority' => 30],
        ],
        'fvcn_new_post_pre_anonymous_author_email' => [
            ['function' => 'trim'],
            ['function' => 'sanitize_email'],
            ['function' => 'wp_filter_kses'],
        ],
        'fvcn_new_post_pre_post_title' => [
            ['function' => 'trim'],
            ['function' => 'wp_strip_all_tags'],
            ['function' => 'wp_filter_kses'],
        ],
        'fvcn_new_post_pre_post_url' => [
            ['function' => 'trim'],
            ['function' => 'wp_strip_all_tags'],
            ['function' => 'esc_url_raw'],
            ['function' => 'wp_filter_kses'],
        ],
        'fvcn_new_post_pre_post_content' => [
            ['function' => 'trim'],
            ['function' => 'balanceTags'],
            ['function' => 'wp_rel_nofollow'],
            ['function' => 'wp_filter_kses'],
        ],

        'fvcn_get_post_author_link' => [
            ['function' => 'wp_rel_nofollow'],
            ['function' => 'stripslashes'],
        ],
        'fvcn_get_post_content' => [
            ['function' => 'capital_P_dangit'],
            ['function' => 'wptexturize', 'priority' => 3],
            ['function' => 'convert_chars', 'priority' => 5],
            ['function' => 'make_clickable', 'priority' => 9],
            ['function' => 'force_balance_tags', 'priority' => 25],
            ['function' => 'convert_smilies', 'priority' => 20],
            ['function' => 'wpautop', 'priority' => 30],
        ],

        'wp_insert_post_data' => [
            [
                'function' => 'fvcn_fix_post_author',
                'priority' => 30,
                'arguments' => 2,
            ]
        ],

        'fvcn_get_form_option' => [
            ['function' => 'stripslashes'],
        ],

        'single_template' => [
            ['function' => 'fvcn_increase_post_view_count'],
        ],

        'template_include' => [
            ['function' => 'fvcn_template_include'],
        ],
        'fvcn_template_include' => [
            [
                'function' => 'fvcn_theme_compat_template_include',
                'priority' => 4,
                'arguments' => 2
            ],
        ],
    ];

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Register actions and filters.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerHooks($this->actions);
        $this->registerHooks($this->filters);
    }

    /**
     * Register provided hooks.
     *  Since `add_action` simply calls `add_filter`, just calling `add_filter` is sufficient.
     *
     * @param array $hooks The hooks to register.
     * @return void
     */
    protected function registerHooks(array $hooks): void
    {
        foreach ($hooks as $tag => $filters) {
            foreach ($filters as $filter) {
                if (is_array($filter)) {
                    $priority = 10;
                    $arguments = 1;

                    if (isset($filter['priority'])) {
                        $priority = $filter['priority'];
                    }
                    if (isset($filter['arguments'])) {
                        $arguments = $filter['arguments'];
                    }

                    add_filter($tag, $filter['function'], $priority, $arguments);
                } else if (is_string($filter)) {
                    $class = $this->container->get($filter);

                    if (! $class instanceof HookInterface) {
                        throw new InvalidArgumentException('Invalid hook provided.');
                    }

                    add_filter($tag, [$class, 'doHook']);
                } else {
                    throw new InvalidArgumentException('Invalid hook provided.');
                }
            }
        }
    }
}
