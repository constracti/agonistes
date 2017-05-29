<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

add_action( 'add_meta_boxes_post', function() {
	if ( !current_user_can( 'edit_pages' ) )
		return;
	add_meta_box( 'xa_share_post', __( 'Share post', 'xa' ), 'xa_share_post_metabox', 'post', 'side' );
} );

function xa_share_post_metabox( WP_Post $post ) {
	$meta = get_post_meta( $post->ID, 'xa_share_post', FALSE );
	$users = get_users( [
		'orderby'  => 'display_name',
		'order'    => 'ASC',
		'meta_key' => 'xa_author',
		'meta_value' => ['m', 'f'],
		'meta_compare' => 'IN',
		'fields'   => ['ID', 'display_name'],
	] );
	xa_share_post_metabox_checkbox( 'm', in_array( 'm', $meta ), get_option( 'xa_name_male', '' ) );
	xa_share_post_metabox_checkbox( 'f', in_array( 'f', $meta ), get_option( 'xa_name_female', '' ) );
	echo '<div style="max-height: 200px; overflow-y: auto;">' . "\n";
	foreach ( $users as $user )
		xa_share_post_metabox_checkbox( $user->ID, in_array( $user->ID, $meta ), $user->display_name );
	echo '</div>' . "\n";
	echo '<p>' . "\n";
	$nonce = wp_create_nonce( xa_share_post_metabox_nonce( $post->ID ) );
	echo sprintf( '<button type="button" class="button button-primary" data-post="%d" data-nonce="%s">%s</button>', $post->ID, $nonce, __( 'save', 'xa' ) ) . "\n";
	echo '<span class="spinner" style="float: none;"></span>' . "\n";
	echo sprintf( '<button type="button" class="button" style="float: right;">%s</button>', __( 'reset', 'xa' ) ) . "\n";
	echo '</p>' . "\n";
}

function xa_share_post_metabox_checkbox( string $value, bool $checked, string $label ) {
	echo '<div>' . "\n";
	echo '<label>' . "\n";
	$checked = checked( $checked, TRUE, FALSE );
	echo sprintf( '<input type="checkbox" value="%s"%s />', $value, $checked ) . "\n";
	echo sprintf( '<span>%s</span>', $label ) . "\n";
	echo '</label>' . "\n";
	echo '</div>' . "\n";
}

function xa_share_post_metabox_nonce( int $post ): string {
	return sprintf( 'xa-share-post-metabox-%d', $post );
}

add_action( 'wp_ajax_xa_share_post_metabox', function() {
	if ( !current_user_can( 'edit_pages' ) )
		exit( 'role' );
	$post = filter_var( $_POST['post'], FILTER_VALIDATE_INT );
	if ( $post === FALSE )
		exit( 'post' );
	if ( !wp_verify_nonce( $_POST['nonce'], xa_share_post_metabox_nonce( $post ) ) )
		exit( 'nonce' );
	if ( !array_key_exists( 'values', $_POST ) )
		$values = [];
	else
		$values = $_POST['values'];
	$meta = get_post_meta( $post, 'xa_share_post', FALSE );
	foreach ( array_diff( $meta, $values ) as $value )
		delete_post_meta( $post, 'xa_share_post', $value );
	foreach ( array_diff( $values, $meta ) as $value )
		add_post_meta( $post, 'xa_share_post', $value, FALSE );
	exit;
} );

add_action( 'admin_enqueue_scripts', function( $hook ) {
	if ( !in_array( $hook, ['post.php', 'post-new.php'] ) )
		return;
	if ( !current_user_can( 'edit_pages' ) )
		return;
	wp_enqueue_script( 'xa-share-post', XA_URL . '/share-post/metabox.js', [ 'jquery' ] );
} );
