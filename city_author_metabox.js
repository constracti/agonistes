jQuery( function() {

jQuery( '.xa-author-category' ).each( function() {
	var input = jQuery( this );
	var category = input.val();
	input.prop( 'checked', jQuery( '#in-category-' + category ).prop( 'checked' ) );
} ).click( function() {
	var id = jQuery( this ).val();
	jQuery( '#in-category-' + id ).prop( 'checked', true );
	jQuery( '#in-popular-category-' + id ).prop( 'checked', true );
	jQuery( '.xa-author-category' ).not( this ).prop( 'checked', false ).each( function() {
		var id = jQuery( this ).val();
		jQuery( '#in-category-' + id ).prop( 'checked', false );
		jQuery( '#in-popular-category-' + id ).prop( 'checked', false );
	} );
} );

if ( jQuery( '.xa-author-category:checked' ).length === 0 )
	jQuery( '.xa-author-category' ).first().click();

} );
