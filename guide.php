<?php

if ( !defined( 'ABSPATH' ) )
	exit;

add_action( 'admin_footer', function(): void {
	$screen = get_current_screen();
	// https://developer.wordpress.org/reference/classes/wp_screen/
	if ( is_null( $screen ) )
		return;
	if ( $screen->base !== 'post' )
		return;
	// action '' or 'add'
	if ( $screen->post_type !== 'post' )
		return;
	if ( $screen->is_block_editor() )
		return;
	$style = esc_attr( implode( [
		'margin: 8px;',
		'padding: 8px;',
		'border: 1px solid #c3c4c7;',
		'border-left: 4px solid #72aee6;',
	] ) );
	$href = esc_url_raw( 'https://docs.google.com/document/d/1VvbCTgvP3M4XZJNeitpVTv8dIX-BOBOmablNBHVHX_M/edit?usp=sharing' );
	$icon = '<span class="fas fa-fw fa-info-circle"></span>';
	$text = esc_html( 'Οδηγός Συγγραφής Άρθρου' );
?>
<script>
(function($) {
	$(document).on('ready', function() {
		const html = '<div style="<?= $style ?>"><?= $icon ?> <a href="<?= $href ?>" target="_blank"><?= $text ?></a></div>';
		$('#submitdiv .inside #minor-publishing').prepend(html);
	});
})(jQuery);
</script>
<?php
} );
