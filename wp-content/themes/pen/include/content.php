<?php
/**
 * Post meta data fields.
 *
 * @package Pen
 */

defined( 'ABSPATH' ) || die();

if ( ! function_exists( 'pen_post_classes' ) ) {
	/**
	 * Generates class names for posts.
	 *
	 * @param array  $classes     List of class names.
	 * @param int    $content_id  Content ID.
	 * @param string $output_type Type of output.
	 * @param bool   $single      Result of is_singular(), for better performance.
	 *
	 * @since Pen 1.0.0
	 * @return string
	 */
	function pen_post_classes( $classes = array(), $content_id, $output_type = 'return_string', $single = null ) {

		if ( is_sticky() ) {
			$classes[] = 'sticky';
		}

		$classes[] = 'pen_article';

		if ( is_null( $single ) ) {
			$single = pen_is_singular();
		}

		if ( $single ) {

			$classes[] = pen_class_animation( 'content', false, $content_id );

		} else {

			$list_type = pen_list_type( $content_id );

			if ( 'plain' === $list_type ) {
				$thumbnail_rotate = get_post_meta( $content_id, 'pen_list_thumbnail_rotate_override', true );
				if ( ! $thumbnail_rotate || 'default' === $thumbnail_rotate ) {
					$thumbnail_rotate = pen_option_get( 'list_thumbnail_rotate' );
				}
				if ( $thumbnail_rotate && 'no' !== $thumbnail_rotate ) {
					$classes[] = 'pen_list_thumbnail_rotate';
				} else {
					$classes[] = 'pen_list_thumbnail_rotate_not';
				}

				$thumbnail_frame = get_post_meta( $content_id, 'pen_list_thumbnail_frame_override', true );
				if ( ! $thumbnail_frame || 'default' === $thumbnail_frame ) {
					$thumbnail_frame = pen_option_get( 'list_thumbnail_frame' );
				}
				if ( $thumbnail_frame && 'no' !== $thumbnail_frame ) {
					$classes[] = 'pen_list_thumbnail_frame';
				} else {
					$classes[] = 'pen_list_thumbnail_frame_not';
				}

				$thumbnail_frame_color = get_post_meta( $content_id, 'pen_color_list_thumbnail_frame_override', true );
				if ( ! $thumbnail_frame_color || 'default' === $thumbnail_frame_color ) {
					$thumbnail_frame_color = pen_option_get( 'color_list_thumbnail_frame' );
				}
				if ( '#000000' === $thumbnail_frame_color ) {
					$classes[] = 'pen_list_thumbnail_frame_dark';
				} else {
					$classes[] = 'pen_list_thumbnail_frame_light';
				}

				$thumbnail_alignment = get_post_meta( $content_id, 'pen_list_thumbnail_alignment_override', true );
				if ( ! $thumbnail_alignment || 'default' === $thumbnail_alignment ) {
					$thumbnail_alignment = pen_option_get( 'list_thumbnail_alignment' );
				}
				$classes[] = 'pen_list_thumbnail_' . $thumbnail_alignment;
			}

			$visibility_options = array(
				'list_header_display'           => 'list_header_hide',
				'list_title_display'            => 'list_title_hide',
				'list_author_display'           => 'list_author_hide',
				'list_date_display'             => 'list_date_hide',
				'list_category_display'         => 'list_category_hide',
				'list_thumbnail_display'        => 'list_thumbnail_hide',
				'list_summary_display'          => 'list_summary_hide',
				'list_footer_display'           => 'list_footer_hide',
				'list_tags_display'             => 'list_tags_hide',
				'list_button_comment_display'   => 'list_button_comment_hide',
				'list_button_edit_display'      => 'list_button_edit_hide',
				'list_button_read_more_display' => 'list_button_read_more_hide',
			);
			foreach ( $visibility_options as $option => $class ) {
				$value = get_post_meta( $content_id, 'pen_' . $option . '_override', true );
				if ( $value && 'default' !== $value ) {
					$classes[] = 'pen_' . $class;
				}
			}

			$classes[] = pen_class_animation( 'list', false, $content_id );

			$header_alignment = get_post_meta( $content_id, 'pen_list_header_alignment_override', true );
			if ( ! $header_alignment || 'default' === $header_alignment ) {
				$header_alignment = pen_option_get( 'list_header_alignment' );
			}
			if ( $header_alignment && 'no' !== $header_alignment ) {
				$classes[] = 'pen_list_header_center';
			}

			$title_alignment = get_post_meta( $content_id, 'pen_list_title_alignment_override', true );
			if ( ! $title_alignment || 'default' === $title_alignment ) {
				$title_alignment = pen_option_get( 'list_title_alignment' );
			}
			if ( $title_alignment && 'no' !== $title_alignment ) {
				$classes[] = 'pen_list_title_center';
			}

			if ( 'masonry' === $list_type || 'tiles' === $list_type ) {
				$type = ( 'tiles' === $list_type ) ? 'tile' : $list_type;

				$classes[] = 'list_' . $list_type;

				$thumbnail_style = pen_option_get( 'list_' . $type . '_thumbnail_style' );
				// The customize.php applies some CSS to this one.
				$classes[] = 'pen_thumbnail_style_' . $thumbnail_style;

				$thumbnail_style = get_post_meta( $content_id, 'pen_list_' . $type . '_thumbnail_style_override', true );
				if ( $thumbnail_style && 'default' !== $thumbnail_style ) {
					$classes[] = 'pen_thumbnail_style_' . $thumbnail_style;
				}
			}
		}

		/**
		 * Adding this class just to apply a larger border-radius to the top left
		 * and top right corners of the <article> element. This is to hide the
		 * light background of the <article> when the header has a dark background.
		 */
		if ( $single ) {
			$option = 'content_header_display';
		} else {
			$option = 'list_header_display';
		}
		$display = get_post_meta( $content_id, 'pen_' . $option . '_override', true );
		if ( ! $display || 'default' === $display ) {
			$display = pen_option_get( $option );
		}
		if ( ! $display || 'no' === $display ) {
			$classes[] = 'pen_header_hide';
		} else {
			$classes[] = 'pen_header_show';
		}

		$classes = array_unique( array_filter( $classes ) );

		if ( 'return_array' === $output_type ) {
			return get_post_class( $classes, $content_id );
		}
		return post_class( $classes );
	}
}

if ( ! function_exists( 'pen_thumbnail_classes' ) ) {
	/**
	 * Generates class names for posts.
	 * (Turned into a separate function since v1.2.8)
	 *
	 * @param string $view       Whether full content or summary.
	 * @param int    $content_id Content ID.
	 *
	 * @return string
	 */
	function pen_thumbnail_classes( $view, $content_id = null ) {

		if ( ! in_array( $view, array( 'content', 'list' ), true ) ) {
			return;
		}

		if ( is_null( $content_id ) ) {
			$content_id = pen_post_id();
		}

		$classes = array(
			'post-thumbnail',
			'pen_image_thumbnail',
			'pen_thumbnail_size_' . sanitize_html_class( pen_content_thumbnail_size( $view, $content_id ) ),
		);

		$animation = get_post_meta( $content_id, 'pen_' . $view . '_thumbnail_animation_reveal_override', true );
		if ( ! $animation || 'default' === $animation ) {
			$animation = pen_option_get( $view . '_thumbnail_animation_reveal' );
		}
		if ( $animation && 'none' !== $animation ) {
			$classes[]       = 'pen_animate_on_scroll';
			$classes[]       = 'pen_custom_animation_' . sanitize_html_class( $animation );
			$animation_delay = get_post_meta( $content_id, 'pen_' . $view . '_thumbnail_animation_delay_reveal_override', true );
			if ( ! $animation_delay || 'default' === $animation_delay ) {
				$animation_delay = pen_option_get( $view . '_thumbnail_animation_delay_reveal' );
			}
			if ( (int) $animation_delay ) {
				$classes[] = 'pen_custom_animation_delay_' . $animation_delay;
			}
		}

		return implode( ' ', array_filter( $classes ) );
	}
}

if ( ! function_exists( 'pen_content_thumbnail_size' ) ) {
	/**
	 * Calculates the thumbnail size.
	 * (Turned into a separate function since v1.2.8)
	 *
	 * @param string $view       Whether full content or summary.
	 * @param int    $content_id Content ID.
	 *
	 * @return int
	 */
	function pen_content_thumbnail_size( $view, $content_id = null ) {

		if ( ! in_array( $view, array( 'content', 'list' ), true ) ) {
			return;
		}

		if ( is_null( $content_id ) ) {
			$content_id = pen_post_id();
		}

		$thumbnail_size = get_post_meta( $content_id, 'pen_' . $view . '_thumbnail_resize_override', true );
		if ( ! $thumbnail_size || 'default' === $thumbnail_size ) {
			$thumbnail_size = pen_option_get( $view . '_thumbnail_resize' );
		}
		if ( 'content' === $view ) {
			if ( 'none' === $thumbnail_size ) {
				if ( 'image' === get_post_type() ) {
					$thumbnail_size = 'large';
				} else {
					$thumbnail_size = 'medium';
				}
			}
		} else {
			if ( 'none' === $thumbnail_size || in_array( pen_option_get( 'list_type' ), array( 'tiles', 'masonry' ), true ) ) {
				$thumbnail_size = 'large';
			}
		}

		return $thumbnail_size;
	}
}

if ( ! function_exists( 'pen_read_more_link' ) ) {
	/**
	 * Tweaks the "Continue Reading" links.
	 *
	 * @since Pen 1.0.0
	 * @return string
	 */
	function pen_read_more_link() {
		$content_id = pen_post_id();
		$classes    = array(
			'more-link',
			( 'button' === pen_option_get( 'list_button_read_more_type' ) ) ? 'pen_button' : '',
			pen_class_lists( 'button_read_more_display_override', $content_id ),
			'pen_icon_' . sanitize_html_class( pen_option_get( 'list_button_read_more_icon' ) ),
		);
		$classes    = implode( ' ', array_filter( $classes ) );
		$link       = sprintf(
			'<a href="%1$s" class="%2$s">%3$s</a>',
			esc_url( get_permalink( $content_id ) ),
			esc_attr( $classes ),
			pen_read_more_text( $content_id )
		);
		return sprintf(
			' &hellip;<br>%s',
			$link
		);
	}
	if ( ! is_admin() ) {
		add_filter( 'excerpt_more', 'pen_read_more_link' );
		add_filter( 'the_content_more_link', 'pen_read_more_link' );
	}
}

if ( ! function_exists( 'pen_read_more_text' ) ) {
	/**
	 * Changes the "Continue Reading" text.
	 *
	 * @param int $content_id The content ID.
	 *
	 * @since Pen 1.3.8
	 * @return string
	 */
	function pen_read_more_text( $content_id ) {
		$text = get_post_meta( $content_id, 'pen_list_button_read_more_text_override', true );
		if ( ! $text || 'default' === $text ) {
			$text = pen_option_get( 'list_button_read_more_text' );
		}
		$choices = array(
			'buy_now'       => __( 'Buy Now', 'pen' ),
			'details'       => __( 'Details', 'pen' ),
			'download'      => __( 'Download', 'pen' ),
			'enrol_now'     => __( 'Enrol Now', 'pen' ),
			'free_download' => __( 'Free Download', 'pen' ),
			'join_now'      => __( 'Join Now', 'pen' ),
			'know_more'     => __( 'Know More', 'pen' ),
			'members_only'  => __( 'Members Only', 'pen' ),
			'more'          => __( 'More', 'pen' ),
			'order_now'     => __( 'Order Now', 'pen' ),
			'read'          => __( 'Read', 'pen' ),
			'read_more'     => __( 'Read More', 'pen' ),
			'register_now'  => __( 'Register Now', 'pen' ),
			'view'          => __( 'View', 'pen' ),
		);
		if ( ! empty( $choices[ $text ] ) ) {
			$text = $choices[ $text ];
		} else {
			$text = sprintf(
				/* Translators: Content title. */
				__( 'Continue reading %s', 'pen' ),
				sprintf(
					'<span class="screen-reader-text">%s</span>',
					get_the_title( $content_id )
				)
			);
		}
		return wp_kses_post( $text );
	}
}

if ( ! function_exists( 'pen_post_sticky' ) ) {
	/**
	 * Sends sticky posts to the top of the lists.
	 *
	 * @param WP_Posts $posts An instance of WP_Post.
	 *
	 * @since Pen 1.0.0
	 */
	function pen_post_sticky( $posts ) {
		$is_sticky = array();
		foreach ( $posts as $key => $post ) {
			if ( is_sticky( $post->ID ) ) {
				$is_sticky[] = $post;
				unset( $posts[ $key ] );
			}
		}
		return array_merge( $is_sticky, $posts );
	}
	add_filter( 'the_posts', 'pen_post_sticky' );
}

if ( ! function_exists( 'pen_post_meta' ) ) {
	/**
	 * Custom post meta data fields.
	 *
	 * @param object $post An instance of the $post.
	 *
	 * @since Pen 1.0.0
	 * @return void
	 */
	function pen_post_meta( $post ) {
		$post_type               = get_post_type();
		$options_animation       = pen_animations();
		$options_animation_delay = pen_animations_delay();
		$options_image_size      = pen_wp_image_sizes();

		ob_start();
		?>

	<div id="pen_postmeta_hint">
		<?php
		esc_html_e( 'If you switch to another theme these settings will be no longer used. Other settings are either parts of the WordPress core or added via plugins and they will be available even without this theme.', 'pen' );
		?>
	</div>

	<div id="pen_postmeta">
		<p>
		<?php
		echo esc_html(
			sprintf(
				/* Translators: Path to the settings. */
				__( 'The following options would only apply to this specific content. If you want to apply them to your whole website you should go to %s.', 'pen' ),
				sprintf(
					'%1$s &rarr; %2$s &rarr; %3$s',
					__( 'Appearance', 'pen' ),
					__( 'Customize', 'pen' ),
					__( 'Content', 'pen' )
				)
			)
		);
		?>
		</p>

		<?php
		if ( function_exists( 'pen_plugin_postmeta_tools' ) ) {
			pen_plugin_postmeta_tools( $post->ID );
		} else {
			?>
		<div class="pen_missing_feature">
			<p>
			<?php
			printf(
				/* Translators: The name of a plugin. */
				esc_html__( 'The %s plugin is not installed. Therefore, some important features are not available.', 'pen' ),
				esc_html__( 'Pen', 'pen' )
			);

			printf(
				'<a href="%1$s" class="button-primary">%2$s</a>',
				esc_url( self_admin_url( 'themes.php?page=pen-theme-install-plugins' ) ),
				esc_html__( 'Install Now', 'pen' )
			);
			?>
			</p>
		</div>
			<?php
		}
		?>

		<div id="pen_postmeta_full_content" class="pen_postmeta_options pen_post_meta_full postbox">
			<h3>
		<?php
		esc_html_e( 'Full Content View', 'pen' );
		?>
			</h3>
			<div class="pen_postmeta_container">

				<div>

					<fieldset>
						<legend>
		<?php
		$section_title = __( 'Style', 'pen' );
		$label_prefix  = $section_title;
		echo esc_html( $section_title );
		?>
						</legend>
		<?php
		$setting_id = 'content_custom_preset_color';
		$label      = __( 'Color Scheme', 'pen' );
		$default    = PEN_THEME_PRESET_COLOR;
		$choices    = array(
			'default' => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Default', 'pen' ),
				sprintf(
					/* Translators: Just a number. */
					__( 'Style %d', 'pen' ),
					(int) str_replace( 'preset_', '', $default )
				)
			),
		);
		for ( $s = 1; $s <= 15; $s++ ) {
			$preset = 'preset_' . $s;
			/* Translators: Just a number. */
			$choices[ $preset ] = sprintf( __( 'Style %d', 'pen' ), $s );
		}
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default, $label, $label_prefix );

		$setting_id = 'content_custom_preset_font';
		$label      = __( 'Typography', 'pen' );
		$default    = PEN_THEME_PRESET_FONT;
		$choices    = array(
			'default' => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Default', 'pen' ),
				sprintf(
					/* Translators: Just a number. */
					__( 'Style %d', 'pen' ),
					(int) str_replace( 'preset_', '', $default )
				)
			),
		);
		for ( $f = 1; $f <= 10; $f++ ) {
			$preset = 'preset_' . $f;
			/* Translators: Just a number. */
			$choices[ $preset ] = sprintf( __( 'Style %d', 'pen' ), $f );
		}
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default, $label, $label_prefix );
		?>
					</fieldset>
				</div>

				<div>

					<fieldset>
						<legend>
		<?php
		$section_title = __( 'Featured Image', 'pen' );
		$label_prefix  = $section_title;
		echo esc_html( $section_title );
		?>
						</legend>

		<?php
		$setting_id = 'content_thumbnail_display';
		$label      = __( 'Visibility', 'pen' );
		if ( pen_option_get( $setting_id ) ) {
			$default = array(
				'value' => 'yes',
				'text'  => __( 'Show', 'pen' ),
			);
		} else {
			$default = array(
				'value' => 'no',
				'text'  => __( 'Hide', 'pen' ),
			);
		}
		$choices = array(
			'default' => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Default', 'pen' ),
				esc_html( $default['text'] )
			),
			'yes'     => __( 'Show', 'pen' ),
			'no'      => __( 'Hide', 'pen' ),
		);
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default['value'], $label, $label_prefix );

		$setting_id      = 'content_thumbnail_resize';
		$label           = __( 'Size', 'pen' );
		$default         = pen_option_get( $setting_id );
		$thumbnail_sizes = array(
			'default' => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Default', 'pen' ),
				esc_html( $default )
			),
		);
		$thumbnail_sizes = array_merge( $thumbnail_sizes, $options_image_size );
		pen_post_meta_select( $post->ID, $setting_id, $thumbnail_sizes, $default, $label, $label_prefix );

		$setting_id = 'content_thumbnail_rotate';
		$label      = __( 'Rotate', 'pen' );
		if ( pen_option_get( $setting_id ) ) {
			$default = array(
				'value' => 'yes',
				'text'  => __( 'Yes', 'pen' ),
			);
		} else {
			$default = array(
				'value' => 'no',
				'text'  => __( 'No', 'pen' ),
			);
		}
		$choices = array(
			'default' => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Default', 'pen' ),
				esc_html( $default['text'] )
			),
			'yes'     => __( 'Yes', 'pen' ),
			'no'      => __( 'No', 'pen' ),
		);
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default['value'], $label, $label_prefix );

		$setting_id = 'content_thumbnail_alignment';
		$label      = __( 'Alignment', 'pen' );
		$default    = pen_option_get( $setting_id );
		$choices    = array(
			'default' => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Default', 'pen' ),
				esc_html( ucwords( $default ) )
			),
			'left'    => __( 'Left', 'pen' ),
			'center'  => __( 'Center', 'pen' ),
			'right'   => __( 'Right', 'pen' ),
		);
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default, $label, $label_prefix );

		$setting_id = 'content_thumbnail_frame';
		$label      = _x( 'Frame', 'As in photo or picture frame.', 'pen' );
		if ( pen_option_get( $setting_id ) ) {
			$default = array(
				'value' => 'yes',
				'text'  => __( 'Yes', 'pen' ),
			);
		} else {
			$default = array(
				'value' => 'no',
				'text'  => __( 'No', 'pen' ),
			);
		}
		$choices = array(
			'default' => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Default', 'pen' ),
				esc_html( $default['text'] )
			),
			'yes'     => __( 'Yes', 'pen' ),
			'no'      => __( 'No', 'pen' ),
		);
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default['value'], $label, $label_prefix );

		$setting_id = 'color_content_thumbnail_frame';
		$label      = sprintf(
			'%1$s &rarr; %2$s',
			_x( 'Frame', 'As in photo or picture frame.', 'pen' ),
			__( 'Color', 'pen' )
		);
		if ( '#000000' === pen_option_get( $setting_id ) ) {
			$default = array(
				'value' => 'dark',
				'text'  => __( 'Dark', 'pen' ),
			);
		} else {
			$default = array(
				'value' => 'light',
				'text'  => __( 'Light', 'pen' ),
			);
		}
		$choices = array(
			'default' => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Default', 'pen' ),
				esc_html( $default['text'] )
			),
			'#000000' => __( 'Dark', 'pen' ),
			'#ffffff' => __( 'Light', 'pen' ),
		);
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default['value'], $label, $label_prefix );

		?>
					</fieldset>

					<fieldset>
						<legend>
		<?php
		$section_title = __( 'Dynamic Background Images', 'pen' );
		$label_prefix  = $section_title;
		echo esc_html( $section_title );
		?>
						</legend>
		<?php

		$setting_id = 'background_image_site_dynamic';
		// There is no such option as content_background_image_site_dynamic
		// so we will add content_ prefix down there.
		$label      = __( 'Site', 'pen' );
		$default    = pen_option_get( $setting_id );
		$choices    = array(
			'none'           => __( 'None', 'pen' ),
			'featured_image' => __( 'Featured Image', 'pen' ),
		);
		$choices    = array_merge(
			array(
				'default' => sprintf(
					/* Translators: Just some words. */
					__( '%1$s (%2$s)', 'pen' ),
					__( 'Default', 'pen' ),
					esc_html( ! empty( $choices[ $default ] ) ? $choices[ $default ] : ucwords( str_replace( '_', ' ', $default ) ) )
				),
			),
			$choices
		);
		$setting_id = 'content_' . $setting_id;
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default, $label, $label_prefix );

		$setting_id = 'background_image_header_dynamic';
		// There is no such option as content_background_image_header_dynamic
		// so we will add content_ prefix down there.
		$label      = __( 'Header', 'pen' );
		$default    = pen_option_get( $setting_id );
		$choices    = array(
			'none'           => __( 'None', 'pen' ),
			'featured_image' => __( 'Featured Image', 'pen' ),
		);
		$choices    = array_merge(
			array(
				'default' => sprintf(
					/* Translators: Just some words. */
					__( '%1$s (%2$s)', 'pen' ),
					__( 'Default', 'pen' ),
					esc_html( ! empty( $choices[ $default ] ) ? $choices[ $default ] : ucwords( str_replace( '_', ' ', $default ) ) )
				),
			),
			$choices
		);
		$setting_id = 'content_' . $setting_id;
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default, $label, $label_prefix );

		$setting_id = 'background_image_navigation_dynamic';
		// There is no such option as content_background_image_navigation_dynamic
		// so we will add content_ prefix down there.
		$label      = __( 'Main Menu', 'pen' );
		$default    = pen_option_get( $setting_id );
		$choices    = array(
			'none'           => __( 'None', 'pen' ),
			'featured_image' => __( 'Featured Image', 'pen' ),
		);
		$choices    = array_merge(
			array(
				'default' => sprintf(
					/* Translators: Just some words. */
					__( '%1$s (%2$s)', 'pen' ),
					__( 'Default', 'pen' ),
					esc_html( ! empty( $choices[ $default ] ) ? $choices[ $default ] : ucwords( str_replace( '_', ' ', $default ) ) )
				),
			),
			$choices
		);
		$setting_id = 'content_' . $setting_id;
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default, $label, $label_prefix );

		$setting_id = 'background_image_navigation_submenu_dynamic';
		// There is no such option as content_background_image_navigation_submenu_dynamic
		// so we will add content_ prefix down there.
		$label      = __( 'Sub-menus', 'pen' );
		$default    = pen_option_get( $setting_id );
		$choices    = array(
			'none'           => __( 'None', 'pen' ),
			'featured_image' => __( 'Featured Image', 'pen' ),
		);
		$choices    = array_merge(
			array(
				'default' => sprintf(
					/* Translators: Just some words. */
					__( '%1$s (%2$s)', 'pen' ),
					__( 'Default', 'pen' ),
					esc_html( ! empty( $choices[ $default ] ) ? $choices[ $default ] : ucwords( str_replace( '_', ' ', $default ) ) )
				),
			),
			$choices
		);
		$setting_id = 'content_' . $setting_id;
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default, $label, $label_prefix );

		$setting_id = 'background_image_search_dynamic';
		// There is no such option as content_background_image_search_dynamic
		// so we will add content_ prefix down there.
		$label      = __( 'Search', 'pen' );
		$default    = pen_option_get( $setting_id );
		$choices    = array(
			'none'           => __( 'None', 'pen' ),
			'featured_image' => __( 'Featured Image', 'pen' ),
		);
		$choices    = array_merge(
			array(
				'default' => sprintf(
					/* Translators: Just some words. */
					__( '%1$s (%2$s)', 'pen' ),
					__( 'Default', 'pen' ),
					esc_html( ! empty( $choices[ $default ] ) ? $choices[ $default ] : ucwords( str_replace( '_', ' ', $default ) ) )
				),
			),
			$choices
		);
		$setting_id = 'content_' . $setting_id;
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default, $label, $label_prefix );

		$setting_id = 'background_image_content_title_dynamic';
		// There is no such option as content_background_image_content_title_dynamic
		// so we will add content_ prefix down there.
		$label      = sprintf(
			'%1$s &rarr; %2$s',
			__( 'Content', 'pen' ),
			__( 'Header', 'pen' )
		);
		$default    = pen_option_get( $setting_id );
		$choices    = array(
			'none'           => __( 'None', 'pen' ),
			'featured_image' => __( 'Featured Image', 'pen' ),
		);
		$choices    = array_merge(
			array(
				'default' => sprintf(
					/* Translators: Just some words. */
					__( '%1$s (%2$s)', 'pen' ),
					__( 'Default', 'pen' ),
					esc_html( ! empty( $choices[ $default ] ) ? $choices[ $default ] : ucwords( str_replace( '_', ' ', $default ) ) )
				),
			),
			$choices
		);
		$setting_id = 'content_' . $setting_id;
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default, $label, $label_prefix );

		$setting_id = 'background_image_bottom_dynamic';
		// There is no such option as content_background_image_bottom_dynamic
		// so we will add content_ prefix down there.
		$label = sprintf(
			/* Translators: Part of the theme, e.g. "Left" Widget Area. */
			__( '"%s" Widget Area', 'pen' ),
			__( 'Bottom', 'pen' )
		);
		$default    = pen_option_get( $setting_id );
		$choices    = array(
			'none'           => __( 'None', 'pen' ),
			'featured_image' => __( 'Featured Image', 'pen' ),
		);
		$choices    = array_merge(
			array(
				'default' => sprintf(
					/* Translators: Just some words. */
					__( '%1$s (%2$s)', 'pen' ),
					__( 'Default', 'pen' ),
					esc_html( ! empty( $choices[ $default ] ) ? $choices[ $default ] : ucwords( str_replace( '_', ' ', $default ) ) )
				),
			),
			$choices
		);
		$setting_id = 'content_' . $setting_id;
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default, $label, $label_prefix );

		$setting_id = 'background_image_footer_dynamic';
		// There is no such option as content_background_image_footer_dynamic
		// so we will add content_ prefix down there.
		$label      = __( 'Footer', 'pen' );
		$default    = pen_option_get( $setting_id );
		$choices    = array(
			'none'           => __( 'None', 'pen' ),
			'featured_image' => __( 'Featured Image', 'pen' ),
		);
		$choices    = array_merge(
			array(
				'default' => sprintf(
					/* Translators: Just some words. */
					__( '%1$s (%2$s)', 'pen' ),
					__( 'Default', 'pen' ),
					esc_html( ! empty( $choices[ $default ] ) ? $choices[ $default ] : ucwords( str_replace( '_', ' ', $default ) ) )
				),
			),
			$choices
		);
		$setting_id = 'content_' . $setting_id;
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default, $label, $label_prefix );
		?>
					</fieldset>

					<fieldset>
						<legend>
		<?php
		$section_title = __( 'Layout', 'pen' );
		$label_prefix  = $section_title;
		echo esc_html( $section_title );
		?>
						</legend>
		<?php
		$setting_id = 'site_width';
		$label      = __( 'Site Layout', 'pen' );
		$default    = pen_option_get( $setting_id );
		if ( 'default' === $default || 'standard' === $default ) {
			$default = 'standard';
		}
		$choices = array(
			'boxed'    => __( 'Boxed', 'pen' ),
			'narrow'   => __( 'Narrow', 'pen' ),
			'standard' => __( 'Standard', 'pen' ),
			'wide'     => __( 'Wide', 'pen' ),
		);
		$choices = array_merge(
			array(
				'default' => sprintf(
					/* Translators: Just some words. */
					__( '%1$s (%2$s)', 'pen' ),
					__( 'Default', 'pen' ),
					esc_html( ! empty( $choices[ $default ] ) ? $choices[ $default ] : ucwords( $default ) )
				),
			),
			$choices
		);
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default, $label, $label_prefix );

		$setting_id = 'content_header_alignment';
		$label      = sprintf(
			'%1$s &rarr; %2$s &rarr; %3$s &rarr; %4$s',
			__( 'Content', 'pen' ),
			__( 'Header', 'pen' ),
			__( 'Alignment', 'pen' ),
			__( 'Center', 'pen' )
		);
		if ( pen_option_get( $setting_id ) ) {
			$default = array(
				'value' => 'yes',
				'text'  => __( 'Yes', 'pen' ),
			);
		} else {
			$default = array(
				'value' => 'no',
				'text'  => __( 'No', 'pen' ),
			);
		}
		$choices = array(
			'default' => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Default', 'pen' ),
				esc_html( $default['text'] )
			),
			'yes'     => __( 'Yes', 'pen' ),
			'no'      => __( 'No', 'pen' ),
		);
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default['value'], $label, $label_prefix );

		$setting_id = 'content_title_alignment';
		$label      = sprintf(
			'%1$s &rarr; %2$s &rarr; %3$s &rarr; %4$s',
			__( 'Content', 'pen' ),
			__( 'Title', 'pen' ),
			__( 'Alignment', 'pen' ),
			__( 'Center', 'pen' )
		);
		if ( pen_option_get( $setting_id ) ) {
			$default = array(
				'value' => 'yes',
				'text'  => __( 'Yes', 'pen' ),
			);
		} else {
			$default = array(
				'value' => 'no',
				'text'  => __( 'No', 'pen' ),
			);
		}
		$choices = array(
			'default' => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Default', 'pen' ),
				esc_html( $default['text'] )
			),
			'yes'     => __( 'Yes', 'pen' ),
			'no'      => __( 'No', 'pen' ),
		);
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default['value'], $label, $label_prefix );

		?>

					</fieldset>

					<fieldset>
						<legend>
		<?php
		$section_title = __( 'Animation', 'pen' );
		$label_prefix  = $section_title;
		echo esc_html( $section_title );
		?>
						</legend>
		<?php
		$setting_id = 'content_animation_reveal';
		$label      = __( 'Content Area', 'pen' );
		$default    = pen_option_get( $setting_id );
		$animations = array(
			'default' => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Default', 'pen' ),
				esc_html( $default )
			),
		);
		$animations = array_merge( $animations, $options_animation );
		pen_post_meta_select( $post->ID, $setting_id, $animations, $default, $label, $label_prefix );

		$setting_id       = 'content_animation_delay_reveal';
		$label            = sprintf(
			'%1$s &rarr; %2$s',
			__( 'Animation Delay', 'pen' ),
			__( 'Content Area', 'pen' )
		);
		$default          = (int) pen_option_get( $setting_id );
		$animations_delay = array(
			'default' => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Default', 'pen' ),
				esc_html( $default ? $default : __( 'None', 'pen' ) )
			),
		);
		$animations_delay = array_merge( $animations_delay, $options_animation_delay );
		pen_post_meta_select( $post->ID, $setting_id, $animations_delay, $default, $label, $label_prefix );

		$setting_id = 'content_thumbnail_animation_reveal';
		$label      = __( 'Featured Image', 'pen' );
		$default    = pen_option_get( $setting_id );
		$animations = array(
			'default' => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Default', 'pen' ),
				esc_html( $default )
			),
		);
		$animations = array_merge( $animations, $options_animation );
		pen_post_meta_select( $post->ID, $setting_id, $animations, $default, $label, $label_prefix );

		$setting_id       = 'content_thumbnail_animation_delay_reveal';
		$label            = sprintf(
			'%1$s &rarr; %2$s',
			__( 'Animation Delay', 'pen' ),
			__( 'Featured Image', 'pen' )
		);
		$default          = (int) pen_option_get( $setting_id );
		$animations_delay = array(
			'default' => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Default', 'pen' ),
				esc_html( $default ? $default : __( 'None', 'pen' ) )
			),
		);
		$animations_delay = array_merge( $animations_delay, $options_animation_delay );
		pen_post_meta_select( $post->ID, $setting_id, $animations_delay, $default, $label, $label_prefix );
		?>
					</fieldset>

					<fieldset>
						<legend>
		<?php
		$section_title = __( 'Visibility', 'pen' );
		$label_prefix  = $section_title;
		echo esc_html( $section_title );
		?>
						</legend>
		<?php
		$setting_id = 'site_header_display';
		$label      = __( 'Site Header', 'pen' );
		if ( pen_option_get( $setting_id ) ) {
			$default = array(
				'value' => 'yes',
				'text'  => __( 'Show', 'pen' ),
			);
		} else {
			$default = array(
				'value' => 'no',
				'text'  => __( 'Hide', 'pen' ),
			);
		}
		$choices = array(
			'default' => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Default', 'pen' ),
				esc_html( $default['text'] )
			),
			'yes'     => __( 'Show', 'pen' ),
			'no'      => __( 'Hide', 'pen' ),
		);
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default['value'], $label, $label_prefix );

		$setting_id = 'site_footer_display';
		$label      = __( 'Site Footer', 'pen' );
		if ( pen_option_get( $setting_id ) ) {
			$default = array(
				'value' => 'yes',
				'text'  => __( 'Show', 'pen' ),
			);
		} else {
			$default = array(
				'value' => 'no',
				'text'  => __( 'Hide', 'pen' ),
			);
		}
		$choices = array(
			'default' => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Default', 'pen' ),
				esc_html( $default['text'] )
			),
			'yes'     => __( 'Show', 'pen' ),
			'no'      => __( 'Hide', 'pen' ),
		);
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default['value'], $label, $label_prefix );

		$setting_id = 'navigation_display';
		$label      = __( 'Main Menu', 'pen' );
		if ( pen_option_get( $setting_id ) ) {
			$default = array(
				'value' => 'yes',
				'text'  => __( 'Show', 'pen' ),
			);
		} else {
			$default = array(
				'value' => 'no',
				'text'  => __( 'Hide', 'pen' ),
			);
		}
		$choices = array(
			'default' => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Default', 'pen' ),
				esc_html( $default['text'] )
			),
			'yes'     => __( 'Show', 'pen' ),
			'no'      => __( 'Hide', 'pen' ),
		);
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default['value'], $label, $label_prefix );

		$setting_id = 'content_header_display';
		$label      = sprintf(
			'%1$s &rarr; %2$s',
			__( 'Content', 'pen' ),
			__( 'Header', 'pen' )
		);
		if ( pen_option_get( $setting_id ) ) {
			$default = array(
				'value' => 'yes',
				'text'  => __( 'Show', 'pen' ),
			);
		} else {
			$default = array(
				'value' => 'no',
				'text'  => __( 'Hide', 'pen' ),
			);
		}
		$choices = array(
			'default' => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Default', 'pen' ),
				esc_html( $default['text'] )
			),
			'yes'     => __( 'Show', 'pen' ),
			'no'      => __( 'Hide', 'pen' ),
		);
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default['value'], $label, $label_prefix );

		$setting_id = 'content_title_display';
		$label      = sprintf(
			'%1$s &rarr; %2$s',
			__( 'Content', 'pen' ),
			__( 'Title', 'pen' )
		);
		if ( pen_option_get( $setting_id ) ) {
			$default = array(
				'value' => 'yes',
				'text'  => __( 'Show', 'pen' ),
			);
		} else {
			$default = array(
				'value' => 'no',
				'text'  => __( 'Hide', 'pen' ),
			);
		}
		$choices = array(
			'default' => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Default', 'pen' ),
				esc_html( $default['text'] )
			),
			'yes'     => __( 'Show', 'pen' ),
			'no'      => __( 'Hide', 'pen' ),
		);
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default['value'], $label, $label_prefix );

		if ( 'post' === $post_type ) {

			$setting_id = 'content_author_display';
			$label      = __( 'Author', 'pen' );
			if ( pen_option_get( $setting_id ) ) {
				$default = array(
					'value' => 'yes',
					'text'  => __( 'Show', 'pen' ),
				);
			} else {
				$default = array(
					'value' => 'no',
					'text'  => __( 'Hide', 'pen' ),
				);
			}
			$choices = array(
				'default' => sprintf(
					/* Translators: Just some words. */
					__( '%1$s (%2$s)', 'pen' ),
					__( 'Default', 'pen' ),
					esc_html( $default['text'] )
				),
				'yes'     => __( 'Show', 'pen' ),
				'no'      => __( 'Hide', 'pen' ),
			);
			pen_post_meta_select( $post->ID, $setting_id, $choices, $default['value'], $label, $label_prefix );

			$setting_id = 'content_date_display';
			$label      = __( 'Publish Date', 'pen' );
			if ( pen_option_get( $setting_id ) ) {
				$default = array(
					'value' => 'yes',
					'text'  => __( 'Show', 'pen' ),
				);
			} else {
				$default = array(
					'value' => 'no',
					'text'  => __( 'Hide', 'pen' ),
				);
			}
			$choices = array(
				'default' => sprintf(
					/* Translators: Just some words. */
					__( '%1$s (%2$s)', 'pen' ),
					__( 'Default', 'pen' ),
					esc_html( $default['text'] )
				),
				'yes'     => __( 'Show', 'pen' ),
				'no'      => __( 'Hide', 'pen' ),
			);
			pen_post_meta_select( $post->ID, $setting_id, $choices, $default['value'], $label, $label_prefix );

			$setting_id = 'content_category_display';
			$label      = __( 'Categories', 'pen' );
			if ( pen_option_get( $setting_id ) ) {
				$default = array(
					'value' => 'yes',
					'text'  => __( 'Show', 'pen' ),
				);
			} else {
				$default = array(
					'value' => 'no',
					'text'  => __( 'Hide', 'pen' ),
				);
			}
			$choices = array(
				'default' => sprintf(
					/* Translators: Just some words. */
					__( '%1$s (%2$s)', 'pen' ),
					__( 'Default', 'pen' ),
					esc_html( $default['text'] )
				),
				'yes'     => __( 'Show', 'pen' ),
				'no'      => __( 'Hide', 'pen' ),
			);
			pen_post_meta_select( $post->ID, $setting_id, $choices, $default['value'], $label, $label_prefix );

			$setting_id = 'content_profile_display';
			$label      = __( 'Author Profile', 'pen' );
			if ( pen_option_get( $setting_id ) ) {
				$default = array(
					'value' => 'yes',
					'text'  => __( 'Show', 'pen' ),
				);
			} else {
				$default = array(
					'value' => 'no',
					'text'  => __( 'Hide', 'pen' ),
				);
			}
			$choices = array(
				'default' => sprintf(
					/* Translators: Just some words. */
					__( '%1$s (%2$s)', 'pen' ),
					__( 'Default', 'pen' ),
					esc_html( $default['text'] )
				),
				'yes'     => __( 'Show', 'pen' ),
				'no'      => __( 'Hide', 'pen' ),
			);
			pen_post_meta_select( $post->ID, $setting_id, $choices, $default['value'], $label, $label_prefix );

			$setting_id = 'content_tags_display';
			$label      = __( 'Tags', 'pen' );
			if ( pen_option_get( $setting_id ) ) {
				$default = array(
					'value' => 'yes',
					'text'  => __( 'Show', 'pen' ),
				);
			} else {
				$default = array(
					'value' => 'no',
					'text'  => __( 'Hide', 'pen' ),
				);
			}
			$choices = array(
				'default' => sprintf(
					/* Translators: Just some words. */
					__( '%1$s (%2$s)', 'pen' ),
					__( 'Default', 'pen' ),
					esc_html( $default['text'] )
				),
				'yes'     => __( 'Show', 'pen' ),
				'no'      => __( 'Hide', 'pen' ),
			);
			pen_post_meta_select( $post->ID, $setting_id, $choices, $default['value'], $label, $label_prefix );

		}
		?>

		<?php
		$setting_id = 'content_share_display';
		$label      = sprintf(
			'%1$s &rarr; %2$s',
			__( 'Buttons', 'pen' ),
			_x( 'Share', 'noun', 'pen' )
		);
		if ( pen_option_get( $setting_id ) ) {
			$default = array(
				'value' => 'yes',
				'text'  => __( 'Show', 'pen' ),
			);
		} else {
			$default = array(
				'value' => 'no',
				'text'  => __( 'Hide', 'pen' ),
			);
		}
		$choices = array(
			'default' => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Default', 'pen' ),
				esc_html( $default['text'] )
			),
			'yes'     => __( 'Show', 'pen' ),
			'no'      => __( 'Hide', 'pen' ),
		);
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default['value'], $label, $label_prefix );

		$setting_id = 'content_footer_display';
		$label      = sprintf(
			'%1$s &rarr; %2$s',
			__( 'Content', 'pen' ),
			__( 'Footer', 'pen' )
		);
		if ( pen_option_get( $setting_id ) ) {
			$default = array(
				'value' => 'yes',
				'text'  => __( 'Show', 'pen' ),
			);
		} else {
			$default = array(
				'value' => 'no',
				'text'  => __( 'Hide', 'pen' ),
			);
		}
		$choices = array(
			'default' => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Default', 'pen' ),
				esc_html( $default['text'] )
			),
			'yes'     => __( 'Show', 'pen' ),
			'no'      => __( 'Hide', 'pen' ),
		);
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default['value'], $label, $label_prefix );

		$setting_id = 'search_display';
		$label      = __( 'Search Box', 'pen' );
		if ( pen_option_get( $setting_id ) ) {
			$default = array(
				'value' => 'yes',
				'text'  => __( 'Show', 'pen' ),
			);
		} else {
			$default = array(
				'value' => 'no',
				'text'  => __( 'Hide', 'pen' ),
			);
		}
		$choices    = array(
			'default' => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Default', 'pen' ),
				esc_html( $default['text'] )
			),
			'yes'     => __( 'Show', 'pen' ),
			'no'      => __( 'Hide', 'pen' ),
		);
		$setting_id = 'content_' . $setting_id;
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default['value'], $label, $label_prefix );
		?>
					</fieldset>

					<fieldset>
						<legend>
		<?php
		$section_title = __( 'Location', 'pen' );
		$label_prefix  = $section_title;
		echo esc_html( $section_title );
		?>
						</legend>
		<?php
		if ( 'post' === $post_type ) {

			$setting_id = 'content_author_location';
			$label      = __( 'Author', 'pen' );
			$default    = pen_option_get( $setting_id );
			$choices    = array(
				'header' => sprintf(
					'%1$s &rarr; %2$s',
					__( 'Content', 'pen' ),
					__( 'Header', 'pen' )
				),
				'footer' => sprintf(
					'%1$s &rarr; %2$s',
					__( 'Content', 'pen' ),
					__( 'Footer', 'pen' )
				),
			);
			$choices    = array_merge(
				array(
					'default' => sprintf(
						/* Translators: Just some words. */
						__( '%1$s (%2$s)', 'pen' ),
						__( 'Default', 'pen' ),
						esc_html( ! empty( $choices[ $default ] ) ? $choices[ $default ] : ucwords( $default ) )
					),
				),
				$choices
			);
			pen_post_meta_select( $post->ID, $setting_id, $choices, $default, $label, $label_prefix );

			$setting_id = 'content_date_location';
			$label      = __( 'Publish Date', 'pen' );
			$default    = pen_option_get( $setting_id );
			$choices    = array(
				'header' => sprintf(
					'%1$s &rarr; %2$s',
					__( 'Content', 'pen' ),
					__( 'Header', 'pen' )
				),
				'footer' => sprintf(
					'%1$s &rarr; %2$s',
					__( 'Content', 'pen' ),
					__( 'Footer', 'pen' )
				),
			);
			$choices    = array_merge(
				array(
					'default' => sprintf(
						/* Translators: Just some words. */
						__( '%1$s (%2$s)', 'pen' ),
						__( 'Default', 'pen' ),
						esc_html( ! empty( $choices[ $default ] ) ? $choices[ $default ] : ucwords( $default ) )
					),
				),
				$choices
			);
			pen_post_meta_select( $post->ID, $setting_id, $choices, $default, $label, $label_prefix );

			$setting_id = 'content_category_location';
			$label      = __( 'Categories', 'pen' );
			$default    = pen_option_get( $setting_id );
			$choices    = array(
				'header' => sprintf(
					'%1$s &rarr; %2$s',
					__( 'Content', 'pen' ),
					__( 'Header', 'pen' )
				),
				'footer' => sprintf(
					'%1$s &rarr; %2$s',
					__( 'Content', 'pen' ),
					__( 'Footer', 'pen' )
				),
			);
			$choices    = array_merge(
				array(
					'default' => sprintf(
						/* Translators: Just some words. */
						__( '%1$s (%2$s)', 'pen' ),
						__( 'Default', 'pen' ),
						esc_html( ! empty( $choices[ $default ] ) ? $choices[ $default ] : ucwords( $default ) )
					),
				),
				$choices
			);
			pen_post_meta_select( $post->ID, $setting_id, $choices, $default, $label, $label_prefix );

		}

		$setting_id = 'content_share_location';
		$label      = sprintf(
			'%1$s &rarr; %2$s',
			__( 'Buttons', 'pen' ),
			_x( 'Share', 'noun', 'pen' )
		);
		$default    = pen_option_get( $setting_id );
		$choices    = array(
			'header'  => sprintf(
				'%1$s &rarr; %2$s',
				__( 'Content', 'pen' ),
				__( 'Header', 'pen' )
			),
			'content' => __( 'Content', 'pen' ),
			'footer'  => sprintf(
				'%1$s &rarr; %2$s',
				__( 'Content', 'pen' ),
				__( 'Footer', 'pen' )
			),
		);
		$choices    = array_merge(
			array(
				'default' => sprintf(
					/* Translators: Just some words. */
					__( '%1$s (%2$s)', 'pen' ),
					__( 'Default', 'pen' ),
					esc_html( ! empty( $choices[ $default ] ) ? $choices[ $default ] : ucwords( $default ) )
				),
			),
			$choices
		);
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default, $label, $label_prefix );

		$setting_id = 'search_location';
		$label      = __( 'Search Box', 'pen' );
		$default    = pen_option_get( 'search_location' );
		$choices    = array(
			'header'  => sprintf(
				'%1$s &rarr; %2$s',
				__( 'Site', 'pen' ),
				__( 'Header', 'pen' )
			),
			'content' => __( 'Content', 'pen' ),
		);
		$choices    = array_merge(
			array(
				'default' => sprintf(
					/* Translators: Just some words. */
					__( '%1$s (%2$s)', 'pen' ),
					__( 'Default', 'pen' ),
					esc_html( ! empty( $choices[ $default ] ) ? $choices[ $default ] : ucwords( $default ) )
				),
			),
			$choices
		);
		$setting_id = 'content_' . $setting_id;
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default, $label, $label_prefix );
		?>
					</fieldset>

					<fieldset class="pen_sidebars">
						<legend>
		<?php
		$section_title = __( 'Sidebars', 'pen' );
		$label_prefix  = $section_title;
		echo esc_html( $section_title );
		?>
						</legend>

						<p>
		<?php
		esc_html_e( 'You can control the visibility of your sidebars for this specific content.', 'pen' );
		?>
						</p>
		<?php
		$widget_areas = array(
			'header_primary'   => sprintf(
				/* Translators: Widget area, e.g. "Header - Primary" Widget Area . */
				__( 'Hide the "%s" Widget Area', 'pen' ),
				sprintf(
					'%s - %s',
					__( 'Header', 'pen' ),
					__( 'Primary', 'pen' )
				)
			),
			'header_secondary' => sprintf(
				/* Translators: Widget area, e.g. "Header - Primary" Widget Area . */
				__( 'Hide the "%s" Widget Area', 'pen' ),
				sprintf(
					'%s - %s',
					__( 'Header', 'pen' ),
					__( 'Secondary', 'pen' )
				)
			),
			'search_top'       => sprintf(
				/* Translators: Widget area, e.g. "Header - Primary" Widget Area . */
				__( 'Hide the "%s" Widget Area', 'pen' ),
				sprintf(
					'%s - %s',
					__( 'Search', 'pen' ),
					__( 'Top', 'pen' )
				)
			),
			'search_left'      => sprintf(
				/* Translators: Widget area, e.g. "Header - Primary" Widget Area . */
				__( 'Hide the "%s" Widget Area', 'pen' ),
				sprintf(
					'%s - %s',
					__( 'Search', 'pen' ),
					__( 'Left', 'pen' )
				)
			),
			'search_right'     => sprintf(
				/* Translators: Widget area, e.g. "Header - Primary" Widget Area . */
				__( 'Hide the "%s" Widget Area', 'pen' ),
				sprintf(
					'%s - %s',
					__( 'Search', 'pen' ),
					__( 'Right', 'pen' )
				)
			),
			'search_bottom'    => sprintf(
				/* Translators: Widget area, e.g. "Header - Primary" Widget Area . */
				__( 'Hide the "%s" Widget Area', 'pen' ),
				sprintf(
					'%s - %s',
					__( 'Search', 'pen' ),
					__( 'Bottom', 'pen' )
				)
			),
			'top'              => sprintf(
				/* Translators: Widget area, e.g. "Top" Widget Area . */
				__( 'Hide the "%s" Widget Area', 'pen' ),
				__( 'Top', 'pen' )
			),
			'left'             => __( 'Hide the Left Sidebar', 'pen' ),
			'right'            => __( 'Hide the Right Sidebar', 'pen' ),
			'content_top'      => sprintf(
				/* Translators: Widget area, e.g. "Header - Primary" Widget Area . */
				__( 'Hide the "%s" Widget Area', 'pen' ),
				sprintf(
					'%s - %s',
					__( 'Content', 'pen' ),
					__( 'Top', 'pen' )
				)
			),
			'content_bottom'   => sprintf(
				/* Translators: Widget area, e.g. "Header - Primary" Widget Area . */
				__( 'Hide the "%s" Widget Area', 'pen' ),
				sprintf(
					'%s - %s',
					__( 'Content', 'pen' ),
					__( 'Bottom', 'pen' )
				)
			),
			'bottom'           => sprintf(
				/* Translators: Widget area, e.g. "Header Primary" Widget Area . */
				__( 'Hide the "%s" Widget Area', 'pen' ),
				__( 'Bottom', 'pen' )
			),
			'footer_top'       => sprintf(
				/* Translators: Widget area, e.g. "Header - Primary" Widget Area . */
				__( 'Hide the "%s" Widget Area', 'pen' ),
				sprintf(
					'%s - %s',
					__( 'Footer', 'pen' ),
					__( 'Top', 'pen' )
				)
			),
			'footer_left'      => sprintf(
				/* Translators: Widget area, e.g. "Header - Primary" Widget Area . */
				__( 'Hide the "%s" Widget Area', 'pen' ),
				sprintf(
					'%s - %s',
					__( 'Footer', 'pen' ),
					__( 'Left', 'pen' )
				)
			),
			'footer_right'     => sprintf(
				/* Translators: Widget area, e.g. "Header - Primary" Widget Area . */
				__( 'Hide the "%s" Widget Area', 'pen' ),
				sprintf(
					'%s - %s',
					__( 'Footer', 'pen' ),
					__( 'Right', 'pen' )
				)
			),
			'footer_bottom'    => sprintf(
				/* Translators: Widget area, e.g. "Header - Primary" Widget Area . */
				__( 'Hide the "%s" Widget Area', 'pen' ),
				sprintf(
					'%s - %s',
					__( 'Footer', 'pen' ),
					__( 'Bottom', 'pen' )
				)
			),
		);

		foreach ( $widget_areas as $id => $label ) {
			$setting_id = 'pen_sidebar_' . $id . '_display';
			pen_post_meta_checkbox( $post->ID, $setting_id, $label, $label_prefix );
		}
		?>
					</fieldset>

				</div>
			</div>
		</div>

		<div id="pen_postmeta_lists" class="pen_postmeta_options pen_post_meta_list postbox">

			<h3>
		<?php
		esc_html_e( 'List Views', 'pen' );
		?>
			</h3>
			<div class="pen_postmeta_container">
				<div>
					<fieldset>
						<legend>
		<?php
		$section_title = __( 'Featured Image', 'pen' );
		$label_prefix  = $section_title;
		echo esc_html( $section_title );
		?>
						</legend>
						<p class="pen_postmeta_tip">
		<?php
		echo esc_html(
			sprintf(
				/* Translators: Path to the settings. */
				__( 'Some of these settings would only apply to the %1$s layout (%2$s).', 'pen' ),
				__( 'Plain List', 'pen' ),
				sprintf(
					'%1$s &rarr; %2$s &rarr; %3$s',
					__( 'Customize', 'pen' ),
					__( 'Content', 'pen' ),
					__( 'Layout', 'pen' )
				)
			)
		);
		?>
						</p>
		<?php
		$setting_id = 'background_image_content_title_dynamic';
		// There is no such option as list_background_image_content_title_dynamic
		// so we will add list_ prefix down there.
		$label = sprintf(
			'%1$s &rarr; %2$s &rarr; %3$s',
			__( 'Background', 'pen' ),
			__( 'Content', 'pen' ),
			__( 'Title', 'pen' )
		);
		if ( pen_option_get( $setting_id ) ) {
			$default = array(
				'value' => 'yes',
				'text'  => __( 'Yes', 'pen' ),
			);
		} else {
			$default = array(
				'value' => 'no',
				'text'  => __( 'No', 'pen' ),
			);
		}
		$choices    = array(
			'default' => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Default', 'pen' ),
				esc_html( $default['text'] )
			),
			'yes'     => __( 'Yes', 'pen' ),
			'no'      => __( 'No', 'pen' ),
		);
		$setting_id = 'list_' . $setting_id;
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default['value'], $label, $label_prefix );

		$setting_id = 'list_thumbnail_display';
		$label      = __( 'Featured Image', 'pen' );
		if ( pen_option_get( $setting_id ) ) {
			$default = array(
				'value' => 'yes',
				'text'  => __( 'Show', 'pen' ),
			);
		} else {
			$default = array(
				'value' => 'no',
				'text'  => __( 'Hide', 'pen' ),
			);
		}
		$choices = array(
			'default' => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Default', 'pen' ),
				esc_html( $default['text'] )
			),
			'yes'     => __( 'Show', 'pen' ),
			'no'      => __( 'Hide', 'pen' ),
		);
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default['value'], $label, $label_prefix );

		$setting_id      = 'list_thumbnail_resize';
		$label           = __( 'Size', 'pen' );
		$default         = pen_option_get( $setting_id );
		$thumbnail_sizes = array(
			'default' => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Default', 'pen' ),
				esc_html( ucwords( str_replace( '_', ' ', $default ) ) )
			),
		);
		$thumbnail_sizes = array_merge( $thumbnail_sizes, $options_image_size );
		pen_post_meta_select( $post->ID, $setting_id, $thumbnail_sizes, $default, $label, $label_prefix );

		$setting_id = 'list_thumbnail_rotate';
		$label      = __( 'Rotate', 'pen' );
		if ( pen_option_get( $setting_id ) ) {
			$default = array(
				'value' => 'yes',
				'text'  => __( 'Yes', 'pen' ),
			);
		} else {
			$default = array(
				'value' => 'no',
				'text'  => __( 'No', 'pen' ),
			);
		}
		$choices = array(
			'default' => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Default', 'pen' ),
				esc_html( $default['text'] )
			),
			'yes'     => __( 'Yes', 'pen' ),
			'no'      => __( 'No', 'pen' ),
		);
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default['value'], $label, $label_prefix );

		$setting_id = 'list_thumbnail_alignment';
		$label      = __( 'Alignment', 'pen' );
		$default    = pen_option_get( $setting_id );
		$choices    = array(
			'left'   => __( 'Left', 'pen' ),
			'center' => __( 'Center', 'pen' ),
			'right'  => __( 'Right', 'pen' ),
		);
		$choices    = array_merge(
			array(
				'default' => sprintf(
					/* Translators: Just some words. */
					__( '%1$s (%2$s)', 'pen' ),
					__( 'Default', 'pen' ),
					esc_html( ! empty( $choices[ $default ] ) ? $choices[ $default ] : ucwords( $default ) )
				),
			),
			$choices
		);
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default, $label, $label_prefix );

		$setting_id = 'list_thumbnail_frame';
		$label      = _x( 'Frame', 'As in photo or picture frame.', 'pen' );
		if ( pen_option_get( $setting_id ) ) {
			$default = array(
				'value' => 'yes',
				'text'  => __( 'Yes', 'pen' ),
			);
		} else {
			$default = array(
				'value' => 'no',
				'text'  => __( 'No', 'pen' ),
			);
		}
		$choices = array(
			'default' => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Default', 'pen' ),
				esc_html( $default['text'] )
			),
			'yes'     => __( 'Yes', 'pen' ),
			'no'      => __( 'No', 'pen' ),
		);
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default['value'], $label, $label_prefix );

		$setting_id = 'color_list_thumbnail_frame';
		$label      = sprintf(
			'%1$s &rarr; %2$s',
			_x( 'Frame', 'As in photo or picture frame.', 'pen' ),
			__( 'Color', 'pen' )
		);
		if ( '#000000' === pen_option_get( $setting_id ) ) {
			$default = array(
				'value' => 'dark',
				'text'  => __( 'Dark', 'pen' ),
			);
		} else {
			$default = array(
				'value' => 'light',
				'text'  => __( 'Light', 'pen' ),
			);
		}
		$choices = array(
			'default' => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Default', 'pen' ),
				esc_html( $default['text'] )
			),
			'#000000' => __( 'Dark', 'pen' ),
			'#ffffff' => __( 'Light', 'pen' ),
		);
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default['value'], $label, $label_prefix );

		$setting_id = 'list_tile_thumbnail_style';
		$label      = sprintf(
			/* Translators: Just some words. */
			__( '%1$s: %2$s (%3$s)', 'pen' ),
			__( 'Featured Image', 'pen' ),
			__( 'Style', 'pen' ),
			__( 'Tiles', 'pen' )
		);
		$default = pen_option_get( $setting_id );
		$choices = array(
			'default' => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Default', 'pen' ),
				sprintf(
					/* Translators: Just a number. */
					__( 'Style %d', 'pen' ),
					esc_html( $default )
				)
			),
			0         => __( 'None', 'pen' ),
		);
		for ( $i = 1; $i <= 10; $i++ ) {
			/* Translators: The style number. */
			$choices[ $i ] = sprintf( __( 'Style %d', 'pen' ), $i );
		}
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default, $label, $label_prefix );

		$setting_id = 'list_masonry_thumbnail_style';
		$label      = sprintf(
			/* Translators: Just some words. */
			__( '%1$s: %2$s (%3$s)', 'pen' ),
			__( 'Featured Image', 'pen' ),
			__( 'Style', 'pen' ),
			'jQuery Masonry'
		);
		$default = pen_option_get( $setting_id );
		$choices = array(
			'default' => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Default', 'pen' ),
				sprintf(
					/* Translators: Just a number. */
					__( 'Style %d', 'pen' ),
					esc_html( $default )
				)
			),
			0         => __( 'None', 'pen' ),
		);
		for ( $i = 1; $i <= 10; $i++ ) {
			/* Translators: Just a number. */
			$choices[ $i ] = sprintf( __( 'Style %d', 'pen' ), $i );
		}
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default, $label, $label_prefix );
		?>
					</fieldset>

					<fieldset>
						<legend>
		<?php
		$section_title = __( 'Layout', 'pen' );
		$label_prefix  = $section_title;
		echo esc_html( $section_title );
		?>
						</legend>
		<?php
		if ( 'page' === $post_type ) {
			$setting_id = 'list_type';
			$label      = __( 'List Type', 'pen' );
			$default    = pen_option_get( $setting_id );
			$choices    = array(
				'tiles'   => __( 'Tiles', 'pen' ),
				'masonry' => 'jQuery Masonry',
				'plain'   => __( 'Plain List', 'pen' ),
			);
			$choices    = array_merge(
				array(
					'default' => sprintf(
						/* Translators: Just some words. */
						__( '%1$s (%2$s)', 'pen' ),
						__( 'Default', 'pen' ),
						esc_html( ! empty( $choices[ $default ] ) ? $choices[ $default ] : ucwords( $default ) )
					),
				),
				$choices
			);
			pen_post_meta_select( $post->ID, $setting_id, $choices, $default, $label, $label_prefix );
		}

		$setting_id = 'list_header_alignment';
		$label      = sprintf(
			'%1$s &rarr; %2$s &rarr; %3$s &rarr; %4$s',
			__( 'Content', 'pen' ),
			__( 'Header', 'pen' ),
			__( 'Alignment', 'pen' ),
			__( 'Center', 'pen' )
		);
		if ( pen_option_get( $setting_id ) ) {
			$default = array(
				'value' => 'yes',
				'text'  => __( 'Yes', 'pen' ),
			);
		} else {
			$default = array(
				'value' => 'no',
				'text'  => __( 'No', 'pen' ),
			);
		}
		$choices = array(
			'default' => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Default', 'pen' ),
				esc_html( $default['text'] )
			),
			'yes'     => __( 'Yes', 'pen' ),
			'no'      => __( 'No', 'pen' ),
		);
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default['value'], $label, $label_prefix );

		$setting_id = 'list_title_alignment';
		$label      = sprintf(
			'%1$s &rarr; %2$s &rarr; %3$s &rarr; %4$s',
			__( 'Content', 'pen' ),
			__( 'Title', 'pen' ),
			__( 'Alignment', 'pen' ),
			__( 'Center', 'pen' )
		);
		if ( pen_option_get( $setting_id ) ) {
			$default = array(
				'value' => 'yes',
				'text'  => __( 'Yes', 'pen' ),
			);
		} else {
			$default = array(
				'value' => 'no',
				'text'  => __( 'No', 'pen' ),
			);
		}
		$choices = array(
			'default' => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Default', 'pen' ),
				esc_html( $default['text'] )
			),
			'yes'     => __( 'Yes', 'pen' ),
			'no'      => __( 'No', 'pen' ),
		);
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default['value'], $label, $label_prefix );
		?>
					</fieldset>

					<fieldset>
						<legend>
		<?php
		$section_title = __( 'Animation', 'pen' );
		$label_prefix  = $section_title;
		echo esc_html( $section_title );
		?>
						</legend>
		<?php
		$setting_id = 'list_animation_reveal';
		$label      = __( 'Items', 'pen' );
		$default    = pen_option_get( $setting_id );
		$animations = array(
			'default' => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Default', 'pen' ),
				esc_html( $default )
			),
		);
		$animations = array_merge( $animations, $options_animation );
		pen_post_meta_select( $post->ID, $setting_id, $animations, $default, $label, $label_prefix );

		$setting_id       = 'list_animation_delay_reveal';
		$label            = sprintf(
			'%1$s &rarr; %2$s',
			__( 'Animation Delay', 'pen' ),
			__( 'Items', 'pen' )
		);
		$default          = (int) pen_option_get( $setting_id );
		$animations_delay = array(
			'default' => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Default', 'pen' ),
				esc_html( $default ? $default : __( 'None', 'pen' ) )
			),
		);
		$animations_delay = array_merge( $animations_delay, $options_animation_delay );
		pen_post_meta_select( $post->ID, $setting_id, $animations_delay, $default, $label, $label_prefix );

		$setting_id = 'list_thumbnail_animation_reveal';
		$label      = __( 'Featured Image', 'pen' );
		$default    = pen_option_get( $setting_id );
		$animations = array(
			'default' => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Default', 'pen' ),
				esc_html( $default )
			),
		);
		$animations = array_merge( $animations, $options_animation );
		pen_post_meta_select( $post->ID, $setting_id, $animations, $default, $label, $label_prefix );

		$setting_id       = 'list_thumbnail_animation_delay_reveal';
		$label            = sprintf(
			'%1$s &rarr; %2$s',
			__( 'Animation Delay', 'pen' ),
			__( 'Featured Image', 'pen' )
		);
		$default          = (int) pen_option_get( $setting_id );
		$animations_delay = array(
			'default' => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Default', 'pen' ),
				esc_html( $default ? $default : __( 'None', 'pen' ) )
			),
		);
		$animations_delay = array_merge( $animations_delay, $options_animation_delay );
		pen_post_meta_select( $post->ID, $setting_id, $animations_delay, $default, $label, $label_prefix );
		?>
					</fieldset>

					<fieldset>
						<legend>
		<?php
		$section_title = __( 'Visibility', 'pen' );
		$label_prefix  = $section_title;
		echo esc_html( $section_title );
		?>
						</legend>
		<?php
		$setting_id = 'list_header_display';
		$label      = sprintf(
			'%1$s &rarr; %2$s',
			__( 'Content', 'pen' ),
			__( 'Header', 'pen' )
		);
		if ( pen_option_get( $setting_id ) ) {
			$default = array(
				'value' => 'yes',
				'text'  => __( 'Show', 'pen' ),
			);
		} else {
			$default = array(
				'value' => 'no',
				'text'  => __( 'Hide', 'pen' ),
			);
		}
		$choices = array(
			'default' => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Default', 'pen' ),
				esc_html( $default['text'] )
			),
			'yes'     => __( 'Show', 'pen' ),
			'no'      => __( 'Hide', 'pen' ),
		);
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default['value'], $label, $label_prefix );

		$setting_id = 'list_title_display';
		$label      = sprintf(
			'%1$s &rarr; %2$s',
			__( 'Content', 'pen' ),
			__( 'Title', 'pen' )
		);
		if ( pen_option_get( $setting_id ) ) {
			$default = array(
				'value' => 'yes',
				'text'  => __( 'Show', 'pen' ),
			);
		} else {
			$default = array(
				'value' => 'no',
				'text'  => __( 'Hide', 'pen' ),
			);
		}
		$choices = array(
			'default' => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Default', 'pen' ),
				esc_html( $default['text'] )
			),
			'yes'     => __( 'Show', 'pen' ),
			'no'      => __( 'Hide', 'pen' ),
		);
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default['value'], $label, $label_prefix );

		if ( 'post' === $post_type ) {

			$setting_id = 'list_author_display';
			$label      = __( 'Author', 'pen' );
			if ( pen_option_get( $setting_id ) ) {
				$default = array(
					'value' => 'yes',
					'text'  => __( 'Show', 'pen' ),
				);
			} else {
				$default = array(
					'value' => 'no',
					'text'  => __( 'Hide', 'pen' ),
				);
			}
			$choices = array(
				'default' => sprintf(
					/* Translators: Just some words. */
					__( '%1$s (%2$s)', 'pen' ),
					__( 'Default', 'pen' ),
					esc_html( $default['text'] )
				),
				'yes'     => __( 'Show', 'pen' ),
				'no'      => __( 'Hide', 'pen' ),
			);
			pen_post_meta_select( $post->ID, $setting_id, $choices, $default['value'], $label, $label_prefix );

			$setting_id = 'list_date_display';
			$label      = __( 'Publish Date', 'pen' );
			if ( pen_option_get( $setting_id ) ) {
				$default = array(
					'value' => 'yes',
					'text'  => __( 'Show', 'pen' ),
				);
			} else {
				$default = array(
					'value' => 'no',
					'text'  => __( 'Hide', 'pen' ),
				);
			}
			$choices = array(
				'default' => sprintf(
					/* Translators: Just some words. */
					__( '%1$s (%2$s)', 'pen' ),
					__( 'Default', 'pen' ),
					esc_html( $default['text'] )
				),
				'yes'     => __( 'Show', 'pen' ),
				'no'      => __( 'Hide', 'pen' ),
			);
			pen_post_meta_select( $post->ID, $setting_id, $choices, $default['value'], $label, $label_prefix );

			$setting_id = 'list_category_display';
			$label      = __( 'Categories', 'pen' );
			if ( pen_option_get( $setting_id ) ) {
				$default = array(
					'value' => 'yes',
					'text'  => __( 'Show', 'pen' ),
				);
			} else {
				$default = array(
					'value' => 'no',
					'text'  => __( 'Hide', 'pen' ),
				);
			}
			$choices = array(
				'default' => sprintf(
					/* Translators: Just some words. */
					__( '%1$s (%2$s)', 'pen' ),
					__( 'Default', 'pen' ),
					esc_html( $default['text'] )
				),
				'yes'     => __( 'Show', 'pen' ),
				'no'      => __( 'Hide', 'pen' ),
			);
			pen_post_meta_select( $post->ID, $setting_id, $choices, $default['value'], $label, $label_prefix );

			$setting_id = 'list_summary_display';
			$label      = __( 'Summary', 'pen' );
			if ( pen_option_get( $setting_id ) ) {
				$default = array(
					'value' => 'yes',
					'text'  => __( 'Show', 'pen' ),
				);
			} else {
				$default = array(
					'value' => 'no',
					'text'  => __( 'Hide', 'pen' ),
				);
			}
			$choices = array(
				'default' => sprintf(
					/* Translators: Just some words. */
					__( '%1$s (%2$s)', 'pen' ),
					__( 'Default', 'pen' ),
					esc_html( $default['text'] )
				),
				'yes'     => __( 'Show', 'pen' ),
				'no'      => __( 'Hide', 'pen' ),
			);
			pen_post_meta_select( $post->ID, $setting_id, $choices, $default['value'], $label, $label_prefix );

			$setting_id = 'list_profile_display';
			$label      = __( 'Author Profile', 'pen' );
			if ( pen_option_get( $setting_id ) ) {
				$default = array(
					'value' => 'yes',
					'text'  => __( 'Show', 'pen' ),
				);
			} else {
				$default = array(
					'value' => 'no',
					'text'  => __( 'Hide', 'pen' ),
				);
			}
			$choices = array(
				'default' => sprintf(
					/* Translators: Just some words. */
					__( '%1$s (%2$s)', 'pen' ),
					__( 'Default', 'pen' ),
					esc_html( $default['text'] )
				),
				'yes'     => __( 'Show', 'pen' ),
				'no'      => __( 'Hide', 'pen' ),
			);
			pen_post_meta_select( $post->ID, $setting_id, $choices, $default['value'], $label, $label_prefix );

			$setting_id = 'list_tags_display';
			$label      = __( 'Tags', 'pen' );
			if ( pen_option_get( $setting_id ) ) {
				$default = array(
					'value' => 'yes',
					'text'  => __( 'Show', 'pen' ),
				);
			} else {
				$default = array(
					'value' => 'no',
					'text'  => __( 'Hide', 'pen' ),
				);
			}
			$choices = array(
				'default' => sprintf(
					/* Translators: Just some words. */
					__( '%1$s (%2$s)', 'pen' ),
					__( 'Default', 'pen' ),
					esc_html( $default['text'] )
				),
				'yes'     => __( 'Show', 'pen' ),
				'no'      => __( 'Hide', 'pen' ),
			);
			pen_post_meta_select( $post->ID, $setting_id, $choices, $default['value'], $label, $label_prefix );

		}

		$setting_id = 'list_footer_display';
		$label      = sprintf(
			'%1$s &rarr; %2$s',
			__( 'Content', 'pen' ),
			__( 'Footer', 'pen' )
		);
		if ( pen_option_get( $setting_id ) ) {
			$default = array(
				'value' => 'yes',
				'text'  => __( 'Show', 'pen' ),
			);
		} else {
			$default = array(
				'value' => 'no',
				'text'  => __( 'Hide', 'pen' ),
			);
		}
		$choices = array(
			'default' => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Default', 'pen' ),
				esc_html( $default['text'] )
			),
			'yes'     => __( 'Show', 'pen' ),
			'no'      => __( 'Hide', 'pen' ),
		);
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default['value'], $label, $label_prefix );

		$setting_id = 'list_button_comment_display';
		$label      = sprintf(
			'%1$s &rarr; %2$s',
			__( 'Button', 'pen' ),
			_x( 'Comment', 'noun', 'pen' )
		);
		if ( pen_option_get( $setting_id ) ) {
			$default = array(
				'value' => 'yes',
				'text'  => __( 'Show', 'pen' ),
			);
		} else {
			$default = array(
				'value' => 'no',
				'text'  => __( 'Hide', 'pen' ),
			);
		}
		$choices = array(
			'default' => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Default', 'pen' ),
				esc_html( $default['text'] )
			),
			'yes'     => __( 'Show', 'pen' ),
			'no'      => __( 'Hide', 'pen' ),
		);
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default['value'], $label, $label_prefix );

		$setting_id = 'list_button_edit_display';
		$label      = sprintf(
			'%1$s &rarr; %2$s',
			__( 'Button', 'pen' ),
			__( 'Edit', 'pen' )
		);
		if ( pen_option_get( $setting_id ) ) {
			$default = array(
				'value' => 'yes',
				'text'  => __( 'Show', 'pen' ),
			);
		} else {
			$default = array(
				'value' => 'no',
				'text'  => __( 'Hide', 'pen' ),
			);
		}
		$choices = array(
			'default' => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Default', 'pen' ),
				esc_html( $default['text'] )
			),
			'yes'     => __( 'Show', 'pen' ),
			'no'      => __( 'Hide', 'pen' ),
		);
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default['value'], $label, $label_prefix );

		$setting_id = 'list_button_read_more_display';
		$label      = sprintf(
			'%1$s &rarr; %2$s',
			__( 'Button', 'pen' ),
			__( 'Read More', 'pen' )
		);
		if ( pen_option_get( $setting_id ) ) {
			$default = array(
				'value' => 'yes',
				'text'  => __( 'Show', 'pen' ),
			);
		} else {
			$default = array(
				'value' => 'no',
				'text'  => __( 'Hide', 'pen' ),
			);
		}
		$choices = array(
			'default' => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Default', 'pen' ),
				esc_html( $default['text'] )
			),
			'yes'     => __( 'Show', 'pen' ),
			'no'      => __( 'Hide', 'pen' ),
		);
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default['value'], $label, $label_prefix );
		?>
					</fieldset>

					<fieldset>
						<legend>
		<?php
		$section_title = __( 'Wording', 'pen' );
		$label_prefix  = $section_title;
		echo esc_html( $section_title );
		?>
						</legend>
		<?php
		$setting_id = 'list_button_read_more_text';
		$label      = __( 'Read More', 'pen' );
		$default    = pen_option_get( $setting_id );
		$choices    = array(
			'buy_now'          => __( 'Buy Now', 'pen' ),
			'continue_reading' => sprintf(
				/* Translators: Content title. */
				__( 'Continue reading %s', 'pen' ),
				sprintf(
					/* Translators: Just some text. */
					__( '[%s]', 'pen' ),
					sprintf(
						/* Translators: Just some text. */
						__( '%1$s: %2$s', 'pen' ),
						__( 'Hidden', 'pen' ),
						__( 'Content Title', 'pen' )
					)
				)
			),
			'details'          => __( 'Details', 'pen' ),
			'download'         => __( 'Download', 'pen' ),
			'enrol_now'        => __( 'Enrol Now', 'pen' ),
			'free_download'    => __( 'Free Download', 'pen' ),
			'join_now'         => __( 'Join Now', 'pen' ),
			'know_more'        => __( 'Know More', 'pen' ),
			'members_only'     => __( 'Members Only', 'pen' ),
			'more'             => __( 'More', 'pen' ),
			'order_now'        => __( 'Order Now', 'pen' ),
			'read'             => __( 'Read', 'pen' ),
			'read_more'        => __( 'Read More', 'pen' ),
			'register_now'     => __( 'Register Now', 'pen' ),
			'view'             => __( 'View', 'pen' ),
		);
		$choices    = array_merge(
			array(
				'default' => sprintf(
					/* Translators: Just some words. */
					__( '%1$s (%2$s)', 'pen' ),
					__( 'Default', 'pen' ),
					esc_html( ! empty( $choices[ $default ] ) ? $choices[ $default ] : ucwords( $default ) )
				),
			),
			$choices
		);
		pen_post_meta_select( $post->ID, $setting_id, $choices, $default, $label, $label_prefix );
		?>
					</fieldset>

		<?php
		if ( 'post' === $post_type ) {
			?>
					<fieldset>
						<legend>
			<?php
			$section_title = __( 'Location', 'pen' );
			$label_prefix  = $section_title;
			echo esc_html( $section_title );
			?>
						</legend>

			<?php
			$setting_id = 'list_author_location';
			$label      = __( 'Author', 'pen' );
			$default    = pen_option_get( $setting_id );
			$choices    = array(
				'header' => sprintf(
					'%1$s &rarr; %2$s',
					__( 'Content', 'pen' ),
					__( 'Header', 'pen' )
				),
				'footer' => sprintf(
					'%1$s &rarr; %2$s',
					__( 'Content', 'pen' ),
					__( 'Footer', 'pen' )
				),
			);
			$choices    = array_merge(
				array(
					'default' => sprintf(
						/* Translators: Just some words. */
						__( '%1$s (%2$s)', 'pen' ),
						__( 'Default', 'pen' ),
						esc_html( ! empty( $choices[ $default ] ) ? $choices[ $default ] : ucwords( $default ) )
					),
				),
				$choices
			);
			pen_post_meta_select( $post->ID, $setting_id, $choices, $default, $label, $label_prefix );

			$setting_id = 'list_date_location';
			$label      = __( 'Publish Date', 'pen' );
			$default    = pen_option_get( $setting_id );
			$choices    = array(
				'header' => sprintf(
					'%1$s &rarr; %2$s',
					__( 'Content', 'pen' ),
					__( 'Header', 'pen' )
				),
				'footer' => sprintf(
					'%1$s &rarr; %2$s',
					__( 'Content', 'pen' ),
					__( 'Footer', 'pen' )
				),
			);
			$choices    = array_merge(
				array(
					'default' => sprintf(
						/* Translators: Just some words. */
						__( '%1$s (%2$s)', 'pen' ),
						__( 'Default', 'pen' ),
						esc_html( ! empty( $choices[ $default ] ) ? $choices[ $default ] : ucwords( $default ) )
					),
				),
				$choices
			);
			pen_post_meta_select( $post->ID, $setting_id, $choices, $default, $label, $label_prefix );

			$setting_id = 'list_category_location';
			$label      = __( 'Categories', 'pen' );
			$default    = pen_option_get( $setting_id );
			$choices    = array(
				'header' => sprintf(
					'%1$s &rarr; %2$s',
					__( 'Content', 'pen' ),
					__( 'Header', 'pen' )
				),
				'footer' => sprintf(
					'%1$s &rarr; %2$s',
					__( 'Content', 'pen' ),
					__( 'Footer', 'pen' )
				),
			);
			$choices    = array_merge(
				array(
					'default' => sprintf(
						/* Translators: Just some words. */
						__( '%1$s (%2$s)', 'pen' ),
						__( 'Default', 'pen' ),
						esc_html( ! empty( $choices[ $default ] ) ? $choices[ $default ] : ucwords( $default ) )
					),
				),
				$choices
			);
			pen_post_meta_select( $post->ID, $setting_id, $choices, $default, $label, $label_prefix );
			?>
					</fieldset>
			<?php
		}
		?>
				</div>
			</div>
		</div>
	</div>

	<div id="pen_meta_box_footer">
		<?php
		$pen_post_meta_hp = esc_attr( md5( NONCE_SALT . $post->ID . gmdate( 'd' ) ) );
		?>

		<input class="screen-reader-text" type="email" name="<?php echo $pen_post_meta_hp; /* phpcs:ignore */ ?>" id="<?php echo $pen_post_meta_hp; /* phpcs:ignore */ ?>" size="30" value="" />

		<div class="pen_left">
			<a href="<?php echo esc_url( PEN_THEME_SUPPORT_URL ); ?>" class="button pen_support" target="_blank">
		<?php
		esc_html_e( 'Do you need help?', 'pen' );
		?>
			</a>
		</div>
	</div>

	<div style="clear:both"></div>
		<?php
		echo ob_get_clean(); /* phpcs:ignore */
	}
}

if ( ! function_exists( 'pen_post_meta_select' ) ) {
	/**
	 * Generates HTML for <select> fields.
	 *
	 * @param int    $content_id   The content ID.
	 * @param string $setting_id   The setting ID.
	 * @param array  $choices      Choices.
	 * @param mixed  $default      The default.
	 * @param string $label        Field label.
	 * @param array  $label_prefix Label prefix. Only visible to screen readers.
	 *
	 * @since Pen 1.0.8
	 * @return void
	 */
	function pen_post_meta_select( $content_id, $setting_id, $choices, $default, $label, $label_prefix = '' ) {
		$setting_id    = 'pen_' . $setting_id . '_override';
		$current_value = get_post_meta( $content_id, $setting_id, true );
		$setting_id    = esc_attr( $setting_id );
		?>
		<div class="pen_postmeta_wrapper" id="<?php echo $setting_id; /* phpcs:ignore */ ?>_wrap">
			<label for="<?php echo $setting_id; /* phpcs:ignore */ ?>">
		<?php
		if ( $label_prefix ) {
			?>
				<span class="screen-reader-text">
			<?php
			echo esc_html(
				sprintf(
					'%s: ',
					$label_prefix
				)
			);
			?>
				</span>
			<?php
		}
		echo esc_html( $label );
		?>
			</label>
			<select class="widefat" name="<?php echo $setting_id; /* phpcs:ignore */ ?>" id="<?php echo $setting_id; /* phpcs:ignore */ ?>" data-default="<?php echo esc_attr( $default ); ?>">
		<?php
		foreach ( $choices as $id => $name ) {
			?>
				<option value="<?php echo esc_attr( $id ); ?>"<?php selected( $id, $current_value ); ?>>
			<?php
			echo esc_html( $name );
			?>
				</option>
			<?php
		}
		?>
			</select>
		</div>
		<?php
	}
}

if ( ! function_exists( 'pen_post_meta_checkbox' ) ) {
	/**
	 * Generates HTML for checkboxes fields.
	 *
	 * @param int    $content_id   The content ID.
	 * @param string $setting_id   The setting ID.
	 * @param string $label        Field label.
	 * @param string $label_prefix Label prefix. Only visible to screen readers.
	 *
	 * @since Pen 1.0.8
	 * @return void
	 */
	function pen_post_meta_checkbox( $content_id, $setting_id, $label, $label_prefix = '' ) {
		$current_value = get_post_meta( $content_id, $setting_id, true );
		$setting_id    = esc_attr( $setting_id );
		?>
		<label for="<?php echo $setting_id; /* phpcs:ignore */ ?>" class="pen_postmeta_wrapper" id="<?php echo $setting_id; /* phpcs:ignore */ ?>_wrap">
			<input type="checkbox" name="<?php echo $setting_id; /* phpcs:ignore */ ?>" id="<?php echo $setting_id; /* phpcs:ignore */ ?>" <?php checked( $current_value, 'on' ); ?>>
		<?php
		if ( $label_prefix ) {
			?>
				<span class="screen-reader-text">
			<?php
			echo esc_html(
				sprintf(
					'%s: ',
					$label_prefix
				)
			);
			?>
				</span>
			<?php
		}
		echo esc_html( $label );
		?>
		</label>
		<?php
	}
}

if ( ! function_exists( 'pen_post_meta_save' ) ) {
	/**
	 * Saves the meta data.
	 *
	 * @since Pen 1.0.0
	 * @return void
	 */
	function pen_post_meta_save() {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		global $post;
		if ( ! is_object( $post ) ) {
			return;
		}

		if ( wp_is_post_revision( $post->ID ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post->ID ) ) {
			return;
		}

		// Honey pot.
		if ( pen_filter_input( 'POST', md5( NONCE_SALT . $post->ID . gmdate( 'd' ) ) ) ) {
			return;
		}

		$options = pen_post_meta_options();
		foreach ( $options as $option => $label ) {
			$new = pen_filter_input( 'POST', $option );
			if ( $new ) {
				update_post_meta( $post->ID, $option, $new );
			} else {
				delete_post_meta( $post->ID, $option );
			}
		}

		// Plugin option.
		$meta_name = pen_filter_input( 'POST', 'pen_meta_name' );
		if ( $meta_name ) {
			update_post_meta( $post->ID, 'pen_meta_name', $meta_name );
		} else {
			delete_post_meta( $post->ID, 'pen_meta_name' );
		}
	}
	add_action( 'save_post', 'pen_post_meta_save' );
}

if ( ! function_exists( 'pen_post_meta_box' ) ) {
	/**
	 * Adds the meta box.
	 *
	 * @param object $post An instance of the $post.
	 *
	 * @since Pen 1.0.0
	 * @return void
	 */
	function pen_post_meta_box( $post ) {
		if ( in_array( (string) get_post_type(), array( 'page', 'post', 'product' ), true ) ) {
			add_meta_box( 'pen_meta_box', __( 'Options', 'pen' ), 'pen_post_meta', get_post_type(), 'normal', 'high' );
		}
	}
	add_action( 'add_meta_boxes', 'pen_post_meta_box' );
}

if ( ! function_exists( 'pen_post_meta_scripts' ) ) {
	/**
	 * Adds post meta JavaScripts.
	 *
	 * @param string $hook_suffix The file name.
	 *
	 * @since Pen 1.0.0
	 * @return void
	 */
	function pen_post_meta_scripts( $hook_suffix ) {
		if ( 'post.php' === $hook_suffix || 'post-new.php' === $hook_suffix ) {
			wp_enqueue_script( 'pen-postmeta-js', PEN_THEME_DIRECTORY_URI . '/assets/js/pen-postmeta.js', array( 'jquery' ), PEN_THEME_VERSION, true );
			wp_localize_script(
				'pen-postmeta-js',
				'pen_postmeta_js',
				array(
					'text' => array(
						'pen_theme'        => esc_html__( 'Pen', 'pen' ),
						'nothing_selected' => esc_html__( 'Please select an item.', 'pen' ),
						'toggle'           => esc_attr(
							sprintf(
								/* Translators: Just some words. */
								__( '%1$s: %2$s', 'pen' ),
								_x( 'Toggle Panel', 'verb', 'pen' ),
								sprintf(
									/* Translators: Just some words. */
									__( '%1$s - %2$s', 'pen' ),
									__( 'Pen', 'pen' ),
									__( 'Options', 'pen' )
								)
							)
						),
					),
				)
			);
			wp_enqueue_style( 'pen-postmeta-css', PEN_THEME_DIRECTORY_URI . '/assets/css/pen-postmeta.css', array(), PEN_THEME_VERSION );
		}
	}
	add_action( 'admin_enqueue_scripts', 'pen_post_meta_scripts' );
}

if ( ! function_exists( 'pen_content_title_background' ) ) {
	/**
	 * Returns background image URL for content header.
	 *
	 * @param bool $single     Result of is_singular(), for better performance.
	 * @param int  $content_id Content ID.
	 *
	 * @since Pen 1.1.1
	 * @return string
	 */
	function pen_content_title_background( $single, $content_id ) {
		$background_image_dynamic = '';
		if ( $single ) {
			$thumbnail_as_background = get_post_meta( $content_id, 'pen_content_background_image_content_title_dynamic_override', true );
			if ( ! $thumbnail_as_background || 'default' === $thumbnail_as_background ) {
				$thumbnail_as_background = pen_option_get( 'background_image_content_title_dynamic' );
			}
		} else {
			$thumbnail_as_background = get_post_meta( $content_id, 'pen_list_background_image_content_title_dynamic_override', true );
			if ( ! $thumbnail_as_background || 'default' === $thumbnail_as_background ) {
				$thumbnail_as_background = pen_option_get( 'background_image_content_title_dynamic' );
			}
		}
		if ( 'none' !== $thumbnail_as_background ) {
			$thumbnail = esc_url( get_the_post_thumbnail_url( $content_id, 'large' ) );
			if ( $thumbnail ) {
				$background_image_dynamic = $thumbnail;
			}
		}
		return $background_image_dynamic;
	}
}
