<?php

if ( !defined( 'ABSPATH' ) )
	exit;

class XA_Slider_New_Widget extends XA_Widget {

	function settings(): array {
		$settings = parent::settings();
		$settings['number'] = $this->settings_number( __( 'number', 'xa' ) );
		$settings['mode'] = [
			'default' => self::$modes[0],
			'sanitize' => 'strval',
			'label' => __( 'mode', 'xa' ),
			'field' => function( string $id, string $name, string $value, string $label ) {
				echo '<p>' . "\n";
				echo sprintf( '<label for="%s">%s</label>', $id, esc_html( $label ) ) . "\n";
				echo sprintf( '<select class="widefat" id="%s" name="%s">', $id, $name ) . "\n";
				foreach ( self::$modes as $mode ) {
					$selected = selected( $mode, $value, FALSE );
					echo sprintf( '<option value="%s"%s>%s</option>', esc_attr( $mode ), $selected, esc_html( $mode ) ) . "\n";
				}
				echo '</select>' . "\n";
				echo '</p>' . "\n";
			},
		];
		$settings['thumbnails'] = $this->settings_checkbox( __( 'thumbnails', 'xa' ) );
		return $settings;
	}

	private static $modes = [
		'horizontal',
		'vertical',
		'fade',
	];

	function name(): string {
		return __( 'XA Slider New', 'xa' );
	}

	function classname(): string {
		return 'xa_slider_widget widget_featured_slider';
	}

	function description(): string {
		return __( 'Display latest posts of a category in a slider.', 'xa' );
	}

	function query( array $instance ): array {
		return [
			'cat' => $instance['category'],
			'posts_per_page' => $instance['number'],
		];
	}

	final function content( array $instance ) {
		$query = new WP_Query( $this->query( $instance ) );
		echo sprintf( '<ul class="xa-slider" data-mode="%s">', $instance['mode'] ) . "\n";
		while ( $query->have_posts() ) {
			$query->the_post();
			echo '<li>' . "\n";
			xa_figure_div( 'colormag-featured-image', 'slider-featured-image.png' );
			xa_content_div( 'slide' );
			echo '</li>' . "\n";
		}
		echo '</ul>' . "\n";
		if ( $instance['thumbnails'] ) {
			$query->rewind_posts();
			$style = sprintf ( 'max-width: calc( ( 100%% - %d * 2px ) / %d );', $query->post_count - 1, $query->post_count );
			echo '<div class="xa-pager">' . "\n";
			while ( $query->have_posts() ) {
				$query->the_post();
				echo sprintf( '<a href="%s" data-slide-index="%d" style="%s">', get_permalink(), $query->current_post, $style ) . "\n";
				echo get_the_post_thumbnail( NULL, 'thumbnail', [ 'title' => get_the_title() ] ) . "\n";
				echo '</a>' . "\n";
			}
			echo '</div>' . "\n";
		}
		wp_reset_query();
	}

}

add_action( 'widgets_init', function() {
	register_widget( 'XA_Slider_New_Widget' );
} );

add_action( 'wp_enqueue_scripts', function() {
	wp_enqueue_style( 'xa-bxslider', XA_URL . '/widgets/bxslider.css' );
	wp_enqueue_script( 'xa-bxslider', XA_URL . '/widgets/bxslider.min.js', ['jquery'] );
	wp_enqueue_style( 'xa-slider', XA_URL . '/widgets/slider-3.css', ['colormag_style', 'xa-bxslider'] );
	wp_enqueue_script( 'xa-slider', XA_URL . '/widgets/slider-3.js', ['jquery', 'xa-bxslider'] );
} );
