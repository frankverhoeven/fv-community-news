<?php

namespace FvCommunityNews\Post;

use FvCommunityNews\Registry;

/**
 * PostType
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class PostType
{
    const POST_TYPE_KEY  = 'fvcn-post';
    const TAG_TYPE_KEY   = 'fvcn-tag';

    const STATUS_PUBLISH = 'publish';
    const STATUS_PRIVATE = 'private';
    const STATUS_PENDING = 'pending';
    const STATUS_SPAM    = 'spam';
    const STATUS_TRASH   = 'trash';

    public static function register()
    {
        $postType = new self();
        $postType->registerPostType()
            ->registerPostStatuses()
            ->registerTaxonomy();
    }

    /**
     * registerPostType()
     *
     * @version 20120710
     * @return PostType
     */
    public function registerPostType()
    {
        $post = [
            'labels' => [
                'name' => __('FV Community News', 'fvcn'),
                'menu_name' => __('Community News', 'fvcn'),
                'singular_name' => __('Community News', 'fvcn'),
                'all_items' => __('Community News', 'fvcn'),
                'add_new' => __('New Post', 'fvcn'),
                'add_new_item' => __('Create New Post', 'fvcn'),
                'edit' => __('Edit', 'fvcn'),
                'edit_item' => __('Edit Post', 'fvcn'),
                'new_item' => __('New Post', 'fvcn'),
                'view' => __('View Post', 'fvcn'),
                'view_item' => __('View Post', 'fvcn'),
                'search_items' => __('Search Community News', 'fvcn'),
                'not_found' => __('No posts found', 'fvcn'),
                'not_found_in_trash'=> __('No posts found in Trash','fvcn')
            ],
            'rewrite' => [
                'slug' => Registry::get('postSlug'),
                'with_front' => false
            ],
            'supports' => [
                'title',
                'editor',
                'thumbnail',
                'comments'
            ]
        ];

        $options = apply_filters('fvcn_register_fvcn_post_type', [
            'labels' => $post['labels'],
            'rewrite' => $post['rewrite'],
            'supports' => $post['supports'],
            'description' => __('FV Community News Posts', 'fvcn'),
            'has_archive' => Registry::get('postArchiveSlug'),
            'public' => true,
            'publicly_queryable' => true,
            'can_export' => true,
            'hierarchical' => false,
            'query_var' => true,
            'exclude_from_search' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 20,
            'menu_icon' => '',
            'capability_type' => 'post',
        ]);

        register_post_type(self::POST_TYPE_KEY, $options);

        return $this;
    }

    /**
     * registerPostStatuses()
     *
     * @version 20120716
     * @return PostType
     */
    public function registerPostStatuses()
    {
        $status = apply_filters('fvcn_register_post_status_spam', [
            'label' => __('Spam', 'fvcn'),
            'label_count' => _nx_noop('Spam <span class="count">(%s)</span>', 'Spam <span class="count">(%s)</span>', 'fvcn'),
            'protected' => true,
            'exclude_from_search' => true,
            'show_in_admin_status_list' => true,
            'show_in_admin_all_list' => false
        ]);

        register_post_status(self::STATUS_SPAM, $status);

        return $this;
    }

    /**
     * registerTaxonomy()
     *
     * @version 20120716
     * @return PostType
     */
    public function registerTaxonomy()
    {
        $tag = [
            'labels' => [
                'name' => __('Tags', 'fvcn'),
                'singular_name' => __('Tag', 'fvcn'),
                'search_items' => __('Search Tags', 'fvcn'),
                'popular_items' => __('Popular Tags', 'fvcn'),
                'all_items' => __('All Tags', 'fvcn'),
                'edit_item' => __('Edit Tag', 'fvcn'),
                'update_item' => __('Update Tag', 'fvcn'),
                'add_new_item' => __('Add New Tag', 'fvcn'),
                'new_item_name' => __('New Tag Name', 'fvcn'),
            ],
            'rewrite' => [
                'slug' => Registry::get('postTagSlug'),
                'with_front' => false
            ]
        ];

        $options = apply_filters('fvcn_register_fvcn_post_tag_id', [
            'labels' => $tag['labels'],
            'rewrite' => $tag['rewrite'],
            'public' => true
        ]);

        register_taxonomy(
            self::TAG_TYPE_KEY,
            self::POST_TYPE_KEY,
            $options
        );

        return $this;
    }
}
