<?php

namespace FvCommunityNews\Widget;

use WP_Widget;

/**
 * ListPosts
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class ListPosts extends WP_Widget
{
    /**
     * register()
     *
     */
    public static function register()
    {
        register_widget(self::class);
    }

    /**
     * __construct()
     *
     */
    public function __construct()
    {
        $options = apply_filters('fvcn_list_posts_widget_options', [
            'classname' => 'fvcn_list_posts_widget',
            'description' => __('A list of the most recent community news.', 'fvcn')
        ]);

        parent::__construct('FvCommunityNewsWidgetListPosts', __('FV Community News Posts', 'fvcn'), $options);
    }

    /**
     * widget()
     *
     * @param mixed $args
     * @param array $instance
     */
    public function widget($args, $instance)
    {
        $before_widget = $after_widget = $before_title = $after_title = '';
        extract($args);

        $title = apply_filters('fvcn_list_posts_widget_title', $instance['title']);
        $num_posts = !empty($instance['num_posts']) ? $instance['num_posts'] : '5';

        $registry = fvcn_container_get('Registry');
        $registry['widgetShowThumbnail'] = !empty($instance['thumbnail']) ? true : false;
        $registry['widgetShowViewAll'] = !empty($instance['view_all']) ? true : false;

        $options = [
            'posts_per_page' => $num_posts
        ];

        if (fvcn_has_posts($options)) {

            echo $before_widget;
            echo $before_title . $title . $after_title;

            fvcn_get_template_part('fvcn/widget', 'loop-posts');

            echo $after_widget;
        }
    }

    /**
     * update()
     *
     * @param array $new_instance
     * @param array $old_instance
     * @return array
     */
    public function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['thumbnail'] = strip_tags($new_instance['thumbnail']);
        $instance['view_all'] = strip_tags($new_instance['view_all']);
        $instance['num_posts'] = $new_instance['num_posts'];

        if (empty($instance['num_posts']) || !is_numeric($instance['num_posts'])) {
            $instance['num_posts'] = 5;
        } else {
            $instance['num_posts'] = abs($instance['num_posts']);
        }

        return $instance;
    }

    /**
     * form()
     *
     * @param array $instance
     * @return void
     */
    public function form($instance)
    {
        $title = !empty($instance['title'])    ? esc_attr($instance['title'])        : 'FV Community News';
        $num_posts = !empty($instance['num_posts'])? esc_attr($instance['num_posts'])    : '5';
        $thumbnail = !empty($instance['thumbnail'])? esc_attr($instance['thumbnail'])    : '';
        $view_all = !empty($instance['view_all'])    ? esc_attr($instance['view_all'])    : '';
        ?>

        <p>
            <label for="<?= $this->get_field_id('title'); ?>"><?php _e('Title:', 'fvcn'); ?></label>
            <input type="text" id="<?= $this->get_field_id('title'); ?>" name="<?= $this->get_field_name('title'); ?>" value="<?= $title; ?>" class="widefat">
        </p>
        <p>
            <label for="<?= $this->get_field_id('num_posts'); ?>"><?php _e('Number of posts to show:', 'fvcn'); ?></label>
            <input type="text" id="<?= $this->get_field_id('num_posts'); ?>" name="<?= $this->get_field_name('num_posts'); ?>" value="<?= $num_posts; ?>" size="3">
        </p>
        <p>
            <label for="<?= $this->get_field_id('thumbnail'); ?>">
                <input type="checkbox" id="<?= $this->get_field_id('thumbnail'); ?>" name="<?= $this->get_field_name('thumbnail'); ?>" <?php checked('on', $thumbnail); ?>>
                <?php _e('Show thumbnails', 'fvcn'); ?>
            </label>
        </p>
        <p>
            <label for="<?= $this->get_field_id('view_all'); ?>">
                <input type="checkbox" id="<?= $this->get_field_id('view_all'); ?>" name="<?= $this->get_field_name('view_all'); ?>" <?php checked('on', $view_all); ?>>
                <?php _e('Show "view all" link', 'fvcn'); ?>
            </label>
        </p>

        <?php
    }
}
