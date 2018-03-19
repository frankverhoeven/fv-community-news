<?php

namespace FvCommunityNews\Factory\Syncer;

use FvCommunityNews\Container\Container;
use FvCommunityNews\Container\FactoryInterface;

/**
 * Syncer
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Syncer implements FactoryInterface
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
        return new \FvCommunityNews\Syncer\Syncer();
    }
}
