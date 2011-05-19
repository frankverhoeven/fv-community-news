
/**
 *		settings-tabs.js
 *
 *		Tabs for the settings panel
 *
 *		@version 2.0
 */

jQuery(document).ready(function($) {
	$('#fvcn-general').addClass('current');
	
	// Hide all tabs exept the current one
	$('#fvcn-tabs-container div.fvcn-tab').hide();
	$('#fvcn-tabs-container div.fvcn-tab.current').show();
	
	$('#fvcn-tabs-container ul.subsubsub li a').click(function() {
		// Hide old tab
		$('#fvcn-tabs-container ul.subsubsub li a.current').removeClass('current');
		$('#fvcn-tabs-container div.fvcn-tab.current').hide(250);
		$('#fvcn-tabs-container div.fvcn-tab.current').removeClass('current');
		
		// Show new tab
		$(this).addClass('current');
		$( $(this).attr('href') ).addClass('current');
		$( $(this).attr('href') ).show(250);
		
		return false;
	});
});
