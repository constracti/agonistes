<?php

if ( !defined( 'ABSPATH' ) )
	exit;

add_action( 'admin_enqueue_scripts', function( $hook ) {
	if ( !in_array( $hook, ['post.php', 'post-new.php'] ) )
		return;
	$user = wp_get_current_user();
	$meta = get_user_meta( $user->ID, 'xa_author', TRUE );
	if ( $meta !== 'c' )
		return;
	$category = get_category_by_slug( $user->user_login );
	wp_register_script( 'xa_category_author', XA_URL . '/category_author.js', ['jquery'] );
	wp_localize_script( 'xa_category_author', 'xa_category_author', [ 'term_id' => $category->term_id ] );
	wp_enqueue_script( 'xa_category_author' );
} );
