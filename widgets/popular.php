<?php

if ( !defined( 'ABSPATH' ) )
	exit;

final class XA_Popular_Widget extends XA_Featured1_Widget {

	function settings(): array {
		$settings = parent::settings();
		unset( $settings['category'] );
		$settings['days'] = $this->settings_number( __( 'days', 'xa' ) );
		return $settings;
	}

	function name(): string {
		return __( 'XA Popular Posts', 'xa' );
	}

	function description(): string {
		return __( 'Display popular posts of a time period.', 'xa' );
	}

	function query( array $instance ): array {
		$dt = new DateTime();
		$dt = $dt->sub( new DateInterval( sprintf( 'P%dD', $instance['days'] ) ) );
		return [
			'post_type' => 'post',
			'post_status' => 'publish',
			'posts_per_page' => $instance['number'],
			'order' => 'DESC',
			'orderby' => 'comment_count',
			'date_query' => [
				'after' => [
					'year' => $dt->format( 'Y' ),
					'month' => $dt->format( 'm' ),
					'day' => $dt->format( 'd' ),
				],
			],
		];
	}

}

add_action( 'widgets_init', function() {
	register_widget( 'XA_Popular_Widget' );
} );
