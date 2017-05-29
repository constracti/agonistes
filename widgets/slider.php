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
		echo sprintf( '<ul data-mode="%s">', $instance['mode'] ) . "\n";
		while ( $query->have_posts() ) {
			$query->the_post();
			echo '<li>' . "\n";
			xa_figure_div( 'colormag-featured-image', 'slider-featured-image.png' );
			xa_content_div( 'slide' );
			echo '</li>' . "\n";
		}
		echo '</ul>' . "\n";
		wp_reset_query();
	}

}

add_action( 'widgets_init', function() {
	register_widget( 'XA_Slider_New_Widget' );
} );

add_action( 'wp_enqueue_scripts', function() {
	wp_enqueue_style( 'xa-bxslider', XA_URL . '/widgets/bxslider.css' );
	wp_enqueue_script( 'xa-bxslider', XA_URL . '/widgets/bxslider.min.js', ['jquery'] );
	wp_enqueue_style( 'xa-slider', XA_URL . '/widgets/slider-2.css', ['colormag_style', 'xa-bxslider'] );
	wp_enqueue_script( 'xa-slider', XA_URL . '/widgets/slider-2.js', ['jquery', 'xa-bxslider'] );
} );
