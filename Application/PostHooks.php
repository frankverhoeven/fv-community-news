<?php


class fvCommunityNewsPostHooks {
	
	protected $_hooks = array(
		'<!--fvCommunityNews:Form-->'			=> 'fvCommunityNewsForm_AddSubmission',
		'<!--fvCommunityNews:Submissions-->'	=> 'fvCommunityNewsSubmissionsList',
		'<!--fvCommunityNews:Archive-->'		=> 'fvCommunityNewsSubmissionsArchive'
	);
	
	public function fetchHooks($content) {
		foreach ($this->_hooks as $name=>$class) {
			if (strstr($content, $name)) {
				// TODO: Fix this without using ob_
				ob_start();
					$obj = new $class();
					$obj->render();
					$data = ob_get_contents();
				ob_end_clean();
				
				$content = str_replace($name, $data, $content);
			}
		}
		
		return $content;
	}
	
}

