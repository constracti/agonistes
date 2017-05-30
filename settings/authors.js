jQuery( document ).ready( function( $ ) {

$( '.xa_user_meta[name="xa_author"]' ).change( function() {
	$( this ).parent( 'label' ).siblings( 'label' ).children( '.xa_user_meta[name="xa_author"]' ).prop( 'checked', false );
} );

} );
