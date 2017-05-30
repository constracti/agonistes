<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

# TODO unnecessary share-post property some authors
# TODO reset property

require_once( XA_DIR . '/share-post/filter.php' );
require_once( XA_DIR . '/share-post/metabox.php' );
require_once( XA_DIR . '/share-post/custom-column.php' );

add_action( 'xa_menu', function() {
	global $xa_tabs;
	$xa_tabs['xa_share_post'] = __( 'Share post', 'xa' );
} );

function xa_share_post_page() {
	if ( !current_user_can( 'administrator' ) )
		return;
	xa_header();
	echo '<table class="form-table">' . "\n";
	echo '<tbody>' . "\n";
	$key = 'xa_share_post_settings_clear';
	echo '<tr>' . "\n";
	echo sprintf( '<th scope="row"><label for="%s">%s</label></th>', $key, __( 'property', 'xa' ) ) . "\n";
	echo '<td>' . "\n";
	echo sprintf( '<button type="button" id="%s" class="button xa_button" name="%s">%s</button>', $key, $key, __( 'clear', 'xa' ) ) . "\n";
	xa_input_nonce( $key );
	xa_spinner();
	xa_description( 'delete meta xa_share_post from posts with a single meta value same as the author' );
	echo '</td>' . "\n";
	echo '</tr>' . "\n";
	echo '</tbody>' . "\n";
	echo '</table>' . "\n";
	xa_footer();
}

add_action( 'wp_ajax_xa_share_post_settings_clear', function() {
	if ( !current_user_can( 'administrator' ) )
		exit( 'role' );
	if ( wp_verify_nonce( $_POST['nonce'], $_POST['action'] ) === FALSE )
		exit( 'nonce' );
	$posts = get_posts( [
		'post_type' => 'post',
		'post_status' => 'any',
		'nopaging' => TRUE,
		'meta_key' => 'xa_share_post',
	] );
	$count = 0;
	foreach ( $posts as $post ) {
		$meta = get_post_meta( $post->ID, 'xa_share_post', FALSE );
		if ( count( $meta ) !== 1 )
			continue;
		$meta = $meta[0];
		if ( in_array( $meta, [ 'm', 'f' ] ) )
			continue;
		if ( intval( $meta ) !== intval( $post->post_author ) )
			continue;
		delete_post_meta( $post->ID, 'xa_share_post' );
		$count++;
	}
	exit( sprintf( __( 'deleted metadata of %d posts', 'xa' ), $count ) );
} );
