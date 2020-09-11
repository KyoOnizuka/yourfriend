<?php
/**
 * Widget Areas.
 *
 * @package Pen
 */

defined( 'ABSPATH' ) || die();

if ( ! function_exists( 'pen_sidebars' ) ) {
	/**
	 * Sidebars.
	 *
	 * @since Pen 1.3.1
	 * @return array
	 */
	function pen_sidebars() {
		return array(
			'left'               => array(
				'name'        => __( 'Left Sidebar', 'pen' ),
				'description' => '',
			),
			'right'              => array(
				'name'        => __( 'Right Sidebar', 'pen' ),
				'description' => '',
			),
			'header-primary'     => array(
				'name' => sprintf(
					'%1$s &rarr; %2$s',
					__( 'Header', 'pen' ),
					__( 'Primary', 'pen' )
				),
			),
			'header-secondary'   => array(
				'name' => sprintf(
					'%1$s &rarr; %2$s',
					__( 'Header', 'pen' ),
					__( 'Secondary', 'pen' )
				),
			),
			'search-top'         => array(
				'name' => sprintf(
					'%1$s &rarr; %2$s',
					__( 'Search Bar', 'pen' ),
					__( 'Top', 'pen' )
				),
			),
			'search-left'        => array(
				'name' => sprintf(
					'%1$s &rarr; %2$s',
					__( 'Search Bar', 'pen' ),
					__( 'Left', 'pen' )
				),
			),
			'search-right'       => array(
				'name' => sprintf(
					'%1$s &rarr; %2$s',
					__( 'Search Bar', 'pen' ),
					__( 'Right', 'pen' )
				),
			),
			'search-bottom'      => array(
				'name' => sprintf(
					'%1$s &rarr; %2$s',
					__( 'Search Bar', 'pen' ),
					__( 'Bottom', 'pen' )
				),
			),
			'top'                => array(
				'name' => __( 'Top', 'pen' ),
			),
			'bottom'             => array(
				'name' => __( 'Bottom', 'pen' ),
			),
			'content-top'        => array(
				'name' => sprintf(
					'%1$s &rarr; %2$s',
					__( 'Content', 'pen' ),
					__( 'Top', 'pen' )
				),
			),
			'content-bottom'     => array(
				'name' => sprintf(
					'%1$s &rarr; %2$s',
					__( 'Content', 'pen' ),
					__( 'Bottom', 'pen' )
				),
			),
			'footer-top'         => array(
				'name' => sprintf(
					'%1$s &rarr; %2$s',
					__( 'Footer', 'pen' ),
					__( 'Top', 'pen' )
				),
			),
			'footer-left'        => array(
				'name' => sprintf(
					'%1$s &rarr; %2$s',
					__( 'Footer', 'pen' ),
					__( 'Left', 'pen' )
				),
			),
			'footer-right'       => array(
				'name' => sprintf(
					'%1$s &rarr; %2$s',
					__( 'Footer', 'pen' ),
					__( 'Right', 'pen' )
				),
			),
			'footer-bottom'      => array(
				'name' => sprintf(
					'%1$s &rarr; %2$s',
					__( 'Footer', 'pen' ),
					__( 'Bottom', 'pen' )
				),
			),
			'mobile-menu-top'    => array(
				'name' => sprintf(
					'%1$s &rarr; %2$s',
					__( 'Mobile Menu', 'pen' ),
					__( 'Top', 'pen' )
				),
			),
			'mobile-menu-bottom' => array(
				'name' => sprintf(
					'%1$s &rarr; %2$s',
					__( 'Mobile Menu', 'pen' ),
					__( 'Bottom', 'pen' )
				),
			),
		);
	}
}

if ( ! function_exists( 'pen_sidebars_register' ) ) {
	/**
	 * Register widget areas.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
	 *
	 * @since Pen 1.0.0
	 * @return void
	 */
	function pen_sidebars_register() {

		$sidebars = pen_sidebars();

		foreach ( $sidebars as $id => $sidebar ) {
			register_sidebar(
				array(
					'name'          => esc_html( $sidebar['name'] ),
					'id'            => 'sidebar-' . esc_attr( $id ),
					'description'   => ! empty( $sidebar['description'] ) ? esc_html( $sidebar['description'] ) : '',
					'before_widget' => '<section id="%1$s" class="widget clearfix %2$s">',
					'after_widget'  => '</section>',
					'before_title'  => '<h3 class="widget-title"><span><span>',
					'after_title'   => '</span></span></h3>',
				)
			);
		}

	}
	add_action( 'widgets_init', 'pen_sidebars_register' );
}

if ( ! function_exists( 'pen_sidebar_get' ) ) {
	/**
	 * Sidebars.
	 *
	 * @global array $wp_registered_sidebars
	 *
	 * @param string $sidebar    The sidebar ID.
	 * @param int    $content_id Content ID.
	 *
	 * @since Pen 1.0.0
	 * @return void
	 */
	function pen_sidebar_get( $sidebar, $content_id = null ) {
		// For maximum compatibility.
		if ( is_null( $content_id ) ) {
			$content_id = pen_post_id();
		}

		if ( ! is_registered_sidebar( $sidebar ) ) {
			return;
		}

		global $wp_registered_sidebars;

		if ( ! empty( $wp_registered_sidebars[ $sidebar ]['name'] ) ) {
			$sidebar_name = $wp_registered_sidebars[ $sidebar ]['name'];
		} else {
			// Just in case.
			$sidebar_name = ucwords( str_replace( '-', ' ', $sidebar ) );
		}

		if ( pen_sidebar_check( $sidebar, $content_id ) && is_active_sidebar( $sidebar ) ) {
			ob_start();
			dynamic_sidebar( $sidebar );
			$sidebar_html = trim( ob_get_clean() );
			if ( ! $sidebar_html ) {
				return;
			}
			$html_id = str_ireplace( array( 'sidebar-', '-' ), array( 'pen_', '_' ), $sidebar );
			$classes = array(
				'sidebar',
				'clearfix',
				'widget-area',
				pen_class_animation( str_ireplace( '-', '_', $sidebar ), false, $content_id ),
			);
			if ( 'sidebar-bottom' === $sidebar ) {
				if ( pen_option_get( 'color_bottom_background_transparent' ) ) {
					$classes[] = 'pen_is_transparent';
				} else {
					$classes[] = 'pen_not_transparent';
				}
			}
			$classes = implode( ' ', array_filter( $classes ) );
			?>
	<aside id="<?php echo esc_attr( $html_id ); ?>" class="<?php echo esc_attr( $classes ); ?>" role="complementary" aria-label="<?php echo esc_attr( $sidebar_name ); ?>">
		<h2 class="pen_element_hidden">
			<?php
			echo esc_html( $sidebar_name );
			?>
		</h2>
			<?php
			if ( in_array( $sidebar, array( 'sidebar-top', 'sidebar-bottom' ), true ) ) {
				?>
		<div class="pen_container">
				<?php
			}

			echo $sidebar_html; /* phpcs:ignore */

			if ( in_array( $sidebar, array( 'sidebar-top', 'sidebar-bottom' ), true ) ) {
				?>
		</div>
				<?php
			}

			pen_html_jump_menu( $sidebar, $content_id );
			?>
	</aside>
			<?php
		}
	}
}

if ( ! function_exists( 'pen_sidebar_check' ) ) {
	/**
	 * Checks sidebars visibility.
	 *
	 * @param string $sidebar    The unique sidebar ID.
	 * @param int    $content_id Content ID.
	 *
	 * @since Pen 1.0.0
	 */
	function pen_sidebar_check( $sidebar, $content_id = null ) {
		// For maximum compatibility.
		if ( is_null( $content_id ) ) {
			$content_id = pen_post_id();
		}

		if ( ! is_registered_sidebar( $sidebar ) ) {
			return;
		}
		$sidebar = str_ireplace( '-', '_', $sidebar );
		$visible = true;
		if ( is_front_page() ) {
			$visible = pen_option_get( 'front_' . $sidebar . '_display' ) ? false : true;
		}
		if ( pen_is_singular() ) {
			$visible = get_post_meta( $content_id, 'pen_' . $sidebar . '_display', true ) ? false : true;
		}
		return $visible;
	}
}
