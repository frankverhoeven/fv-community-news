<?php

/**
 *		ModeratePosts.php
 *		FvCommunityNews_Forms_ModeratePosts
 *
 *		Post Moderation
 *
 *		@version 1.0
 */

class FvCommunityNews_Forms_Admin_ModeratePosts extends FvCommunityNews_Form {
	
	/**
	 *		init()
	 *
	 */
	public function init() {
		$this->setName('fvcn-moderate-posts');
		
		$this->addElement(new FvCommunityNews_Form_Element_Text(
			'fvcn-action',
			__('Action', 'fvcn'),
			array(
				new FvCommunityNews_Form_Validator_NotEmpty(),
				new FvCommunityNews_Form_Validator_Alpha(true),
			)
		));
		
		$this->addElement(new FvCommunityNews_Form_Element_Text(
			'fvcn-post-id',
			__('Post Id', 'fvcn'),
			array(
				new FvCommunityNews_Form_Validator_NotEmpty(),
			)
		));
		
		$this->addElement(new FvCommunityNews_Form_Element_Nonce(
			'fvcn-nonce',
			null,
			array(
				new FvCommunityNews_Form_Validator_Nonce(),
			)
		));
		
		$this->addElement(new FvCommunityNews_Form_Element_Hidden(
			'fvcn-ajax'
		));
	}
	
	/**
	 *		process()
	 *
	 */
	public function process() {
		if (isset($_POST['fvcn-action-2']) && '-1' != $_POST['fvcn-action-2'] && '-1' == $_POST['fvcn-action'])
			$this->getElement('fvcn-action')->setValue($_POST['fvcn-action-2']);
		
		if ($this->isValid()) {
			$mapper = new FvCommunityNews_Models_PostMapper();
			$ids = (array)$this->getElement('fvcn-post-id')->getValue();
			$posts = array();
			
			foreach ($ids as $id) {
				$posts[] = $mapper->get( $id );
			}
			
			switch ($this->getElement('fvcn-action')->getValue()) {
				case 'approve' :
					$approved = '1';
					break;
				case 'unapprove' :
					$approved = '0';
					break;
				case 'spam' :
					$approved = 'spam';
					break;
				case 'unspam' :
					$approved = '1';
					break;
				case 'delete' :
					$approved = 'delete';
					break;
				default :
					return;
			}
			
			foreach ($posts as $post) {
				if ('delete' == $approved) {
					$mapper->delete( $post->getId() );
				} else {
					$mapper->update( $post->setApproved($approved) );
				}
			}
			
			$settings = FvCommunityNews_Settings::getInstance();
			if (in_array($this->getElement('fvcn-action')->getValue(), array('spam', 'unspam')) && $settings->AkismetEnabled) {
				$akismet = new FvCommunityNews_Akismet(get_option('home'), $settings->AkismetApiKey);
				
				if ($akismet->isKeyValid()) {
					foreach ($posts as $post) {
						$akismet->setCommunityNews( $post );
						
						if ('spam' == $this->getElement('fvcn-action')->getValue()) {
							$akismet->submitSpam();
						} else {
							$akismet->submitHam();
						}
					}
				}
			}
			
			$messages = array(
				'1'		=> __('approved', 'fvcn'),
				'0'		=> __('unapproved', 'fvcn'),
				'spam'	=> __('marked as spam', 'fvcn'),
				'delete'=> __('deleted', 'fvcn'),
			);
			$num = count($posts);
			
			$this->setMessage($num . ' post' . ($num>1?'s':'') . ' ' . $messages[ $approved ]);
			$this->setProcessed(true);
		} else {
			$this->setMessage(__('Invallid action!', 'fvcn'));
		}
		
		if ('true' == $this->getElement('fvcn-ajax')->getValue()) {
			header('Content-type: text/xml');
			echo $this->renderAjax();
			exit;
		}
	}
	
}
