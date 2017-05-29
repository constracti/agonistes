jQuery( document ).ready( function( $ ) {

var widgets = [];
$( '.xa-selector-target' ).each( function() {
	var w = $( this );
	widgets.push( [ w.data( 'id' ), w.data( 'class' ), w.data( 'name' ), w.data( 'number' ) ].join( ';' ) );
} );

$( '.xa_selector_new_widget' ).each( function() {
	var widget = $( this );
	widget.children( 'select' ).change( function() {
		var data = {};
		widget.children( 'input, select' ).each( function() {
			data[ $( this ).prop( 'name' ) ] = $( this ).val();
		} );
		data.widgets = widgets;
		$.post( data.url, data, function( data ) {
			if ( typeof( data ) === 'object' )
				for ( var id in data )
					$( '.xa-selector-target[data-id="' + id + '"]' ).html( data[ id ] );
			else
				console.log( data );
		} );
	} );
} );

} );
