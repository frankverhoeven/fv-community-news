<?php

namespace FvCommunityNews\Container;

/**
 * FactoryInterface
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
interface FactoryInterface
{
    /**
     * Create new container object
     *
     * @param Container $container Container object.
     * @param string $requestedName Name of the requested entry.
     * @return mixed
     */
    public function create(Container $container, string $requestedName);
}
