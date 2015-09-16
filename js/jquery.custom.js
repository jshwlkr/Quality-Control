jQuery(document).ready(function($) {

	$( '#ticket_content, #comment' ).focus(function() {
		$( this ).animate({
			height: '+200px'
		}, 200 );
	});
	
	$( '#ticket-attachments > p' ).hide();
	
	$( '.add-attachment' ).live( 'click', function() {
		$( '#ticket-attachments > p' ).toggle();
		
		return false;
	});
	
	$( '.tabber-navigation > ul > li > a' ).click(function(ev) {
		if ( '#' == $(this).attr( 'href' ) )
			ev.preventDefault();
	});
	
	/** That's it... for now ;) */

}); 