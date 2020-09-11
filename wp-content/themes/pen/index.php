<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
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
	if ( is_home() && ! is_front_page() ) {
		?>
			<header class="pen_element_hidden">
				<h1 class="page-title pen_content_title">
		<?php
		single_post_title();
		?>
				</h1>
			</header>
		<?php
	}
	?>
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
if ( ! pen_is_singular() ) {
	pen_html_jump_menu( 'list', $content_id );
}
?>
</div><!-- #primary -->
<?php
get_footer();
