<?php

use FvCommunityNews\Admin\Admin;
use FvCommunityNews\Admin\Dashboard\Dashboard as AdminDashboard;
use FvCommunityNews\Admin\Post\Edit as AdminEdit;
use FvCommunityNews\Admin\Post\Moderation as AdminModeration;
use FvCommunityNews\Admin\Settings\Form as AdminForm;
use FvCommunityNews\Admin\Settings\Settings as AdminSettings;
use FvCommunityNews\Akismet\Akismet;
use FvCommunityNews\Akismet\Handler as AkismetHandler;
use FvCommunityNews\Config\Memory as MemoryConfig;
use FvCommunityNews\Container\InvokableFactory;
use FvCommunityNews\Factory\Admin\Admin as AdminFactory;
use FvCommunityNews\Factory\Admin\Dashboard\Dashboard as AdminDashboardFactory;
use FvCommunityNews\Factory\Admin\Post\Moderation as AdminModerationFactory;
use FvCommunityNews\Factory\Akismet\Akismet as AkismetFactory;
use FvCommunityNews\Factory\Akismet\Handler as AkismetHandlerFactory;
use FvCommunityNews\Factory\Hook\Akismet as AkismetHookFactory;
use FvCommunityNews\Factory\Hook\Controller as PostControllerHookFactory;
use FvCommunityNews\Factory\Hook\EnqueueScripts as EnqueueScriptsHookFactory;
use FvCommunityNews\Factory\Hook\Init as InitHookFactory;
use FvCommunityNews\Factory\Post\Controller as PostControllerFactory;
use FvCommunityNews\Factory\Post\Form as PostFormFactory;
use FvCommunityNews\Factory\View\AjaxForm as AjaxFormFactory;
use FvCommunityNews\Hook\Akismet as AkismetHook;
use FvCommunityNews\Hook\Controller as PostControllerHook;
use FvCommunityNews\Hook\EnqueueScripts as EnqueueScriptsHook;
use FvCommunityNews\Hook\Head as HeadHook;
use FvCommunityNews\Hook\Init as InitHook;
use FvCommunityNews\Hook\WidgetsInit as WidgetsInitHook;
use FvCommunityNews\Post\Controller as PostController;
use FvCommunityNews\Post\Mapper as PostMapper;
use FvCommunityNews\Post\Form as PostForm;
use FvCommunityNews\View\AjaxForm;

return [
    PostController::class       => PostControllerFactory::class,
    PostMapper::class           => InvokableFactory::class,
    PostForm::class             => PostFormFactory::class,

    Admin::class                => AdminFactory::class,
    AdminDashboard::class       => AdminDashboardFactory::class,
    AdminEdit::class            => InvokableFactory::class,
    AdminModeration::class      => AdminModerationFactory::class,
    AdminForm::class            => InvokableFactory::class,
    AdminSettings::class        => InvokableFactory::class,

    Akismet::class              => AkismetFactory::class,
    AkismetHandler::class       => AkismetHandlerFactory::class,

    AkismetHook::class          => AkismetHookFactory::class,
    EnqueueScriptsHook::class   => EnqueueScriptsHookFactory::class,
    HeadHook::class             => InvokableFactory::class,
    InitHook::class             => InitHookFactory::class,
    PostControllerHook::class   => PostControllerHookFactory::class,
    WidgetsInitHook::class      => InvokableFactory::class,

    AjaxForm::class             => AjaxFormFactory::class,

    WP_Error::class             => InvokableFactory::class,

    'Registry'                  => new MemoryConfig(),
];
