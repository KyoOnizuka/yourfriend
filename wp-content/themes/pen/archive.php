<?php
/**
 * The template for displaying archive pages.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Pen
 */

$content_id = pen_post_id();

get_header(); ?>
<div id="primary" class="content-area">
	<main id="main" class="<?php echo esc_attr( pen_content_classes( $content_id ) ); ?>" role="main">
		<div class="pen_article_wrapper">
<?php
if ( have_posts() ) {
	$classes_header = array(
		'page-header',
		'pen_content_header',
		pen_class_lists( 'list_page_header', $content_id ),
	);
	$classes_header = implode( ' ', array_filter( $classes_header ) );
	?>
			<header class="<?php echo esc_attr( $classes_header ); ?>">
				<h1 class="page-title pen_content_title">
	<?php
	echo get_the_archive_title(); /* phpcs:ignore */
	?>
				</h1>
	<?php
	the_archive_description( '<div class="archive-description">', '</div>' );
	?>
			</header><!-- .pen_content_header -->
			<div id="<?php echo esc_attr( pen_html_id_layout( $content_id ) ); ?>" class="pen_articles">
	<?php
	/* Start the Loop */
	while ( have_posts() ) {
		the_post();
		/**
		 * Include the Post-Type-specific template for the content.
		 * If you want to override this in a child theme, then include a file
		 * called content-___.php (where ___ is the Post Type name) and that will be used instead.
		 */
		get_template_part( 'partials/content', get_post_type() );
	}
	?>
			</div>
	<?php
	get_template_part( 'partials/content', 'pagination' );
} else {
	get_template_part( 'partials/content', 'none' );
}
?>
		</div>
	</main>
<?php
pen_html_jump_menu( 'list', $content_id );
?>
</div><!-- #primary -->
<?php
get_footer();
