<?php

class fvCommunityNewsAdmin_Head {
	
	public function addHtml() {
		fvCommunityNewsRegistry::get('view')->render('Admin_Head');
	}
	
}

