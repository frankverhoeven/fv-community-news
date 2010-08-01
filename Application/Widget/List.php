<?php


class fvCommunityNewsWidget_List extends WP_Widget {
	
	protected $_view = null;
	
	protected $_settings = null;
	
	public function fvCommunityNewsWidget_List() {
		$this->_view = fvCommunityNewsRegistry::get('view');
		$this->_settings = fvCommunityNewsSettings::getInstance();
		
		$widget_ops = array('description' => __('The Submissions list.', 'fvcn'));
		$control_ops = array('width' => 400);
		$this->WP_Widget('fvcn_list', __('Community News Submissions', 'fvcn'), $widget_ops, $control_ops);
	}
	
	public function widget($args, $instance) {
		$this->_view->args = $args;
		$this->_view->title = (empty($instance['fvcn_SubmissionsTitle'])?$this->_settings->get('SubmissionsTitle'):$instance['fvcn_SubmissionsTitle']);
		$num = (empty($instance['fvcn_NumSubmissionsWidget'])?$this->_settings->get('NumSubmissions'):$instance['fvcn_NumSubmissionsWidget']);
		$template = (empty($instance['fvcn_SubmissionTemplateWidget'])?$this->_settings->get('SubmissionTemplate'):$instance['fvcn_SubmissionTemplateWidget']);
		
		$this->_view->list = new fvCommunityNewsSubmissionsList($num, $template);
		$this->_view->render('Widget_List');
	}
	
	public function update($new_instance, $old_instance) {
		
		return $new_instance;
	}
	
	public function form($instance) {
		$this->_view->instance = wp_parse_args((array) $instance, array(
			'fvcn_SubmissionsTitle'			=> $this->_settings->get('SubmissionsTitle'),
			'fvcn_NumSubmissionsWidget'		=> $this->_settings->get('NumSubmissions'),
			'fvcn_SubmissionTemplateWidget'	=> $this->_settings->get('SubmissionTemplate')
		));
		
		$this->_view->wpWidget = $this;
		$this->_view->render('Widget_ListSettings');
	}
	
}

