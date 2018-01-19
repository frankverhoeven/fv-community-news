<?php

namespace FvCommunityNews\Application;

use FvCommunityNews;
use FvCommunityNews\Container\Container;

class Application
{
    /**
     * @var array
     */
    private $config;

    /**
     * __construct()
     *
     * @param array $config Application config
     * @version 20171112
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    protected function setupContainer()
    {
        // @todo: move to separate config?
        FvCommunityNews::$container = new Container([
            'Config' => new FvCommunityNews\Config\WordPress($this->config),
            'Registry' => new FvCommunityNews\Config\Memory(),

            FvCommunityNews\Admin\Admin::class => FvCommunityNews\Factory\Admin\Admin::class,
            FvCommunityNews\Admin\Dashboard\Dashboard::class => FvCommunityNews\Factory\Admin\Dashboard\Dashboard::class,
            FvCommunityNews\Admin\Post\Edit::class => FvCommunityNews\Container\InvokableFactory::class,
            FvCommunityNews\Admin\Post\Moderation::class => FvCommunityNews\Container\InvokableFactory::class,
            FvCommunityNews\Admin\Settings\Form::class => FvCommunityNews\Container\InvokableFactory::class,
            FvCommunityNews\Admin\Settings\Settings::class => FvCommunityNews\Container\InvokableFactory::class,

            FvCommunityNews\Akismet\Akismet::class => FvCommunityNews\Factory\Akismet\Akismet::class,
            FvCommunityNews\Akismet\Handler::class => FvCommunityNews\Factory\Akismet\Handler::class,

            FvCommunityNews\Hook\Akismet::class => FvCommunityNews\Factory\Hook\Akismet::class,
            FvCommunityNews\Hook\EnqueueScripts::class => FvCommunityNews\Factory\Hook\EnqueueScripts::class,
            FvCommunityNews\Hook\Head::class => FvCommunityNews\Container\InvokableFactory::class,
            FvCommunityNews\Hook\Init::class => FvCommunityNews\Factory\Hook\Init::class,
            FvCommunityNews\Hook\WidgetsInit::class => FvCommunityNews\Container\InvokableFactory::class,

            FvCommunityNews\View\AjaxForm::class => FvCommunityNews\Factory\View\AjaxForm::class,

            \WP_Error::class => FvCommunityNews\Container\InvokableFactory::class,
        ]);
    }

    protected function setupHooks()
    {
        $hookCollection = new FvCommunityNews\Hook\Collection(FvCommunityNews::$container);
        $hookCollection->register();
    }

    /**
     * run()
     *
     */
    public function run()
    {
        $this->setupContainer();
        $this->setupHooks();
    }
}
