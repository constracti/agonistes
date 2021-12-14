<?php

/*
 * Plugin Name: Charoumenoi Agonistes
 * Plugin URI: https://github.com/constracti/agonistes
 * Description: Customization plugin of agonistes.gr website.
 * Author: constracti
 * Version: 0.3
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
	return strval( time() ); // TODO delete line
	$plugin_data = get_plugin_data( __FILE__ );
	return $plugin_data['Version'];
}

// load plugin translations
add_action( 'init', function(): void {
	load_plugin_textdomain( 'agonistes', FALSE, basename( __DIR__ ) . '/languages' );
} );
