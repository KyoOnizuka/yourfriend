<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Pen
 */

$content_id = pen_post_id();

pen_sidebar_get( 'sidebar-left', $content_id );
pen_sidebar_get( 'sidebar-right', $content_id );
?>
						</div><!-- #content -->
					</div><!-- .pen_container -->
<?php
pen_html_jump_menu( 'site', $content_id );
?>
				</div><!-- #pen_section -->
<?php
pen_sidebar_get( 'sidebar-bottom', $content_id );

$footer_display = get_post_meta( $content_id, 'pen_site_footer_display_override', true );
if ( ! $footer_display || 'default' === $footer_display ) {
	$footer_display = pen_option_get( 'site_footer_display' );
}
if ( $footer_display && 'no' !== $footer_display ) {
	$footer_display = true;
} else {
	$footer_display = false;
}

$menu_html    = pen_html_menu( 'secondary', $content_id );
$phone_html   = pen_html_phone( 'footer', $content_id );
$connect_html = pen_html_connect( 'footer', $content_id );

$classes_footer = array(
	'site-footer',
	( ! $footer_display ) ? 'pen_element_hidden' : '',
	'pen_menu_' . ( $menu_html ? 'show' : 'hide' ),
	'pen_phone_' . ( $phone_html ? 'show' : 'hide' ),
	'pen_connect_' . ( $connect_html ? 'show' : 'hide' ),
	pen_class_animation( 'footer', false, $content_id ),
	'pen_' . ( pen_option_get( 'color_footer_background_transparent' ) ? 'is_transparent' : 'not_transparent' ),
);
$classes_footer = implode( ' ', array_filter( $classes_footer ) );
?>
				<footer id="pen_footer" class="<?php echo esc_attr( $classes_footer ); ?>" role="contentinfo">
					<div class="pen_container">
<?php
pen_sidebar_get( 'sidebar-footer-left', $content_id );
?>
						<div class="pen_footer_inner">
<?php
pen_sidebar_get( 'sidebar-footer-top', $content_id );

if ( $menu_html ) {
	$classes_footer_menu = array(
		'pen_separator_' . (int) pen_option_get( 'footer_menu_separator' ),
		pen_class_animation( 'footer_menu', false, $content_id ),
	);
	$classes_footer_menu = implode( ' ', array_filter( $classes_footer_menu ) );
	?>
							<nav id="pen_footer_menu" role="navigation" class="<?php echo esc_attr( $classes_footer_menu ); ?>" aria-label="<?php esc_attr_e( 'Menu', 'pen' ); ?>">
	<?php
	echo $menu_html; /* phpcs:ignore */
	?>
							</nav>
	<?php
}
echo $phone_html; /* phpcs:ignore */

echo $connect_html; /* phpcs:ignore */

get_template_part( 'partials/content', 'copyright' );

pen_sidebar_get( 'sidebar-footer-bottom', $content_id );
?>
						</div>
<?php
pen_sidebar_get( 'sidebar-footer-right', $content_id );
?>
					</div>
<?php
pen_html_jump_menu( 'footer', $content_id );
?>
				</footer><!-- #pen_footer -->
<?php
if ( ! is_customize_preview() ) {
	pen_html_jump_menu( 'color_schemes', $content_id );
	pen_html_jump_menu( 'font_presets', $content_id );
}
?>
			</div><!-- .pen_wrapper -->
		</div><!-- #page -->
<?php
pen_html_back_to_top();

wp_footer();
?>
	</body>
</html>
<?php
ob_end_flush();
