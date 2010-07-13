/**
 *		Settings Tabs
 */
jQuery(document).ready(function($) {
	// Hide all tabs exept the current one
	$('#fvcn-tabs-container div.fvcn-tab-content').hide();
	$('#fvcn-tabs-container div.fvcn-tab-content.current').show();
	
	$('#fvcn-tabs-container ul.subsubsub li a').click(function() {
		// Hide old tab
		$('#fvcn-tabs-container ul.subsubsub li a.current').removeClass('current');
		$('#fvcn-tabs-container div.fvcn-tab-content.current').hide();
		$('#fvcn-tabs-container div.fvcn-tab-content.current').removeClass('current');
		
		// Show new tab
		$(this).addClass('current');
		$( $(this).attr('href') ).addClass('current');
		$( $(this).attr('href') ).show();
		
		return false;
	});
});
