<?php

if ( !defined( 'ABSPATH' ) )
	exit;

add_action( 'add_meta_boxes_post', function() {
	$meta = get_user_meta( get_current_user_id(), 'xa_author', TRUE );
	if ( !in_array( $meta, ['m', 'f'] ) )
		return;
	add_meta_box( 'xa_city_author_metabox', __( 'Category', 'xa' ), 'xa_city_author_metabox', 'post', 'side' );
} );

function xa_city_author_metabox( $post ) {
	$cat = get_option( 'xa_author_category' );
	$cat = ( $cat === FALSE ) ? 0 : intval( $cat );
	$cats = get_categories( [
		'taxonomy' => 'category',
		'orderby'  => 'name',
		'order'    => 'ASC',
		'hide_empty' => FALSE,
		'number'     => 0,
		'fields'     => 'id=>name',
		'parent'     => $cat,
	] );
	foreach ( $cats as $cat_id => $cat_name )
		echo sprintf( '<p><label><input type="radio" class="xa-author-category" value="%d" /> %s</label></p>', $cat_id, $cat_name ) . "\n";
}

add_action( 'admin_enqueue_scripts', function( $hook ) {
	if ( !in_array( $hook, ['post.php', 'post-new.php'] ) )
		return;
	$meta = get_user_meta( get_current_user_id(), 'xa_author', TRUE );
	if ( !in_array( $meta, ['m', 'f'] ) )
		return;
	wp_enqueue_script( 'xa_city_author_metabox', XA_URL . '/city_author_metabox.js', ['jquery'] );
} );
