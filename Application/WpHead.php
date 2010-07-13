<?php

class fvCommunityNewsWpHead {
	
	public function addHtml() {
		fvCommunityNewsRegistry::get('view')->render('WpHead');
	}
	
}
