<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

add_action( 'widgets_init', function() {
	register_widget( 'XA_City_Widget' );
} );

add_action( 'wp_enqueue_scripts', function() {
	wp_enqueue_style( 'xa_city_widget_style', XA_URL . '/city-widget.css', array( 'colormag_style' ) );
	wp_enqueue_script( 'xa_city_widget_script', XA_URL . '/city-widget.js', array( 'jquery' ) );
} );

final class XA_City_Widget extends WP_Widget {

function __construct() {
	parent::__construct( 'xa_city_widget', __( 'XA City Widget', 'xa' ), array( 'description' => 'Displays the city archive widget.' ) );
}

function filter( $instance ) {
	if ( ! array_key_exists( 'title', $instance ) )
		$instance['title'] = '';
	return $instance;
}

function form( $instance ) {
	$instance = $this->filter( $instance );
?>
<p>
	<label>
		<span><?php esc_html_e( 'title', 'xa' ); ?></span>
		<br />
		<input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
	</label>
</p>
<?php
}

function update( $new_instance, $old_instance ) {
	$instance = array();
	$instance['title'] = $new_instance['title'];
	return $instance;
}

function widget( $args, $instance ) {
	echo $args['before_widget'];
	if ( $instance['title'] )
		echo $args['before_title'] . $instance['title'] . $args['after_title'];
?>
<p>
<select class="xa-city-widget-select">
	<option value="0" selected="selected">επίλεξε πόλη…</option>
<?php
	$cities = get_posts( array(
		'nopaging' => TRUE,
		'post_parent' => xa_city_page(),
		'post_type' => 'page',
		'orderby' => 'title',
		'order' => 'ASC',
	) );
	foreach ( $cities as $city )
		echo "\t" . sprintf( '<option value="%d">%s</option>', $city->ID, $city->post_title ) . "\n";
?>
</select>
</p>
<?php
	foreach ( $cities as $city ) {
		$meta = get_post_meta( $city->ID, 'xa_user_m', TRUE );
		if ( $meta !== '' ) {
			$user = get_user_by( 'ID', intval( $meta ) );
			echo sprintf( '<p class="xa-city-widget-author" data-city="%d"><a href="%s" title="%s"><i class="fa fa-link"></i> Χαρούμενοι Αγωνιστές</a></p>',
				esc_attr( $city->ID ), esc_url( get_author_posts_url( $user->ID ) ), esc_attr( $user->display_name )
			) . "\n";
		}
		$meta = get_post_meta( $city->ID, 'xa_user_f', TRUE );
		if ( $meta !== '' ) {
			$user = get_user_by( 'ID', intval( $meta ) );
			echo sprintf( '<p class="xa-city-widget-author" data-city="%d"><a href="%s" title="%s"><i class="fa fa-link"></i> Χαρούμενοι Αγωνιστές</a></p>',
				esc_attr( $city->ID ), esc_url( get_author_posts_url( $user->ID ) ), esc_attr( $user->display_name )
			) . "\n";
		}
	}
	echo $args['after_widget'];
}

}
