<?php

/**
 *		Mail.php
 *		FvCommunityNews_Mail
 *
 *		Mailer
 *
 *		@version 1.0
 */

class FvCommunityNews_Mail {
	
	/**
	 *	Reciever
	 *	@var string
	 */
	protected $_to = '';
	
	/**
	 *	Sender
	 *	@var string
	 */
	protected $_from = '';
	
	/**
	 *	Subject
	 *	@var string
	 */
	protected $_subject = '';
	
	/**
	 *	Message
	 *	@var string
	 */
	protected $_message = '';
	
	/**
	 *	Headers
	 *	@var string
	 */
	protected $_headers = '';
	
	/**
	 *		__construct()
	 *
	 */
	public function __construct() {
		$this->setFrom( get_option('admin_email') )
			 ->setTo( get_option('admin_email') );
	}
	
	/**
	 *		setTo()
	 *
	 *		@param string $email
	 *		@return object $this
	 */
	public function setTo($email) {
		$this->_to = $email;
		return $this;
	}
	
	/**
	 *		getTo()
	 *
	 *		@return string
	 */
	public function getTo() {
		return $this->_to;
	}
	
	/**
	 *		setFrom()
	 *
	 *		@param string $email
	 *		@return object $this
	 */
	public function setFrom($email) {
		$this->_from = $email;
		return $this;
	}
	
	/**
	 *		getFrom()
	 *
	 *		@return string
	 */
	public function getFrom() {
		return $this->_from;
	}
	
	/**
	 *		setSubject()
	 *
	 *		@param string $subject
	 *		@return object $this
	 */
	public function setSubject($subject) {
		$this->_subject = $subject;
		return $this;
	}
	
	/**
	 *		getSubject()
	 *
	 *		@return string
	 */
	public function getSubject() {
		return $this->_subject;
	}
	
	/**
	 *		setMessage()
	 *
	 *		@param string $message
	 *		@return object $this
	 */
	public function setMessage($message) {
		$this->_message = $message;
		return $this;
	}
	
	/**
	 *		getMessage()
	 *
	 *		@return string
	 */
	public function getMessage() {
		return $this->_message;
	}
	
	/**
	 *		setHeaders()
	 *
	 *		@param string $headers
	 *		@return object $this
	 */
	public function setHeaders($headers) {
		$this->_headers = $headers;
		return $this;
	}
	
	/**
	 *		getHeaders()
	 *
	 *		@return string
	 */
	public function getHeaders() {
		return $this->_headers;
	}
	
	/**
	 *		setCommunityNews()
	 *
	 *		@param object $post
	 *		@param bool $mod
	 *		@return object $this
	 */
	public function setCommunityNews(FvCommunityNews_Models_Post $post, $mod=false) {
		$this->setSubject('[' . get_option('blogname') . '] New Community News Post: "' . $post->getTitle() . '"');
		
		$headers =	'From: ' . $post->getAuthor() . ' <' . $post->getAuthorEmail() . '>' . PHP_EOL .
					'Reply-To: ' . $post->getAuthor() . ' <' . $post->getAuthorEmail() . '>' . PHP_EOL .
					'Return-Path: Mail-Error <' . $this->getTo() . '>' . PHP_EOL .
					'X-Mailer: PHP/' . phpversion() . PHP_EOL .
					'X-Priority: Normal' . PHP_EOL;
		$this->setHeaders( $headers );
		
		$message =	'A new Community News post has been added.' . "\n\n" .
					'Author: ' . $post->getAuthor() . ' (Ip: ' . $post->getAuthorIp() . ")\n" .
					'E-mail: ' . $post->getAuthorEmail() . "\n" .
					'URL: ' . $post->getUrl() . "\n" .
					'Whois: http://whois.arin.net/rest/ip/' . $post->getAuthorIp() . "\n" .
					'Description:' . "\n" . $post->getContent() . "\n\n" .
					'Moderation page: ' . get_option('home') . '/wp-admin/admin.php?page=fvcn-admin&approved=0';
		$this->setMessage( $message );
		
		return $this;
	}
	
	/**
	 *		send()
	 *
	 */
	public function send() {
		return wp_mail(
			$this->getTo(),
			$this->getSubject(),
			$this->getMessage(),
			$this->getHeaders()
		);
	}
	
	
}

