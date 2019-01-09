<?php

if ( !defined( 'ABSPATH' ) )
	exit;

final class XA_Beside_New_Widget extends XA_Widget {

	private function keys(): array {
		$keys = [];
		for ( $cnt = 0; $cnt < 4; $cnt++ )
			$keys[] = 'category-' . $cnt;
		return $keys;
	}

	function settings(): array {
		$settings = parent::settings();
		unset( $settings['category'] );
		foreach ( $this->keys() as $key )
			$settings[ $key ] = [
				'default' => [],
				'sanitize' => function( array $cats ): array {
					return array_map( 'intval', array_filter( $cats, function( $cat ): bool { return $cat !== ''; } ) );
				},
				'label' => __( 'categories', 'xa' ),
				'field' => function( string $id, string $name, array $value, string $label ) {
					echo sprintf( '<div id="%s" class="xa-control-items-container" style="margin: 1em 0;">', $id ) . "\n";
					echo sprintf( '<label>%s</label>', esc_html( $label ) ) . "\n";
					echo '<div class="xa-control-items" style="margin: 0.5em 0;">' . "\n";
					foreach ( $value as $cat )
						$this->category_div( $name, $cat );
					echo '</div>' . "\n";
					echo '<div class="xa-control-item0" style="display: none;">' . "\n";
					$this->category_div( $name, 0 );
					echo '</div>' . "\n";
					echo sprintf( '<button type="button" class="button xa-control-add">%s</button>', __( 'add', 'xa' ) ) . "\n";
					echo '</div>' . "\n";
				},
			];
		return $settings;
	}

	private function category_div( string $name, int $cat0 ) {
		echo '<div class="xa-control-item" style="margin: 0.5em 0;">' . "\n";
		$cats = get_terms( [
			'taxonomy' => 'category',
			'hide_empty' => FALSE,
			'parent' => 0,
		] );
		echo sprintf( '<select name="%s[]">', $name ) . "\n";
		echo sprintf( '<option value="">%s</option>', _x( 'none', 'f', 'xa' ) ) . "\n";
		foreach ( $cats as $cat )
			$this->category_div_aux( $cat, $cat0 );
		echo '</select>' . "\n";
		echo sprintf( '<button type="button" class="button xa-control-delete">%s</button>', __( 'delete', 'xa' ) ) . "\n";
		echo '</div>' . "\n";
	}

	private function category_div_aux( WP_Term $cat, int $cat0, int $level = 0 ) {
		$indent = str_repeat( '&nbsp;', 3 );
		$selected = selected( $cat->term_id, $cat0, FALSE );
		$html = sprintf( '%s%s (%d)', str_repeat( $indent, $level ), $cat->name, $cat->count );
		echo sprintf( '<option value="%d"%s>%s</option>', $cat->term_id, $selected, $html ) . "\n";
		$cats = get_terms( [
			'taxonomy' => 'category',
			'hide_empty' => FALSE,
			'parent' => $cat->term_id,
		] );
		foreach ( $cats as $cat )
			$this->category_div_aux( $cat, $cat0, $level + 1 );
	}

	function name(): string {
		return __( 'XA Beside New', 'xa' );
	}

	function classname(): string {
		return 'xa_beside_widget widget_highlighted_posts';
	}

	function description(): string {
		return __( 'Display latest post of several categories.' , 'xa' );
	}

	function content( array $instance ) {
		echo '<ul>' . "\n";
		foreach ( $this->keys() as $key ) {
			$query = new WP_Query( [
				'category__in' => $instance[ $key ],
				'posts_per_page' => 1,
			] );
			if ( !$query->have_posts() )
				continue;
			$query->the_post();
			echo '<li>' . "\n";
			xa_figure_div( 'colormag-highlighted-post', 'highlights-featured-image.png' );
			xa_content_div( 'article' );
			echo '</li>' . "\n";
			wp_reset_query();
		}
		echo '</ul>' . "\n";
	}

}

add_action( 'widgets_init', function() {
	register_widget( 'XA_Beside_New_Widget' );
} );

add_action( 'wp_enqueue_scripts', function() {
	wp_enqueue_style( 'xa-beside', XA_URL . '/widgets/beside-2.css', ['colormag_style'] );
} );
