<?php

namespace FvCommunityNews\Factory\Hook;

use FvCommunityNews\Container\Container;
use FvCommunityNews\Container\FactoryInterface;

/**
 * EnqueueScripts
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class EnqueueScripts implements FactoryInterface
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
        return new \FvCommunityNews\Hook\EnqueueScripts($container);
    }
}
