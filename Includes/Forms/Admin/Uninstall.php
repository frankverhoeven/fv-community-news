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
		$this->setName('fvcn-uninstall')
			 ->setMethod('post');
		
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
		
		$this->addGroup(new FvCommunityNews_Form_Group(array(
			'Prefix'	=> '<div id="%name%" class="fvcn-tab"><table class="form-table">',
			'Suffix'	=> '</table></div>',
			'Name'		=> 'fvcn-appearance',
			'Elements'	=> array(
				new FvCommunityNews_Form_Element_Admin_Checkbox(
					'fvcn_RemoveSettings',
					__('Remove Settings', 'fvcn'),
					array(),
					array(),
					true,
					__('Remove your current settings.', 'fvcn')
				),
				new FvCommunityNews_Form_Element_Admin_Checkbox(
					'fvcn_RemoveData',
					__('Remove Data', 'fvcn'),
					array(),
					array(),
					false,
					__('Remove all the data (Community News Posts).', 'fvcn')
				),
				new FvCommunityNews_Form_Element_Admin_Checkbox(
					'fvcn_ConfirmUninstall',
					__('Confirm Uninstall', 'fvcn'),
					array(),
					array(),
					false,
					__('Check this box to confirm that you want to uninstall the plugin.', 'fvcn')
				),
			)
		)));
		
	}
	
	/**
	 *		process()
	 *
	 */
	public function process() {
		if ($this->isValid()) {
			if (!$this->getElement('fvcn_ConfirmUninstall')->getValue()) {
				$this->setMessage(__('You must confirm the uninstall.', 'fvcn'));
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
