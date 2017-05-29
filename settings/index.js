jQuery( document ).ready( function( $ ) {

function xa_ajax( element, action ) {
	var data = {
		action: action,
	};
	if ( element.prop( 'type' ) !== 'button' ) {
		data.key = element.prop( 'name' );
		if ( element.prop( 'type' ) === 'checkbox' && !element.prop( 'checked' ) )
			data.value = '';
		else
			data.value = element.val();
	}
	element.siblings( 'input[type="hidden"]' ).each( function() {
		var hidden = $( this );
		data[ hidden.prop( 'name' ) ] = hidden.val();
	} );
	var spinner = element.siblings( '.spinner' );
	if ( spinner.hasClass( 'is-active' ) )
		return;
	spinner.addClass( 'is-active' );
	$.post( ajaxurl, data ).done( function( data, textStatus, jqXHR ) {
		if ( typeof( data ) !== 'object' )
			alert( data );
	} ).fail( function( jqXHR, textStatus, errorThrown ) {
		alert( errorThrown );
	} ).always( function() {
		spinner.removeClass( 'is-active' );
	} );
}

$( '.xa_option' ).change( function() {
	xa_ajax( $( this ), 'xa_option' );
} );

$( '.xa_post_meta' ).change( function() {
	xa_ajax( $( this ), 'xa_post_meta' );
} );

$( '.xa_user_meta' ).change( function() {
	xa_ajax( $( this ), 'xa_user_meta' );
} );

$( '.xa_button' ).click( function() {
	var button = $( this );
	if ( button.data( 'confirm' ) !== undefined )
		if ( !confirm( button.data( 'confirm' ) ) )
			return;
	xa_ajax( button, button.prop( 'name' ) );
} );

} );
