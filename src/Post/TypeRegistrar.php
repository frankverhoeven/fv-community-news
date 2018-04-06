<?php

namespace FvCommunityNews\Post;

class TypeRegistrar
{
    /**
     * registerPostType()
     *
     * @param string $postSlug
     * @param string $archiveSlug
     * @return void
     */
    public static function registerPostType(string $postSlug, string $archiveSlug)
    {
        $post = [
            'labels' => [
                'name' => \__('FV Community News', 'fvcn'),
                'menu_name' => \__('Community News', 'fvcn'),
                'singular_name' => \__('Community News', 'fvcn'),
                'all_items' => \__('Community News', 'fvcn'),
                'add_new' => \__('New Post', 'fvcn'),
                'add_new_item' => \__('Create New Post', 'fvcn'),
                'edit' => \__('Edit', 'fvcn'),
                'edit_item' => \__('Edit Post', 'fvcn'),
                'new_item' => \__('New Post', 'fvcn'),
                'view' => \__('View Post', 'fvcn'),
                'view_item' => \__('View Post', 'fvcn'),
                'search_items' => \__('Search Community News', 'fvcn'),
                'not_found' => \__('No posts found', 'fvcn'),
                'not_found_in_trash'=> \__('No posts found in Trash','fvcn')
            ],
            'rewrite' => [
                'slug' => $postSlug,
                'with_front' => false
            ],
            'supports' => [
                'title',
                'editor',
                'thumbnail',
                'comments'
            ]
        ];

        $options = \apply_filters('fvcn_register_fvcn_post_type', [
            'labels' => $post['labels'],
            'rewrite' => $post['rewrite'],
            'supports' => $post['supports'],
            'description' => \__('FV Community News Posts', 'fvcn'),
            'has_archive' => $archiveSlug,
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

        \register_post_type(Type::post(), $options);
    }

    /**
     * registerPostStatuses()
     *
     * @return void
     */
    public static function registerPostStatuses()
    {
        $status = \apply_filters('fvcn_register_post_status_spam', [
            'label' => \__('Spam', 'fvcn'),
            'label_count' => \_nx_noop(
                'Spam <span class="count">(%s)</span>',
                'Spam <span class="count">(%s)</span>',
                'fvcn'
            ),
            'protected' => true,
            'exclude_from_search' => true,
            'show_in_admin_status_list' => true,
            'show_in_admin_all_list' => false
        ]);

        \register_post_status(Status::spam(), $status);
    }

    /**
     * registerTaxonomy()
     *
     * @param string $tagSlug
     * @return void
     */
    public static function registerTaxonomy(string $tagSlug)
    {
        $tag = [
            'labels' => [
                'name' => \__('Tags', 'fvcn'),
                'singular_name' => \__('Tag', 'fvcn'),
                'search_items' => \__('Search Tags', 'fvcn'),
                'popular_items' => \__('Popular Tags', 'fvcn'),
                'all_items' => \__('All Tags', 'fvcn'),
                'edit_item' => \__('Edit Tag', 'fvcn'),
                'update_item' => \__('Update Tag', 'fvcn'),
                'add_new_item' => \__('Add New Tag', 'fvcn'),
                'new_item_name' => \__('New Tag Name', 'fvcn'),
            ],
            'rewrite' => [
                'slug' => $tagSlug,
                'with_front' => false
            ]
        ];

        $options = \apply_filters('fvcn_register_fvcn_post_tag_id', [
            'labels' => $tag['labels'],
            'rewrite' => $tag['rewrite'],
            'public' => true
        ]);

        \register_taxonomy(Type::tag()->getType(), Type::post()->getType(), $options);
    }
}
