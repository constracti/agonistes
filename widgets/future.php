<?php

if ( !defined( 'ABSPATH' ) )
	exit;

final class XA_Future_Widget extends XA_Featured2_Widget {

	function settings(): array {
		$settings = parent::settings();
		unset( $settings['highlight'] );
		return $settings;
	}

	function name(): string {
		return __( 'XA Future Posts', 'xa' );
	}

	function description(): string {
		return __( 'Display future posts of a specific category vertically.', 'xa' );
	}

	function query( array $instance ): array {
		return [
			'cat' => $instance['category'],
			'post_type' => 'post',
			'post_status' => 'future',
			'posts_per_page' => $instance['number'],
			'order' => 'ASC',
			'orderby' => 'date',
		];
	}

	function content( array $instance ) {
		$instance['highlight'] = FALSE;
		parent::content( $instance );
	}

}

add_action( 'widgets_init', function() {
	register_widget( 'XA_Future_Widget' );
} );
