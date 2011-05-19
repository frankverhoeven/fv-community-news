<?php

/**
 *		EditPost.php
 *		FvCommunityNews_Form_AddSubmission
 *
 *		Form for adding news
 *
 *		@version 1.0
 */

class FvCommunityNews_Forms_Admin_EditPost extends FvCommunityNews_Form {
	
	/**
	 *	Post
	 *	@var object
	 */
	protected $_post = null;
	
	/**
	 *	Post Mapper
	 *	@var object
	 */
	protected $_postMapper = null;
	
	/**
	 *	Valid post selected
	 *	@var bool
	 */
	protected $_hasPost = false;
	
	/**
	 *		init()
	 *
	 */
	public function init() {
		if (!isset($_REQUEST['fvcn-post-id'], $_REQUEST['fvcn-action'])) {
			return;
		}
		
		$this->_postMapper = new FvCommunityNews_Models_PostMapper();
		
		if (!($this->_post = $this->_postMapper->get($_REQUEST['fvcn-post-id']))) {
			$this->hasPost(false);
			return;
		}
		
		$this->hasPost(true);
		
		$registry = FvCommunityNews_Registry::getInstance();
		$registry->Posts = array( 0 => $this->_post );
		$registry->PostNum = -1;
		
		fvcn_posts();
		fvcn_the_post();
		
		
		$this->setName('fvcn-edit-post')
			 ->setMethod('post');
		
		
		$this->addElement(new FvCommunityNews_Form_Element_Text(
			'fvcn-author',
			__('Author', 'fvcn'),
			array(
				new FvCommunityNews_Form_Validator_NotEmpty(),
				new FvCommunityNews_Form_Validator_Alpha(true),
			),
			array(
				new FvCommunityNews_Form_Filter_Trim(),
				new FvCommunityNews_Form_Filter_Stripslashes(),
				new FvCommunityNews_Form_Filter_Striptags(),
			),
			$this->_post->Author
		));
		
		$this->addElement(new FvCommunityNews_Form_Element_Text(
			'fvcn-author-email',
			__('Author Email', 'fvcn'),
			array(
				new FvCommunityNews_Form_Validator_NotEmpty(),
				new FvCommunityNews_Form_Validator_Email(),
			),
			array(
				new FvCommunityNews_Form_Filter_Trim(),
				new FvCommunityNews_Form_Filter_Stripslashes(),
				new FvCommunityNews_Form_Filter_Striptags(),
			),
			$this->_post->AuthorEmail
		));
		
		$this->addElement(new FvCommunityNews_Form_Element_Text(
			'fvcn-title',
			__('Title', 'fvcn'),
			array(
				new FvCommunityNews_Form_Validator_NotEmpty(),
			),
			array(
				new FvCommunityNews_Form_Filter_Trim(),
				new FvCommunityNews_Form_Filter_Stripslashes(),
				new FvCommunityNews_Form_Filter_Striptags(),
			),
			$this->_post->Title
		));
		
		$this->addElement(new FvCommunityNews_Form_Element_Text(
			'fvcn-url',
			__('URL', 'fvcn'),
			array(),
			array(
				new FvCommunityNews_Form_Filter_Trim(),
				new FvCommunityNews_Form_Filter_Stripslashes(),
				new FvCommunityNews_Form_Filter_Striptags(),
			),
			$this->_post->Url
		));
		
		$this->addElement(new FvCommunityNews_Form_Element_Text(
			'fvcn-approved',
			null,
			array(
				new FvCommunityNews_Form_Validator_NotEmpty(),
				new FvCommunityNews_Form_Validator_Alnum(),
			),
			array(
				new FvCommunityNews_Form_Filter_Trim(),
				new FvCommunityNews_Form_Filter_Stripslashes(),
				new FvCommunityNews_Form_Filter_Striptags(),
			),
			$this->_post->Approved
		));
		
		$this->addElement(new FvCommunityNews_Form_Element_Textarea(
			'content',
			__('Content', 'fvcn'),
			array(
				new FvCommunityNews_Form_Validator_NotEmpty(),
			),
			array(
				new FvCommunityNews_Form_Filter_Trim(),
				new FvCommunityNews_Form_Filter_Stripslashes(),
				new FvCommunityNews_Form_Filter_SpecialChars(),
			),
			$this->_post->Content
		));
		
		$this->addElement(new FvCommunityNews_Form_Element_Nonce(
			'fvcn-nonce-edit-post',
			null,
			array(
				new FvCommunityNews_Form_Validator_Nonce()
			)
		));
		
		$this->addElement(new FvCommunityNews_Form_Element_Submit(
			'fvcn-submit'
		));
	}
	
	/**
	 *		process()
	 *
	 */
	public function process() {
		if ($this->isValid()) {
			$post = array();
			$elements = $this->getElements();
			
			foreach ($elements as $name=>$element) {
				$name = str_replace(' ', '', ucwords( str_replace(array('fvcn-', '-'), ' ', $name)));
				$post[ $name ] = $element->getValue();
			}
			
			$post['Content'] = htmlspecialchars_decode($post['Content']);
			
			$this->_post->setOptions($post);
			$this->_postMapper->update( $this->_post );
			
			$this->setProcessed(true);
			$this->setMessage(__('Post updated.', 'fvcn'));
		} else {
			$this->setMessage(__('Validation errors occured, please fix them.'));
		}
	}
	
	/**
	 *		hasPost()
	 *
	 *		@param bool $hasPost
	 *		@return bool
	 */
	public function hasPost($hasPost=null) {
		if (null === $hasPost) {
			return $this->_hasPost;
		}
		
		return $this->_hasPost = (bool)$hasPost;
	}
	
	
	
}
