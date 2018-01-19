<?php

namespace FvCommunityNews\Factory\Hook;

use FvCommunityNews\Container\Container;
use FvCommunityNews\Container\FactoryInterface;

/**
 * Init
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Init implements FactoryInterface
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
        return new \FvCommunityNews\Hook\Init($container->get('Config'), $container);
    }
}
