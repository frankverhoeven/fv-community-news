<?php

namespace FvCommunityNews\Factory\Akismet;

use FvCommunityNews\Akismet\Akismet;
use FvCommunityNews\Akismet\Handler as AkismetHandler;
use FvCommunityNews\Container\Container;
use FvCommunityNews\Container\FactoryInterface;
use FvCommunityNews\Post\Mapper;

/**
 * Handler
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Handler implements FactoryInterface
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
        return new AkismetHandler(
            $container->get(Akismet::class),
            $container->get(Mapper::class)
        );
    }
}
