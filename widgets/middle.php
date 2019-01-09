<?php

if ( !defined( 'ABSPATH' ) )
	exit;

final class XA_Middle_New_Widget extends XA_Featured2_Widget {

	function settings(): array {
		$settings = parent::settings();
		$settings['cookie'] = $this->settings_text( __( 'cookie', 'xa' ) );
		return $settings;
	}

	function name(): string {
		return __( 'XA Middle New', 'xa' );
	}

	function description(): string {
		return __( 'Display latest posts of a category vertically, filter by a selector.', 'xa' );
	}

	function query( array $instance ): array {
		$query = parent::query( $instance );
		if ( array_key_exists( 'authors', $instance ) )
			$query['author__in'] = $instance['authors'];
		return $query;
	}

	function content( array $instance ) {
		if ( !wp_doing_ajax() ) {
			$city = XA_Selector_New_Widget::cookie_city( $instance['cookie'] );
			$section = XA_Selector_New_Widget::cookie_section( $instance['cookie'] );
			$instance['authors'] = XA_Selector_New_Widget::authors( $city, $section );
			echo sprintf( '<div class="xa-selector-target" data-id="%s" data-class="%s" data-name="%s" data-number="%d">',
				$this->id,
				get_class(),
				$this->option_name,
				$this->number
			) . "\n";
		}
		parent::content( $instance );
		if ( !wp_doing_ajax() ) {
			echo '</div>' . "\n";
		}
	}

}

add_action( 'widgets_init', function() {
	register_widget( 'XA_Middle_New_Widget' );
} );
