/**
 *		@package		FV Community News
 *		@author			WordPress
 *		@version		1.1
 */

(function($) {
	
	$(document).ready(function(){
	
		if ( typeof $.table_hotkeys != 'undefined' ) {
			var toggle_all = function() {
				var master_checkbox = $('form#submissions-form .check-column :checkbox:first');
				master_checkbox.attr('checked', master_checkbox.attr('checked')? '' : 'checked');
				checkAll('form#submissions-form');
			}
		}
		
		
		$('.tab').click(function() {
			$('#tab-interface > .subsubsub > li > a.current').removeClass('current');
			$(this).addClass('current');
			
			$('#tab-interface > #tabContainer > div.currentTab').removeClass('currentTab');
			$(this.rel).addClass('currentTab');
			
			return false;
		});
		
	});
	
})(jQuery);
