<?php

if ( !defined( 'ABSPATH' ) )
	exit;

final class XA_Beside_New_Widget extends WP_Widget {

	private static $count = 4;

	private static function keys(): array {
		$keys = [];
		for ( $cnt = 0; $cnt < self::$count; $cnt++ )
			$keys[] = 'category-' . $cnt;
		return $keys;
	}

	private static function instance(): array {
		$instance = [];
		foreach ( self::keys() as $key )
			$instance[ $key ] = [];
		return $instance;
	}

	function __construct() {
		$widget_ops = [
			'classname' => 'xa-beside-new-widget widget_highlighted_posts',
			'description' => __( 'Display latest post of several categories.' , 'xa')
		];
		parent::__construct( FALSE, __( 'XA Beside New', 'xa' ), $widget_ops );
	}

	function form( $instance ) {
		if ( is_null( $instance ) || !is_array( $instance ) || empty( $instance ) )
			$instance = self::instance();
		$cats = get_terms( [
			'taxonomy' => 'category',
			'hide_empty' => FALSE,
		] );
		foreach ( self::keys() as $key ) {
			$id = $this->get_field_id( $key );
			$name = $this->get_field_name( $key );
			echo sprintf( '<div id="%s" class="xa-control-items-container" style="margin: 1em 0;">', $id ) . "\n";
			echo sprintf( '<label>%s</label>', __( 'categories', 'xa' ) ) . "\n";
			echo '<div class="xa-control-items" style="margin: 0.5em 0;">' . "\n";
			foreach ( $instance[ $key ] as $cat )
				self::category_div( $cats, $name, $cat );
			echo '</div>' . "\n";
			echo '<div class="xa-control-item0" style="display: none;">' . "\n";
			self::category_div( $cats, $name, 0 );
			echo '</div>' . "\n";
			echo sprintf( '<button type="button" class="button xa-control-add">%s</button>', __( 'add', 'xa' ) ) . "\n";
			echo '</div>' . "\n";
		}
	}

	private static function category_div( array $cats, string $name, int $cat0 ) {
		echo '<div class="xa-control-item" style="margin: 0.5em 0;">' . "\n";
		echo sprintf( '<select name="%s[]">', $name ) . "\n";
		echo sprintf( '<option value="">%s</option>', _x( 'none', 'f', 'xa' ) ) . "\n";
		foreach ( $cats as $cat ) {
			$selected = selected( $cat->term_id, $cat0, FALSE );
			echo sprintf( '<option value="%d"%s>%s</option>', $cat->term_id, $selected, $cat->name ) . "\n";
		}
		echo '</select>' . "\n";
		echo sprintf( '<button type="button" class="button xa-control-delete">%s</button>', __( 'delete', 'xa' ) ) . "\n";
		echo '</div>' . "\n";
	}

	function update( $new_instance, $old_instance ) {
		$instance = self::instance();
		foreach ( self::keys() as $key )
			if ( array_key_exists( $key, $new_instance ) )
				$instance[ $key ] = array_map( 'intval', array_filter( $new_instance[ $key ], function( string $cat ) { return $cat !== ''; } ) );
		return $instance;
	}

	function widget( $args, $instance ) {
		if ( is_null( $instance ) || !is_array( $instance ) || empty( $instance ) )
			$instance = self::instance();
		echo $args['before_widget'];
		echo '<ul class="xa-beside">' . "\n";
		global $post;
		foreach ( self::keys() as $key ) {
			$query = new WP_Query( [
				'category__in' => $instance[ $key ],
				'posts_per_page' => 1,
			] );
			if ( !$query->have_posts() )
				continue;
			$query->the_post();
			echo '<li>' . "\n";
			xa_image_div( 'beside' );
			xa_content_div( 'beside' );
			echo '</li>' . "\n";
			wp_reset_query();
		}
		echo '</ul>' . "\n";
		echo $args['after_widget'];
	}

}

add_action( 'widgets_init', function() {
	register_widget( 'XA_Beside_New_Widget' );
} );

add_action( 'wp_enqueue_scripts', function() {
	wp_enqueue_style( 'xa-beside', XA_URL . '/widgets/beside.css', ['colormag_style'] );
} );
