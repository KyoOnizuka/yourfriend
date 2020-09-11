<?php
/**
 * Recommended plugins.
 *
 * @package Pen
 */

if ( ! class_exists( 'TGM_Plugin_Activation' ) ) {
	require __DIR__ . '/class-tgm-plugin-activation.php';
}

if ( ! function_exists( 'pen_install_recommended_plugins' ) ) {
	/**
	 * Recommended plugins for this awesome theme.
	 *
	 * @since Pen 1.2.8
	 * @return void
	 */
	function pen_install_recommended_plugins() {
		$plugins = array(
			array(
				'name'     => esc_html__( 'Pen', 'pen' ),
				'slug'     => 'pen-extra-features',
				'required' => false,
			),
		);
		$config  = array(
			'id'           => 'pen',
			'menu'         => 'pen-theme-install-plugins',
			'has_notices'  => true,
			'dismissable'  => false,
			'is_automatic' => true,
			'strings'      => array(
				'page_title' => sprintf(
					/* Translators: Just some words. */
					esc_html__( '%1$s - %2$s', 'pen' ),
					esc_html__( 'Pen', 'pen' ),
					esc_html__( 'Plugin', 'pen' )
				),
				'menu_title' => sprintf(
					/* Translators: Just some words. */
					esc_html__( '%1$s - %2$s', 'pen' ),
					esc_html__( 'Pen', 'pen' ),
					esc_html__( 'Plugin', 'pen' )
				),
			),
		);

		tgmpa( $plugins, $config );
	}
	add_action( 'tgmpa_register', 'pen_install_recommended_plugins' );
}
