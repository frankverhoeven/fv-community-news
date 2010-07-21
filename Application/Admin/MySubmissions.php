<?php

class fvCommunityNewsAdmin_MySubmissions extends fvCommunityNewsAdmin_Abstract {
	
	protected $_dbTable = null;
	
	protected $_submissionsPerPage = 10;
	
	public function init() {
		if (!current_user_can('read')) {
			wp_die(__('Cheatin&#8217; uh?', 'fvcn'));
		}
		
		$this->_dbTable = new fvCommunityNewsModel_DbTable_FvCommunityNews();
	}
	
	public function render() {
		$current_user = wp_get_current_user();
		if (!($current_user instanceof WP_User)) {
			return;
		}
		
		if (isset($_GET['apage'])) {
			$page = abs((int)$_GET['apage']);
		} else {
			$page = 1;
		}
		
		$options = array(
			'Approved'	=> '1',
			'Email'		=> $current_user->user_email
		);
		
		$start = ($page - 1) * $this->_submissionsPerPage;
		$total = $this->_dbTable->getCount($options);
		
		$this->_view->pageLinks = sprintf('<span class="displaying-num">' . __('Displaying %s&#8211;%s of %s', 'fvcn') . '</span>%s',
			number_format_i18n($start + 1),
			number_format_i18n(min($page * $this->_submissionsPerPage, $total)),
			'<span class="total-type-count">' . number_format_i18n($total) . '</span>',
			paginate_links(array(
				'base' => add_query_arg('apage', '%#%'),
				'format' => '',
				'prev_text' => __('&laquo;'),
				'next_text' => __('&raquo;'),
				'total' => ceil($total / $this->_submissionsPerPage),
				'current' => $page
			))
		);
		
		$this->_view->submissions = $this->_dbTable->getAll($start, $this->_submissionsPerPage, $options);
		$this->_view->form = new fvCommunityNewsForm_AddSubmission();
		
		$this->_view->render('Admin_MySubmissions');
	}
	
}

