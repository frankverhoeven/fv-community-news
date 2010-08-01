<?php

class fvCommunityNewsForm_AddSubmission extends fvCommunityNewsForm {
	
	protected $_view = null;
	
	public function init() {
		$this->_view = fvCommunityNewsRegistry::get('view');
		
		if ($this->_settings->get('CaptchaEnabled')) {
			if (is_user_logged_in() && $this->_settings->get('HideCaptchaLoggedIn')) {
				$this->_view->captcha = false;
			} else {
				$this->_view->captcha = true;
			}
		}
	}
	
	protected function _buildForm() {
		$userinfo = wp_get_current_user();
		
		$this->addElement('fvcn_User')
			 ->setValue(@$userinfo->display_name)
			 ->setRequired();
		
		$this->addElement('fvcn_Email')
			 ->setValue(@$userinfo->user_email)
			 ->setRequired()
			 ->addValidator('Email');
		
		$this->addElement('fvcn_Title')
			 ->setRequired();
		
		$this->addElement('fvcn_Location')
			 ->setValue('http://');
		
		if ($this->_view->captcha) {
			$this->addElement('fvcn_Captcha')
				 ->setRequired()
				 ->addValidator('Captcha');
		}
		
		$this->addElement('fvcn_Description')
			 ->setRequired();
		
		$this->addElement('fvcn_AddSubmissionNonce')
			 ->setRequired()
			 ->addValidator('Nonce');
		
		$this->addElement('fvcn_Phone')
			 ->addValidator('Empty');
		
	}
	
	protected function _handleRequest() {
		if ($this->isValid()) {
			$dbTable = new fvCommunityNewsModel_DbTable_FvCommunityNews();
			$approved = '0';
			
			if ($this->_settings->get('AkismetEnabled')) {
				$akismet = new fvCommunityNewsAkismet(get_option('home'), $this->_settings->get('AkismetApiKey'));
				
				if ($akismet->isKeyValid()) {
					if (($url = parse_url($this->getElement('fvcn_Location')->getValue()))) {
						$url = $url['scheme'] . '://' . $url['host'] . '/';
					} else {
						$url = '';
					}
					
					$akismet->setCommentAuthor($this->getElement('fvcn_User')->getValue())
							->setCommentAuthorEmail($this->getElement('fvcn_Email')->getValue())
							->setCommentAuthorURL($url)
							->setCommentContent($this->getElement('fvcn_Description')->getValue())
							->setPermalink(get_option('home'));
					
					if($akismet->isCommentSpam()) {
						$approved = 'spam';
					}
				}
			}
			
			if (!$this->_settings->get('AlwaysAdmin') && 'spam' != $approved) {
				if ($this->_settings->get('PreviousApproved') &&
					$dbTable->getCount(array(
						'Name'		=> $this->getElement('fvcn_User')->getValue(),
						'Email'		=> $this->getElement('fvcn_Email')->getValue(),
						'Approved'	=> '1'
					)) > 0) {
					$approved = '1';
				}
			}
			
			
			$dbTable->add(
				new fvCommunityNewsModel_Submission(array(
					'Name'			=> apply_filters('fvcn_PreName', $this->getElement('fvcn_User')->getValue()),
					'Email'			=> apply_filters('fvcn_PreEmail', $this->getElement('fvcn_Email')->getValue()),
					'Title'			=> apply_filters('fvcn_PreTitle', $this->getElement('fvcn_Title')->getValue()),
					'Location'		=> apply_filters('fvcn_PreLocation', ('http://'==$this->getElement('fvcn_Location')->getValue()?'':$this->getElement('fvcn_Location')->getValue())),
					'Description'	=> apply_filters('fvcn_PreDescription', $this->getElement('fvcn_Description')->getValue()),
					'Date'			=> current_time('mysql', 1),
					'Ip'			=> $_SERVER['REMOTE_ADDR'],
					'Approved'		=> $approved
				))
			);
			
			
		} else {
			//$this->_view->validationError = __('Validation errors occured.', 'fvcn');
		}
	}
	
	
	public function render() {
		$this->_buildForm();
		$this->_view->form = $this;
		
		if ($this->isPost() && isset($_POST['fvcn-action'])) {
			$this->_handleRequest();
		}
		
		$this->_view->render('Form_AddSubmission');
	}
	
}

