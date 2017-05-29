<?php

if ( !defined( 'ABSPATH' ) )
	exit;

final class XA_Archive_Widget extends XA_Widget {

	function settings(): array {
		$settings = parent::settings();
		unset( $settings['category'] );
		$settings['prompt'] = $this->settings_text( __( 'prompt', 'xa' ) );
		return $settings;
	}

	function name(): string {
		return __( 'XA Archive', 'xa' );
	}

	function classname(): string {
		return 'xa_archive_widget';
	}

	function description(): string {
		return __( 'Display a dropdown list with links to city authors.', 'xa' );
	}

	function content( array $instance ) {
		$pages = get_posts( [
			'nopaging' => TRUE,
			'post_parent' => intval( get_option( 'xa_city_page' ) ),
			'post_type' => 'page',
			'orderby' => 'title',
			'order' => 'ASC',
		] );
		echo '<p>' . "\n";
		echo '<select style="width: initial;">' . "\n";
		echo sprintf( '<option value="">%s</option>', esc_html( $instance['prompt'] ) ) . "\n";
		foreach ( $pages as $page )
			echo sprintf( '<option value="%d">%s</option>', $page->ID, $page->post_title ) . "\n";
		echo '</select>' . "\n";
		echo '</p>' . "\n";
		$names = [
			'xa_user_m' => get_option( 'xa_name_male' ),
			'xa_user_f' => get_option( 'xa_name_female' ),
		];
		foreach ( $pages as $page )
			foreach ( $names as $key => $name ) {
				$meta = get_post_meta( $page->ID, $key, TRUE );
				if ( $meta !== '' ) {
					$user = get_user_by( 'ID', intval( $meta ) );
					echo sprintf( '<p class="xa-archive-widget-author" data-city="%d" style="display: none;">', $page->ID ) . "\n";
					echo sprintf( '<a href="%s" title="%s">',
						get_author_posts_url( $user->ID ),
						esc_attr( $user->display_name )
					) . "\n";
					echo '<span class="fa fa-link"></span>' . "\n";
					echo sprintf( '<span>%s</span>', esc_html( $name ) ) . "\n";
					echo '</a>' . "\n";
					echo '</p>' . "\n";
				}
			}
	}

}

add_action( 'widgets_init', function() {
	register_widget( 'XA_Archive_Widget' );
} );

add_action( 'wp_enqueue_scripts', function() {
	wp_enqueue_script( 'xa-archive', XA_URL . '/widgets/archive.js', [ 'jquery' ] );
} );
