<?php


class fvCommunityNewsSubmissionsArchive {
	
	protected $_view = null;
	
	protected $_dbTable = null;
	
	protected $_settings = null;
	
	protected $_submissionsPerPage = 10;
	
	public function __construct() {
		$this->_view = fvCommunityNewsRegistry::get('view');
		$this->_dbTable = new fvCommunityNewsModel_DbTable_FvCommunityNews();
		$this->_settings = fvCommunityNewsSettings::getInstance();
	}
	
	public function render() {
		global $wp_rewrite, $wp_query;	
		
		$options = array(
			'Approved'	=> '1'
		);
				
		$wp_query->query_vars['paged'] > 1 ? $current = $wp_query->query_vars['paged'] : $current = 1;
		$start = ($current - 1) * $this->_submissionsPerPage;
		$total = $this->_dbTable->getCount($options);
		
		$pagination = array(
			'base' => @add_query_arg('page','%#%'),
			'format' => '',
			'total' => ceil($total / $this->_submissionsPerPage),
			'current' => $current,
			'next_text' => __('Next &raquo;', 'fvcn'),
			'prev_text' => __('&laquo; Previous', 'fvcn'),
		);
		if($wp_rewrite->using_permalinks()) {
			$pagination['base'] = user_trailingslashit(trailingslashit(remove_query_arg('s',get_pagenum_link(1))) . 'page/%#%/', 'paged');
		}
		if(!empty($wp_query->query_vars['s'])) {
			$pagination['add_args'] = array('s'=>get_query_var('s'));
		}
		
		$this->_view->pageLinks = paginate_links($pagination);
		$this->_view->format = $this->_settings->get('SubmissionTemplate');
		$this->_view->submissions = $this->_dbTable->getAll($start, $this->_submissionsPerPage, $options);
		
		$this->_view->render('SubmissionsArchive');
	}
	
	
}

