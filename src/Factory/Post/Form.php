<?php

namespace FvCommunityNews\Factory\Post;

use FvCommunityNews\Container\Container;
use FvCommunityNews\Container\FactoryInterface;
use FvCommunityNews\Post\Form as PostForm;
use WP_Error;

/**
 * Form
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Form implements FactoryInterface
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
        return new PostForm(
            $container->get('Config'),
            $container->get(WP_Error::class)
        );
    }
}
