<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

add_action( 'pre_get_posts', function( WP_Query $query ) {
	$q = $query->query;
	if ( is_admin() && ( !array_key_exists( 'xa_share_post', $q ) || $q['xa_share_post'] !== 'force' ) )
		return;
	if ( array_key_exists( 'xa_share_post', $q ) && $q['xa_share_post'] === TRUE )
		return;
	if ( array_key_exists( 'author__not_in', $q ) )
		return;
	if ( array_key_exists( 'meta_key', $q ) )
		return;
	// backup query global variable
	if ( $query->is_main_query() ) {
		global $xa_query;
		if ( $query->is_archive || $query->is_posts_page )
			$xa_query = $query->query;
		else
			$xa_query = NULL;
	}
	// populate array with author ids
	$as = [];
	if ( array_key_exists( 'author', $q ) ) {
		$author_ids = array_map( 'intval', explode( ',', $q['author'] ) );
		foreach ( $author_ids as $author_id ) {
			if ( $author_id < 0 )
				return;
			$author = get_user_by( 'id', $author_id );
			if ( $author !== FALSE )
				$as[] = $author->ID;
		}
		unset( $q['author'] );
	} elseif ( array_key_exists( 'author_name', $q ) ) {
		$author_name = $q['author_name'];
		$author = get_user_by( 'slug', $author_name );
		if ( $author !== FALSE )
			$as[] = $author->ID;
		unset( $q['author_name'] );
	} elseif ( array_key_exists( 'author__in', $q ) ) {
		$author_ids = array_map( 'intval', $q['author__in'] );
		foreach ( $author_ids as $author_id ) {
			if ( $author_id < 0 )
				return;
			$author = get_user_by( 'id', $author_id );
			if ( $author !== FALSE )
				$as[] = $author->ID;
		}
		unset( $q['author__in'] );
	} else {
		return;
	}
	// populate array with meta values
	$vs = [];
	foreach ( $as as $a ) {
		$vs[] = $a;
		$meta = get_user_meta( $a, 'xa_author', TRUE );
		if ( in_array( $meta, [ 'm', 'f' ] ) )
			$vs[] = $meta;
	}
	$vs = array_unique( $vs );
	// first query
	$q1 = $q;
	$q1['author__in'] = $as;
	$q1['nopaging'] = TRUE;
	$q1['fields'] = 'ids';
	$q1['xa_share_post'] = TRUE;
	$p1 = get_posts( $q1 );
	// second query
	$q2 = $q;
	$q2['meta_key'] = 'xa_share_post';
	$q2['meta_compare'] = 'IN';
	$q2['meta_value'] = $vs;
	$q1['nopaging'] = TRUE;
	$q2['fields'] = 'ids';
	$q2['xa_share_post'] = TRUE;
	$p2 = get_posts( $q2 );
	// initial query
	$ps = array_merge( $p1, $p2 );
	$ps = array_unique( $ps, SORT_NUMERIC );
	if ( !empty( $ps ) )
		$q['post__in'] = $ps;
	else
		$q['p'] = -1;
	$q['xa_share_post'] = TRUE;
	$query->parse_query( $q );
} );
