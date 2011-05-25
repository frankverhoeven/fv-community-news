
/**
 *		moderate-posts.js
 *
 *		Ajax community news moderation
 *
 *		@version 1.0
 *		@since 2.0
 */

jQuery(document).ready( function($) {
	
	$('.fvcn-post').each(function() {
		var parent = this;
		
		$('.fvcn-actions a', this).each(function() {
			$(this).click(function() {
				var action = $(this).parent().attr('class');
				
				if ('edit' == action) {
					return true;
				}
				
				$.get(this.href + '&fvcn-ajax=true');
				
				if ('approve' == action) {
					$(parent).removeClass('unapproved').addClass('approved');
					$('#fvcn-pending-count').text(parseInt($('#fvcn-pending-count').text()) - 1);
					
					$('#fvcn-menu-count').text(parseInt($('#fvcn-menu-count').text()) - 1);
					
					if (0 == parseInt($('#fvcn-menu-count').text())) {
						$('#fvcn-awaiting-mod').addClass('count-0');
					}
				}
				if ('unapprove' == action) {
					$(parent).removeClass('approved').addClass('unapproved');
					$('#fvcn-pending-count').text(parseInt($('#fvcn-pending-count').text()) + 1);
					
					if ($('#fvcn-awaiting-mod').hasClass('count-0')) {
						$('#fvcn-awaiting-mod').removeClass('count-0');
					}
					
					$('#fvcn-menu-count').text(parseInt($('#fvcn-menu-count').text()) + 1);
				}
				if ('spam' == action) {
					if ($(parent).hasClass('unapproved')) {
						$('#fvcn-pending-count').text(parseInt($('#fvcn-pending-count').text()) - 1);
						$('#fvcn-menu-count').text(parseInt($('#fvcn-menu-count').text()) - 1);
						
						if (0 == parseInt($('#fvcn-menu-count').text())) {
							$('#fvcn-awaiting-mod').addClass('count-0');
						}
					}
					$('#fvcn-spam-count').text(parseInt($('#fvcn-spam-count').text()) + 1);
				}
				if ('unspam' == action) {
					$('#fvcn-spam-count').text(parseInt($('#fvcn-spam-count').text()) - 1);
				}
				if ('trash' == action) {
					if ($(parent).hasClass('unapproved')) {
						$('#fvcn-pending-count').text(parseInt($('#fvcn-pending-count').text()) - 1);
						$('#fvcn-menu-count').text(parseInt($('#fvcn-menu-count').text()) - 1);
						
						if (0 == parseInt($('#fvcn-menu-count').text())) {
							$('#fvcn-awaiting-mod').addClass('count-0');
						}
					}
				}
				if ('trash' == action || 'spam' == action || 'unspam' == action) {
					$(parent).hide();
				}
				
				return false;
			});
		});
	});
	
});
