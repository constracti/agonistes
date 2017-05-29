jQuery( document ).ready( function( $ ) {

var widget = $( '.xa_archive_widget' );

widget.find( 'select' ).change( function() {
	var city = $( this ).val();
	widget.find( '.xa-archive-widget-author' ).hide().filter( '[data-city="' + city + '"]' ).fadeIn();
} );

} );
