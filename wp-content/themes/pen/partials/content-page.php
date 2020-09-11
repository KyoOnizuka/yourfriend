<?php
/**
 * Template part for displaying page content in page.php.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Pen
 */

$content_id      = pen_post_id();
$pen_is_singular = true;
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
	'page-title',
	'pen_content_title',
	pen_class_lists( 'title_display_override', $content_id ),
);
$classes_title = implode( ' ', array_filter( $classes_title ) );
the_title(
	sprintf(
		'<h1 class="%1$s">',
		esc_attr( $classes_title )
	),
	'</h1>'
);

echo pen_html_share( 'header', $content_id ); /* phpcs:ignore */
?>
	</header><!-- .pen_content_header -->
<?php
ob_start();
the_content();
$content = trim( ob_get_clean() );

ob_start();
get_template_part( 'partials/content', 'thumbnail' );
$thumbnail = trim( ob_get_clean() );

ob_start();
echo $thumbnail; /* phpcs:ignore */
if ( $content ) {
	?>
	<div class="pen_content_wrapper pen_inside">
	<?php
	echo $content; /* phpcs:ignore */
	?>
	</div>
	<?php
}

pen_html_pagination_content( $content_id );

$classes_content = array(
	'entry-content',
	'pen_content',
	'clearfix',
	pen_class_lists( 'summary_display_override', $content_id ),
	$thumbnail ? 'pen_has_thumbnail' : 'pen_without_thumbnail',
);
$classes_content = implode( ' ', array_filter( $classes_content ) );

$content = trim( ob_get_clean() );
if ( $content ) {
	?>
	<div class="<?php echo esc_attr( $classes_content ); ?>">
	<?php
	pen_sidebar_get( 'sidebar-content-top', $content_id );
	echo $content; /* phpcs:ignore */
	echo pen_html_share( 'content', $content_id ); /* phpcs:ignore */
	pen_sidebar_get( 'sidebar-content-bottom', $content_id );
	?>
	</div><!-- .pen_content -->
	<?php
}

$edit_link    = pen_option_get( 'content_button_edit_display' ) ? get_edit_post_link( $content_id ) : '';
$social_share = pen_html_share( 'footer', $content_id );
if ( $edit_link || $social_share ) {
	$classes_footer = array(
		'entry-footer',
		'pen_content_footer',
		pen_class_lists( 'footer_display_override', $content_id ),
	);
	$classes_footer = implode( ' ', array_filter( $classes_footer ) );
	?>
	<footer class="<?php echo esc_attr( $classes_footer ); ?>">
		<div class="pen_actions">
	<?php
	if ( $edit_link ) {
		?>
			<span class="edit-link">
				<a href="<?php echo esc_url( $edit_link ); ?>" class="pen_button post-edit-link">
		<?php
		echo wp_kses_post(
			sprintf(
				'%s%s',
				esc_html__( 'Edit', 'pen' ),
				the_title(
					'<span class="pen_element_hidden"> &rarr; "',
					'"</span>',
					false
				)
			)
		);
		?>
				</a>
			</span>
		<?php
	}
	echo $social_share; /* phpcs:ignore */
	?>
		</div>
	</footer><!-- .pen_content_footer -->
	<?php
}

echo pen_html_configuration_overview( $content_id ); /* phpcs:ignore */

pen_html_jump_menu( 'content', $content_id );
?>
</article><!-- #post-<?php echo (int) $content_id; /* phpcs:ignore */ ?> -->
