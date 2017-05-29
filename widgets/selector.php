<?php

if ( !defined( 'ABSPATH' ) )
	exit;

final class XA_Selector_New_Widget extends XA_Widget {

	function settings(): array {
		$settings = parent::settings();
		unset( $settings['category'] );
		$settings['cookie'] = $this->settings_text( __( 'cookie', 'xa' ) );
		$settings['message'] = $this->settings_text( __( 'message', 'xa' ) );
		$settings['city'] = $this->settings_text( __( 'city', 'xa' ) );
		$settings['section'] = $this->settings_text( __( 'section', 'xa' ) );
		return $settings;
	}

	function name(): string {
		return __( 'XA Selector New', 'xa' );
	}

	function classname(): string {
		return 'xa_selector_new_widget';
	}

	function description(): string {
		return __( 'Display dropdown lists to filter content of other widgets.', 'xa' );
	}

	function content( array $instance ) {
		$style = 'width: initial; min-width: calc(50% - 3px);';
		echo sprintf( '<input type="hidden" name="url" value="%s" />', admin_url( 'admin-ajax.php' ) ) . "\n";
		echo sprintf( '<input type="hidden" name="action" value="%s" />', 'xa_selector_widget' ) . "\n";
		echo sprintf( '<input type="hidden" name="name" value="%s" />', esc_attr( $this->option_name ) ) . "\n";
		echo sprintf( '<input type="hidden" name="number" value="%d" />', $this->number ) . "\n";
		$nonce = $this->nonce( $this->option_name, $this->number );
		echo sprintf( '<input type="hidden" name="nonce" value="%s" />', esc_attr( wp_create_nonce( $nonce ) ) ) . "\n";
		echo sprintf( '<p>%s</p>', esc_html( $instance['message'] ) ) . "\n";
		// cities
		$cities = get_posts( [
			'nopaging' => TRUE,
			'post_parent' => intval( get_option( 'xa_city_page' ) ),
			'post_type' => 'page',
			'orderby' => 'title',
			'order' => 'ASC',
		] );
		$city0 = self::cookie_city( $instance['cookie'] );
		echo sprintf( '<select name="city" style="%s">', $style ) . "\n";
		echo sprintf( '<option value="0">%s</option>', $instance['city'] ) . "\n";
		foreach ( $cities as $city ) {
			$selected = selected( $city->ID, $city0, FALSE );
			echo sprintf( '<option value="%d"%s>%s</option>', $city->ID, $selected, $city->post_title );
		}
		echo '</select>' . "\n";
		// sections
		$sections = [
			'' => $instance['section'],
			'm' => get_option( 'xa_name_male' ),
			'f' => get_option( 'xa_name_female' ),
		];
		$section0 = self::cookie_section( $instance['cookie'] );
		echo sprintf( '<select name="section" style="%s">', $style ) . "\n";
		foreach ( $sections as $key => $name ) {
			$selected = selected( $key, $section0, FALSE );
			echo sprintf( '<option value="%s"%s>%s</option>', $key, $selected, $name );
		}
		echo '</select>' . "\n";
	}

	private function nonce( string $name, int $number ) {
		return sprintf( '%s-%d' );
	}

	static function cookie_city( string $cookie ): int {
		$key = sprintf( 'xa-selector-widget-%s-city', esc_attr( $cookie ) );
		if ( array_key_exists( $key, $_COOKIE ) )
			return intval( $_COOKIE[ $key ] );
		return 0;
	}

	static function check_city( int $city ): bool {
		if ( $city === 0 )
			return TRUE;
		$city = get_post( $city );
		if ( is_null( $city ) )
			return FALSE;
		return $city->post_parent === intval( get_option( 'xa_city_page' ) );
	}

	static function cookie_section( string $cookie ): string {
		$key = sprintf( 'xa-selector-widget-%s-section', esc_attr( $cookie ) );
		if ( array_key_exists( $key, $_COOKIE ) )
			return $_COOKIE[ $key ];
		return '';
	}

	static function check_section( string $section ): bool {
		return in_array( $section, [ '', 'm', 'f' ] );
	}

	static function authors( int $city, string $section ): array {
		$authors = [];
		if ( $city !== 0 )
			$cities = [ $city ];
		else
			$cities = get_posts( [
				'nopaging' => TRUE,
				'post_parent' => intval( get_option( 'xa_city_page' ) ),
				'post_type' => 'page',
				'orderby' => 'title',
				'order' => 'ASC',
				'fields' => 'ids',
			] );
		foreach ( $cities as $city ) {
			if ( $section !== 'f' ) {
				$meta = get_post_meta( $city, 'xa_user_m', TRUE );
				if ( $meta !== '' )
					$authors[] = intval( $meta );
			}
			if ( $section !== 'm' ) {
				$meta = get_post_meta( $city, 'xa_user_f', TRUE );
				if ( $meta !== '' )
					$authors[] = intval( $meta );
			}
		}
		return $authors;
	}

	function callback() {
		$name = $_POST['name'];
		$number = intval( $_POST['number'] );
		$option = get_option( $name );
		if ( !is_array( $option ) || !array_key_exists( $number, $option ) )
			exit( 'name' );
		$instance = $option[ $number ];
		if ( !is_array( $instance ) )
			exit( 'number' );
		if ( !wp_verify_nonce( $_POST['nonce'], self::nonce( $name, $number ) ) )
			exit( 'nonce' );
		$city = intval( $_POST['city'] );
		if ( !self::check_city( $city ) )
			exit( 'city' );
		$section = $_POST['section'];
		if ( !self::check_section( $section ) )
			exit( 'section' );
		$cookie = $instance['cookie'];
		$expire = time() + 60 * 60 * 24 * 30 * 12;
		$name = sprintf( 'xa-selector-widget-%s-city', esc_attr( $cookie ) );
		$value = strval( $city );
		setcookie( $name, $value, $expire, COOKIEPATH, COOKIE_DOMAIN, TRUE );
		$name = sprintf( 'xa-selector-widget-%s-section', esc_attr( $cookie ) );
		$value = $section;
		setcookie( $name, $value, $expire, COOKIEPATH, COOKIE_DOMAIN, TRUE );
		$array = [];
		foreach ( $_POST['widgets'] as $str ) {
			list( $id, $class, $name, $number ) = explode( ';', $str );
			$number = intval( $number );
			$option = get_option( $name );
			if ( !is_array( $option ) || !array_key_exists( $number, $option ) )
				continue;
			$instance = $option[ $number ];
			if ( $instance['cookie'] !== $cookie )
				continue;
			$instance['authors'] = self::authors( $city, $section );
			ob_start();
			$widget = new $class();
			$widget->content( $instance );
			$array[ $id ] = ob_get_clean();
		}
		xa_success( $array );
	}

}

add_action( 'widgets_init', function() {
	register_widget( 'XA_Selector_New_Widget' );
} );

add_action( 'wp_enqueue_scripts', function() {
	wp_enqueue_script( 'xa-selector', XA_URL . '/widgets/selector.js', [ 'jquery' ] );
} );

add_action( 'wp_ajax_xa_selector_widget', ['XA_Selector_New_Widget', 'callback'] );
add_action( 'wp_ajax_nopriv_xa_selector_widget', ['XA_Selector_New_Widget', 'callback'] );
