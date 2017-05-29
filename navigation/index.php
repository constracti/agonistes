<?php

if ( !defined( 'ABSPATH' ) )
	exit;

add_action( 'colormag_before_body_content', 'xa_navigation' );

function xa_navigation() {
	global $xa_query;
	if ( is_null( $xa_query ) )
		return;
	$href = get_permalink( get_option( 'page_for_posts' ) );
	echo sprintf( '<p id="xa-navigation" data-href="%s">', $href ) . "\n";
	$cat0 = 0;
	if ( array_key_exists( 'category_name', $xa_query ) ) {
		$cat = $xa_query['category_name'];
		$pos = strrpos( $cat, '/' );
		if ( $pos !== FALSE )
			$cat = substr( $cat, $pos + 1 );
		$cat = get_term_by( 'slug', $cat, 'category' );
		if ( $cat !== FALSE )
			$cat0 = $cat->term_id;
	}
	$cats = get_terms( [
		'taxonomy' => 'category',
		'orderby' => 'name',
		'order' => 'ASC',
		'hide_empty' => FALSE,
		'parent' => 0,
	] );
	echo '<select>' . "\n";
	echo sprintf( '<option value="">%s</option>', __( 'Categories' ) ) . "\n";
	foreach ( $cats as $cat )
		xa_navigation_category( $cat, $cat0 );
	echo '</select>' . "\n";
	$user0 = 0;
	if ( array_key_exists( 'author_name', $xa_query ) ) {
		$user = $xa_query['author_name'];
		$user = get_user_by( 'slug', $user );
		if ( $user !== FALSE )
			$user0 = $user->ID;
	}
	$authors = get_users( [
		'meta_key' => 'xa_author',
		'meta_value' => ['m', 'f'],
		'meta_compare' => 'IN',
		'orderby' => 'display_name',
		'order' => 'ASC',
	] );
	echo '<select>' . "\n";
	echo sprintf( '<option value="">%s</option>', __( 'Author' ) ) . "\n";
	foreach ( $authors as $author )
		xa_navigation_user( $author, $user0 );
	echo '</select>' . "\n";
	echo '</p>' . "\n";
?>
<script>
jQuery( document ).ready( function( $ ) {
	var p = $( '#xa-navigation' );
	var elems = p.find( 'select' );
	elems.change( function() {
		var href = undefined;
		var params = [];
		elems.each( function() {
			var elem = $( this );
			if ( elem.val() === '' )
				return;
			elem = elem.find( 'option:selected' );
			if ( href === undefined )
				href = elem.data( 'href' );
			else
				params.push( elem.data( 'slug' ) );
		} );
		if ( href === undefined )
			href = p.data( 'href' );
		else if ( params.length > 0 )
			href += '?' + params.join( '&' );
		location.href = href;
	} );
} );
</script>
<?php
}

function xa_navigation_category( WP_Term $cat, int $cat0, int $level = 0 ) {
	$indent = str_repeat( '&nbsp;', 3 );
	$value = $cat->term_id;
	$href = get_category_link( $cat->term_id );
	$slug = $cat->slug;
	$selected = selected( $cat->term_id, $cat0, FALSE );
	$html = sprintf( '%s%s (%d)', str_repeat( $indent, $level ), $cat->name, $cat->count );
	echo sprintf( '<option value="%d" data-href="%s" data-slug="category_name=%s"%s>%s</option>', $cat->term_id, $href, $slug, $selected, $html ) . "\n";
	$cats = get_terms( [
		'taxonomy' => 'category',
		'hide_empty' => FALSE,
		'parent' => $cat->term_id,
	] );
	foreach ( $cats as $cat )
		xa_navigation_category( $cat, $cat0, $level + 1 );
}

function xa_navigation_user( WP_User $user, int $user0 ) {
	$value = $user->ID;
	$href = get_author_posts_url( $user->ID );
	$slug = $user->user_nicename;
	$selected = selected( $user->ID, $user0, FALSE );
	$html = $user->display_name;
	echo sprintf( '<option value="%d" data-href="%s" data-slug="author_name=%s"%s>%s</option>', $value, $href, $slug, $selected, $html ) . "\n";
}
