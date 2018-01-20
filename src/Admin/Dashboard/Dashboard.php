<?php

namespace FvCommunityNews\Admin\Dashboard;

use FvCommunityNews\Admin\Dashboard\Widget\RecentPosts;
use FvCommunityNews\Config\AbstractConfig as Config;
use FvCommunityNews\Post\Mapper as PostMapper;

/**
 * Dashboard
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Dashboard
{
    /**
     * @var Config
     */
    private $config;
    /**
     * @var PostMapper
     */
    private $postMapper;

    /**
     * __construct()
     *
     * @param Config $config
     * @param PostMapper $postMapper
     */
    public function __construct(Config $config, PostMapper $postMapper)
    {
        $this->config = $config;
        $this->postMapper = $postMapper;

        $this->registerWidgets();
    }

    /**
     * registerWidgets()
     *
     * @return Dashboard
     */
    public function registerWidgets()
    {
        add_action('wp_dashboard_setup', [new RecentPosts($this->config, $this->postMapper), 'register']);
        do_action('fvcn_register_dashboard_widgets');

        return $this;
    }
}
