jQuery( document ).ready( function( $ ) {
	$( '.xa_banner_widget' ).each( function() {
		var elem = $( this );
		var div = elem.children();
		if ( !div.hasClass( 'xa-banner-column' ) )
			return;
		elem.css( 'display', 'inline-block' );
		elem.css( 'width', div.data( 'width' ) );
		elem.css( 'margin', div.data( 'margin' ) );
	} );
} );
