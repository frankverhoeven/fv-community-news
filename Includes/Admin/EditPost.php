<?php

/**
 *		EditPost.php
 *		FvCommunityNews_Admin_EditPost
 *
 *		Edit Community News
 *
 *		@version 1.0
 */

class FvCommunityNews_Admin_EditPost extends FvCommunityNews_Admin {
	
	/**
	 *		init()
	 *
	 */
	public function init() {
		if (!current_user_can('moderate_comments')) {
			wp_die(__('Cheatin&#8217; uh?', 'fvcn'));
		}
	}
	
	/**
	 *		render()
	 *
	 */
	public function render() {
		
		$this->_template->render('EditPost');
		
	}
	
}

