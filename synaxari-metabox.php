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
	add_meta_box( 'xa_synaxari', __( 'Synaxari', 'xa' ), 'xa_synaxari_metabox', 'post', 'side' );
} );

# TODO I was here!

/*
function xa_synaxari_metabox( $post ) {
?>
<div id="xa-synaxari-dates">
<?php
	$meta = get_post_meta( $post->ID, 'xa_synaxari', FALSE );
	foreach ( $meta as $date )
		xa_synaxari_field( $date );
?>
</div>
<div id="xa-synaxari-date"><?php xa_synaxari_field(); ?></div>
<p>
	<button id="xa-synaxari-submit" class="button" type="button" data-post="<?php echo $post->ID; ?>"><?php esc_html_e( 'Submit' , 'xa' ); ?></button>
	<span id="xa-synaxari-spinner" class="spinner"></span>
	<span id="xa-synaxari-insert" class="dashicons dashicons-plus-alt" title="<?php esc_attr_e( 'Insert', 'xa' ); ?>"></span>
</p>
<?php
}

function xa_synaxari_field( $date = NULL ) {
	if ( is_null( $date ) )
		$date = current_time( 'md' );
	$month = substr( $date, 0, 2 );
	$day = substr( $date, 2, 4 );
?>
<p class="xa-synaxari-date">
	<select class="xa-synaxari-day">
<?php
	for ( $cnt = 1; $cnt <= 31; $cnt++ )
		echo sprintf( '<option value="%02d" %s>%d</option>', $cnt, selected( $cnt, intval( $day ), FALSE ), $cnt ) . "\n";
?>
	</select>
	<select class="xa-synaxari-month">
<?php
	global $wp_locale;
	foreach ( $wp_locale->month as $month_id => $month_name )
		echo sprintf( '<option value="%s" %s>%s</option>', $month_id, selected( $month_id, $month , FALSE ), $month_name ) . "\n";
?>
	</select>
	<span class="xa-synaxari-delete dashicons dashicons-trash" title="<?php esc_attr_e( 'Delete', 'xa' ); ?>"></span>
	<input type="hidden" class="xa-synaxari-value" value="<?php echo $date; ?>" />
</p>
<?php
}

add_action( 'wp_ajax_xa_synaxari', function() {
	if ( ! xa_synaxari_access() )
		exit;
	// TODO synaxari user can alter meta of any post
	$post = intval( $_POST['post'] );
	$dates = array_unique( $_POST['date'] );
	$meta = get_post_meta( $post, 'xa_synaxari', FALSE );
	foreach ( $meta as $date )
		if ( ! in_array( $date, $dates ) )
			delete_post_meta( $post, 'xa_synaxari', $date );
	foreach ( $dates as $date )
		if ( ! in_array( $date, $meta ) )
			add_post_meta( $post, 'xa_synaxari', $date, FALSE );
	printf( '%d %s', count( $dates ), 'dates saved to post' );
	exit;
} );

add_action( 'admin_enqueue_scripts', function( $hook ) {
	$url = plugin_dir_url( __FILE__ );
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php', ) ) )
		return;
	if ( ! xa_synaxari_access() )
		return;
	wp_enqueue_style( 'xa_synaxari_metabox_style', $url . 'xa-synaxari-metabox.css' );
	wp_enqueue_script( 'xa_synaxari_metabox_script', $url . 'xa-synaxari-metabox.js', array( 'jquery' ) );
} );
*/
