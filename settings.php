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
	xa_description( 'users with meta xa_author set to m or f will be restricted to children of this category' );
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
	echo '<tr>' . "\n";
	$key = 'xa_author_guide_page';
	echo sprintf( '<th scope="row"><label for="%s">%s</label></th>', $key, __( 'author guide page', 'xa' ) ) . "\n";
	echo '<td colspan="2">' . "\n";
	echo sprintf( '<select id="%s" class="xa_option" name="%s">', $key, $key ) . "\n";
	echo sprintf( '<option>%s</option>', _x( 'none', 'f', 'xa' ) ) . "\n";
	$posts = get_posts( [
		'post_parent' => 0,
		'post_type' => 'page',
		'nopaging' => TRUE,
		'order' => 'ASC',
		'orderby' => 'title',
	] );
	$post0 = get_option( $key );
	$post0 = ( $post0 !== FALSE ) ? intval( $post0 ) : 0;
	foreach ( $posts as $post ) {
		$selected = selected( $post->ID, $post0, FALSE );
		echo sprintf( '<option value="%d"%s>%s</option>', $post->ID, $selected, $post->post_title ) . "\n";
	}
	echo '</select>' . "\n";
	xa_input_nonce( xa_option_nonce( $key ) );
	xa_spinner();
	xa_description( 'set option xa_author_guide_page' );
	xa_description( 'users with meta xa_author set to m or f will be alerted with an info notice' );
	echo '</td>' . "\n";
	echo '</tr>' . "\n";
	echo '<tr>' . "\n";
	echo '</tbody>' . "\n";
	echo '</table>' . "\n";
	echo sprintf( '<h2>%s</h2>', __( 'Cities', 'xa' ) ) . "\n";
	echo '<table class="form-table">' . "\n";
	echo '<tbody>' . "\n";
	echo '<tr>' . "\n";
	$key = 'xa_city_page';
	echo sprintf( '<th scope="row"><label for="%s">%s</label></th>', $key, __( 'parent page', 'xa' ) ) . "\n";
	echo '<td colspan="2">' . "\n";
	echo sprintf( '<select id="%s" class="xa_option" name="%s">', $key, $key ) . "\n";
	echo sprintf( '<option>%s</option>', _x( 'none', 'f', 'xa' ) ) . "\n";
	$posts = get_posts( [
		'post_parent' => 0,
		'post_type' => 'page',
		'nopaging' => TRUE,
		'order' => 'ASC',
		'orderby' => 'title',
	] );
	$post0 = get_option( $key );
	$post0 = ( $post0 !== FALSE ) ? intval( $post0 ) : 0;
	foreach ( $posts as $post ) {
		$selected = selected( $post->ID, $post0, FALSE );
		echo sprintf( '<option value="%d"%s>%s</option>', $post->ID, $selected, $post->post_title ) . "\n";
	}
	echo '</select>' . "\n";
	xa_input_nonce( xa_option_nonce( $key ) );
	xa_spinner();
	xa_description( 'set option xa_city_page' );
	echo '</td>' . "\n";
	echo '</tr>' . "\n";
	echo '<tr>' . "\n";
	echo sprintf( '<th scope="row">%s</th>', __( 'property', 'xa' ) ) . "\n";
	echo '<td>' . "\n";
	$key = 'xa_city_clear';
	echo sprintf( '<button type="button" id="%s" class="button xa_button" name="%s">%s</button>', $key, $key, __( 'clear', 'xa' ) ) . "\n";
	xa_input_nonce( xa_option_nonce( $key ) );
	xa_spinner();
	xa_description( 'delete meta xa_city from unrelated pages' );
	echo '</td>' . "\n";
	echo '<td>' . "\n";
	$key = 'xa_city_reset';
	echo sprintf( '<button type="button" id="%s" class="button xa_button" name="%s" data-confirm="%s">%s</button>', $key, $key, __( 'sure?', 'xa' ), __( 'reset', 'xa' ) ) . "\n";
	xa_input_nonce( xa_option_nonce( $key ) );
	xa_spinner();
	xa_description( 'delete meta xa_city from all pages' );
	echo '</td>' . "\n";
	echo '</tr>' . "\n";
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
	echo sprintf( '<th scope="row">%s</th>', __( 'property', 'xa' ) ) . "\n";
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

add_action( 'admin_notices', function( $hook ) {
	$meta = get_user_meta( get_current_user_id(), 'xa_author', TRUE );
	if ( !in_array( $meta, ['m', 'f'] ) )
		return;
	global $pagenow;
	if ( !in_array( $pagenow, ['index.php', 'profile.php', 'post.php', 'post-new.php', 'edit.php'] ) )
		return;
	$option = get_option( 'xa_author_guide_page' );
	if ( $option === FALSE )
		return;
	$id = intval( $option );
	$page = get_post( $id );
	$url = get_permalink( $id );
	echo '<div class="notice notice-info">' . "\n";
	echo '<p class="dashicons-before dashicons-info">' . "\n";
	echo sprintf( '<a href="%s" target="_blank">%s</a>', $url, $page->post_title ) . "\n";
	echo '</p>' . "\n";
	echo '</div>' . "\n";
} );

add_action( 'wp_ajax_xa_city_clear', function() {
	if ( !current_user_can( 'administrator' ) )
		exit( 'role' );
	if ( wp_verify_nonce( $_POST['nonce'], $_POST['action'] ) === FALSE )
		exit( 'nonce' );
	$page = get_option( 'xa_city_page' );
	if ( $page === FALSE )
		exit( 'option xa_city_page not set' );
	$posts = get_posts( [
		'post_parent__not_in' => intval( $page ),
		'post_type' => 'page',
		'post_status' => 'any',
		'nopaging' => TRUE,
		'meta_key' => 'xa_city',
		'fields' => 'ids',
	] );
	foreach ( $posts as $post_id )
		delete_post_meta( $post_id, 'xa_city' );
	xa_success();
} );

add_action( 'wp_ajax_xa_city_reset', function() {
	if ( !current_user_can( 'administrator' ) )
		exit( 'role' );
	if ( wp_verify_nonce( $_POST['nonce'], $_POST['action'] ) === FALSE )
		exit( 'nonce' );
	$posts = get_posts( [
		'post_type' => 'page',
		'post_status' => 'any',
		'nopaging' => TRUE,
		'meta_key' => 'xa_city',
		'fields' => 'ids',
	] );
	foreach ( $posts as $post )
		delete_post_meta( $post_id, 'xa_city' );
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
