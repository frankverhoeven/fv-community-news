<?php


class fvCommunityNewsWidget_Form extends WP_Widget {
	
	protected $_view = null;
	
	protected $_settings = null;
	
	public function fvCommunityNewsWidget_Form() {
		$this->_view = fvCommunityNewsRegistry::get('view');
		$this->_settings = fvCommunityNewsSettings::getInstance();
		
		$widget_ops = array('description' => __('The Add Submission form.', 'fvcn'));
		$this->WP_Widget('fvcn_form', __('Community News Form', 'fvcn'), $widget_ops);
	}
	
	public function widget($args, $instance) {
		$this->_view->args = $args;
		$this->_view->title = (empty($instance['fvcn_FormTitle'])?$this->_settings->get('FormTitle'):$instance['fvcn_FormTitle']);
		$this->_view->description = (empty($instance['fvcn_FormDescription'])?'':$instance['fvcn_FormDescription']);
		$this->_view->form = new fvCommunityNewsForm_AddSubmission();
		$this->_view->render('Widget_Form');
	}
	
	public function update($new_instance, $old_instance) {
		
		return $new_instance;
	}
	
	public function form($instance) {
		$this->_view->instance = wp_parse_args((array) $instance, array(
			'fvcn_FormTitle'		=> $this->_settings->get('FormTitle'),
			'fvcn_FormDescription'	=> $this->_settings->get('FormDescription'),
		));
		
		$this->_view->wpWidget = $this;
		$this->_view->render('Widget_FormSettings');
	}
	
}

