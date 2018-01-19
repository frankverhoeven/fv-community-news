<?php

namespace FvCommunityNews\Factory\Akismet;

use FvCommunityNews\Container\Container;
use FvCommunityNews\Container\FactoryInterface;

/**
 * Akismet
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Akismet implements FactoryInterface
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
        return new \FvCommunityNews\Akismet\Akismet(akismet_get_key(), get_option('home'));
    }
}
