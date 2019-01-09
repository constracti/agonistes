<?php

if ( !defined( 'ABSPATH' ) )
	exit;

final class XA_Banner_Widget extends XA_Widget {

	function settings(): array {
		$settings = parent::settings();
		unset( $settings['category'] );
		$settings['link'] = $this->settings_text( __( 'link', 'xa' ) );
		$settings['text'] = $this->settings_text( __( 'text', 'xa' ) );
		$settings['image'] = $this->settings_text( __( 'image', 'xa' ) );
		$settings['border'] = $this->settings_checkbox( __( 'border', 'xa' ) );
		$settings['columns'] = $this->settings_number( __( 'columns', 'xa' ) );
		return $settings;
	}

	function name(): string {
		return __( 'XA Banner', 'xa' );
	}

	function classname(): string {
		return 'xa_banner_widget';
	}

	function description(): string {
		return __( 'Display a link as an image.', 'xa' );
	}

	function content( array $instance ) {
		$style = $instance['border'] ? 'border: 5px solid white; box-shadow: 0 0 5px #ccc; margin: 0 auto 30px auto;' : '';
		if ( $instance['columns'] !== 0 ) {
			$width = 100 / $instance['columns'];
			$margin = $width * 0.05;
			$width = $width * 0.9;
			echo sprintf( '<div class="xa-banner-column" data-width="%0.2f%%" data-margin="0 %0.2f%%" style="%s">', $width, $margin, $style ) . "\n";
		} else {
			echo sprintf( '<div style="%s">', $style ) . "\n";
		}
		echo sprintf( '<a href="%s" title="%s"><img src="%s" style="margin: 0;" /></a>',
			esc_url( $instance['link'] ),
			esc_attr( $instance['text'] ),
			esc_url( $instance['image'] )
		) . "\n";
		echo '</div>' . "\n";
	}

}

add_action( 'widgets_init', function() {
	register_widget( 'XA_Banner_Widget' );
} );

add_action( 'wp_enqueue_scripts', function() {
	wp_enqueue_script( 'xa-banner', XA_URL . '/widgets/banner.js', [ 'jquery' ] );
} );
