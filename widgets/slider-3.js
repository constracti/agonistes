jQuery( document ).ready( function( $ ) {

$( '.xa_slider_widget .xa-slider' ).each( function() {
	var slider = $( this );
	var thumbs = slider.siblings( '.xa-pager' );
	var simple = thumbs.length === 0;
	slider.bxSlider( {
		mode: slider.data('mode'),
		pagerCustom: simple ? null : thumbs,
		autoControls: simple,
		autoControlsCombine: true,
		auto: true,
	} );
} );

} );
