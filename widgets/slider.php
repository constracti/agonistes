<?php

if ( !defined( 'ABSPATH' ) )
	exit;

class XA_Slider_New_Widget extends WP_Widget {

	function settings(): array {
		return [
			'title' => [
				'default' => '',
				'sanitize' => 'strval',
				'field' => function( string $id, string $name, string $value ) {
					echo '<p>' . "\n";
					echo sprintf( '<label for="%s">%s</label>', $id, __( 'title', 'xa' ) ) . "\n";
					echo sprintf( '<input class="widefat" id="%s" name="%s" type="text" value="%s" />', $id, $name, $value ) . "\n";
					echo '</p>' . "\n";
				},
			],
			'number' => [
				'default' => 0,
				'sanitize' => 'intval',
				'field' => function( string $id, string $name, int $value ) {
					echo '<p>' . "\n";
					echo sprintf( '<label for="%s">%s</label>', $id, __( 'number', 'xa' ) ) . "\n";
					echo sprintf( '<input class="widefat" id="%s" name="%s" type="number" step="1" min="1" value="%d" />', $id, $name, $value ) . "\n";
					echo '</p>' . "\n";
				},
			],
			'category' => [
				'default' => 0,
				'sanitize' => 'intval',
				'field' => function( string $id, string $name, int $value ) {
					$cats = get_terms( [
						'taxonomy' => 'category',
						'hide_empty' => FALSE,
					] );
					echo '<p>' . "\n";
					echo sprintf( '<label for="%s">%s</label>', $id, __( 'category', 'xa' ) ) . "\n";
					echo sprintf( '<select class="widefat" id="%s" name="%s">', $id, $name ) . "\n";
					echo sprintf( '<option value="">%s</option>', _x( 'none', 'f', 'xa' ) ) . "\n";
					foreach ( $cats as $cat ) {
						$selected = selected( $cat->term_id, $value, FALSE );
						echo sprintf( '<option value="%d"%s>%s</option>', $cat->term_id, $selected, $cat->name ) . "\n";
					}
					echo '</select>' . "\n";
					echo '</p>' . "\n";
				},
			],
			'mode' => [
				'default' => self::$modes[0],
				'sanitize' => 'strval',
				'field' => function( string $id, string $name, string $value ) {
					echo '<p>' . "\n";
					echo sprintf( '<label for="%s">%s</label>', $id, __( 'mode', 'xa' ) ) . "\n";
					echo sprintf( '<select class="widefat" id="%s" name="%s">', $id, $name ) . "\n";
					foreach ( self::$modes as $mode ) {
						$selected = selected( $mode, $value, FALSE );
						echo sprintf( '<option value="%s"%s>%s</option>', $mode, $selected, $mode ) . "\n";
					}
					echo '</select>' . "\n";
					echo '</p>' . "\n";
				}
			],
		];
	}

	function name(): string {
		return __( 'XA Slider New', 'xa' );
	}

	function description(): string {
		return __( 'Displays latest posts of a category in a slider.', 'xa' );
	}

	function title( array $args, array $instance ) {
		$color = colormag_category_color( $instance['category'] );
		$border_style = sprintf( ' style="border-bottom-color: %s;"', $color );
		$title_style = sprintf( ' style="background-color: %s;"', $color );
		echo sprintf( '<h3 class="widget-title"%s><span%s>%s</span></h3>', $border_style, $title_style, $instance['title'] ) . "\n";
	}

	function query( array $instance ): array {
		return [
			'cat' => $instance['category'],
			'posts_per_page' => $instance['number'],
		];
	}

	private static $modes = [
		'horizontal',
		'vertical',
		'fade',
	];

	function instance(): array {
		$settings = $this->settings();
		$instance = [];
		foreach ( $settings as $key => $value )
			$instance[ $key ] = $value['default'];
		return $instance;
	}

	final function __construct() {
		$widget_ops = [
			'classname' => 'xa-slider-new-widget widget_featured_slider',
			'description' => $this->description(),
		];
		parent::__construct( FALSE, $this->name(), $widget_ops );
	}

	final function form( $instance ) {
		if ( is_null( $instance ) || !is_array( $instance ) || empty( $instance ) )
			$instance = $this->instance();
		foreach ( $this->settings() as $key => $value ) {
			$id = $this->get_field_id( $key );
			$name = $this->get_field_name( $key );
			$value['field']( $id, $name, $instance[ $key ] );
		}
	}

	final function update( $new_instance, $old_instance ): array {
		$instance = [];
		foreach ( $this->settings() as $key => $value )
			if ( array_key_exists( $key, $new_instance ) )
				$instance[ $key ] = $value['sanitize']( $new_instance[ $key ] );
			else
				$instance[ $key ] = $value['default'];
		return $instance;
	}

	final function widget( $args, $instance ) {
		if ( is_null( $instance ) || !is_array( $instance ) || empty( $instance ) )
			$instance = $this->instance();
		echo $args['before_widget'];
		if ( $instance['title'] !== '' )
			$this->title( $args, $instance );
		global $post;
		$query = new WP_Query( $this->query( $instance ) );
		echo sprintf( '<ul class="xa-slider-new" data-mode="%s">', $instance['mode'] ) . "\n";
		while ( $query->have_posts() ) {
			$query->the_post();
			echo '<li>' . "\n";
			xa_image_div( 'slider' );
			xa_content_div( 'slider' );
			echo '</li>' . "\n";
		}
		echo '</ul>' . "\n";
		wp_reset_query();
		echo $args['after_widget'];
	}

}

add_action( 'widgets_init', function() {
	register_widget( 'XA_Slider_New_Widget' );
} );

add_action( 'wp_enqueue_scripts', function() {
	wp_enqueue_style( 'xa-bxslider', XA_URL . '/widgets/bxslider.css' );
	wp_enqueue_script( 'xa-bxslider', XA_URL . '/widgets/bxslider.min.js', ['jquery'] );
	wp_enqueue_style( 'xa-slider', XA_URL . '/widgets/slider.css', ['colormag_style', 'xa-bxslider'] );
	wp_enqueue_script( 'xa-slider', XA_URL . '/widgets/slider.js', ['jquery', 'xa-bxslider'] );
} );
