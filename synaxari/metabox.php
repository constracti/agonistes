<?php

if ( !defined( 'ABSPATH' ) )
	exit;

function xa_synaxari_access(): bool {
	if ( current_user_can( 'edit_pages' ) )
		return TRUE;
	$user = wp_get_current_user();
	$meta = get_user_meta( $user->ID, 'xa_author', TRUE );
	if ( $meta !== 'c' )
		return FALSE;
	$cat = get_category( intval( get_option( 'xa_synaxari_category' ) ) );
	return $cat->slug === $user->user_login;
}

add_action( 'add_meta_boxes_post', function() {
	if ( !xa_synaxari_access() )
		return;
	add_meta_box( 'xa-synaxari', __( 'Synaxari', 'xa' ), function( $post ) {
		echo sprintf( '<div class="xa-control-items-container" data-xa-synaxari-post="%d">', $post->ID ) . "\n";
		$meta = get_post_meta( $post->ID, 'xa_synaxari', FALSE );
		sort( $meta, SORT_STRING );
		echo '<div class="xa-control-items">' . "\n";
		foreach ( $meta as $date )
			xa_synaxari_field( $date );
		echo '</div>' . "\n";
		echo '<div class="xa-control-item0" style="display: none;">' . "\n";
		xa_synaxari_field();
		echo '</div>' . "\n";
		echo '<div>' . "\n";
		echo sprintf( '<button type="button" class="button button-primary xa-synaxari-save">%s</button>', __( 'save', 'xa' ) ) . "\n";
		echo '<span class="spinner xa-synaxari-spinner" style="float: none;"></span>' . "\n";
		echo sprintf( '<button type="button" class="button xa-control-add" style="float: right;">%s</button>', __( 'add', 'xa' ) ) . "\n";
		echo '</div>' . "\n";
		echo '</div>' . "\n";
	}, 'post', 'side' );
} );

function xa_synaxari_field( $date = NULL ) {
	if ( is_null( $date ) )
		$date = current_time( 'md' );
	$month = substr( $date, 0, 2 );
	$day = substr( $date, 2, 4 );
	echo '<div class="xa-control-item" style="margin: 0.5em 0;">' . "\n";
	echo '<select class="xa-synaxari-day">' . "\n";
	for ( $cnt = 1; $cnt <= 31; $cnt++ )
		echo sprintf( '<option value="%02d" %s>%d</option>', $cnt, selected( $cnt, intval( $day ), FALSE ), $cnt ) . "\n";
	echo '</select>' . "\n";
	echo '<select class="xa-synaxari-month">' . "\n";
	global $wp_locale;
	foreach ( $wp_locale->month as $month_id => $month_name )
		echo sprintf( '<option value="%s" %s>%s</option>', $month_id, selected( $month_id, $month , FALSE ), $month_name ) . "\n";
	echo '</select>' . "\n";
	echo sprintf( '<button type="button" class="button xa-control-delete" style="float: right;">%s</button>', __( 'delete', 'xa' ) ) . "\n";
	echo '</div>' . "\n";
}

add_action( 'wp_ajax_xa-synaxari', function() {
	if ( !xa_synaxari_access() )
		exit;
	# TODO synaxari user can alter meta of any post
	$post = intval( $_POST['post'] );
	$dates = array_unique( $_POST['date'] );
	$meta = get_post_meta( $post, 'xa_synaxari', FALSE );
	foreach ( $meta as $date )
		if ( !in_array( $date, $dates ) )
			delete_post_meta( $post, 'xa_synaxari', $date );
	foreach ( $dates as $date )
		if ( !in_array( $date, $meta ) )
			add_post_meta( $post, 'xa_synaxari', $date, FALSE );
	exit;
} );

add_action( 'admin_enqueue_scripts', function( $hook ) {
	if ( !in_array( $hook, ['post.php', 'post-new.php'] ) )
		return;
	if ( !xa_synaxari_access() )
		return;
	wp_enqueue_script( 'xa-synaxari-metabox', XA_URL . '/synaxari/metabox.js', [ 'jquery' ] );
} );
