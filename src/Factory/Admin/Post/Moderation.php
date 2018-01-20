<?php

namespace FvCommunityNews\Factory\Admin\Post;

use FvCommunityNews\Admin\Post\Moderation as AdminModeration;
use FvCommunityNews\Container\Container;
use FvCommunityNews\Container\FactoryInterface;
use FvCommunityNews\Post\Mapper;

/**
 * Moderation
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Moderation implements FactoryInterface
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
        return new AdminModeration(
            $container->get(Mapper::class)
        );
    }
}
