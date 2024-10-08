<?php

if ( !defined( 'ABSPATH' ) )
	exit;

add_action( 'post_submitbox_misc_actions', function( WP_Post $post ): void {
	if ( $post->post_type !== 'post' )
		return;
	echo sprintf( '<div class="notice notice-info inline">' ) . "\n";
	echo '<p>' . "\n";
	echo '<span class="dashicons dashicons-info"></span>' . "\n";
	echo sprintf( '<a href="%s" target="_blank">%s</a>',
		'https://docs.google.com/document/d/1VvbCTgvP3M4XZJNeitpVTv8dIX-BOBOmablNBHVHX_M/edit?usp=sharing',
		esc_html( 'Οδηγός Συγγραφής Άρθρου' ),
	) . "\n";
	echo '</p>' . "\n";
	echo '</div>' . "\n";
} );
