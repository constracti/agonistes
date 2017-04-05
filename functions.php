<?php

define( 'XA_DIR', get_stylesheet_directory() );
define( 'XA_URL', get_stylesheet_directory_uri() );

require_once( XA_DIR . '/main.php' );

require_once( XA_DIR . '/city-widget.php' );

add_action( 'after_setup_theme', function() {
	load_theme_textdomain( 'xa', XA_DIR );
} );

add_action( 'wp_enqueue_scripts', function() {
	wp_enqueue_style( 'colormag_style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'xa-colormag-style', XA_URL . '/style.css', array( 'colormag_style' ) );
} );

function colormag_footer_copyright() {
}

function xa_time_span() {
	$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ), esc_html( get_the_date() )
	);
	printf( __( '<span class="posted-on"><a href="%1$s" title="%2$s" rel="bookmark"><i class="fa fa-calendar-o"></i> %3$s</a></span>', 'colormag' ),
		esc_url( get_permalink() ), esc_attr( get_the_time() ), $time_string
	);
}

function xa_author_span() {
	if ( ! in_array( get_the_author_meta( 'xa_author' ), array( 'm', 'f' ) ) )
		return;
?>
<span class="byline">
	<span class="author vcard">
		<i class="fa fa-user"></i>
		<a class="url fn n" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" title="<?php echo get_the_author(); ?>"><?php echo esc_html( get_the_author() ); ?></a>
	</span>
</span>
<?php
}

function xa_comments_span() {
?>
<span class="comments"><i class="fa fa-comment"></i><?php comments_popup_link( '0', '1', '%' );?></span>
<?php
}

function colormag_entry_meta() {
   if ( 'post' == get_post_type() ) :
   	echo '<div class="below-entry-meta">';
   	?>

      <?php
      $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
      if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
         $time_string .= '<time class="updated" datetime="%3$s">%4$s</time>';
      }
      $time_string = sprintf( $time_string,
         esc_attr( get_the_date( 'c' ) ),
         esc_html( get_the_date() ),
         esc_attr( get_the_modified_date( 'c' ) ),
         esc_html( get_the_modified_date() )
      );
   	printf( __( '<span class="posted-on"><a href="%1$s" title="%2$s" rel="bookmark"><i class="fa fa-calendar-o"></i> %3$s</a></span>', 'colormag' ),
   		esc_url( get_permalink() ),
   		esc_attr( get_the_time() ),
   		$time_string
   	); ?>

<?php xa_author_span(); ?>

      <?php
      if ( ! post_password_required() && comments_open() ) { ?>
         <span class="comments"><?php comments_popup_link( __( '<i class="fa fa-comment"></i> 0 Comment', 'colormag' ), __( '<i class="fa fa-comment"></i> 1 Comment', 'colormag' ), __( '<i class="fa fa-comments"></i> % Comments', 'colormag' ) ); ?></span>
      <?php }
   	$tags_list = get_the_tag_list( '<span class="tag-links"><i class="fa fa-tags"></i>', __( ', ', 'colormag' ), '</span>' );
   	if ( $tags_list ) echo $tags_list;

   	edit_post_link( __( 'Edit', 'colormag' ), '<span class="edit-link"><i class="fa fa-edit"></i>', '</span>' );

   	echo '</div>';
   endif;
}
