<?php


class fvCommunityNewsAjaxRequest_AddSubmission extends fvCommunityNewsForm_AddSubmission {
	
	public function render() {
		$this->_buildForm();
		
		if ($this->isPost()) {
			$this->_handleRequest();
			
			if ($this->isValid()) {
				$this->_view->valid = true;
				$this->_view->message = __('Your submission has been added. Thank you!', 'fvcn');
			} else {
				$this->_view->valid = false;
				$this->_view->message = __('Validation errors occured, please fix them.', 'fvcn');
				$this->_view->errors = $this->getErrors();
			}
		}
		
		$this->_view->render('Form_AddSubmission_Ajax');
	}
	
}

