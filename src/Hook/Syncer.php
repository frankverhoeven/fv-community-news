<?php

namespace FvCommunityNews\Hook;

use FvCommunityNews\Config\AbstractConfig as Config;
use FvCommunityNews\Container\Container;

class Syncer implements HookInterface
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
    public function doHook()
    {
        /* @var Config $config */
        $config = $this->container->get('Config');

        if ($config->get('_fvcn_syncer_enabled', true)) {
            $syncer = $this->container->get(\FvCommunityNews\Syncer\Syncer::class);

            add_action('fvcn_insert_post', [$syncer, 'submitPost'], 999	);
            add_action('fvcn_publish_post',	 [$syncer, 'submitPost'], 999);
            add_action('fvcn_like_post', [$syncer, 'likePost'], 999);
            add_action('fvcn_unlike_post', [$syncer, 'unlikePost'], 999);
            add_action('fvcn_increase_post_view_count',	 [$syncer, 'increasePostView'], 999);
        }
    }
}
