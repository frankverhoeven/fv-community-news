jQuery(document).ready( function($) {
	$('#fvcn-add-submission').submit(function() {
		$('#fvcn_Loader').fadeIn('slow');
		
		var url = $('#fvcn').val() + '?fvcn-ajax-request=true';
		var data = $('#fvcn-add-submission').serialize();
		
		$.ajax({
			type: 'POST',
			url: url,
			data: data,
			success: function(response) {
				$('#fvcn_Loader').hide();
				
				$('fvcnAjaxResponse', response).each(function() {
					var message = $('message', this).text();
					
					$('#fvcn-add-submission .error').removeClass('error');
					$('#fvcn-add-submission .fvcn_ErrorMessage').remove();
					
					if ('error' == $('status', this).text()) {
						$('errorfields > field', this).each(function() {
							$('#' + $('name', this).text()).addClass('error');
							$('#' + $('name', this).text()).parent().append('<span class="fvcn_ErrorMessage">' + $('error', this).text() + '</span>');
						});
						
						//$('#fvcn_Response').html( message );
					} else {
						$('#fvcn_Response').hide();
						
						$('#fvcn-add-submission').slideUp('slow');
						$('#fvcn_AjaxResponse').html('<p>' + message + '</p>');
						$('#fvcn_AjaxResponse').fadeIn('slow');
					}
					
				});
			},
			error: function() {
				$('#fvcn_AjaxResponse').html('<p>Unable to add your submission, please try again later.</p>');
				$('#fvcn_Loader').fadeOut('slow');
			}
		});
		
		return false;
	});
	
});