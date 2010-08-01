<?php


class fvCommunityNewsAdmin_Request {
	
	protected $_dbTable = null;
	
	public function __construct($request) {
		$this->_dbTable = new fvCommunityNewsModel_DbTable_FvCommunityNews();
		
		$methods = get_class_methods($this);
		$method = '_process' . $request;
		
		if (in_array($method, $methods)) {
			$this->$method();
		}
	}
	
	
	protected function _processManageSubmissions() {
		if (!check_admin_referer('fvcn_ManageSubmissions')) {
			wp_die(__('Cheatin&#8217; uh?', 'fvcn'));
		}
		
		if (is_string($_REQUEST['fvcn-submission-id'])) {
			$submissions = array($_REQUEST['fvcn-submission-id']);
		} else {
			$submissions = $_REQUEST['fvcn-submission-id'];
		}
		
		if (!is_array($submissions)) {
			return;
		}
		if ('-1' == $_REQUEST['fvcn-action'] && '-1' != $_REQUEST['fvcn-action-2']) {
			$_REQUEST['fvcn-action'] = $_REQUEST['fvcn-action-2'];
		}
		
		switch ($_REQUEST['fvcn-action']) {
			case 'approve' :
				$options = array('Approved'=>'1');
				$message = __('approved', 'fvcn');
				break;
			case 'unapprove' :
				$options = array('Approved'=>'0');
				$message = __('unapproved', 'fvcn');
				break;
			case 'spam' :
				$options = array('Approved'=>'spam');
				$message = __('marked as spam', 'fvcn');
				break;
			case 'delete' :
				$message = __('deleted', 'fvcn');
				break;
			default :
				return;
		}
		
		if ('delete' != $_REQUEST['fvcn-action']) {
			foreach ($submissions as $id) {
				$this->_dbTable->update($options, array('Id'=>$id), array('%d'));
			}
		} else {
			foreach ($submissions as $id) {
				$this->_dbTable->delete($id);
			}
		}
		
		if (count($submissions) > 1) {
			$message = sprintf(__('%d submissions %s.', 'fvcn'), count($submissions), $message);
		} else {
			$message = sprintf(__('One submission %s.', 'fvcn'), $message);
		}
		
		fvCommunityNewsRegistry::get('view')->message = $message;
	}
	
	protected function _processEditSubmission() {
		if ('POST' != $_SERVER['REQUEST_METHOD'] || !check_admin_referer('fvcn_EditSubmission')) {
			wp_die(__('Cheatin&#8217; uh?', 'fvcn'));
		}
		
		$submission = array(
			'Name'			=> apply_filters('fvcn_PreName', $_POST['fvcn-submission-user']),
			'Email'			=> apply_filters('fvcn_PreEmail', $_POST['fvcn-submission-email']),
			'Title'			=> apply_filters('fvcn_PreTitle', $_POST['fvcn-submission-title']),
			'Location'		=> apply_filters('fvcn_PreLocation', $_POST['fvcn-submission-location']),
			'Description'	=> apply_filters('fvcn_PreDescription', $_POST['content']),
			'Approved'		=> $_POST['fvcn-submission-approved']
		);
		
		$this->_dbTable->update($submission, array('Id'=>(int)$_POST['fvcn-submission-id']), array('%d'));
		
		fvCommunityNewsRegistry::get('view')->message = __('Submission updated.', 'fvcn');
	}
	
	protected function _processSettings() {
		if ('POST' != $_SERVER['REQUEST_METHOD'] || !check_admin_referer('fvcn_AdminSettings')) {
			wp_die(__('Cheatin&#8217; uh?', 'fvcn'));
		}
		
		$settings = fvCommunityNewsSettings::getInstance();
		
		foreach ($settings->getAll() as $setting) {
			
			if (isset($_POST[ $settings->getPrefix() . $setting->getName() ]) || 'bool' == $setting['type']) {
				
				switch ($setting['type']) {
					case 'bool' :
						$settings->set($setting->getName(), (isset($_POST[ $settings->getPrefix() . $setting->getName() ])?true:false));
						break;
					case 'int' :
						$settings->set($setting->getName(), abs( (int)$_POST[ $settings->getPrefix() . $setting->getName() ] ));
						break;
					case 'string' :
					default :
						$settings->set($setting->getName(), stripslashes( (string)$_POST[ $settings->getPrefix() . $setting->getName() ] ));
				}
				
			}
		}
	}
	
	protected function _processUninstall() {
		if ('POST' != $_SERVER['REQUEST_METHOD'] || !check_admin_referer('fvcn_AdminUninstall')) {
			wp_die(__('Cheatin&#8217; uh?', 'fvcn'));
		}
		
		$view = fvCommunityNewsRegistry::get('view');
		$settings = fvCommunityNewsSettings::getInstance();
		
		if ($_POST['fvcn_ConfirmUninstall'] != $_POST['fvcn_ConfirmCode']) {
			$view->errorMessage = __('Incorrect confirm code.', 'fvcn');
			return;
		}
		
		if (isset($_POST['fvcn_RemoveData'])) {
			fvCommunityNewsRegistry::get('wpdb')->query("DROP TABLE " . $settings->get('DbName'));
		}
		
		if (isset($_POST['fvcn_RemoveSettings'])) {
			foreach ($settings->getAll() as $setting) {
				$settings->delete($setting->getName());
			}
		}
		
		// Might need some improvement :)
		$plugin = FVCN_PLUGINBASENAME;
		deactivate_plugins($plugin);
		update_option('recently_activated', array($plugin => time()) + (array)get_option('recently_activated'));
		die('<meta http-equiv="refresh" content="0;URL=plugins.php?deactivate=true" />');
	}
	
}

