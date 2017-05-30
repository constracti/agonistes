<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

add_filter( 'manage_post_posts_columns', function( array $columns ): array {
	if ( !current_user_can( 'edit_pages' ) )
		return $columns;
	$columns['share-post'] = __( 'Share post', 'xa' );
	return $columns;
} );

add_action( 'manage_post_posts_custom_column', function( string $column, int $post_id ) {
	if ( $column !== 'share-post' )
		return;
	$meta = get_post_meta( $post_id, 'xa_share_post', FALSE );
	if ( empty( $meta ) ) {
		echo '<span>—</span>' . "\n";
		return;
	}
	echo '<ul>' . "\n";
	foreach ( $meta as $item ) {
		switch ( $item ) {
			case 'm':
				echo sprintf( '<li>%s</li>', get_option( 'xa_name_male' ) ) . "\n";
				break;
			case 'f':
				echo sprintf( '<li>%s</li>', get_option( 'xa_name_female' ) ) . "\n";
				break;
			default:
				$user = get_user_by( 'id', intval( $item ) );
				echo sprintf( '<li>%s</li>', $user->display_name ) . "\n";
				break;
		}
	}
	echo '</ul>' . "\n";
}, 10, 2 );

add_action( 'admin_enqueue_scripts', function( string $hook ) {
	if ( !current_user_can( 'edit_pages' ) )
		return;
	if ( $hook !== 'edit.php' )
		return;
	wp_enqueue_style( 'xa-share-post-custom-column', XA_URL . '/share-post/custom-column.css' );
} );
