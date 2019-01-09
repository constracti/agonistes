<?php

if ( !defined( 'ABSPATH' ) )
	exit;

function xa_authors_page() {
	if ( !current_user_can( 'administrator' ) )
		return;
	xa_header();
	xa_description( 'set user meta xa_author' );
	xa_description( 'only non-subscribers are listed' );
	xa_description( 'c for category authors, m for city male authors and f for city female authors' );
	$table = new XA_Authors_Table();
	$table->prepare_items();
	$table->display();
	xa_footer();
}

add_action( 'admin_enqueue_scripts', function( string $hook ) {
	if ( !current_user_can( 'administrator' ) )
		return;
	if ( $hook !== 'xa_page_xa_authors' )
		return;
	wp_enqueue_style( 'xa_authors', XA_URL . '/settings/authors.css' );
	wp_enqueue_script( 'xa_authors', XA_URL . '/settings/authors.js', ['jquery'] );
} );

if ( !class_exists( 'WP_List_Table' ) )
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

final class XA_Authors_Table extends WP_List_Table {

	private $url;
	private $types;

	function get_columns(): array {
		return [
			'login' => __( 'Username', 'xa' ),
			'email' => __( 'Email', 'xa' ),
			'type' => __( 'Type', 'xa' ),
		];
	}
	
	function get_sortable_columns(): array {
		return [
			'login' => ['login', FALSE],
			'email' => ['email', FALSE],
		];
		# TODO already sorted?
	}

	function column_login( $user ): string {
		return sprintf( '<a href="%s?user_id=%d">%s</a>', $this->url, $user->ID, $user->user_login ) . "\n";
	}

	function column_email( $user ): string {
		return sprintf( '<a href="mailto:%s">%s</a>', $user->user_email, $user->user_email ) . "\n";
	}

	function column_type( $user ): string {
		ob_start();
		$key = 'xa_author';
		$meta = get_user_meta( $user->ID, $key, TRUE );
		foreach ( $this->types as $type => $label ) {
			$checked = checked( $meta, $type, FALSE );
			echo '<label style="display: inline-block;">' . "\n";
			echo sprintf( '<input type="checkbox" class="xa_user_meta" name="%s" value="%s"%s />', $key, $type, $checked ) . "\n";
			echo sprintf( '<span>%s</span>', $label ) . "\n";
			xa_hidden( 'id', $user->ID );
			xa_input_nonce( xa_user_nonce( $key, $user->ID ) );
			xa_spinner();
			echo '</label>' . "\n";
		}
		return ob_get_clean();
	}

	function prepare_items() {
		$this->_column_headers = [
			$this->get_columns(),
			[],
			$this->get_sortable_columns()
		];
		$orderby = ( array_key_exists( 'orderby', $_GET ) && in_array( $_GET['orderby'], ['login', 'email'] ) ) ? $_GET['orderby'] : 'login';
		$order = ( array_key_exists( 'order', $_GET ) && in_array( strtoupper( $_GET['order'] ), ['ASC', 'DESC'] ) ) ? strtoupper( $_GET['order'] ) : 'ASC';
		$users = get_users( [
			'role__in' => ['administrator', 'editor', 'author', 'contributor'],
			'orderby' => $orderby,
			'order' => $order,
		] );
		$this->items = $users;
		$this->url = admin_url( 'user-edit.php' );
		$this->types = [
			'c' => __( 'category', 'xa' ),
			'm' => __( 'male author', 'xa' ),
			'f' => __( 'female author', 'xa' ),
		];
	}

	function display_tablenav( $which ) {}
}
