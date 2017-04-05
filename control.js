jQuery( document ).on( 'click', '.xa-control-add', function() {
	var container = jQuery( this ).parents( '.xa-control-items-container' );
	var items = container.find( '.xa-control-items' );
	var item = container.find( '.xa-control-item0' ).find( '.xa-control-item' );
	item.clone( true ).appendTo( items ).children( 'select' ).focus();
} );

jQuery( document ).on( 'click', '.xa-control-up', function() {
	var item = jQuery( this ).parents( '.xa-control-item' );
	var target = item.prev();
	if ( target.length === 0 )
		return;
	item.detach().insertBefore( target );
} );

jQuery( document ).on( 'click', '.xa-control-down', function() {
	var item = jQuery( this ).parents( '.xa-control-item' );
	var target = item.next();
	if ( target.length === 0 )
		return;
	item.detach().insertAfter( target );
} );

jQuery( document ).on( 'click', '.xa-control-delete', function() {
	var item = jQuery( this ).parents( '.xa-control-item' );
	item.remove();
} );
