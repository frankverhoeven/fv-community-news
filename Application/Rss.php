<?php


class fvCommunityNewsRss {
	
	protected $_view = null;
	
	protected $_settings = null;
	
	protected $_dbTable = null;
	
	protected $_feedLocation = 'community-news.rss';
	
	protected $_numFeedItems = 10;
	
	public function __construct() {
		$this->_view = fvCommunityNewsRegistry::get('view');
		$this->_settings = fvCommunityNewsSettings::getInstance();
		$this->_dbTable = new fvCommunityNewsModel_DbTable_FvCommunityNews();
		$this->_feedLocation = $this->_settings->getDefault('RssLocation');
	}
	
	public function addFeed() {
		$this->_settings->set(
			'RssHook',
			add_feed($this->_feedLocation, array($this, 'render'))
		);
	}
	
	public function render() {
		$this->_view->submissions = $this->_dbTable->getAll(0, $this->_numFeedItems, array('Approved'=>'1'));
		$this->_view->render('Rss');
		exit;
	}
	
}

