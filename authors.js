jQuery( function() {

jQuery( '.xa_user_meta[name="xa_author"]' ).change( function() {
	jQuery( this ).parent( 'label' ).siblings( 'label' ).children( '.xa_user_meta[name="xa_author"]' ).prop( 'checked', false );
} );

} );
