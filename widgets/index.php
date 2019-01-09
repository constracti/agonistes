<?php

if ( !defined( 'ABSPATH' ) )
	exit;

require_once( XA_DIR . '/widgets/widget.php' );
require_once( XA_DIR . '/widgets/archive.php' );
require_once( XA_DIR . '/widgets/slider.php' );
require_once( XA_DIR . '/widgets/central.php' );
require_once( XA_DIR . '/widgets/featured1.php' );
require_once( XA_DIR . '/widgets/popular.php' );
require_once( XA_DIR . '/widgets/featured2.php' );
require_once( XA_DIR . '/widgets/future.php' );
require_once( XA_DIR . '/widgets/beside.php' );
require_once( XA_DIR . '/widgets/banner.php' );
require_once( XA_DIR . '/widgets/selector.php' );
require_once( XA_DIR . '/widgets/middle.php' );

function xa_figure_div( string $size, string $png = '' ) {
	if ( has_post_thumbnail() )
		$html = get_the_post_thumbnail( NULL, $size, [
			'title' => esc_attr( get_the_title() ),
			'alt' => esc_attr( get_the_title() ),
		] );
	elseif ( $png !== '' )
		$html = sprintf( '<img src="%s/img/%s" title="%s" alt="%s" />',
			get_template_directory_uri(),
			$png,
			esc_attr( get_the_title() ),
			esc_attr( get_the_title() )
		);
	else
		$html = '';
	if ( get_post_status() === 'publish' || current_user_can( 'read_post', get_the_ID() ) )
		$html = sprintf( '<a href="%s" title="%s">%s</a>',
			esc_url( get_permalink() ),
			esc_attr( get_the_title() ),
			$html
		);
	echo '<figure>' . $html . '</figure>' . "\n";
}

function xa_content_div( string $prefix, array $args = [] ) {
	$defs = [
		'meta' => TRUE,
		'category' => TRUE,
		'excerpt' => FALSE,
	];
	foreach ( $defs as $key => $val )
		if ( !array_key_exists( $key, $args ) )
			$args[ $key ] = $val;
	echo sprintf( '<div class="%s-content">', $prefix ) . "\n";
	if ( $args['category'] )
		colormag_colored_category();
	$html = esc_html( get_the_title() );
	if ( get_post_status() === 'publish' || current_user_can( 'read_post', get_the_ID() ) )
		$html = sprintf( '<a href="%s" title="%s">%s</a>',
			esc_url( get_permalink() ),
			esc_attr( get_the_title() ),
			$html
		);
	echo '<h3 class="entry-title">' . $html . '</h3>' . "\n";
	if ( $args['meta'] ) {
		echo '<div class="below-entry-meta">' . "\n";
		xa_time_span();
		xa_author_span();
		xa_comments_span();
		echo '</div>' . "\n";
	}
	if ( $args['excerpt'] )
		echo sprintf( '<div class="entry-content">%s</div>',
			esc_html( get_the_excerpt() )
		) . "\n";
	echo '</div>' . "\n";
}
