<?php

namespace FvCommunityNews\Factory\Post;

use FvCommunityNews\Container\Container;
use FvCommunityNews\Container\FactoryInterface;
use FvCommunityNews\Post\Controller as PostController;
use FvCommunityNews\Post\Mapper;
use FvCommunityNews\Post\Form;

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
        return new PostController(
            $container->get('Config'),
            $container->get(Mapper::class),
            $container->get(Form::class)
        );
    }
}
