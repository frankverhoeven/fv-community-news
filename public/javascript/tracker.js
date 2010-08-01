jQuery(document).ready( function($) {
	$('.fvcn-submissions-list a').click(function(event) {
		event.preventDefault();
		
		var data = {
			'fvcn-ajax-request':	true,
			'fvcn-action':			'TrackLink',
			'fvcn_Location':		$(this).attr('href')
		};
		data = $.param(data);
		
		$.ajax({
			type: 'POST',
			data: data
		});
		
		window.location.href = $(this).attr('href');
	});
});
