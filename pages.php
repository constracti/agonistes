<?php

if ( !defined( 'ABSPATH' ) )
	exit;

function xa_pages_page() {
	if ( !current_user_can( 'administrator' ) )
		return;
	xa_header();
	xa_description( 'set page meta xa_user_m and xa_user_f' );
	xa_description( 'only children of xa_city_page are listed' );
	if ( get_option( 'xa_city_page' ) === FALSE ) {
		xa_notice( 'error', 'option xa_city_page not set' );
	} else {
		$table = new XA_Pages_Table();
		$table->prepare_items();
		$table->display();
	}
	xa_footer();
}

if ( !class_exists( 'WP_List_Table' ) )
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

final class XA_Pages_Table extends WP_List_Table {

	private $url;
	private $users;

	function get_columns(): array {
		return [
			'title' => __( 'Title', 'xa' ),
			'user_m' => __( 'Male author', 'xa' ),
			'user_f' => __( 'Female author', 'xa' ),
		];
	}
	
	function get_sortable_columns(): array {
		return [
			'title' => ['title', FALSE], # TODO is this already sorted?
		];
	}

	function column_title( $post ): string {
		return sprintf( '<a href="%s?post=%d&action=edit">%s</a>', $this->url, $post->ID, $post->post_title ) . "\n";
	}

	function column_user_m( $post ): string {
		return $this->_column_user( $post, 'm', _x( 'none', 'm', 'xa' ) );
	}

	function column_user_f( $post ): string {
		return $this->_column_user( $post, 'f', _x( 'none', 'f', 'xa' ) );
	}

	function _column_user( $post, string $who, string $none ): string {
		ob_start();
		$key = 'xa_user_' . $who;
		$meta = get_post_meta( $post->ID, $key, TRUE );
		$user0 = ( $meta === '' ) ? 0 : intval( $meta );
		echo sprintf( '<select class="xa_post_meta" name="%s">', $key ) . "\n";
		echo sprintf( '<option>%s</option>', $none ) . "\n";
		foreach ( $this->users[ $who ] as $user ) {
			$selected = selected( $user->ID, $user0, FALSE );
			echo sprintf( '<option value="%d"%s>%s</option>', $user->ID, $selected, $user->display_name ) . "\n";
		}
		xa_hidden( 'id', $post->ID );
		xa_input_nonce( xa_post_nonce( $key, $post->ID ) );
		xa_spinner();
		echo '</select>' . "\n";
		return ob_get_clean();
	}

	function prepare_items() {
		$this->_column_headers = [
			$this->get_columns(),
			[],
			$this->get_sortable_columns()
		];
		$order = ( array_key_exists( 'order', $_GET ) && in_array( strtoupper( $_GET['order'] ), ['ASC', 'DESC'] ) ) ? strtoupper( $_GET['order'] ) : 'ASC';
		$orderby = ( array_key_exists( 'orderby', $_GET ) && in_array( $_GET['orderby'], ['title'] ) ) ? $_GET['orderby'] : 'title';
		$posts = get_posts( [
			'post_parent' => intval( get_option( 'xa_city_page' ) ),
			'post_type' => 'page',
			'post_status' => 'publish',
			'nopaging' => TRUE,
			'order' => $order,
			'orderby' => $orderby,
		] );
		$this->items = $posts;
		$this->url = admin_url( 'post.php' );
		$this->users = [
			'm' => get_users( [
				'meta_key' => 'xa_author',
				'meta_value' => 'm',
				'orderby' => 'display_name',
				'order' => 'ASC',
			] ),
			'f' => get_users( [
				'meta_key' => 'xa_author',
				'meta_value' => 'f',
				'orderby' => 'display_name',
				'order' => 'ASC',
			] ),
		];
	}

	function display_tablenav( $which ) {}
}
