<?php

/**
 *		MyCommunityNews.php
 *		FvCommunityNews_Admin_MyCommunityNews
 *
 *		MyCommunityNews Community News
 *
 *		@version 1.0
 */

class FvCommunityNews_Admin_MyCommunityNews extends FvCommunityNews_Admin {
	
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
		
	}
	
	/**
	 *		render()
	 *
	 */
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
		
		$options = array('Approved'=>'1', 'Email'=>$current_user->user_email);
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
		
		$this->_template->options = array(
			'start'		=> $start,
			'num'		=> $this->_postsPerPage,
			'approved'	=> $options['Approved'],
			'where'		=> array(
				'Email'		=> $current_user->user_email
			),
		);
		
		$this->_template->render('MyCommunityNews');
	}
	
}

