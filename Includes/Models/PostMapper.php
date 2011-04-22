<?php

/**
 *		PostMapper.php
 *		FvCommunityNews_Models_PostMapper
 *
 *		Database mapper between MySQL and FvCommunityNews
 *
 *		@uses wpdb
 *		@version 1.0
 */

class FvCommunityNews_Models_PostMapper {
	
	/**
	 *	wpdb object
	 *	@var object
	 */
	private $_db = null;
	
	/**
	 *	Table Name
	 *	@var string
	 */
	private $_tableName = 'fv_community_news';
	
	/**
	 *		__construct()
	 *
	 *		@param object $wpdb
	 */
	public function __construct(wpdb $wpdb = null) {
		$this->setDb($wpdb);
		$this->_tableName = $this->_db->prefix . $this->_tableName;
	}
	
	/**
	 *		setDb()
	 *
	 *		@global $wpdb
	 *		@param object $wpdb
	 */
	public function setDb(wpdb $db = null) {
		if (null === $this->_db) {
			global $wpdb;
			
			if (null !== $db && $db instanceof wpdb) {
				$this->_db = $db;
			} else {
				$this->_db = $wpdb;
			}
		}
	}
	
	/**
	 *		getDb()
	 *
	 *		@return object
	 */
	public function getDb() {
		$this->setDb();
		return $this->_db;
	}
	
	/**
	 *		add()
	 *
	 *		@param object $post
	 *		@return int
	 */
	public function add(FvCommunityNews_Models_Post $post) {
		$this->_db->insert(
			$this->_tableName,
			array(
				'Name'			=> $post->Author,
				'Email'			=> $post->AuthorEmail,
				'Ip'			=> $post->AuthorIp,
				'Title'			=> $post->Title,
				'Description'	=> $post->Content,
				'Location'		=> $post->Url,
				'Date'			=> $post->Date,
				'Approved'		=> $post->Approved,
			)
		);
		
		return $this->_db->insert_id;
	}
	
	/**
	 *		update()
	 *
	 *		@param object $post
	 *		@return int
	 */
	public function update(FvCommunityNews_Models_Post $post) {
		$this->_db->update(
			$this->_tableName,
			array(
				'Name'			=> $post->Author,
				'Email'			=> $post->AuthorEmail,
				'Ip'			=> $post->AuthorIp,
				'Title'			=> $post->Title,
				'Description'	=> $post->Content,
				'Location'		=> $post->Url,
				'Date'			=> $post->Date,
				'Approved'		=> $post->Approved,
			),
			array(
				'Id'			=> $post->Id,
			),
			null,
			array('%d')
		);
		
		return $post->Id;
	}
	
	/**
	 *		delete()
	 *
	 *		@param int $id
	 */
	public function delete($id) {
		$this->_db->query(
			$this->_db->prepare(
				"DELETE FROM " . $this->_tableName . " WHERE Id = '%d'",
				$id
			)
		);
	}
	
	
	
	/**
	 *		get()
	 *
	 *		@param int $id
	 *		@return array|bool
	 */
	public function get($id) {
		$row = $this->_db->get_row( $this->_db->prepare("SELECT * FROM " . $this->_tableName . " WHERE Id = '%d'", $id) );
		
		if (0 == count($row)) {
			return false;
		}
		
		return new FvCommunityNews_Models_Post(array(
				'Id'			=> $row->Id,
				'Author'		=> $row->Name,
				'AuthorEmail'	=> $row->Email,
				'AuthorIp'		=> $row->Ip,
				'Title'			=> $row->Title,
				'Content'		=> $row->Description,
				'Url'			=> $row->Location,
				'Date'			=> $row->Date,
				'Views'			=> $row->Views,
				'Approved'		=> $row->Approved
		));
	}
	
	/**
	 *		_fetchOptions()
	 *
	 *		@param array $options
	 *		@return array
	 */
	protected function _fetchOptions(array $options) {
		$where = '';
		$values = array();
		
		if (!empty($options)) {
			$where = ' WHERE ';
			$keys = array();
			
			foreach ($options as $key=>$val) {
				if (is_array($val)) {
					$key = key($val);
					$val = $val[ key($val) ];
				}
				
				if ('!' == substr($val, 0, 1)) {
					$keys[] = $key . " != '%s'";
					$values[] = substr($val, 1);
				} else {
					$keys[] = $key . " = '%s'";
					$values[] = $val;
				}
			}
			
			$where .= implode(' AND ', $keys);
		}
		
		return array('where' => $where, 'values' => $values);
	}
	
	/**
	 *		getAll()
	 *
	 *		@param int $start
	 *		@param int $limit
	 *		@param array $options
	 *		@param array $order
	 *		@return array|bool
	 */
	public function getAll($start=0, $limit=10, array $options=array(), array $order=array('Date'=>'DESC')) {
		$options = $this->_fetchOptions($options);
		
		$options['values'][] = abs( (int)$start );
		$options['values'][] = (int)$limit;
		
		if (!empty($order)) {
			$order = sprintf(' ORDER BY %s %s', key($order), $order[ key($order) ]);
		} else {
			$order = '';
		}
		
		$rows = $this->_db->get_results(
			$this->_db->prepare("SELECT * FROM " . $this->_tableName . $options['where'] . $order . " LIMIT %d, %d", $options['values'])
		);
		
		if (0 == count($rows)) {
			return false;
		}
		
		$entries = array();
		foreach ($rows as $row) {
			$entries[] = new FvCommunityNews_Models_Post(array(
				'Id'			=> $row->Id,
				'Author'		=> $row->Name,
				'AuthorEmail'	=> $row->Email,
				'AuthorIp'		=> $row->Ip,
				'Title'			=> $row->Title,
				'Content'		=> $row->Description,
				'Url'			=> $row->Location,
				'Date'			=> $row->Date,
				'Views'			=> $row->Views,
				'Approved'		=> $row->Approved
			));
		}
		
		return $entries;
	}
	
	/**
	 *		getCount()
	 *
	 *		@param array $options
	 */
	public function getCount(array $options=array()) {
		$options = $this->_fetchOptions($options);
		
		return $this->_db->get_var(
			$this->_db->prepare("SELECT COUNT(*) FROM " . $this->_tableName . $options['where'], $options['values'])
		);
	}
	
	/**
	 *		addView()
	 *
	 *		@param string $url
	 */
	public function addView($url) {
		$this->_db->query(
			$this->_db->prepare("UPDATE " . $this->_tableName . " SET Views = Views+1 WHERE Location = '%s'", $url)
		);
	}
	
}