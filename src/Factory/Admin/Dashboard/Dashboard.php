<?php

namespace FvCommunityNews\Factory\Admin\Dashboard;

use FvCommunityNews\Admin\Dashboard\Dashboard as AdminDashboard;
use FvCommunityNews\Container\Container;
use FvCommunityNews\Container\FactoryInterface;
use FvCommunityNews\Post\Mapper;

/**
 * Dashboard
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Dashboard implements FactoryInterface
{
    /**
     * Create new container object
     *
     * @param Container $container Container object.
     * @param string $requestedName Name of the requested entry.
     * @return mixed
     */
    public function create(Container $container, string $requestedName)
    {
        return new AdminDashboard(
            $container->get('Config'),
            $container->get(Mapper::class)
        );
    }
}
