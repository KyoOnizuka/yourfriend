<?php
/**
 * Template part for displaying a message that posts cannot be found.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Pen
 */

$content_id = pen_post_id();
?>
<div class="no-results not-found">
	<div class="pen_article_wrapper">
<?php
$classes_header = array(
	'page-header',
	'pen_content_header',
	pen_class_lists( 'header_display_override', $content_id ),
);
$classes_header = implode( ' ', array_filter( $classes_header ) );
?>
		<header class="<?php echo esc_attr( $classes_header ); ?>">
<?php
$classes_title = array(
	'page-title',
	'pen_content_title',
	pen_class_lists( 'title_display_override', $content_id ),
);
$classes_title = implode( ' ', array_filter( $classes_title ) );
?>
			<h1 class="<?php echo esc_attr( $classes_title ); ?>">
<?php
esc_html_e( 'Nothing Found', 'pen' );
?>
			</h1>
		</header><!-- .pen_content_header -->
		<article <?php echo pen_post_classes( array(), $content_id, 'return_string', 'is_single' ); /* phpcs:ignore */ ?>>
			<div class="page-content pen_content clearfix">
				<div class="pen_content_wrapper pen_inside">
<?php
if ( is_home() && current_user_can( 'publish_posts' ) ) {
	?>
					<p>
	<?php
	printf(
		wp_kses(
			/* Translators: URL. */
			__( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'pen' ),
			array(
				'a' => array(
					'href' => array(),
				),
			)
		),
		esc_url( admin_url( 'post-new.php' ) )
	);
	?>
					</p>
	<?php
} elseif ( is_search() ) {
	?>
					<p>
	<?php
	esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'pen' );
	?>
					</p>
	<?php
} else {
	?>
					<p>
	<?php
	esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'pen' );
	?>
					</p>
	<?php
}
?>
				</div><!-- .pen_content_wrapper -->
			</div><!-- .pen_content -->
		</article>
	</div><!-- .pen_article_wrapper -->
</div><!-- .no-results -->
