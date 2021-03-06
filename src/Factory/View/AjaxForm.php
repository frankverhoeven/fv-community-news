<?php

namespace FvCommunityNews\Factory\View;

use FvCommunityNews\Container\Container;
use FvCommunityNews\Container\FactoryInterface;
use FvCommunityNews\Post\Controller;
use FvCommunityNews\View\AjaxForm as AjaxFormView;
use WP_Error;

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
        return new AjaxFormView(
            $container->get(Controller::class),
            $container->get(WP_Error::class)
        );
    }
}
