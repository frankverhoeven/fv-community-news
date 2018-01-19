<?php

namespace FvCommunityNews\Factory\Akismet;

use FvCommunityNews\Container\Container;
use FvCommunityNews\Container\FactoryInterface;

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
        return new \FvCommunityNews\Akismet\Handler($container->get(\FvCommunityNews\Akismet\Akismet::class));
    }
}
