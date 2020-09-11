<?php
/**
 * Template part for displaying results in search pages.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Pen
 */

$content_id      = pen_post_id();
$pen_is_singular = false;
?>
<article id="post-<?php echo (int) $content_id; /* phpcs:ignore */ ?>" <?php echo pen_post_classes( array(), $content_id, 'return_string', $pen_is_singular ); /* phpcs:ignore */ ?>>
<?php
$background_image_dynamic = pen_content_title_background( $pen_is_singular, $content_id );

$classes_header = array(
	'entry-header',
	'pen_content_header',
	pen_class_lists( 'header_display_override', $content_id ),
);
$classes_header = implode( ' ', array_filter( $classes_header ) );

?>
	<header class="<?php echo esc_attr( $classes_header ); ?>"<?php if ( $background_image_dynamic ) { echo ' style="background-image:url(' . $background_image_dynamic . ');background-size:cover"'; /* phpcs:ignore */ } ?>>
<?php
$classes_title = array(
	'entry-title',
	'pen_content_title',
	pen_class_lists( 'title_display_override', $content_id ),
	pen_class_animation( 'list_title', false, $content_id ),
);
$classes_title = implode( ' ', array_filter( $classes_title ) );

the_title(
	sprintf(
		'<h2 class="%1$s"><a href="%2$s" rel="bookmark">',
		esc_attr( $classes_title ),
		esc_url( get_permalink() )
	),
	'</a></h2>'
);

echo pen_html_content_information( 'header', $content_id ); /* phpcs:ignore */
?>
	</header><!-- .pen_content_header -->
<?php
$list_type = pen_option_get( 'list_type' );
if ( 'plain' !== $list_type ) {
	get_template_part( 'partials/content', 'thumbnail' );
}
?>
	<div class="entry-summary pen_summary">
<?php
if ( 'plain' === $list_type ) {
	get_template_part( 'partials/content', 'thumbnail' );
}
the_excerpt();
echo pen_html_author( array(), $content_id ); /* phpcs:ignore */
?>
	</div><!-- .pen_summary -->
<?php
get_template_part( 'partials/content', 'search-footer' );
?>
</article><!-- #post-<?php echo (int) $content_id; /* phpcs:ignore */ ?> -->
