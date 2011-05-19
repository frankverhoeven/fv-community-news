
/**
 *		tracker.js
 *
 *		Link clicks tracking
 *
 *		@version 1.1
 */

jQuery(document).ready( function($) {
	$('.fvcn-posts-list a').click(function(event) {
		event.preventDefault();
		
		var data = {
			'fvcn':			'fvcn-tracker',
			'fvcn-ajax':	'true',
			'fvcn-url':		$(this).attr('href')
		};
		data = $.param(data);
		
		$.ajax({
			type: 'POST',
			data: data,
			success: function(response) {
				alert(response);
			}
		});
		
		window.location.href = $(this).attr('href');
	});
});
