<?php

define( 'XA_DIR', get_stylesheet_directory() );
define( 'XA_URL', get_stylesheet_directory_uri() );


/* load theme textdomain */

add_action( 'after_setup_theme', function() {
	load_theme_textdomain( 'xa', XA_DIR . '/languages' );
} );

require_once( XA_DIR . '/settings/index.php' );

require_once( XA_DIR . '/city_author_metabox.php' );
require_once( XA_DIR . '/category_author.php' );

require_once( XA_DIR . '/widgets/index.php' );

require_once( XA_DIR . '/navigation/index.php' );
require_once( XA_DIR . '/questions/index.php' );
require_once( XA_DIR . '/share-post/index.php' );
require_once( XA_DIR . '/synaxari/index.php' );

add_action( 'wp_enqueue_scripts', function() {
	wp_enqueue_style( 'colormag_style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'xa-colormag', XA_URL . '/style.css', [ 'colormag_style' ] );
} );

add_action( 'wp_head', function() {
	$color = get_theme_mod( 'colormag_primary_color', '#289dcc' );
	echo sprintf( '<meta name="theme-color" content="%s" />', $color ) . "\n";
} );


/* dynamically add, delete and move content */

add_action( 'admin_enqueue_scripts', function( string $hook ) {
	wp_enqueue_script( 'xa-control', XA_URL . '/control.js', [ 'jquery' ] );
} );


function colormag_colored_category() {
	$categories = get_the_category();
	$output = '';
	if ( !empty( $categories ) ) {
		$output .= '<div class="above-entry-meta">' . "\n";
		$output .= '<span class="cat-links">' . "\n";
		foreach ( $categories as $category ) {
			$color_code = colormag_category_color( $category->term_id );
			if ( empty( $color_code ) ) {
				$style = '';
			} else {
				$style = colormag_category_color( $category->term_id );
				$style = sprintf( 'background-color: %s;', $style );
				$style = sprintf( ' style="%s"', esc_attr( $style ) );
			}
			$output .= sprintf( '<a href="%s"%s rel="category tag">%s</a>',
				get_category_link( $category->term_id ),
				$style,
				esc_html( $category->name )
			) . "\n";
		}
		$output .='</span></div>';
		echo trim($output, $separator);
	}
}

function colormag_footer_copyright() {
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
         <span class="comments"><?php comments_popup_link( __( '<i class="fa fa-comment"></i> 0 Comments', 'colormag' ), __( '<i class="fa fa-comment"></i> 1 Comment', 'colormag' ), __( '<i class="fa fa-comments"></i> % Comments', 'colormag' ) ); ?></span>
      <?php }
   	$tags_list = get_the_tag_list( '<span class="tag-links"><i class="fa fa-tags"></i>', __( ', ', 'colormag' ), '</span>' );
   	if ( $tags_list ) echo $tags_list;

   	edit_post_link( __( 'Edit', 'colormag' ), '<span class="edit-link"><i class="fa fa-edit"></i>', '</span>' );

   	echo '</div>';
   endif;
}

function xa_time_span() {
	$html = sprintf( '<time class="entry-date published" datetime="%s">%s</time>',
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() )
	);
	$html = '<i class="fa fa-calendar-o"></i> ' . $html;
	if ( get_post_status() === 'publish' || current_user_can( 'read_post', get_the_ID() ) )
		$html = sprintf( '<a href="%s" title="%s" rel="bookmark">%s</a>',
			esc_url( get_permalink() ),
			esc_attr( get_the_time() ),
			$html
		);
	else
		$html = sprintf( '<a href="#" title="%s" onclick="return false;">%s</a>',
			esc_attr( get_the_time() ),
			$html
		);
	printf( __( '<span class="posted-on">%s</span>', 'colormag' ), $html );
}

function xa_author_span() {
	if ( !in_array( get_the_author_meta( 'xa_author' ), [ 'm', 'f' ] ) )
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
	if ( get_post_status() === 'publish' || current_user_can( 'read_post', get_the_ID() ) ) {
		echo '<span class="comments"><i class="fa fa-comment"></i>';
		comments_popup_link( '0', '1', '%' );
		echo '</span>' . "\n";
	}
}
