<?php


class fvCommunityNewsWidget_List extends WP_Widget {
	
	protected $_view = null;
	
	protected $_settings = null;
	
	public function fvCommunityNewsWidget_List() {
		$this->_view = fvCommunityNewsRegistry::get('view');
		$this->_settings = fvCommunityNewsSettings::getInstance();
		
		$widget_ops = array('description' => __('The Submissions list.', 'fvcn'));
		$this->WP_Widget('fvcn_list', __('Community News Submissions', 'fvcn'), $widget_ops);
	}
	
	public function widget($args, $instance) {
		$this->_view->args = $args;
		$this->_view->title = (empty($instance['fvcn_SubmissionsTitle'])?$this->_settings->get('SubmissionsTitle'):$instance['fvcn_SubmissionsTitle']);
		$this->_view->list = new fvCommunityNewsSubmissionsList();
		$this->_view->render('Widget_List');
	}
	
	public function update($new_instance, $old_instance) {
		
		return $new_instance;
	}
	
	public function form($instance) {
		$this->_view->instance = wp_parse_args((array) $instance, array(
			'fvcn_SubmissionsTitle'		=> $this->_settings->get('SubmissionsTitle')
		));
		
		$this->_view->wpWidget = $this;
		$this->_view->render('Widget_ListSettings');
	}
	
}

