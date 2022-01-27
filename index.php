<?php

/*
 * Plugin Name: Charoumenoi Agonistes
 * Plugin URI: https://github.com/constracti/agonistes
 * Description: Customization plugin of agonistes.gr website.
 * Author: constracti
 * Version: 0.4.2
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: agonistes
 * Domain Path: /languages
 */

if ( !defined( 'ABSPATH' ) )
	exit;

// define plugin constants
define( 'AGONISTES_DIR', plugin_dir_path( __FILE__ ) );
define( 'AGONISTES_URL', plugin_dir_url( __FILE__ ) );

// require php files
$files = glob( AGONISTES_DIR . '*.php' );
foreach ( $files as $file ) {
	if ( $file !== __FILE__ )
		require_once( $file );
}

// return plugin version
function agonistes_version(): string {
	$plugin_data = get_plugin_data( __FILE__ );
	return $plugin_data['Version'];
}

// load plugin translations
add_action( 'init', function(): void {
	load_plugin_textdomain( 'agonistes', FALSE, basename( __DIR__ ) . '/languages' );
} );

// replace default gallery
add_filter( 'pre_do_shortcode_tag', function( string|bool $return, string $tag, array|string $attr, array $m ): string|bool {
	if ( $tag !== 'gallery' )
		return FALSE;
	if ( !is_array( $attr ) )
		return FALSE;
	if ( !shortcode_exists( 'fusion_gallery' ) )
		return FALSE;
	$shortcode = '[fusion_gallery';
	if ( array_key_exists( 'ids', $attr ) )
		$shortcode .= sprintf( ' image_ids="%s"', $attr['ids'] );
	if ( array_key_exists( 'columns', $attr ) )
		$shortcode .= sprintf( ' columns="%d"', $attr['columns'] );
	$shortcode .= ' lightbox="yes"][/fusion_gallery]';
	return do_shortcode( $shortcode );
}, 10, 4 );

// accept tag__in variable in main query
add_filter( 'query_vars', function( array $qvars ): array {
	$qvars[] = 'tag__in';
	return $qvars;
} );
add_action( 'pre_get_posts', function( WP_Query $query ): void {
	if ( !$query->is_main_query() )
		return;
	if ( !array_key_exists( 'tag__in', $query->query ) )
		return;
	$query->query['tag__in'] = explode( '-', $query->query['tag__in'] );
	$query->parse_query( $query->query );
} );

// hide closed comment section
add_action( 'wp_head', function(): void {
	if ( !is_singular() )
		return;
	if ( comments_open() )
		return;
	echo '<style type="text/css">#comments.comments-container{display: none;}</style>' . "\n";
} );
