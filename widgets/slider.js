jQuery( function() {

jQuery( ".xa-slider-new" ).each( function() {
	var slider = jQuery( this );
	slider.bxSlider( {
		mode : slider.data('mode'),
		auto : true,
		autoControls: true,
		autoControlsCombine: true,
		autoHover: true,
	} );
} );

} );
