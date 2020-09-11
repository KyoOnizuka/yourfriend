<?php
/**
 * Template part for displaying posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Pen
 */

$content_id      = pen_post_id();
$pen_is_singular = pen_is_singular();
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
if ( $pen_is_singular ) {
	$classes_title = array(
		'entry-title',
		'pen_content_title',
		pen_class_lists( 'title_display_override', $content_id ),
		pen_class_animation( 'content_title', false, $content_id ),
	);
	$classes_title = implode( ' ', array_filter( $classes_title ) );

	the_title(
		sprintf(
			'<h1 class="%1$s">',
			esc_attr( $classes_title )
		),
		'</h1>'
	);
} else {
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
}

echo pen_html_content_information( 'header', $content_id ); /* phpcs:ignore */
echo pen_html_share( 'header', $content_id ); /* phpcs:ignore */

?>
	</header><!-- .pen_content_header -->
<?php
$thumbnail = '';
$list_type = pen_option_get( 'list_type' );
if ( ! $pen_is_singular && 'plain' !== $list_type ) {
	ob_start();
	get_template_part( 'partials/content', 'thumbnail' );
	$thumbnail = trim( ob_get_clean() );
	echo $thumbnail; /* phpcs:ignore */
}

$list_excerpt_display = pen_option_get( 'list_excerpt' );

ob_start();
// get_the_content() does not support shortcodes etc.
if ( $pen_is_singular || ! $list_excerpt_display ) {
	the_content();
} elseif ( ! $pen_is_singular && $list_excerpt_display ) {
	the_excerpt();
}
$content = trim( ob_get_clean() );

ob_start();
if ( $pen_is_singular || 'plain' === $list_type ) {
	ob_start();
	get_template_part( 'partials/content', 'thumbnail' );
	$thumbnail = trim( ob_get_clean() );
	echo $thumbnail; /* phpcs:ignore */
}

if ( $content ) {
	?>
	<div class="pen_content_wrapper pen_inside">
	<?php
	if ( $pen_is_singular ) {
		pen_sidebar_get( 'sidebar-content-top', $content_id );
	}
	echo $content; /* phpcs:ignore */
	if ( $pen_is_singular ) {
		pen_sidebar_get( 'sidebar-content-bottom', $content_id );
	}
	pen_html_pagination_content( $content_id );
	?>
	</div>
	<?php
}

$content = trim( ob_get_clean() );
if ( $content ) {
	$classes_content = array(
		'entry-content',
		'pen_content',
		pen_class_lists( 'summary_display_override', $content_id ),
		$thumbnail ? 'pen_with_thumbnail' : 'pen_without_thumbnail',
	);
	$classes_content = implode( ' ', array_filter( $classes_content ) );
	?>
	<div class="<?php echo esc_attr( $classes_content ); ?>">
	<?php
	echo $content; /* phpcs:ignore */
	echo pen_html_share( 'content', $content_id ); /* phpcs:ignore */
	echo pen_html_author( array(), $content_id ); /* phpcs:ignore */
	?>
	</div><!-- .pen_content -->
	<?php
}

get_template_part( 'partials/content', 'footer' );

echo pen_html_configuration_overview( $content_id ); /* phpcs:ignore */

if ( $pen_is_singular ) {
	pen_html_jump_menu( 'content', $content_id );
}
?>
</article><!-- #post-<?php echo (int) $content_id; /* phpcs:ignore */ ?> -->
