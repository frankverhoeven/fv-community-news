<?php

/**
 *		Moderate.php
 *		FvCommunityNews_Admin_Moderate
 *
 *		Moderate Community News
 *
 *		@version 1.0
 */

class FvCommunityNews_Admin_Moderate extends FvCommunityNews_Admin {
	
	/**
	 *	Number of posts per page
	 *	@var int
	 */
	protected $_postsPerPage = 10;
	
	/**
	 *		init()
	 *
	 */
	public function init() {
		if (!current_user_can('manage_options')) {
			wp_die(__('Cheatin&#8217; uh?', 'fvcn'));
		}
	}
	
	/**
	 *		render()
	 *
	 */
	public function render() {
		if (isset($_GET['apage'])) {
			$page = abs((int)$_GET['apage']);
		} else {
			$page = 1;
		}
		
		$options = array();
		if (isset($_GET['approved']) && 'all' != $_GET['approved']) {
			$options = array('Approved'=>$_GET['approved']);
			$this->_template->approved = $_GET['approved'];
		} else {
			$options = array('Approved'=>'!spam');
			$this->_template->approved = 'all';
		}
		
		$start = ($page - 1) * $this->_postsPerPage;
		$total = $this->_dbMapper->getCount($options);
		
		$this->_template->pagination = sprintf('<span class="displaying-num">' . __('Displaying %s&#8211;%s of %s', 'fvcn') . '</span>%s',
			number_format_i18n($start + 1),
			number_format_i18n(min($page * $this->_postsPerPage, $total)),
			'<span class="total-type-count">' . number_format_i18n($total) . '</span>',
			paginate_links(array(
				'base' => add_query_arg('apage', '%#%'),
				'format' => '',
				'prev_text' => __('&laquo;', 'fvcn'),
				'next_text' => __('&raquo;', 'fvcn'),
				'total' => ceil($total / $this->_postsPerPage),
				'current' => $page
			))
		);
		
		$this->_template->numModeration = $this->_dbMapper->getCount(array('Approved'=>'0'));
		$this->_template->numSpam = $this->_dbMapper->getCount(array('Approved'=>'spam'));
		
		$this->_template->options = array(
			'start'		=> $start,
			'num'		=> $this->_postsPerPage,
			'approved'	=> $options['Approved'],
		);
		
		if (FvCommunityNews_Request::getForm('moderate-posts')->hasMessage()) {
			$this->_template->message = $this->_registry->forms['moderate-posts']->getMessage();
		} else {
			$this->_template->message = false;
		}
		
		$this->_template->render('ModeratePosts');
	}
	
}

