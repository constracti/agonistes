<?php

if ( !defined( 'ABSPATH' ) )
	exit;

add_shortcode( 'xa-questions-list', function( array $atts ): string {
	$atts = shortcode_atts( [
		'cat' => 0,
		'tags' => [],
		'solved' => __( 'solved questions', 'xa' ),
		'publish' => __( 'published questions', 'xa' ),
		'future' => __( 'future questions', 'xa' ),
	], $atts );
	if ( $cat === 0 )
		return '';
	$atts['tags'] = array_map( 'intval', explode( ',', $atts['tags'] ) );
	$html = '';
	$posts = get_posts( [
		'cat' => $atts['cat'],
		'tag__in' => $atts['tags'],
		'post_status' => 'publish',
		'nopaging' => TRUE,
		'order' => 'ASC',
		'orderby' => 'date',
	] );
	$html .= sprintf( '<h2>%s</h2>', $atts['solved'] ) . "\n";
	$html .= '<ul>' . "\n";
	foreach ( $posts as $post )
		$html .= sprintf( '<li><a href="%s">%s</a> - %s</li>',
			get_permalink( $post ),
			get_the_title( $post ),
			get_the_date( NULL, $post->ID )
		) . "\n";
	$html .= '</ul>' . "\n";
	$posts = get_posts( [
		'cat' => $atts['cat'],
		'tag__not_in' => $atts['tags'],
		'post_status' => 'publish',
		'nopaging' => TRUE,
		'order' => 'ASC',
		'orderby' => 'date',
	] );
	$html .= sprintf( '<h2>%s</h2>', $atts['publish'] ) . "\n";
	$html .= '<ul>' . "\n";
	foreach ( $posts as $post )
		$html .= sprintf( '<li><a href="%s">%s</a> - %s</li>',
			get_permalink( $post ),
			get_the_title( $post ),
			get_the_date( NULL, $post->ID )
		) . "\n";
	$html .= '</ul>' . "\n";
	$posts = get_posts( [
		'cat' => $atts['cat'],
		'post_status' => 'future',
		'nopaging' => TRUE,
		'order' => 'ASC',
		'orderby' => 'date',
	] );
	$html .= sprintf( '<h2>%s</h2>', $atts['future'] ) . "\n";
	$html .= '<ul>' . "\n";
	foreach ( $posts as $post )
		if ( current_user_can( 'edit_post', $post->ID ) )
			$html .= sprintf( '<li><a href="%s">%s</a> - %s</li>',
				get_permalink( $post ),
				get_the_title( $post ),
				get_the_date( NULL, $post->ID )
			) . "\n";
		else
			$html .= sprintf( '<li>%s</li>',
				get_the_title( $post )
			) . "\n";
	$html .= '</ul>' . "\n";
	return $html;
} );
