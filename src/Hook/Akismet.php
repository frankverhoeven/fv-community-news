<?php

namespace FvCommunityNews\Hook;

use FvCommunityNews\Akismet\Handler;
use FvCommunityNews\Container\Container;

/**
 * Akismet
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Akismet implements HookInterface
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
        if (!defined('AKISMET_VERSION')) {
            return;
        }

        $akismetHandler = $this->container->get(Handler::class);

        if (get_option('_fvcn_akismet_enabled', false)) {
            add_action('fvcn_insert_post', [$akismetHandler, 'checkPost']);
            add_action('fvcn_spam_post', [$akismetHandler, 'submitPost']);
            add_action('fvcn_publish_post', [$akismetHandler, 'submitPost']);
        }

        if (is_admin()) {
            add_action('fvcn_register_admin_settings', [$akismetHandler, 'registerSettings']);
        }
    }
}
