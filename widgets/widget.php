<?php

if ( !defined( 'ABSPATH' ) )
	exit;

abstract class XA_Widget extends WP_Widget {

	function settings(): array {
		return [
			'title' => $this->settings_text( __( 'title', 'xa' ) ),
			'category' => $this->settings_category( __( 'category', 'xa' ) ),
		];
	}

	final function settings_text( string $label ): array {
		return [
			'default' => '',
			'sanitize' => 'strval',
			'label' => $label,
			'field' => function( string $id, string $name, string $value, string $label ) {
				echo '<p>' . "\n";
				echo sprintf( '<label for="%s">%s</label>', $id, esc_html( $label ) ) . "\n";
				echo sprintf( '<input class="widefat" id="%s" name="%s" type="text" value="%s" />', $id, $name, esc_attr( $value ) ) . "\n";
				echo '</p>' . "\n";
			},
		];
	}

	final function settings_number( string $label ): array {
		return [
			'default' => 0,
			'sanitize' => 'intval',
			'label' => $label,
			'field' => function( string $id, string $name, int $value, string $label ) {
				echo '<p>' . "\n";
				echo sprintf( '<label for="%s">%s</label>', $id, esc_html( $label ) ) . "\n";
				echo sprintf( '<input class="widefat" id="%s" name="%s" type="number" step="1" min="1" value="%d" />', $id, $name, $value ) . "\n";
				echo '</p>' . "\n";
			},
		];
	}

	final function settings_checkbox( string $label ): array {
		return [
			'default' => FALSE,
			'sanitize' => function( string $value ): bool {
				return $value === "on";
			},
			'label' => $label,
			'field' => function( string $id, string $name, bool $value, string $label ) {
				echo '<p>' . "\n";
				echo sprintf( '<input name="%s" type="hidden" value="off" />', $name ) . "\n";
				$checked = checked( $value, TRUE, FALSE );
				echo sprintf( '<input id="%s" name="%s" type="checkbox" value="on" %s />', $id, $name, $checked ) . "\n";
				echo sprintf( '<label for="%s">%s</label>', $id, esc_html( $label ) ) . "\n";
				echo '</p>' . "\n";
			},
		];
	}

	final function settings_category( string $label ): array {
		return [
			'default' => 0,
			'sanitize' => 'intval',
			'label' => $label,
			'field' => function( string $id, string $name, int $value, string $label ) {
				$cats = get_terms( [
					'taxonomy' => 'category',
					'hide_empty' => FALSE,
					'parent' => 0,
				] );
				echo '<p>' . "\n";
				echo sprintf( '<label for="%s">%s</label>', $id, $label ) . "\n";
				echo sprintf( '<select class="widefat" id="%s" name="%s">', $id, $name ) . "\n";
				echo sprintf( '<option value="">%s</option>', esc_html( _x( 'none', 'f', 'xa' ) ) ) . "\n";
				foreach ( $cats as $cat )
					$this->settings_category_aux( $cat, $value );
				echo '</select>' . "\n";
				echo '</p>' . "\n";
			},
		];
	}

	final private function settings_category_aux( WP_Term $cat, int $cat0, int $level = 0 ) {
		$indent = str_repeat( '&nbsp;', 3 );
		$selected = selected( $cat->term_id, $cat0, FALSE );
		$html = sprintf( '%s%s (%d)', str_repeat( $indent, $level ), $cat->name, $cat->count );
		echo sprintf( '<option value="%d"%s>%s</option>', $cat->term_id, $selected, esc_html( $html ) ) . "\n";
		$cats = get_terms( [
			'taxonomy' => 'category',
			'hide_empty' => FALSE,
			'parent' => $cat->term_id,
		] );
		foreach ( $cats as $cat )
			$this->settings_category_aux( $cat, $cat0, $level + 1 );
	}

	final private function instance( $instance = NULL ): array {
		$settings = $this->settings();
		if ( is_null( $instance ) || !is_array( $instance ) )
			$instance = [];
		foreach ( $settings as $key => $value )
			if ( !array_key_exists( $key, $instance ) )
				$instance[ $key ] = $value['default'];
		return $instance;
	}

	abstract function name(): string;

	abstract function classname(): string;

	abstract function description(): string;

	final function __construct() {
		$widget_ops = [
			'classname' => $this->classname(),
			'description' => $this->description(),
		];
		parent::__construct( FALSE, $this->name(), $widget_ops );
	}

	final function form( $instance ) {
		$instance = $this->instance( $instance );
		foreach ( $this->settings() as $key => $value ) {
			$id = $this->get_field_id( $key );
			$name = $this->get_field_name( $key );
			$value['field']( $id, $name, $instance[ $key ], $value['label'] );
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

	function title( array $args, array $instance ) {
		if ( $instance['title'] === '' )
			return;
		if ( array_key_exists( 'category', $instance ) && $instance['category'] !== 0 ) {
			$color = colormag_category_color( $instance['category'] );
			$search = '<h3';
			$replace = $search . sprintf( ' style="border-bottom-color: %s;"', $color );
			$args['before_title'] = str_replace( $search, $replace, $args['before_title'] );
			$search = '<span';
			$replace = $search . sprintf( ' style="background-color: %s;"', $color );
			$args['before_title'] = str_replace( $search, $replace, $args['before_title'] );
			$href = get_category_link( $instance['category'] );
			$instance['title'] = sprintf( '<a href="%s" style="color: white;">%s</a>', $href, $instance['title'] );
		}
		echo $args['before_title'] . $instance['title'] . $args['after_title'] . "\n";
	}

	abstract function content( array $instance );

	final function widget( $args, $instance ) {
		$instance = $this->instance( $instance );
		echo $args['before_widget'];
		$this->title( $args, $instance );
		$this->content( $instance );
		echo $args['after_widget'];
	}

}
