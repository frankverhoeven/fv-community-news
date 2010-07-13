<?php


class fvCommunityNewsUpdate {
	
	protected $_settings = null;
	
	public function __construct(fvCommunityNewsSettings_Abstract $settings) {
		$this->_settings = $settings;
		
		$this->_installDatabase()
			 ->_installSettings();
	}
	
	protected function _installDatabase() {
		if (version_compare($this->_settings->get('DbVersion'), $this->_settings->getDefault('DbVersion'), '<')) {
			$dbName = $this->_settings->DbName;
			$sql = str_replace('%DbName%', $dbName, file_get_contents(WP_PLUGIN_DIR . FVCN_PLUGINDIR . '/Config/database.sql'));
			
			if (!file_exists(ABSPATH . 'wp-admin/includes/upgrade.php')) {
				throw new Exception('Cannot find "upgrade.php"');
			} else {
				require_once ABSPATH . 'wp-admin/includes/upgrade.php';
				dbDelta($sql);
				$this->_settings->set('DbVersion', $this->_settings->getDefault('DbVersion'));
			}
		}
		return $this;
	}
	
	protected function _installSettings() {
		foreach ($this->_settings->getAll() as $setting) {
			switch ($setting['type']) {
				case 'bool' :
					$this->_settings->add($setting->getName(), ('true'==$setting?true:false));
					break;
				case 'int' :
					$this->_settings->add($setting->getName(), (int)$setting);
					break;
				case 'string' :
				default :
					$this->_settings->add($setting->getName(), (string)$setting);
			}
		}
		
		$this->_settings->set('Version', $this->_settings->getDefault('Version'));
		
		return $this;
	}
	
}

