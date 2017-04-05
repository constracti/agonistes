<?php

if ( !defined( 'ABSPATH' ) )
	exit;

final class XA_Synaxari_New_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = [
			'classname' => 'xa-synaxari-new-widget widget_featured_slider',
			'description' => __( 'Show a random today\'s synaxari post.' , 'xa')
		];
		parent::__construct( FALSE, __( 'XA Synaxari New', 'xa' ), $widget_ops );
	}

	function widget( $args, $instance ) {
		global $post;
		$cat = intval( get_option( 'xa_synaxari_category' ) );
		echo $args['before_widget'];
		$color = colormag_category_color( $cat );
		$border_style = sprintf( ' style="border-bottom-color: %s;"', $color );
		$title_style = sprintf( ' style="background-color: %s;"', $color );
		$title = date_i18n( 'j F', current_time( 'timestamp' ) );
		echo sprintf( '<h3 class="widget-title"%s><span%s>%s</span></h3>', $border_style, $title_style, $title ) . "\n";
		$query = new WP_Query( [
			'cat' => $cat,
			'posts_per_page' => 1,
			'orderby' => 'rand',
			'meta_key' => 'xa_synaxari',
			'meta_value' => current_time( 'md' ),
		] );
		if ( $query->have_posts() ) {
			$query->the_post();
			echo '<div class="xa-synaxari">' . "\n";
			xa_image_div( 'synaxari' );
			xa_content_div( 'synaxari' );
			echo '</div>' . "\n";
		}
		wp_reset_query();
		echo $args['after_widget'];
	}

}

add_action( 'widgets_init', function() {
	register_widget( 'XA_Synaxari_New_Widget' );
} );

add_action( 'wp_enqueue_scripts', function() {
	wp_enqueue_style( 'xa-synaxari', XA_URL . '/widgets/synaxari.css', ['colormag_style'] );
} );
