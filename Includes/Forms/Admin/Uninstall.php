<?php

/**
 *		Uninstall.php
 *		FvCommunityNews_Forms_Uninstall
 *
 *		Plugin Uninstallation
 *
 *		@version 1.0
 */

class FvCommunityNews_Forms_Admin_Uninstall extends FvCommunityNews_Form {
	
	/**
	 *		init()
	 *
	 */
	public function init() {
		$session = FvCommunityNews_Session::getInstance();
		
		if (!$session->exists('fvcn_UninstallConfirmCode')) {
			$session->set('fvcn_UninstallConfirmCode', mt_rand(100, 999));
		}
		
		$this->setName('fvcn-uninstall')
			 ->setMethod('post')
			 
			 ->setGroupPrefix('<div id="%name%" class="fvcn-tab"><table class="form-table">')
			 ->setGroupSuffix('</table></div>');
		
		$this->addElement(new FvCommunityNews_Form_Element_Nonce(
			'fvcn-nonce',
			null,
			array(
				new FvCommunityNews_Form_Validator_Nonce(),
			),
			array(),
			'fvcn-nonce'
		));
		
		$this->addElement(new FvCommunityNews_Form_Element_Admin_Submit(
			'fvcn-submit',
			null,
			array(),
			array(),
			__('Uninstall', 'fvcn')
		));
		
		$uninstall = array();
		$uninstall[] = new FvCommunityNews_Form_Element_Admin_Checkbox(
			'fvcn_RemoveSettings',
			__('Remove Settings', 'fvcn'),
			array(),
			array(),
			true,
			__('Remove your current settings.', 'fvcn')
		);
		$uninstall[] = new FvCommunityNews_Form_Element_Admin_Checkbox(
			'fvcn_RemoveData',
			__('Remove Data', 'fvcn'),
			array(),
			array(),
			false,
			__('Remove all the data (Community News Posts).', 'fvcn')
		);
		$uninstall[] = new FvCommunityNews_Form_Element_Admin_Text(
			'fvcn_ConfirmUninstall',
			__('Confirm Uninstall', 'fvcn'),
			array(
				new FvCommunityNews_Form_Validator_NotEmpty(),
				new FvCommunityNews_Form_Validator_Digit(),
			),
			array(),
			'',
			' <strong>' . __('Code:', 'fvcn') . ' ' . $session->fvcn_UninstallConfirmCode . '</strong> ' . __('Type the code to confirm your uninstall', 'fvcn')
		);
		
		
		$this->addGroup('fvcn-uninstall', $uninstall);
	}
	
	/**
	 *		process()
	 *
	 */
	public function process() {
		if ($this->isValid()) {
			if ($this->getElement('fvcn_ConfirmUninstall')->getValue() != FvCommunityNews_Session::getInstance()->fvcn_UninstallConfirmCode) {
				$this->setMessage(__('Incorrect confirm code, please try again.', 'fvcn'));
				return;
			}
			
			$settings = FvCommunityNews_Settings::getInstance();
			global $wpdb;
			
			// Delete posts
			if ($this->getElement('fvcn_RemoveData')->getValue()) {
				$wpdb->query("DROP TABLE " . $settings->DbName);
			}
			
			// Delete settings
			if ($this->getElement('fvcn_RemoveSettings')->getValue()) {
				foreach ($settings->getAll() as $name=>$val) {
					$settings->delete( $name );
				}
			}
			
			FvCommunityNews_Session::getInstance()->uninstalled = true;
			
			// Deactivate
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
			
			deactivate_plugins(FVCN_BASENAME);
			update_option('recently_activated', array(FVCN_BASENAME => time()) + (array)get_option('recently_activated'));
			
			
			$this->setMessage('Plugin successfull uninstalled');
			$this->setProcessed(true);
		} else {
			$this->setMessage(__('Invallid form validation, please fix', 'fvcn'));
		}
	}
	
}
