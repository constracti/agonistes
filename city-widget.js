jQuery( function() {

jQuery( ".xa-city-widget-select" ).change( function() {
	var elem = jQuery( this );
	var city = parseInt( elem.val() );
	elem.parents( ".widget_xa_city_widget" ).find( ".xa-city-widget-author" ).hide().filter( "[data-city='" + city + "']" ).fadeIn();
} );

} );