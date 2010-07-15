<?php


class fvCommunityNewsBootstrap_Abstract {
	
	public function __construct() {
		$methods = get_class_methods($this);
		
		foreach ($methods as $method) {
			if ('_init' == substr($method, 0, 5)) {
				$this->$method();
			}
		}
	}
	
}

