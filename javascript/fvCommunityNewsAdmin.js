/**
 *		@package		FV Community News
 *		@author			WordPress
 *		@version		1.0
 */

function fvCommunityNewsCheckAll() {
	var master_checkbox = $('form#comments-form .check-column :checkbox:first');
	master_checkbox.attr('checked', master_checkbox.attr('checked')? '' : 'checked');
	checkAll('form#comments-form');
}
function checkAll(jQ) {
	jQuery(jQ).find( 'tbody:visible .check-column :checkbox' ).attr( 'checked', function() {
		return jQuery(this).attr( 'checked' ) ? '' : 'checked';
	} );
}
jQuery( function($) {
	var lastClicked = false;
	$( 'tbody .check-column :checkbox' ).click( function(e) {
		if ( 'undefined' == e.shiftKey ) { return true; }
		if ( e.shiftKey ) {
			if ( !lastClicked ) { return true; }
			var checks = $( lastClicked ).parents( 'form:first' ).find( ':checkbox' );
			var first = checks.index( lastClicked );
			var last = checks.index( this );
			if ( 0 < first && 0 < last && first != last ) {
				checks.slice( first, last ).attr( 'checked', $( this ).is( ':checked' ) ? 'checked' : '' );
			}
		}
		lastClicked = this;
		return true;
	} );
	$( 'thead :checkbox, tfoot :checkbox' ).click( function() {
		checkAll( $(this).parents( 'form:first' ) );
	} );
} );