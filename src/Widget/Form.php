<?php

namespace FvCommunityNews\Widget;

use WP_Widget;

/**
 * Form
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class Form extends WP_Widget
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
        $options = apply_filters('fvcn_form_widget_options', [
            'classname' => 'fvcn_form_widget',
            'description' => __('A form to add community news.', 'fvcn')
        ]);

        parent::__construct('FvCommunityNewsWidgetForm', __('FV Community News Form', 'fvcn'), $options);
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

        $title = apply_filters('fvcn_form_widget_title', $instance['title']);

        echo $before_widget;
        echo $before_title . $title . $after_title;
        ?>

        <?php if (fvcn_is_anonymous_allowed() || !fvcn_is_anonymous()) : ?>

            <?php fvcn_get_template_part('fvcn/form', 'post'); ?>

        <?php else : ?>

            <?php fvcn_get_template_part('fvcn/feedback', 'no-anonymous'); ?>

        <?php endif; ?>

        <?php
        echo $after_widget;
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

        return $instance;
    }

    /**
     * form()
     *
     * @param array $instance
     */
    public function form($instance)
    {
        $title = !empty($instance['title']) ? esc_attr($instance['title']) : 'Add Community News';
        ?>

        <p>
            <label for="<?= $this->get_field_id('title'); ?>"><?php _e('Title:', 'fvcn'); ?></label>
            <input type="text" id="<?= $this->get_field_id('title'); ?>" name="<?= $this->get_field_name('title'); ?>" value="<?= $title; ?>" class="widefat">
        </p>

        <?php
    }
}
