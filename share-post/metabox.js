jQuery( function() {

var busy = false;
var metabox = jQuery( '#xa_share_post' );
var checkboxes = metabox.find( 'input[type="checkbox"]' );
var spinner = metabox.find( '.spinner' );

metabox.find( 'button.button' ).not( '.button-primary' ).click( function() {
	checkboxes.prop( 'checked', false );
} );

metabox.find( 'button.button.button-primary' ).click( function() {
	if ( busy )
		return;
	busy = true;
	var button = jQuery( this );
	data = {
		action: 'xa_share_post_metabox',
		post: button.data( 'post' ),
		nonce: button.data( 'nonce' ),
		values: [],
	};
	checkboxes.filter( ':checked' ).each( function() {
		data.values.push( jQuery( this ).val() );
	} );
	spinner.addClass( 'is-active' );
	jQuery.post( ajaxurl, data ).always( function() {
		spinner.removeClass( 'is-active' );
		busy = false;
	} );
} );

} );
