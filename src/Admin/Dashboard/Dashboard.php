<?php

namespace FvCommunityNews\Admin\Dashboard;

use FvCommunityNews\Admin\Dashboard\Widget\RecentPosts;

/**
 * Dashboard
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Dashboard
{
    /**
     * __construct()
     *
     * @version 20120729
     */
    public function __construct()
    {
        $this->registerWidgets();
    }

    /**
     * registerWidgets()
     *
     * @return Dashboard
     * @version 20171111
     */
    public function registerWidgets()
    {
        add_action('wp_dashboard_setup', [new RecentPosts(), 'register']);
        do_action('fvcn_register_dashboard_widgets');

        return $this;
    }
}
