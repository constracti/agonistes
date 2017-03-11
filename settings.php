<?php

if ( !defined( 'ABSPATH' ) )
	exit;

# TODO rename option xa_author_category to xa_city_category
# TODO rename user meta xa_author to xa_user_type

function xa_settings_page() {
	if ( !current_user_can( 'administrator' ) )
		return;
	xa_header();
	echo '<table class="form-table">' . "\n";
	echo '<tbody>' . "\n";
	echo '<tr>' . "\n";
	$key = 'xa_author_category';
	echo sprintf( '<th scope="row"><label for="%s">%s</label></th>', $key, __( 'parent category', 'xa' ) ) . "\n";
	echo '<td colspan="2">' . "\n";
	echo sprintf( '<select id="%s" class="xa_option" name="%s">', $key, $key ) . "\n";
	echo sprintf( '<option>%s</option>', _x( 'none', 'f', 'xa' ) ) . "\n";
	$terms = get_terms( [
		'taxonomy' => 'category',
		'hide_empty' => FALSE,
		'parent' => 0,
	] );
	$term0 = get_option( $key );
	$term0 = ( $term0 !== FALSE ) ? intval( $term0 ) : 0;
	foreach ( $terms as $term ) {
		$selected = selected( $term->term_id, $term0, FALSE );
		echo sprintf( '<option value="%d"%s>%s</option>', $term->term_id, $selected, $term->name ) . "\n";
	}
	echo '</select>' . "\n";
	xa_input_nonce( xa_option_nonce( $key ) );
	xa_spinner();
	xa_description( 'set option xa_author_category' );
	echo '</td>' . "\n";
	echo '</tr>' . "\n";
	echo '<tr>' . "\n";
	echo sprintf( '<th scope="row">%s</th>', __( 'user type property', 'xa' ) ) . "\n";
	echo '<td>' . "\n";
	$key = 'xa_user_type_clear';
	echo sprintf( '<button type="button" id="%s" class="button xa_button" name="%s">%s</button>', $key, $key, __( 'clear', 'xa' ) ) . "\n";
	xa_input_nonce( xa_option_nonce( $key ) );
	xa_spinner();
	xa_description( 'delete meta xa_author from subscribers' );
	echo '</td>' . "\n";
	echo '<td>' . "\n";
	$key = 'xa_user_type_reset';
	echo sprintf( '<button type="button" id="%s" class="button xa_button" name="%s" data-confirm="%s">%s</button>', $key, $key, __( 'sure?', 'xa' ), __( 'reset', 'xa' ) ) . "\n";
	xa_input_nonce( xa_option_nonce( $key ) );
	xa_spinner();
	xa_description( 'delete meta xa_author from all users' );
	echo '</td>' . "\n";
	echo '</tr>' . "\n";
	echo '</tbody>' . "\n";
	echo '</table>' . "\n";
	echo sprintf( '<h2>%s</h2>', __( 'Cities', 'xa' ) ) . "\n";
	echo '<table class="form-table">' . "\n";
	echo '<tbody>' . "\n";
	# TODO I was here!
	echo '</tbody>' . "\n";
	echo '</table>' . "\n";
	echo sprintf( '<h2>%s</h2>', __( 'Synaxari', 'xa' ) ) . "\n";
	echo '<table class="form-table">' . "\n";
	echo '<tbody>' . "\n";
	echo '<tr>' . "\n";
	$key = 'xa_synaxari_category';
	echo sprintf( '<th scope="row"><label for="%s">%s</label></th>', $key, __( 'category', 'xa' ) ) . "\n";
	echo '<td colspan="2">' . "\n";
	echo sprintf( '<select id="%s" class="xa_option" name="%s">', $key, $key ) . "\n";
	echo sprintf( '<option>%s</option>', _x( 'none', 'f', 'xa' ) ) . "\n";
	$terms = get_terms( [
		'taxonomy' => 'category',
		'hide_empty' => FALSE,
	] );
	$term0 = get_option( $key );
	$term0 = ( $term0 !== FALSE ) ? intval( $term0 ) : 0;
	foreach ( $terms as $term ) {
		$selected = selected( $term->term_id, $term0, FALSE );
		echo sprintf( '<option value="%d"%s>%s</option>', $term->term_id, $selected, $term->name ) . "\n";
	}
	echo '</select>' . "\n";
	xa_input_nonce( xa_option_nonce( $key ) );
	xa_spinner();
	xa_description( 'set option xa_synaxari_category' );
	echo '</td>' . "\n";
	echo '</tr>' . "\n";
	echo '<tr>' . "\n";
	echo sprintf( '<th scope="row">%s</th>', __( 'synaxari property', 'xa' ) ) . "\n";
	echo '<td>' . "\n";
	$key = 'xa_synaxari_clear';
	echo sprintf( '<button type="button" id="%s" class="button xa_button" name="%s">%s</button>', $key, $key, __( 'clear', 'xa' ) ) . "\n";
	xa_input_nonce( xa_option_nonce( $key ) );
	xa_spinner();
	xa_description( 'delete meta xa_synaxari from unrelated posts' );
	echo '</td>' . "\n";
	echo '<td>' . "\n";
	$key = 'xa_synaxari_reset';
	echo sprintf( '<button type="button" id="%s" class="button xa_button" name="%s" data-confirm="%s">%s</button>', $key, $key, __( 'sure?', 'xa' ), __( 'reset', 'xa' ) ) . "\n";
	xa_input_nonce( xa_option_nonce( $key ) );
	xa_spinner();
	xa_description( 'delete meta xa_synaxari from all posts' );
	echo '</td>' . "\n";
	echo '</tr>' . "\n";
	echo '</tbody>' . "\n";
	echo '</table>' . "\n";
	xa_footer();
}

add_action( 'wp_ajax_xa_user_type_clear', function() {
	if ( !current_user_can( 'administrator' ) )
		exit( 'role' );
	if ( wp_verify_nonce( $_POST['nonce'], $_POST['action'] ) === FALSE )
		exit( 'nonce' );
	$users = get_users( [
		'role__not_in' => ['administrator', 'editor', 'author', 'contributor'],
		'meta_key' => 'xa_author',
		'fields' => 'ids',
	] );
	foreach ( $users as $user_id )
		delete_user_meta( $user_id, 'xa_author' );
	xa_success();
} );

add_action( 'wp_ajax_xa_user_type_reset', function() {
	if ( !current_user_can( 'administrator' ) )
		exit( 'role' );
	if ( wp_verify_nonce( $_POST['nonce'], $_POST['action'] ) === FALSE )
		exit( 'nonce' );
	$users = get_users( [
		'meta_key' => 'xa_author',
		'fields' => 'ids',
	] );
	foreach ( $users as $user_id )
		delete_user_meta( $user_id, 'xa_author' );
	xa_success();
} );

add_action( 'wp_ajax_xa_synaxari_clear', function() {
	if ( !current_user_can( 'administrator' ) )
		exit( 'role' );
	if ( wp_verify_nonce( $_POST['nonce'], $_POST['action'] ) === FALSE )
		exit( 'nonce' );
	$cat = get_option( 'xa_synaxari_category' );
	if ( $cat === FALSE )
		exit( 'option xa_synaxari_category not set' );
	$cat = intval( $cat );
	$posts = get_posts( [
		'category__not_in' => $cat,
		'post_type' => 'post',
		'post_status' => 'any',
		'nopaging' => TRUE,
		'meta_key' => 'xa_synaxari',
		'fields' => 'ids',
	] );
	foreach ( $posts as $post_id )
		delete_post_meta( $post_id, 'xa_synaxari' );
	xa_success();
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
	foreach ( $posts as $post )
		delete_post_meta( $post_id, 'xa_synaxari' );
	xa_success();
} );
