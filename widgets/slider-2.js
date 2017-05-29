jQuery( document ).ready( function( $ ) {

$( '.xa_slider_widget ul' ).each( function() {
	var slider = $( this );
	slider.bxSlider( {
		mode : slider.data('mode'),
		auto : true,
		autoControls: true,
		autoControlsCombine: true,
		autoHover: true,
	} );
} );

} );
