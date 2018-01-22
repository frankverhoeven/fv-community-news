<?php

namespace FvCommunityNews\Factory\Hook;

use FvCommunityNews\Container\Container;
use FvCommunityNews\Container\FactoryInterface;
use FvCommunityNews\Hook\Controller as PostControllerHook;
use FvCommunityNews\Post\Controller as PostController;

/**
 * Controller
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Controller implements FactoryInterface
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
        return new PostControllerHook($container->get(PostController::class));
    }
}
