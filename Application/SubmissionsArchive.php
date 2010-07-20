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
		if (isset($_GET['fvcn_apage'])) {
			$page = abs((int)$_GET['fvcn_apage']);
		} else {
			$page = 1;
		}
		
		$options = array(
			'Approved'	=> '1'
		);
		
		$start = ($page - 1) * $this->_submissionsPerPage;
		$total = $this->_dbTable->getCount($options);
		
		$this->_view->pageLinks = sprintf('<span class="displaying-num">' . __('Displaying %s&#8211;%s of %s', 'fvcn') . '</span>%s',
			number_format_i18n($start + 1),
			number_format_i18n(min($page * $this->_submissionsPerPage, $total)),
			'<span class="total-type-count">' . number_format_i18n($total) . '</span>',
			paginate_links(array(
				'base' => add_query_arg('fvcn_apage', '%#%'),
				'format' => '',
				'prev_text' => __('&laquo;'),
				'next_text' => __('&raquo;'),
				'total' => ceil($total / $this->_submissionsPerPage),
				'current' => $page
			))
		);
		
		
		$this->_view->format = $this->_settings->get('SubmissionTemplate');
		$this->_view->submissions = $this->_dbTable->getAll($start, $this->_submissionsPerPage, $options);
		
		$this->_view->render('SubmissionsArchive');
	}
	
	
}

