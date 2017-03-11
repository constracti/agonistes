<?php

if ( !defined( 'ABSPATH' ) )
	exit;

function xa_users_page() {
	if ( !current_user_can( 'administrator' ) )
		return;
	xa_header();
	xa_description( 'set user meta xa_author' );
	xa_description( 'only non-subscribers are listed' );
	xa_description( 'c for category authors, m for male authors and f for female authors' );
	echo '<table class="form-table">' . "\n";
	echo '<tbody>' . "\n";
	$users = get_users( [
		'role__in' => ['administrator', 'editor', 'author', 'contributor'],
	] );
	$url = admin_url( 'user_edit.php' );
	$types = [
		'c' => __( 'category', 'xa' ),
		'm' => _x( 'author', 'm', 'xa' ),
		'f' => _x( 'author', 'f', 'xa' ),
	];
	foreach ( $users as $user ) {
		echo '<tr>' . "\n";
		echo sprintf( '<th scope="row"><a href="%s?user_id=%d">%s</a></th>', $url, $user->ID, $user->user_login ) . "\n";
		echo sprintf( '<td><a href="mailto:%s">%s</a></td>', $user->user_email, $user->user_email ) . "\n";
		echo '<td>' . "\n";
		$key = 'xa_author';
		$meta = get_user_meta( $user->ID, $key, TRUE );
		foreach ( $types as $type => $label ) {
			$checked = checked( $meta, $type, FALSE );
			echo '<label style="margin-right: 20px; white-space: nowrap;">' . "\n";
			echo sprintf( '<input type="checkbox" class="xa_user_meta" name="%s" value="%s"%s />', $key, $type, $checked ) . '&nbsp;';
			echo sprintf( '<span>%s</span>', $label ) . "\n";
			xa_hidden( 'id', $user->ID );
			xa_input_nonce( xa_user_nonce( $key, $user->ID ) );
			xa_spinner();
			echo '</label>' . "\n";
		}
		echo '</td>' . "\n";
		echo '</tr>' . "\n";
	}
	echo '</tbody>' . "\n";
	echo '</table>' . "\n";
	xa_footer();
}

add_action( 'admin_enqueue_scripts', function( $hook ) {
	if ( !current_user_can( 'administrator' ) )
		return;
	if ( $hook !== 'xa_page_xa_users' )
		return;
	wp_enqueue_script( 'xa_users', XA_URL . '/users.js', ['jquery'] );
} );
