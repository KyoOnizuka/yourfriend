<?php
/**
 * Template part for displaying the content pagination.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Pen
 */

$content_id = pen_post_id();

ob_start();
if ( function_exists( 'wp_pagenavi' ) ) {
	wp_pagenavi();
} else {
	the_posts_navigation();
}
$pager = trim( ob_get_clean() );
if ( $pager ) {
	$classes = array(
		'clearfix',
		pen_class_animation( 'list_pager', false, $content_id ),
		pen_option_get( 'list_infinite_scrolling' ) ? 'pen_element_hidden' : '',
	);
	$classes = implode( ' ', array_filter( $classes ) );
	?>
	<div id="pen_pager" class="<?php echo esc_attr( $classes ); ?>">
	<?php
	echo $pager; /* phpcs:ignore */
	?>
	</div>
	<?php
}
