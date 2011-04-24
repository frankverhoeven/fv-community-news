<?php

/**
 *		Widgets.php
 *		FvCommunityNews_Widget_Form
 *		FvCommunityNews_Widget_ListPosts
 *
 *		Widgets
 *
 *		@version 1.0
 */

if (!class_exists('FvCommunityNews_Widget_Form')) {
	
	/**
	 *		FvCommunityNews_Widget_Form
	 *
	 *		Widget for the add submission form
	 *
	 *		@version 1.0
	 */
	class FvCommunityNews_Widget_Form extends WP_Widget {
		
		/**
		 *		FvCommunityNews_Widget_Form()
		 *
		 */
		public function FvCommunityNews_Widget_Form() {
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
	
}


if (!class_exists('FvCommunityNews_Widget_ListPosts')) {
	
	/**
	 *		FvCommunityNews_Widget_ListPosts
	 *
	 *		Widget for listing posts
	 *
	 *		@version 1.0
	 */
	class FvCommunityNews_Widget_ListPosts extends WP_Widget {
		
		/**
		 *		FvCommunityNews_Widget_ListPosts()
		 *
		 */
		public function FvCommunityNews_Widget_ListPosts() {
			$widget_ops = array('description' => __('A list of Community News posts.', 'fvcn'));
			$this->WP_Widget('fvcn_listposts', __('Community News', 'fvcn'), $widget_ops);
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
			$template->num = $instance['num'];
			
			$template->render('ListPostsWidget');
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
			$new_instance['num']	= (abs((int)$new_instance['num']) <= 0 ? 5 : abs((int)$new_instance['num']));
			
			return $new_instance;
		}
		
		/**
		 *		form()
		 *
		 *		@param array $instance
		 */
		public function form($instance) {
			$instance = wp_parse_args((array)$instance, array(
				'title'	=> __('Community News', 'fvcn'),
				'num'	=> 5,
			));
			
			?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'fvcn'); ?></label>
				<input type="text" name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>" class="widefat" value="<?php echo esc_attr($instance['title']); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('num'); ?>"><?php _e('Number of posts:', 'fvcn'); ?></label>
				<input type="text" name="<?php echo $this->get_field_name('num'); ?>" id="<?php echo $this->get_field_id('num'); ?>" class="widefat" value="<?php echo esc_attr($instance['num']); ?>" />
			</p>
			<?php
		}
		
	}
	
}


/**
 *		fvcn_register_widgets()
 *
 *		@uses register_widget()
 */
function fvcn_register_widgets() {
	register_widget('FvCommunityNews_Widget_Form');
	register_widget('FvCommunityNews_Widget_ListPosts');
}
add_action('widgets_init', 'fvcn_register_widgets');
