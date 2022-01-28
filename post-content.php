<?php

if ( !defined( 'ABSPATH' ) )
	exit;

final class Agonistes_Post_Content {

	private static $posts = [];

	private static function select( int $post ): int|null {
		$key = array_search( $post, self::$posts, TRUE );
		if ( $key === FALSE )
			return NULL;
		return $key;
	}

	private static function insert( int $post ): bool {
		$key = self::select( $post );
		if ( !is_null( $key ) )
			return FALSE;
		self::$posts[] = $post;
		return TRUE;
	}

	private static function delete( int $post ): bool {
		$key = self::select( $post );
		if ( !is_null( $key ) )
			return FALSE;
		unset( self::$posts[$key] );
		return TRUE;
	}

	public static function shortcode( array $atts, string|null $content, string $shortcode_tag ): string {
		$atts = shortcode_atts( [
			'id' => NULL,
		], $atts, $shortcode_tag );
		$post = intval( $atts['id'] );
		if ( $post === 0 )
			return '';
		$post = get_post( $post );
		if ( is_null( $post ) )
			return '';
		if ( $post->post_type !== 'post' )
			return '';
		if ( $post->post_status !== 'publish' )
			return '';
		if ( $post->post_password !== '' )
			return '';
		if ( !self::insert( $post->ID ) )
			return '';
		$content = apply_filters( 'the_content', $post->post_content );
		self::delete( $post->ID );
		return $content;
	}
}

add_shortcode( 'agonistes_post_content', [ 'Agonistes_Post_Content', 'shortcode' ] );

FALSE && add_action( 'init', function(): void {
	global $shortcode_tags;
	$ks = array_keys( $shortcode_tags );
	$vs = array_values( $shortcode_tags );
	$i = array_search( 'agonistes_post_content', $ks, TRUE );
	if ( $i === FALSE )
		return;
	$k = array_slice( $ks, $i, 1 );
	array_unshift( $ks, ...$k );
	$v = array_slice( $vs, $i, 1 );
	array_unshift( $vs, ...$v );
	$shortcode_tags = array_combine( $ks, $vs );
} );
