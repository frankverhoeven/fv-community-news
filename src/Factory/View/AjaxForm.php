<?php

namespace FvCommunityNews\Factory\View;

use FvCommunityNews\Container\Container;
use FvCommunityNews\Container\FactoryInterface;

/**
 * AjaxForm
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class AjaxForm implements FactoryInterface
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
        return new \FvCommunityNews\View\AjaxForm();
    }
}
