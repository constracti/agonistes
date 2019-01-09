jQuery( document ).ready( function( $ ) {

$( ".xa-synaxari-save" ).click( function() {
	var button = $( this );
	var container = button.parents( ".xa-control-items-container" );
	var post = container.data( "xa-synaxari-post" );
	var spinner = container.find( ".xa-synaxari-spinner" ).addClass( "is-active" );
	var query = "action=xa-synaxari&post=" + post;
	container.find( ".xa-control-items" ).find( ".xa-control-item" ).each( function() {
		var item = $( this );
		var month = item.find( ".xa-synaxari-month" ).val();
		var day = item.find( ".xa-synaxari-day" ).val();
		query += "&date[]=" + month + day;
	} );
	$.post( ajaxurl, query, function( data ) {
		spinner.removeClass( "is-active" );
	} );
} );

} );
