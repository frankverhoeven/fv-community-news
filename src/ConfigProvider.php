<?php

declare(strict_types=1);

namespace FvCommunityNews;

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
use FvCommunityNews\Factory\Hook\Syncer as SyncerHookFactory;
use FvCommunityNews\Factory\Post\Controller as PostControllerFactory;
use FvCommunityNews\Factory\Post\Form as PostFormFactory;
use FvCommunityNews\Factory\Syncer\Syncer as SyncerFactory;
use FvCommunityNews\Factory\View\AjaxForm as AjaxFormFactory;
use FvCommunityNews\Hook\Akismet as AkismetHook;
use FvCommunityNews\Hook\Controller as PostControllerHook;
use FvCommunityNews\Hook\EnqueueScripts as EnqueueScriptsHook;
use FvCommunityNews\Hook\Head as HeadHook;
use FvCommunityNews\Hook\Init as InitHook;
use FvCommunityNews\Hook\Syncer as SyncerHook;
use FvCommunityNews\Hook\WidgetsInit as WidgetsInitHook;
use FvCommunityNews\Post\Controller as PostController;
use FvCommunityNews\Post\Mapper as PostMapper;
use FvCommunityNews\Post\Form as PostForm;
use FvCommunityNews\Syncer\Syncer;
use FvCommunityNews\View\AjaxForm;

/**
 * ConfigProvider
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
final class ConfigProvider
{
    /**
     * @return array
     */
    public function __invoke(): array
    {
        return [
            'services' => $this->getServices(),
            'defaults' => $this->getDefaults(),
        ];
    }

    /**
     * @return array
     */
    private function getServices(): array
    {
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

            Syncer::class               => SyncerFactory::class,

            AkismetHook::class          => AkismetHookFactory::class,
            EnqueueScriptsHook::class   => EnqueueScriptsHookFactory::class,
            HeadHook::class             => InvokableFactory::class,
            InitHook::class             => InitHookFactory::class,
            PostControllerHook::class   => PostControllerHookFactory::class,
            SyncerHook::class           => SyncerHookFactory::class,
            WidgetsInitHook::class      => InvokableFactory::class,

            AjaxForm::class             => AjaxFormFactory::class,

            \WP_Error::class            => InvokableFactory::class,

            'Registry'                  => new MemoryConfig(),
        ];
    }

    /**
     * @return array
     */
    private function getDefaults(): array
    {
        return [
            /**
             * @var int Current plugin version
             */
            '_fvcn_version' => Version::getCurrentVersion(),

            /**
             * @var bool Require admin approval of posts
             */
            '_fvcn_admin_moderation' => false,

            /**
             * @var bool Automatically approve posts from trusted users
             */
            '_fvcn_user_moderation' => true,

            /**
             * @var bool Send notification mail after a post is added
             */
            '_fvcn_mail_on_submission' => false,

            /**
             * @var bool Send notification mail if a post is held for moderation
             */
            '_fvcn_mail_on_moderation' => true,

            /**
             * @var bool Allow anonymous user to add posts
             */
            '_fvcn_is_anonymous_allowed' => true,

            /**
             * @var bool Whether syncing posts to the API is enabled
             */
            '_fvcn_syncer_enabled' => true,

            /**
             * @var string Base slug
             */
            '_fvcn_base_slug' => 'fv-community-news',

            /**
             * @var string Post slug
             */
            '_fvcn_post_slug' => 'post',

            /**
             * @var string Tag slug
             */
            '_fvcn_post_tag_slug' => 'tag',

            /**
             * @var string Archive slug
             */
            '_fvcn_post_archive_slug' => 'archive',

            /**
             * @var string Author name form field label
             */
            '_fvcn_post_form_author_name_label' => \__('Author Name', 'fvcn'),

            /**
             * @var int Minimum author name length
             */
            '_fvcn_post_form_author_name_length_min' => 2,

            /**
             * @var int Maximum author name length
             */
            '_fvcn_post_form_author_name_length_max' => 40,

            /**
             * @var string Author email form field label
             */
            '_fvcn_post_form_author_email_label' => \__('Author Email', 'fvcn'),

            /**
             * @var string Title form field label
             */
            '_fvcn_post_form_title_label' => \__('Title', 'fvcn'),

            /**
             * @var int Minimum title length
             */
            '_fvcn_post_form_title_length_min' => 8,

            /**
             * @var int Maximum title length
             */
            '_fvcn_post_form_title_length_max' => 70,

            /**
             * @var bool Enable the use of link
             */
            '_fvcn_post_form_link_enabled' => true,

            /**
             * @var bool Require a link to be posted
             */
            '_fvcn_post_form_link_required' => true,

            /**
             * @var string Link form field label
             */
            '_fvcn_post_form_link_label' => \__('Link', 'fvcn'),

            /**
             * @var int Minimum link length
             */
            '_fvcn_post_form_link_length_min' => 6,

            /**
             * @var int Maximum link length
             */
            '_fvcn_post_form_link_length_max' => 1000,

            /**
             * @var string Description from field label
             */
            '_fvcn_post_form_content_label' => \__('Description', 'fvcn'),

            /**
             * @var int Minimum content length
             */
            '_fvcn_post_form_content_length_min' => 20,

            /**
             * @var int Maximum content length
             */
            '_fvcn_post_form_content_length_max' => 5000,

            /**
             * @var bool Enable the use of tags
             */
            '_fvcn_post_form_tags_enabled' => true,

            /**
             * @var bool Require tags to be added
             */
            '_fvcn_post_form_tags_required' => true,

            /**
             * @var string Tags form field label
             */
            '_fvcn_post_form_tags_label' => \__('Tags', 'fvcn'),

            /**
             * @var int Minimum tags length
             */
            '_fvcn_post_form_tags_length_min' => 2,

            /**
             * @var int Maximum tags length
             */
            '_fvcn_post_form_tags_length_max' => 1000,

            /**
             * @var bool Enable the use of thumbnails
             */
            '_fvcn_post_form_thumbnail_enabled' => true,

            /**
             * @var bool Require a thumbnail
             */
            '_fvcn_post_form_thumbnail_required' => false,

            /**
             * @var string Thumbnail form field label
             */
            '_fvcn_post_form_thumbnail_label' => \__('Thumbnail', 'fvcn'),

            /**
             * @var int Number of posts to show on the admin dashboard
             */
            '_fvcn_dashboard_rp_num' => 5,
        ];
    }
}
