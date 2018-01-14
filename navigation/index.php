<?php

if ( !defined( 'ABSPATH' ) )
	exit;

add_action( 'colormag_before_body_content', 'xa_navigation' );

function xa_navigation() {
	global $xa_query;
	if ( is_null( $xa_query ) )
		return;
	echo sprintf( '<p id="xa-navigation" data-href="%s">',
		esc_url( get_permalink( get_option( 'page_for_posts' ) ) )
	) . "\n";
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
	echo '<select style="min-width: calc( 33.3% - 3px );">' . "\n";
	echo sprintf( '<option value="">%s</option>', esc_html__( 'Author' ) ) . "\n";
	foreach ( $authors as $author )
		xa_navigation_user( $author, $user0 );
	echo '</select>' . "\n";
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
	echo '<select style="min-width: calc( 33.3% - 3px );">' . "\n";
	echo sprintf( '<option value="">%s</option>', esc_html__( 'Categories' ) ) . "\n";
	foreach ( $cats as $cat )
		xa_navigation_category( $cat, $cat0 );
	echo '</select>' . "\n";
	$tag0 = 0;
	if ( array_key_exists( 'tag', $xa_query ) ) {
		$tag = $xa_query['tag'];
		$tag = get_term_by( 'slug', $tag, 'post_tag' );
		if ( $tag !== FALSE )
			$tag0 = $tag->term_id;
	}
	$tags = get_terms( [
		'taxonomy' => 'post_tag',
		'orderby' => 'name',
		'order' => 'ASC',
		'hide_empty' => FALSE,
	] );
	echo '<select style="min-width: calc( 33.3% - 3px );">' . "\n";
	echo sprintf( '<option value="">%s</option>', esc_html__( 'Tags' ) ) . "\n";
	foreach ( $tags as $tag )
		xa_navigation_tag( $tag, $tag0 );
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
		$( this ).val( $( this ).prop( 'defaultValue' ) );
		location.href = href;
	} );
} );
</script>
<?php
}

function xa_navigation_user( WP_User $user, int $user0 ) {
	echo sprintf( '<option value="%d" data-href="%s" data-slug="author_name=%s"%s>%s</option>',
		$user->ID,
		esc_url( get_author_posts_url( $user->ID ) ),
		esc_attr( $user->user_nicename ),
		selected( $user->ID, $user0, FALSE ),
		esc_html( $user->display_name )
	) . "\n";
}

function xa_navigation_category( WP_Term $cat, int $cat0, int $level = 0 ) {
	$indent = str_repeat( '&nbsp;', 3 );
	echo sprintf( '<option value="%d" data-href="%s" data-slug="category_name=%s"%s>%s%s</option>',
		$cat->term_id,
		esc_url( get_category_link( $cat->term_id ) ),
		esc_attr( $cat->slug ),
		selected( $cat->term_id, $cat0, FALSE ),
		str_repeat( $indent, $level ),
		esc_html( sprintf( '%s (%d)', $cat->name, $cat->count ) )
	) . "\n";
	$cats = get_terms( [
		'taxonomy' => 'category',
		'hide_empty' => FALSE,
		'parent' => $cat->term_id,
	] );
	foreach ( $cats as $cat )
		xa_navigation_category( $cat, $cat0, $level + 1 );
}

function xa_navigation_tag( WP_Term $tag, int $tag0 ) {
	echo sprintf( '<option value="%d" data-href="%s" data-slug="tag=%s"%s>%s</option>',
		$tag->term_id,
		esc_url( get_term_link( $tag->term_id ) ),
		esc_attr( $tag->slug ),
		selected( $tag->term_id, $tag0, FALSE ),
		esc_html( sprintf( '%s (%d)', $tag->name, $tag->count ) )
	) . "\n";
}

FALSE && add_action( 'wp', function() {
	if ( !current_user_can( 'administrator' ) )
		return;
	global $wp_filter;
	foreach ( $wp_filter as $key => $value )
		if ( strpos( $key, 'description' ) !== FALSE )
			var_dump( $key, $value );
	exit;
} );

add_filter( 'term_description', 'do_shortcode' );

add_shortcode( 'xa-navigation-term', function( $atts ) {
	if ( !current_user_can( 'administrator' ) ) # TODO delete
		return '';
	if ( !is_category() && !is_tag() && !is_tax() )
		return '';
	$cterm = get_queried_object();
	$path = [];
	$term = $cterm;
	for ( $term = $cterm; !is_null( $term ); $term = get_term( $term->parent ) ) {
		$path[] = $term;
		if ( $term->term_id === intval( $atts['term'] ) )
			break;
	}
	$term = array_pop( $path );
	if ( $term->term_id === $cterm->term_id )
		$class = 'xa-navigation-term-current';
	else
		$class = 'xa-navigation-term-ancestor';
	$html = sprintf( '<p><a class="%s" href="%s">%s</a></p>', $class, esc_url( get_term_link( $term ) ), esc_html( $term->name ) ) . "\n";
	while ( !is_null( $term ) ) {
		$next = array_pop( $path );
		$terms = get_terms( [
			'taxonomy' => $term->taxonomy,
			'hide_empty' => FALSE,
			'parent' => $term->term_id,
		] );
		$items = [];
		foreach ( $terms as $term ) {
			if ( $term->term_id === $cterm->term_id )
				$class = 'xa-navigation-term-current';
			elseif ( !is_null( $next ) && $term->term_id === $next->term_id )
				$class = 'xa-navigation-term-ancestor';
			else
				$class = '';
			$items[] = sprintf( '<a class="%s" href="%s">%s</a>', $class, esc_url( get_term_link( $term ) ), esc_html( $term->name ) );
		}
		$html .= '<p>' . implode( ' | ', $items ) . '</p>' . "\n";
		$term = $next;
	}
	return $html;
} );
