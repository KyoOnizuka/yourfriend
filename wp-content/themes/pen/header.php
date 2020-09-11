<?php
/**
 * The header for our theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Pen
 */

$content_id      = pen_post_id();
$pen_is_singular = pen_is_singular();

ob_start();
?><!doctype html>
<html <?php language_attributes(); ?> class="no-js">
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="profile" href="https://gmpg.org/xfn/11">
<?php
wp_head();
?>
	</head>
	<body <?php body_class(); ?>>
<?php
if ( function_exists( 'wp_body_open' ) ) {
	wp_body_open();
} else {
	do_action( 'wp_body_open' );
}
?>
		<div id="page" class="site">
<?php
pen_html_loading_spinner();
?>
			<div class="pen_wrapper">
				<a class="screen-reader-shortcut screen-reader-text" href="#content">
<?php
esc_html_e( 'Skip to content', 'pen' );
?>
				</a>
<?php
$header_primary = false;
if ( pen_sidebar_check( 'sidebar-header-primary', $content_id ) && is_active_sidebar( 'sidebar-header-primary' ) ) {
	$header_primary = true;
}
$header_secondary = false;
if ( pen_sidebar_check( 'sidebar-header-secondary', $content_id ) && is_active_sidebar( 'sidebar-header-secondary' ) ) {
	$header_secondary = true;
}

$logo_html         = pen_html_logo( 'header', $content_id );
$phone_html        = pen_html_phone( 'header', $content_id );
$connect_html      = pen_html_connect( 'header', $content_id );
$button_users_html = pen_html_button_users( 'header', $content_id );

$search_html     = pen_html_search_box( $content_id );
$search_display  = 'hide';
$search_location = get_post_meta( $content_id, 'pen_content_search_location_override', true );
if ( ! $search_location || 'default' === $search_location ) {
	$search_location = pen_option_get( 'search_location' );
}
if ( $search_html ) {
	if ( 'header' === $search_location ) {
		$search_display = 'show';
	} elseif ( 'content' === $search_location ) {
		$search_display = 'show_toolbar';
	}
}

$navigation_html = pen_html_navigation_main( 'primary', $content_id );

$classes_header = array(
	'site-header',
	'pen_logo_' . sanitize_html_class( $logo_html ? 'show' : 'hide' ),
	'pen_phone_' . sanitize_html_class( $phone_html ? 'show' : 'hide' ),
	'pen_connect_' . sanitize_html_class( $connect_html ? 'show' : 'hide' ),
	'pen_search_' . sanitize_html_class( $search_display ),
	'pen_button_users_' . sanitize_html_class( $button_users_html ? 'show' : 'hide' ),
	// <body> may also get .pen_navigation_hide per content (never the .pen_navigation_show).
	'pen_navigation_' . ( $navigation_html ? 'show' : 'hide' ),
	pen_class_animation( 'header', false, $content_id ),
);

$cart_html = '';
if ( PEN_THEME_HAS_WOOCOMMERCE ) {
	$cart_html        = pen_html_woocommerce_header_cart( 'header', $content_id );
	$classes_header[] = 'pen_cart_' . sanitize_html_class( $cart_html ? 'show' : 'hide' );
}

$classes_header = implode( ' ', array_filter( $classes_header ) );
?>
				<header id="pen_header" class="<?php echo esc_attr( $classes_header ); ?>" role="banner">
					<div class="pen_header_inner">
<?php
$header_display = get_post_meta( $content_id, 'pen_site_header_display_override', true );
if ( ! $header_display || 'default' === $header_display ) {
	$header_display = pen_option_get( 'site_header_display' );
}
if ( $header_display && 'no' !== $header_display ) {
	$header_display = true;
} else {
	$header_display = false;
}
?>
						<div class="pen_header_main<?php echo ( ! $header_display ) ? ' pen_element_hidden' : ''; ?>">
							<div class="pen_container">
<?php
$add_heading = is_home();
if ( $add_heading ) {
	?>
								<h1 id="pen_site_title">
	<?php
} else {
	?>
								<div id="pen_site_title">
	<?php
}

echo $logo_html; /* phpcs:ignore */

echo pen_html_site_title( 'header', $content_id ); /* phpcs:ignore */

if ( $add_heading ) {
	?>
								</h1>
	<?php
} else {
	?>
								</div>
	<?php
}

if ( $header_primary || $header_secondary || $phone_html || $connect_html || ( 'header' === $search_location && $search_html ) || $cart_html || $button_users_html ) {
	?>
								<div class="pen_header_wrap">
	<?php
	pen_sidebar_get( 'sidebar-header-primary', $content_id );

	echo $phone_html; /* phpcs:ignore */

	echo $connect_html; /* phpcs:ignore */

	if ( $search_html && 'header' === $search_location ) {
		$classes_search = array(
			'pen_search',
			pen_class_animation( 'search_header', false, $content_id ),
		);
		$classes_search = implode( ' ', array_filter( $classes_search ) );
		?>
									<div id="pen_header_search" class="<?php echo esc_attr( $classes_search ); ?>">
		<?php
		echo $search_html; /* phpcs:ignore */
		?>
									</div>
		<?php
	}

	echo $cart_html; /* phpcs:ignore */

	if ( $button_users_html ) {
		$classes_button_users = array(
			'pen_button_users',
			pen_class_animation( 'button_users_header', false, $content_id ),
		);
		$classes_button_users = implode( ' ', array_filter( $classes_button_users ) );
		?>
									<div id="pen_header_button_users" class="<?php echo esc_attr( $classes_button_users ); ?>">
		<?php
		echo $button_users_html; /* phpcs:ignore */
		?>
									</div>
		<?php
	}

	pen_sidebar_get( 'sidebar-header-secondary', $content_id );
	?>
								</div><!-- .pen_header_wrap -->
	<?php
}
?>
							</div><!-- .pen_container -->
<?php
pen_html_jump_menu( 'header', $content_id );
?>
						</div><!-- .pen_header_main -->
<?php
// Adds the main navigation menu.
echo $navigation_html; /* phpcs:ignore */
?>
					</div><!-- .pen_header_inner -->
<?php
if ( $search_html && 'content' === $search_location ) {
	$classes_search = pen_class_animation( 'search_bar', false, $content_id );
	?>
					<div id="pen_search" class="<?php echo esc_attr( $classes_search ); ?>">
						<div class="pen_container">
	<?php
	pen_sidebar_get( 'sidebar-search-top', $content_id );
	?>
						</div>
						<div class="pen_container">
	<?php
	pen_sidebar_get( 'sidebar-search-left', $content_id );
	?>
							<div id="pen_search_form">
	<?php
	echo $search_html; /* phpcs:ignore */
	?>
							</div>
	<?php
	pen_sidebar_get( 'sidebar-search-right', $content_id );
	?>
						</div>
						<div class="pen_container">
	<?php
	pen_sidebar_get( 'sidebar-search-bottom', $content_id );

	pen_html_jump_menu( 'search_bar', $content_id );
	?>
						</div>
					</div>
	<?php
}
?>
				</header>
				<div id="pen_section">
<?php
pen_sidebar_get( 'sidebar-top', $content_id );
?>
					<div class="pen_container">
						<div id="content" class="site-content clearfix">
