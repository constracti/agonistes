<?php

if ( !defined( 'ABSPATH' ) )
	exit;

final class XA_Central_New_Widget extends XA_Slider_New_Widget {

	function settings(): array {
		$settings = parent::settings();
		$settings['categories'] = [
			'default' => [],
			'sanitize' => function( array $cats ): array {
				return array_map( 'intval', array_filter( $cats, function( $cat ): bool { return $cat !== ''; } ) );
			},
			'label' => __( 'categories', 'xa' ),
			'field' => function( string $id, string $name, array $value, string $label ) {
				$cats = get_terms( [
					'taxonomy' => 'category',
					'hide_empty' => FALSE,
					'parent' => 0,
				] );
				echo '<div style="margin: 1em 0;">' . "\n";
				echo sprintf( '<label>%s</label>', esc_html( $label ) ) . "\n";
				echo sprintf( '<div id="%s" class="categorydiv" style="max-height: 200px; overflow-y: auto;">', $id ) . "\n";
				echo '<ul class="categorychecklist" style="margin: 0;">' . "\n";
				foreach ( $cats as $cat )
					$this->category_li( $cat, $name, $value );
				echo '</ul>' . "\n";
				echo '</div>' . "\n";
				echo '</div>' . "\n";
			},
		];
		$settings['children'] = $this->settings_checkbox( __( 'children', 'xa' ) );
		return $settings;
	}

	private function category_li( $cat, string $name, array $value ) {
		echo '<li>' . "\n";
		echo '<label class="selectit">' . "\n";
		$checked = checked( in_array( $cat->term_id, $value), TRUE, FALSE );
		echo sprintf( '<input type="checkbox" name="%s[]" value="%d"%s/>', $name, $cat->term_id, $checked ) . "\n";
		echo sprintf( '<span>%s</span>', esc_html( $cat->name ) ) . "\n";
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
		return __( 'Display latest posts of multiple categories in a slider.', 'xa' );
	}

	function query( array $instance ): array {
		$query = [];
		$query['posts_per_page'] = $instance['number'];
		if ( !empty( $instance['categories'] ) )
			$cats = $instance['categories'];
		else
			$cats = [ $instance['category'] ];
		if ( $instance['children'] )
			$query['cat'] = $cats;
		else
			$query['category__in'] = $cats;
		return $query;
	}

}

add_action( 'widgets_init', function() {
	register_widget( 'XA_Central_New_Widget' );
} );
