<?php

if ( !defined( 'ABSPATH' ) )
	exit;

class XA_Featured1_Widget extends XA_Widget {

	function settings(): array {
		$settings = parent::settings();
		$settings['number'] = $this->settings_number( __( 'number', 'xa' ) );
		return $settings;
	}

	function name(): string {
		return __( 'XA Featured Posts (Style 1)', 'xa' );
	}

	function classname(): string {
		return 'widget_featured_posts widget_featured_meta';
	}

	function description(): string {
		return __( 'Display latest posts of a category horizontally.', 'xa' );
	}

	function query( array $instance ): array {
		return [
			'cat' => $instance['category'],
			'posts_per_page' => $instance['number'],
		];
	}

	function content( array $instance ) {
		$query = new WP_Query( $this->query( $instance ) );
		$i = 1;
		while ( $query->have_posts() ):
			$query->the_post();
			if ( $i === 1 )
				echo '<div class="first-post">' . "\n";
			elseif ( $i === 2 )
				echo '<div class="following-post">' . "\n";
			echo '<div class="single-article clearfix">' . "\n";
			xa_figure_div( $i === 1 ? 'colormag-featured-post-medium' : 'colormag-featured-post-small' );
			xa_content_div( 'article', [ 'excerpt' => $i === 1 ] );
			echo '</div>' . "\n";
			if ( $i === 1 )
				echo '</div>' . "\n";
			$i++;
		endwhile;
		if ( $i > 2 )
			echo '</div>';
		wp_reset_query();
	}

}

add_action( 'widgets_init', function() {
	register_widget( 'XA_Featured1_Widget' );
} );
