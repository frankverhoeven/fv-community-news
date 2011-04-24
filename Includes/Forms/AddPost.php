<?php

/**
 *		AddPost.php
 *		FvCommunityNews_Form_AddSubmission
 *
 *		Form for adding news
 *
 *		@version 1.0
 */

class FvCommunityNews_Forms_AddPost extends FvCommunityNews_Form {
	
	/**
	 *		init()
	 *
	 */
	public function init() {
		$this->setName('fvcn-add-post');
		$this->setMethod('post');
		
		$userInfo = wp_get_current_user();
		
		$this->addElement(new FvCommunityNews_Form_Element_Text(
			'fvcn-author',
			__('Auhtor', 'fvcn'),
			array(
				new FvCommunityNews_Form_Validator_NotEmpty(),
				new FvCommunityNews_Form_Validator_Alpha(true),
			),
			array(
				new FvCommunityNews_Form_Filter_Trim(),
				new FvCommunityNews_Form_Filter_Stripslashes(),
				new FvCommunityNews_Form_Filter_Striptags(),
			),
			@$userInfo->display_name
		));
		
		$this->addElement(new FvCommunityNews_Form_Element_Text(
			'fvcn-author-email',
			__('Auhtor Email', 'fvcn'),
			array(
				new FvCommunityNews_Form_Validator_NotEmpty(),
				new FvCommunityNews_Form_Validator_Email(),
			),
			array(
				new FvCommunityNews_Form_Filter_Trim(),
				new FvCommunityNews_Form_Filter_Stripslashes(),
				new FvCommunityNews_Form_Filter_Striptags(),
			),
			@$userInfo->user_email
		));
		
		$this->addElement(new FvCommunityNews_Form_Element_Text(
			'fvcn-title',
			__('Title', 'fvcn'),
			array(
				new FvCommunityNews_Form_Validator_NotEmpty(),
				new FvCommunityNews_Form_Validator_Alnum(true),
			),
			array(
				new FvCommunityNews_Form_Filter_Trim(),
				new FvCommunityNews_Form_Filter_Stripslashes(),
				new FvCommunityNews_Form_Filter_Striptags(),
			)
		));
		
		$this->addElement(new FvCommunityNews_Form_Element_Text(
			'fvcn-url',
			__('URL', 'fvcn'),
			array(),
			array(
				new FvCommunityNews_Form_Filter_Trim(),
				new FvCommunityNews_Form_Filter_Stripslashes(),
				new FvCommunityNews_Form_Filter_Striptags(),
			)
		));
		
		$phone = new FvCommunityNews_Form_Element_Text(
			'fvcn-phone',
			__('Phone Number', 'fvcn'),
			array(
				new FvCommunityNews_Form_Validator_Empty(),
			)
		);
		$phone->setFormat('<div style="display:none;"><label for="%name%">%label%</label><input type="text" name="%name%" id="%id%" class="%class%" value="%value%" /></div>');
		$this->addElement( $phone );
		
		$this->addElement(new FvCommunityNews_Form_Element_Textarea(
			'fvcn-content',
			__('Content', 'fvcn'),
			array(
				new FvCommunityNews_Form_Validator_NotEmpty(),
			),
			array(
				new FvCommunityNews_Form_Filter_Trim(),
				new FvCommunityNews_Form_Filter_Stripslashes(),
				new FvCommunityNews_Form_Filter_Specialchars(),
			)
		));
		
		$this->addElement(new FvCommunityNews_Form_Element_Hidden(
			'fvcn-ajax',
			null,
			array(),
			array(),
			'false'
		));
		
		$this->addElement(new FvCommunityNews_Form_Element_Hidden(
			'fvcn-current-location',
			null,
			array(),
			array(),
			get_option('home') . '/'
		));
		
		$this->addElement(new FvCommunityNews_Form_Element_Nonce(
			'fvcn-nonce-add-post',
			null,
			array(
				new FvCommunityNews_Form_Validator_Nonce()
			)
		));
		
		$this->addElement(new FvCommunityNews_Form_Element_Submit(
			'fvcn-submit',
			null,
			array(),
			array(
				new FvCommunityNews_Form_Filter_Trim(),
				new FvCommunityNews_Form_Filter_Stripslashes(),
				new FvCommunityNews_Form_Filter_Striptags(),
			),
			__('Submit', 'fvcn')
		));
	}
	
	/**
	 *		process()
	 *
	 */
	public function process() {
		$settings = FvCommunityNews_Settings::getInstance();
		
		if ($settings->LoggedInToPost && !is_user_logged_in())
			return;
		
		if ($this->isValid()) {
			$session = FvCommunityNews_Session::getInstance();
			
			if ($session->get('FvcnLastPost') && $session->get('FvcnLastPost')+120 > time()) {
				$this->setMessage(__('You can only add one post every two minutes.', 'fvcn'));
			} else {
				$post = array();
				$elements = $this->getElements();
				
				foreach ($elements as $name=>$element) {
					$name = str_replace(' ', '', ucwords( str_replace(array('fvcn-', '-'), ' ', $name)));
					$post[ $name ] = $element->getValue();
				}
				
				$post['AuthorIp']	= $_SERVER['REMOTE_ADDR'];
				$post['Date']		= current_time('mysql', 1);
				$post['Approved']	= '0';
				$post['Content']	= htmlspecialchars_decode($post['Content']);
				$post = new FvCommunityNews_Models_Post($post);
				
				$mapper = new FvCommunityNews_Models_PostMapper();
				
				// Previous Approved
				if ($settings->PreviousApproved && !$settings->AlwaysAdminModeration) {
					if ((int)$mapper->getCount(array('Email'=>$post->AuthorEmail, 'Name'=>$post->Author, 'Approved'=>'1')) > 0) {
						$post->Approved = '1';
					}
				}
				
				// Spam Check
				if ($settings->AkismetEnabled && '1' != $post->Approved) {
					$akismet = new FvCommunityNews_Akismet(get_option('home'), $settings->AkismetApiKey);
					
					if ($akismet->isKeyValid()) {
						$akismet->setCommunityNews($post);
						
						if ($akismet->isCommentSpam()) {
							$post->Approved = 'spam';
						}
					}
				}
				
				// Approve if no spam, and no options selected
				if (!$settings->AlwaysAdminModeration && !$settings->PreviousApproved && 'spam' != $post->Approved) {
					$post->Approved = '1';
				}
				
				$mapper->add($post);
				$session->set('FvcnLastPost', time());
				
				// Send Mail
				if ('spam' != $post->Approved) {
					if ($settings->MailOnSubmission || ($settings->MailOnModeration && '0' == $post->Approved)) {
						$mail = new FvCommunityNews_Mail();
						$mail->setCommunityNews( $post )
							 ->send();
					}
				}
				
				$this->setProcessed(true);
				$this->setMessage(__('Your post is submitted and will appear soon.', 'fvcn'));
			}
		} else {
			$this->setMessage(__('Validation errors occured, please fix them.'));
		}
		
		if ('true' == $this->getElement('fvcn-ajax')->getValue()) {
			header('Content-Type: text/xml');
			echo $this->renderAjax();
			exit;
		}
	}
	
}
