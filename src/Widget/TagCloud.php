<?php

namespace FvCommunityNews\Widget;

use WP_Widget;

/**
 * TagCloud
 *
 * @author Frank Verhoeven <hi@frankverhoeven.me>
 */
class TagCloud extends WP_Widget
{
    /**
     * register()
     *
     * @version 20120411
     */
    public static function register()
    {
        register_widget(self::class);
    }

    /**
     * __construct()
     *
     * @version 20171108
     */
    public function __construct()
    {
        $options = apply_filters('fvcn_form_widget_options', [
            'classname' => 'fvcn_tag_cloud',
            'description' => __('A tag cloud with tags from community news.', 'fvcn')
        ]);

        parent::__construct('FvCommunityNewsWidgetTagCloud', __('FV Community News Tag Cloud', 'fvcn'), $options);
    }

    /**
     * widget()
     *
     * @param mixed $args
     * @param array $instance
     * @version 20120411
     */
    public function widget($args, $instance)
    {
        $before_widget = $after_widget = $before_title = $after_title = '';
        extract($args);

        $title = apply_filters('fvcn_form_widget_title', $instance['title']);

        echo $before_widget;
        echo $before_title . $title . $after_title;
        ?>
        <div class="fvcn-tag-cloud">
            <?php fvcn_tag_cloud(); ?>
        </div>
        <?php
        echo $after_widget;
    }

    /**
     * update()
     *
     * @param array $new_instance
     * @param array $old_instance
     * @return array
     * @version 20120411
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
         * @version 20120411
     */
    public function form($instance)
    {
        $title = !empty($instance['title']) ? esc_attr($instance['title']) : 'Community News Tags';
        ?>

        <p>
            <label for="<?= $this->get_field_id('title'); ?>"><?php _e('Title:', 'fvcn'); ?></label>
            <input type="text" id="<?= $this->get_field_id('title'); ?>" name="<?= $this->get_field_name('title'); ?>" value="<?= $title; ?>" class="widefat">
        </p>

        <?php
    }
}
