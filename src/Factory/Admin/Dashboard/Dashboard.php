<?php

namespace FvCommunityNews\Factory\Admin\Dashboard;

use FvCommunityNews\Container\Container;
use FvCommunityNews\Container\FactoryInterface;

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
        return new \FvCommunityNews\Admin\Dashboard\Dashboard($container->get('Config'));
    }
}
