<?php

/**
 *		Form.php
 *		FvCommunityNews_Widget_Form
 *
 *		Form Widget
 *
 *		@version 1.0
 */

class FvCommunityNews_Widgets_Form extends WP_Widget {
	
	/**
	 *		FvCommunityNews_Widget_Form()
	 *
	 */
	public function FvCommunityNews_Widgets_Form() {
		$widget_ops = array('description' => __('The form used to add community news.', 'fvcn'));
		$this->WP_Widget('fvcn_form', __('Community News Form', 'fvcn'), $widget_ops);
	}
	
	/**
	 *		widget()
	 *
	 *		@param array $args
	 *		@param array $instance
	 */
	public function widget($args, $instance) {
		$title = apply_filters('widget_title', empty($instance['title'])?'':$instance['title'], $instance, $this->id_base);
		
		$template = FvCommunityNews_Template::getInstance();
		$template->args = $args;
		$template->title = $title;
		
		$template->render('FormWidget');
	}
	
	/**
	 *		update()
	 *
	 *		@param array $new_instance
	 *		@param array $old_instance
	 *		@return array
	 */
	public function update($new_instance, $old_instance) {
		$new_instance['title']	= strip_tags($new_instance['title']);
		
		return $new_instance;
	}
	
	/**
	 *		form()
	 *
	 *		@param array $instance
	 */
	public function form($instance) {
		$instance = wp_parse_args((array)$instance, array(
			'title'	=> __('Add Community News', 'fvcn'),
		));
		
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'fvcn'); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>" class="widefat" value="<?php echo esc_attr($instance['title']); ?>" />
		</p>
		<?php
	}
	
}

