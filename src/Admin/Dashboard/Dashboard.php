<?php

namespace FvCommunityNews\Admin\Dashboard;

use FvCommunityNews\Admin\Dashboard\Widget\RecentPosts;
use FvCommunityNews\Config\AbstractConfig as Config;

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
     * __construct()
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->registerWidgets();
    }

    /**
     * registerWidgets()
     *
     * @return Dashboard
     */
    public function registerWidgets()
    {
        add_action('wp_dashboard_setup', [new RecentPosts($this->config), 'register']);
        do_action('fvcn_register_dashboard_widgets');

        return $this;
    }
}
