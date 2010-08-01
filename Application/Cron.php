<?php


class fvCommunityNewsCron {
	
	protected $_events = array(
		'AutoSpamDeletion'	=> array(
			'recurrance'	=> 'daily',
			'hook'			=> 'fvcn_AutoSpamDeletion',
			'action'		=> null
		)
	);
	
	
	
	public function __construct() {
		
	}
	
	
	
	public function addEvent($name) {
		if (array_key_exists($name, $this->_events)) {
			if (!wp_next_scheduled($this->_events[ $name ]['hook'])) {
				add_action($this->_events[ $name ]['hook'], $this->_events[ $name ]['action']);
				
				wp_schedule_event(
					time(),
					$this->_events[ $name ]['recurrance'],
					$this->_events[ $name ]['hook'] 
				);
			}
		}
		
	}
	
	public function removeEvent($name) {
		
		
	}
	
	
	
	
	
	
}

