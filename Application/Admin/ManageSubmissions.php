<?php


class fvCommunityNewsAdmin_ManageSubmissions extends fvCommunityNewsAdmin_Abstract {
	
	protected $_dbTable = null;
	
	protected $_submissionsPerPage = 10;
	
	public function init() {
		if (!current_user_can('manage_options')) {
			wp_die(__('Cheatin&#8217; uh?', 'fvcn'));
		}
		
		$this->_dbTable = new fvCommunityNewsModel_DbTable_FvCommunityNews();
	}
	
	public function render() {
		if (isset($_GET['apage'])) {
			$page = abs((int)$_GET['apage']);
		} else {
			$page = 1;
		}
		
		$options = array();
		if (isset($_GET['approved']) && 'all' != $_GET['approved']) {
			$options = array('Approved'=>$_GET['approved']);
			$this->_view->approvedStatus = $_GET['approved'];
		} else {
			$options = array('Approved'=>'!spam');
			$this->_view->approvedStatus = 'all';
		}
		
		$start = ($page - 1) * $this->_submissionsPerPage;
		$total = $this->_dbTable->getCount($options);
		
		$this->_view->pageLinks = sprintf('<span class="displaying-num">' . __('Displaying %s&#8211;%s of %s', 'fvcn') . '</span>%s',
			number_format_i18n($start + 1),
			number_format_i18n(min($page * $this->_submissionsPerPage, $total)),
			'<span class="total-type-count">' . number_format_i18n($total) . '</span>',
			paginate_links(array(
				'base' => add_query_arg('apage', '%#%'),
				'format' => '',
				'prev_text' => __('&laquo;', 'fvcn'),
				'next_text' => __('&raquo;', 'fvcn'),
				'total' => ceil($total / $this->_submissionsPerPage),
				'current' => $page
			))
		);
		
		$this->_view->tracking = fvCommunityNewsSettings::getInstance()->get('Tracking');
		$this->_view->numModeration = $this->_dbTable->getCount(array('Approved'=>'0'));
		$this->_view->numSpam = $this->_dbTable->getCount(array('Approved'=>'spam'));
		$this->_view->submissions = $this->_dbTable->getAll($start, $this->_submissionsPerPage, $options);
		
		$this->_view->render('Admin_ManageSubmissions');
	}
	
}

