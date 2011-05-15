<?php

/**
 *		Post.php
 *		FvCommunityNews_Models_Post
 *
 *		Post Placeholder
 *
 *		@version 1.0
 */

class FvCommunityNews_Models_Post {
	
	/**
	 *	Post Id
	 *	@var int
	 */
	private $_id			= null;
	
	/**
	 *	Post Author
	 *	@var string
	 */
	private $_author		= null;
	
	/**
	 *	Post author's email
	 *	@var string
	 */
	private $_authorEmail	= null;
	
	/**
	 *	Post author's Ip
	 *	@var string
	 */
	private $_authorIp		= null;
	
	/**
	 *	Post Title
	 *	@var string
	 */
	private $_title			= null;
	
	/**
	 *	Post Content
	 *	@var string
	 */
	private $_content		= null;
	
	/**
	 *	Post Url
	 *	@var string
	 */
	private $_url			= null;
	
	/**
	 *	Post Date
	 *	@var string
	 */
	private $_date			= null;
	
	/**
	 *	Number of visits to the post.
	 *	@var int
	 */
	private $_views			= null;
	
	/**
	 *	Post Status
	 *	@var string
	 */
	private $_approved		= null;
	
	/**
	 *		__construct()
	 *
	 *		@param array $options
	 */
	public function __construct(array $options=null) {
		if (is_array($options) && !empty($options)) {
			$this->setOptions($options);
		}
	}
	
	/**
	 *		setOptions()
	 *
	 *		@param array $options
	 *		@return object $this
	 */
	public function setOptions(array $options) {
		$methods = get_class_methods($this);
		
		foreach ($options as $key=>$val) {
			$method = 'set' . ucfirst($key);
			
			if (in_array($method, $methods)) {
				$this->$method($val);
			}
		}
		
		return $this;
	}
	
	/**
	 *		__set()
	 *
	 *		@param string $name
	 *		@param mixed $value
	 */
	public function __set($name, $value) {
		$method = 'set' . ucfirst($name);
		
		if (!method_exists($this, $method)) {
			throw new Exception('Invallid Community News Post property');
		}
		
		$this->$method($value);
	}
	
	/**
	 *		__get()
	 *
	 *		@param string $name
	 *		@return mixed
	 */
	public function __get($name) {
		$method = 'get' . ucfirst($name);
		
		if (!method_exists($this, $method)) {
			throw new Exception('Invallid Community News Post property');
		}
		
		return $this->$method();
	}
	
	/**
	 *		setId()
	 *
	 *		@param int $id
	 *		@return object $this
	 */
	public function setId($id) {
		$this->_id = (int) $id;
		return $this;
	}
	
	/**
	 *		getId()
	 *
	 *		@return int
	 */
	public function getId() {
		return $this->_id;
	}
	
	/**
	 *		setAuthor()
	 *
	 *		@param string $author
	 *		@return object $this
	 */
	public function setAuthor($author) {
		$this->_author = (string) $author;
		return $this;
	}
	
	/**
	 *		getAuthor()
	 *
	 *		@return string
	 */
	public function getAuthor() {
		return $this->_author;
	}
	
	/**
	 *		setAuthorEmail()
	 *
	 *		@param string $authorEmail
	 *		@return object $this
	 */
	public function setAuthorEmail($authorEmail) {
		$this->_authorEmail = (string) $authorEmail;
		return $this;
	}
	
	/**
	 *		getAuthorEmail()
	 *
	 *		@return string
	 */
	public function getAuthorEmail() {
		return $this->_authorEmail;
	}
	
	/**
	 *		setAuthorIp()
	 *
	 *		@param string $authorIp
	 *		@return object $this
	 */
	public function setAuthorIp($authorIp) {
		$this->_authorIp = (string) $authorIp;
		return $this;
	}
	
	/**
	 *		getAuthorIp()
	 *
	 *		@return string
	 */
	public function getAuthorIp() {
		return $this->_authorIp;
	}
	
	/**
	 *		setTitle()
	 *
	 *		@param string $title
	 *		@return object $this
	 */
	public function setTitle($title) {
		$this->_title = (string) $title;
		return $this;
	}
	
	/**
	 *		getTitle()
	 *
	 *		@return string
	 */
	public function getTitle() {
		return $this->_title;
	}
	
	/**
	 *		setContent()
	 *
	 *		@param string $content
	 *		@return object $this
	 */
	public function setContent($content) {
		$this->_content = (string) $content;
		return $this;
	}
	
	/**
	 *		getContent()
	 *
	 *		@return string
	 */
	public function getContent() {
		return $this->_content;
	}
	
	/**
	 *		setUrl()
	 *
	 *		@param string $url
	 *		@return object $this
	 */
	public function setUrl($url) {
		$this->_url = (string) $url;
		return $this;
	}
	
	/**
	 *		getUrl()
	 *
	 *		@return string
	 */
	public function getUrl() {
		return $this->_url;
	}
	
	/**
	 *		setDate()
	 *
	 *		@param string $date
	 *		@return object $this
	 */
	public function setDate($date) {
		$this->_date = (string) $date;
		return $this;
	}
	
	/**
	 *		getDate()
	 *
	 *		@return string
	 */
	public function getDate() {
		return $this->_date;
	}
	
	/**
	 *		setViews()
	 *
	 *		@param int $views
	 *		@return object $this
	 */
	public function setViews($views) {
		$this->_views = (int) $views;
		return $this;
	}
	
	/**
	 *		getViews()
	 *
	 *		@return int
	 */
	public function getViews() {
		return $this->_views;
	}
	
	/**
	 *		setApproved()
	 *
	 *		@param mixed $approved
	 *		@return object $this
	 */
	public function setApproved($approved) {
		$this->_approved = $approved;
		return $this;
	}
	
	/**
	 *		getApproved()
	 *
	 *		@return mixed
	 */
	public function getApproved() {
		return $this->_approved;
	}
	
}

