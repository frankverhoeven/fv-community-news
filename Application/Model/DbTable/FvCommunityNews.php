<?php

class fvCommunityNewsModel_DbTable_FvCommunityNews {
	
	protected $_tableName;
	
	protected $_db;
	
	public function __construct() {
		$this->_tableName = fvCommunityNewsSettings::getInstance()->get('DbName');
		$this->_db = fvCommunityNewsRegistry::get('wpdb');
	}
	
	public function add(fvCommunityNewsModel_Submission $submission) {
		$this->_save($submission, 'insert');
	}
	
	public function replace(fvCommunityNewsModel_Submission $submission) {
		$this->_save($submission, 'replace');
	}
	
	protected function _save(fvCommunityNewsModel_Submission $submission, $type='insert') {
		if ('insert' != $type && 'replace' != $type) {
			throw new Exception('Invallid type specified');
		}
		
		$this->_db->$type(
			$this->_tableName,
			array(
				'Name'			=> $submission->Name,
				'Email'			=> $submission->Email,
				'Title'			=> $submission->Title,
				'Location'		=> $submission->Location,
				'Description'	=> $submission->Description,
				'Date'			=> $submission->Date,
				'Ip'			=> $submission->Ip,
				'Approved'		=> $submission->Approved
			),
			array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
		);
		
		return $this->_db->insert_id;
	}
	
	public function update(array $data, $where=null, $whereFormat=null) {
		$format = array();
		foreach ($data as $key) {
			$format[] = '%s';
		}
		
		$this->_db->update($this->_tableName, $data, $where, $format, $whereFormat);
	}
	
	public function delete($id) {
		$this->_db->query(
			$this->_db->prepare(
				"DELETE FROM " . $this->_tableName . " WHERE Id = '%d'",
				$id
			)
		);
	}
	
	public function get($id) {
		$row = $this->_db->get_row( $this->_db->prepare("SELECT * FROM " . $this->_tableName . " WHERE Id = '%d'", $id) );
		
		if (0 == count($row)) {
			return;
		}
		
		return new fvCommunityNewsModel_Submission(array(
			'Id'			=> $row->Id,
			'Name'			=> $row->Name,
			'Email'			=> $row->Email,
			'Title'			=> $row->Title,
			'Location'		=> $row->Location,
			'Description'	=> $row->Description,
			'Date'			=> $row->Date,
			'Ip'			=> $row->Ip,
			'Approved'		=> $row->Approved
		));
	}
	
	public function getAll($start=0, $limit=10, array $options=array(), array $order=array('Date'=>'DESC')) {
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
		
		$values[] = abs( (int)$start );
		$values[] = (int)$limit;
		
		if (!empty($order)) {
			$order = sprintf(' ORDER BY %s %s', key($order), $order[ key($order) ]);
		} else {
			$order = '';
		}
		
		$rows = $this->_db->get_results( $this->_db->prepare("SELECT * FROM " . $this->_tableName . $where . $order . " LIMIT %d, %d", $values) );
		
		if (0 == count($rows)) {
			return;
		}
		
		$entries = array();
		foreach ($rows as $row) {
			$entries[] = new fvCommunityNewsModel_Submission(array(
				'Id'			=> $row->Id,
				'Name'			=> $row->Name,
				'Email'			=> $row->Email,
				'Title'			=> $row->Title,
				'Location'		=> $row->Location,
				'Description'	=> $row->Description,
				'Date'			=> $row->Date,
				'Ip'			=> $row->Ip,
				'Approved'		=> $row->Approved
			));
		}
		
		return $entries;
	}
	
	public function getCount(array $options=array()) {
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
		
		return $this->_db->get_var( $this->_db->prepare("SELECT COUNT(*) FROM " . $this->_tableName . $where, $values) );
	}
	
}

