<?php

if ( !defined( 'ABSPATH' ) )
	exit;

class XA_Central_New_Widget extends XA_Slider_New_Widget {

	function settings(): array {
		$settings = parent::settings();
		unset( $settings['category'] );
		$settings['categories'] = [
			'default' => [],
			'sanitize' => function( array $cats ): array {
				return array_map( 'intval', array_filter( $cats, function( $cat ): bool { return $cat !== ''; } ) );
			},
			'field' => function( string $id, string $name, array $value ) {
				$cats = get_terms( [
					'taxonomy' => 'category',
					'hide_empty' => FALSE,
					'parent' => 0,
				] );
				echo '<div style="margin: 1em 0;">' . "\n";
				echo sprintf( '<label>%s</label>', __( 'categories', 'xa' ) ) . "\n";
				echo sprintf( '<div id="%s" class="categorydiv" style="max-height: 200px; overflow-y: auto;">', $id ) . "\n";
				echo '<ul class="categorychecklist" style="margin: 0;">' . "\n";
				foreach ( $cats as $cat )
					self::category_li( $cat, $name, $value );
				echo '</ul>' . "\n";
				echo '</div>' . "\n";
				echo '</div>' . "\n";
			},
		];
		return $settings;
	}

	private static function category_li( $cat, string $name, array $value ) {
		echo '<li>' . "\n";
		echo '<label class="selectit">' . "\n";
		$checked = checked( in_array( $cat->term_id, $value), TRUE, FALSE );
		echo sprintf( '<input type="checkbox" name="%s[]" value="%d"%s/>', $name, $cat->term_id, $checked ) . "\n";
		echo sprintf( '<span>%s</span>', $cat->name ) . "\n";
		echo '</label>' . "\n";
		$cats = get_terms( [
			'taxonomy' => 'category',
			'hide_empty' => FALSE,
			'parent' => $cat->term_id,
		] );
		if ( !empty( $cats ) ) {
			echo '<ul class="children">' . "\n";
			foreach ( $cats as $cat )
				self::category_li( $cat, $name, $value );
			echo '</ul>' . "\n";
		}
		echo '</li>' . "\n";
	}

	function name(): string {
		return __( 'XA Central New', 'xa' );
	}

	function description(): string {
		return __( 'Displays latest posts of multiple categories in a slider.', 'xa' );
	}

	function title( array $args, array $instance ) {
		echo $args['before_title'] . $instance['title'] . $args['after_title'] . "\n";
	}

	function query( array $instance ): array {
		return [
			'category__in' => $instance['categories'],
			'posts_per_page' => $instance['number'],
		];
	}

}

add_action( 'widgets_init', function() {
	register_widget( 'XA_Central_New_Widget' );
} );
