<?php

if ( !defined( 'ABSPATH' ) )
	exit;

function xa_pages_page() {
	if ( !current_user_can( 'administrator' ) )
		return;
	xa_header();
	xa_description( 'set page meta xa_city' );
	xa_description( 'only children of xa_city_page are listed' );
	echo '<p class="dashicons-before dashicons-warning">TODO<p>';
	echo '<table class="form-table">' . "\n";
	echo '<tbody>' . "\n";
	echo '</tbody>' . "\n";
	echo '</table>' . "\n";
	xa_footer();
}
