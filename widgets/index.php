<?php

if ( !defined( 'ABSPATH' ) )
	exit;

require_once( XA_DIR . '/widgets/slider.php' );
require_once( XA_DIR . '/widgets/central.php' );
require_once( XA_DIR . '/widgets/beside.php' );
require_once( XA_DIR . '/widgets/synaxari.php' );

add_action( 'admin_enqueue_scripts', function( string $hook ) {
	if ( $hook !== 'widgets.php' )
		return;
	wp_enqueue_script( 'xa-control', XA_URL . '/control.js', ['jquery'] );
} );

# TODO xa_*_span definition location

function xa_image_div( $widget ) {
	switch ( $widget ) {
		case 'beside':
			$size = 'colormag-highlighted-post';
			$png = '/img/highlights-featured-image.png';
			break;
		case 'slider':
		case 'synaxari':
			$size = 'colormag-featured-image';
			$png = '/img/slider-featured-image.png';
			break;
	}
	$image = '';
	$image .= '<div class="xa-image">' . "\n";
	$title = get_the_title();
	$permalink = get_permalink();
	if( has_post_thumbnail() ) {
		$image .= sprintf( '<a href="%s" title="%s">', esc_url( $permalink ), esc_attr( $title ) ) . "\n";
		$image .= get_the_post_thumbnail( $post->ID, $size, array( 'title' => esc_attr( $title ), 'alt' => esc_attr( $title ) ) ) . "\n";
		$image .= '</a>' . "\n";
	} else {
		$image .= sprintf( '<a href="%s" title="%s">', esc_url( $permalink ), esc_attr( $title ) ) . "\n";
		$image .= sprintf( '<img src="%s" title="%s" alt="%s" />',
			esc_url( get_template_directory_uri() . $png ), esc_attr( $title ), esc_attr( $title )
		) . "\n";
		$image .= '</a>' . "\n";
	}
	$image .= '</div>' . "\n";
	echo $image;
}

function xa_content_div( $widget ) {
	switch ( $widget ) {
		case 'beside':
		case 'selector':
			$prefix = 'article';
			break;
		case 'slider':
		case 'synaxari':
			$prefix = 'slide';
			break;
	}
?>
<div class="<?php echo $prefix; ?>-content">
	<?php colormag_colored_category(); ?>
	<h3 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute();?>"><?php the_title(); ?></a></h3>
	<div class="below-entry-meta"><?php if ( $widget !== 'synaxari' ) { xa_time_span(); xa_author_span(); xa_comments_span(); } ?></div>
</div>
<?php
}
