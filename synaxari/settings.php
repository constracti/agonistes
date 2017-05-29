<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

add_action( 'xa_menu', function() {
	global $xa_tabs;
	$xa_tabs['xa_synaxari'] = __( 'Synaxari', 'xa' );
} );

function xa_synaxari_page() {
	if ( !current_user_can( 'administrator' ) )
		return;
	xa_header();
	echo '<table class="form-table">' . "\n";
	echo '<tbody>' . "\n";
	echo '<tr>' . "\n";
	$key = 'xa_synaxari_category';
	echo sprintf( '<th scope="row"><label for="%s">%s</label></th>', $key, __( 'category', 'xa' ) ) . "\n";
	echo '<td colspan="2">' . "\n";
	$cats = get_terms( [
		'taxonomy' => 'category',
		'hide_empty' => FALSE,
		'parent' => 0,
	] );
	$cat0 = get_option( $key );
	$cat0 = ( $cat0 !== FALSE ) ? intval( $cat0 ) : 0;
	echo sprintf( '<select id="%s" class="xa_option" name="%s">', $key, $key ) . "\n";
	echo sprintf( '<option>%s</option>', _x( 'none', 'f', 'xa' ) ) . "\n";
	foreach ( $cats as $cat )
		xa_synaxari_page_category( $cat, $cat0 );
	echo '</select>' . "\n";
	xa_input_nonce( xa_option_nonce( $key ) );
	xa_spinner();
	xa_description( 'set option xa_synaxari_category' );
	echo '</td>' . "\n";
	echo '</tr>' . "\n";
	echo '<tr>' . "\n";
	echo sprintf( '<th scope="row">%s</th>', __( 'property', 'xa' ) ) . "\n";
	echo '<td>' . "\n";
	$key = 'xa_synaxari_clear';
	echo sprintf( '<button type="button" id="%s" class="button xa_button" name="%s">%s</button>', $key, $key, __( 'clear', 'xa' ) ) . "\n";
	xa_input_nonce( xa_option_nonce( $key ) );
	xa_spinner();
	xa_description( sprintf( __( 'delete metadata %s from posts not in %s', 'xa' ), 'xa_synaxari', 'xa_synaxari_category' ) );
	echo '</td>' . "\n";
	echo '<td>' . "\n";
	$key = 'xa_synaxari_reset';
	echo sprintf( '<button type="button" id="%s" class="button xa_button" name="%s" data-confirm="%s">%s</button>', $key, $key, __( 'sure?', 'xa' ), __( 'reset', 'xa' ) ) . "\n";
	xa_input_nonce( xa_option_nonce( $key ) );
	xa_spinner();
	xa_description( sprintf( __( 'delete metadata %s from all posts', 'xa' ), 'xa_synaxari' ) );
	echo '</td>' . "\n";
	echo '</tr>' . "\n";
	echo '</tbody>' . "\n";
	echo '</table>' . "\n";
	xa_footer();
}

function xa_synaxari_page_category( WP_Term $cat, int $cat0, int $level = 0 ) {
	$indent = str_repeat( '&nbsp;', 3 );
	$selected = selected( $cat->term_id, $cat0, FALSE );
	$html = sprintf( '%s%s (%d)', str_repeat( $indent, $level ), $cat->name, $cat->count );
	echo sprintf( '<option value="%d"%s>%s</option>', $cat->term_id, $selected, esc_html( $html ) ) . "\n";
	$cats = get_terms( [
		'taxonomy' => 'category',
		'hide_empty' => FALSE,
		'parent' => $cat->term_id,
	] );
	foreach ( $cats as $cat )
		xa_synaxari_page_category( $cat, $cat0, $level + 1 );
}

add_action( 'wp_ajax_xa_synaxari_clear', function() {
	if ( !current_user_can( 'administrator' ) )
		exit( 'role' );
	if ( wp_verify_nonce( $_POST['nonce'], $_POST['action'] ) === FALSE )
		exit( 'nonce' );
	$cat = get_option( 'xa_synaxari_category' );
	if ( $cat === FALSE )
		exit( 'option xa_synaxari_category not set' );
	$posts = get_posts( [
		'category__not_in' => intval( $cat ),
		'post_type' => 'post',
		'post_status' => 'any',
		'nopaging' => TRUE,
		'meta_key' => 'xa_synaxari',
		'fields' => 'ids',
	] );
	foreach ( $posts as $post_id )
		delete_post_meta( $post_id, 'xa_synaxari' );
	exit( sprintf( __( 'deleted metadata of %d posts', 'xa' ), count( $posts ) ) );
} );

add_action( 'wp_ajax_xa_synaxari_reset', function() {
	if ( !current_user_can( 'administrator' ) )
		exit( 'role' );
	if ( wp_verify_nonce( $_POST['nonce'], $_POST['action'] ) === FALSE )
		exit( 'nonce' );
	$posts = get_posts( [
		'post_type' => 'post',
		'post_status' => 'any',
		'nopaging' => TRUE,
		'meta_key' => 'xa_synaxari',
		'fields' => 'ids',
	] );
	foreach ( $posts as $post_id )
		delete_post_meta( $post_id, 'xa_synaxari' );
	exit( sprintf( __( 'deleted metadata of %d posts', 'xa' ), count( $posts ) ) );
} );
