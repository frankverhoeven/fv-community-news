<?php

namespace FvCommunityNews\Factory\Admin;

use FvCommunityNews\Container\Container;
use FvCommunityNews\Container\FactoryInterface;

/**
 * Admin
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Admin implements FactoryInterface
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
        return new \FvCommunityNews\Admin\Admin($container);
    }
}
