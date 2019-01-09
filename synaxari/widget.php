<?php

if ( !defined( 'ABSPATH' ) )
	exit;

final class XA_Synaxari_Widget extends XA_Widget {

	function settings(): array {
		return [];
	}

	function name(): string {
		return __( 'XA Synaxari', 'xa' );
	}

	function classname(): string {
		return 'xa_slider_widget widget_featured_slider';
	}

	function description(): string {
		return __( 'Show a random today\'s synaxari post.' , 'xa' );
	}

	function title( array $args, array $instance ) {
		$instance['title'] = date_i18n( 'j F', current_time( 'timestamp' ) );
		$instance['category'] = intval( get_option( 'xa_synaxari_category' ) );
		parent::title( $args, $instance );
	}

	function content( array $instance ) {
		$query = new WP_Query( [
			'cat' => intval( get_option( 'xa_synaxari_category' ) ),
			'posts_per_page' => 1,
			'orderby' => 'rand',
			'meta_key' => 'xa_synaxari',
			'meta_value' => current_time( 'md' ),
		] );
		if ( $query->have_posts() ) {
			$query->the_post();
			echo '<div style="position: relative;">' . "\n";
			xa_figure_div( 'colormag-featured-image', 'slider-featured-image.png' );
			xa_content_div( 'slide', [ 'category' => FALSE, 'meta' => FALSE ] );
			echo '</div>' . "\n";
		}
		wp_reset_query();
	}

}

add_action( 'widgets_init', function() {
	register_widget( 'XA_Synaxari_Widget' );
} );
