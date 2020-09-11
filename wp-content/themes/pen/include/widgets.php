<?php
/**
 * Custom widgets options.
 *
 * @package Pen
 */

defined( 'ABSPATH' ) || die();

if ( ! function_exists( 'pen_widget_options' ) ) {
	/**
	 * Color scheme and animation options for widgets.
	 *
	 * @param WP_Widget $widget   An instance of WP_Widget.
	 * @param null      $return   Whether a field was added.
	 * @param array     $instance Widget settings.
	 *
	 * @since Pen 1.0.0
	 * @return void
	 */
	function pen_widget_options( $widget, $return, $instance ) {
		$title = sprintf(
			'%s %s',
			__( 'This is a part of the Pen theme.', 'pen' ),
			__( 'These settings will be no longer available if you switch to another theme. Other settings are either parts of the WordPress core or your plugins and they will be available even without this theme.', 'pen' )
		);
		?>
		<fieldset style="border:1px dashed #ccc;margin:0 0 1em;padding:0 1em">
			<legend style="font-weight:bold" title="<?php echo esc_attr( $title ); ?>">
		<?php
		esc_html_e( 'Pen', 'pen' );
		?>
			</legend>
		<?php
		pen_widget_options_color_scheme( $widget, $return, $instance );
		pen_widget_options_animation( $widget, $return, $instance );
		pen_widget_options_animation_delay( $widget, $return, $instance );
		?>
		</fieldset>
		<?php
	}
	add_filter( 'in_widget_form', 'pen_widget_options', 10, 3 );
}

if ( ! function_exists( 'pen_widget_options_color_scheme' ) ) {
	/**
	 * Widget color scheme options.
	 *
	 * @param WP_Widget $widget   An instance of WP_Widget.
	 * @param null      $return   Whether a field was added.
	 * @param array     $instance Widget settings.
	 *
	 * @since Pen 1.0.0
	 * @return void
	 */
	function pen_widget_options_color_scheme( $widget, $return, $instance ) {
		$color = 'automatic';
		if ( ! empty( $instance['pen_theme_color_widget'] ) ) {
			$color = $instance['pen_theme_color_widget'];
		}
		$field_id   = $widget->get_field_id( 'pen_theme_color_widget' );
		$field_name = $widget->get_field_name( 'pen_theme_color_widget' );
		?>
			<p>
				<label for="<?php echo esc_attr( $field_id ); ?>">
		<?php
		echo esc_html(
			sprintf(
				/* Translators: Just some words. */
				__( '%s:', 'pen' ),
				__( 'Widget Color Scheme', 'pen' )
			)
		);
		?>
				</label>
				<select id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $field_name ); ?>">
					<option value="automatic" <?php selected( 'automatic', $color ); ?>>
		<?php
		esc_html_e( 'Automatic', 'pen' );
		?>
					</option>
					<option value="transparent" <?php selected( 'transparent', $color ); ?>>
		<?php
		esc_html_e( 'Transparent Background', 'pen' );
		?>
					</option>
		<?php
		$options = array(
			'blue'             => __( 'Blue', 'pen' ),
			'blue_flat'        => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Blue', 'pen' ),
				__( 'Flat', 'pen' )
			),
			'blue_deep'        => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Blue', 'pen' ),
				__( 'Dark', 'pen' )
			),
			'blue_deep_flat'   => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s, %3$s)', 'pen' ),
				__( 'Blue', 'pen' ),
				__( 'Dark', 'pen' ),
				__( 'Flat', 'pen' )
			),
			'brown'            => __( 'Brown', 'pen' ),
			'brown_flat'       => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Brown', 'pen' ),
				__( 'Flat', 'pen' )
			),
			'crimson'          => __( 'Crimson', 'pen' ),
			'crimson_flat'     => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Crimson', 'pen' ),
				__( 'Flat', 'pen' )
			),
			'dark'             => __( 'Dark', 'pen' ),
			'dark_flat'        => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Dark', 'pen' ),
				__( 'Flat', 'pen' )
			),
			'gray'             => __( 'Gray', 'pen' ),
			'gray_flat'        => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Gray', 'pen' ),
				__( 'Flat', 'pen' )
			),
			'green'            => __( 'Green', 'pen' ),
			'green_flat'       => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Green', 'pen' ),
				__( 'Flat', 'pen' )
			),
			'lime'             => __( 'Lime', 'pen' ),
			'lime_flat'        => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Lime', 'pen' ),
				__( 'Flat', 'pen' )
			),
			'light'            => __( 'Light', 'pen' ),
			'light_flat'       => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Light', 'pen' ),
				__( 'Flat', 'pen' )
			),
			'orange'           => __( 'Orange', 'pen' ),
			'orange_flat'      => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Orange', 'pen' ),
				__( 'Flat', 'pen' )
			),
			'pink'             => __( 'Pink', 'pen' ),
			'pink_flat'        => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Pink', 'pen' ),
				__( 'Flat', 'pen' )
			),
			'purple'           => __( 'Purple', 'pen' ),
			'purple_flat'      => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Purple', 'pen' ),
				__( 'Flat', 'pen' )
			),
			'purple_deep'      => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Purple', 'pen' ),
				__( 'Dark', 'pen' )
			),
			'purple_deep_flat' => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s, %3$s)', 'pen' ),
				__( 'Purple', 'pen' ),
				__( 'Dark', 'pen' ),
				__( 'Flat', 'pen' )
			),
			'red'              => __( 'Red', 'pen' ),
			'red_flat'         => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Red', 'pen' ),
				__( 'Flat', 'pen' )
			),
			'teal'             => __( 'Teal', 'pen' ),
			'teal_flat'        => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Teal', 'pen' ),
				__( 'Flat', 'pen' )
			),
			'violet'           => __( 'Violet', 'pen' ),
			'violet_flat'      => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Violet', 'pen' ),
				__( 'Flat', 'pen' )
			),
			'yellow'           => __( 'Yellow', 'pen' ),
			'yellow_flat'      => sprintf(
				/* Translators: Just some words. */
				__( '%1$s (%2$s)', 'pen' ),
				__( 'Yellow', 'pen' ),
				__( 'Flat', 'pen' )
			),
		);
		foreach ( $options as $key => $label ) {
			?>
					<option value="<?php echo $key; /* phpcs:ignore */ ?>" <?php selected( $key, $color ); ?>>
			<?php
			echo esc_html( $label );
			?>
					</option>
			<?php
		}
		?>
				</select>
			</p>
		<?php
	}
}

if ( ! function_exists( 'pen_widget_options_animation' ) ) {
	/**
	 * Widget animation options.
	 *
	 * @param WP_Widget $widget   An instance of WP_Widget.
	 * @param null      $return   Whether a field was added.
	 * @param array     $instance Widget settings.
	 *
	 * @since Pen 1.0.4
	 * @return void
	 */
	function pen_widget_options_animation( $widget, $return, $instance ) {
		$animation = 'fadeIn';
		if ( ! empty( $instance['pen_theme_animation_widget'] ) ) {
			$animation = $instance['pen_theme_animation_widget'];
		}
		$field_id   = $widget->get_field_id( 'pen_theme_animation_widget' );
		$field_name = $widget->get_field_name( 'pen_theme_animation_widget' );
		?>
			<p>
				<label for="<?php echo esc_attr( $field_id ); ?>">
		<?php
		echo esc_html(
			sprintf(
				/* Translators: Just some words. */
				__( '%s:', 'pen' ),
				__( 'Widget Animation', 'pen' )
			)
		);
		?>
				</label>
				<select id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $field_name ); ?>">
		<?php
		$options = pen_animations();
		foreach ( $options as $key => $label ) {
			?>
					<option value="<?php echo $key; /* phpcs:ignore */ ?>" <?php selected( $key, $animation ); ?>>
			<?php
			echo esc_html( $label );
			?>
					</option>
			<?php
		}
		?>
				</select>
			</p>
		<?php
	}
}

if ( ! function_exists( 'pen_widget_options_animation_delay' ) ) {
	/**
	 * Widget animation delay options.
	 *
	 * @param WP_Widget $widget   An instance of WP_Widget.
	 * @param null      $return   Whether a field was added.
	 * @param array     $instance Widget settings.
	 *
	 * @since Pen 1.2.8
	 * @return void
	 */
	function pen_widget_options_animation_delay( $widget, $return, $instance ) {
		$animation_delay = '';
		if ( ! empty( $instance['pen_theme_animation_delay_widget'] ) ) {
			$animation_delay = $instance['pen_theme_animation_delay_widget'];
		}
		$field_id   = $widget->get_field_id( 'pen_theme_animation_delay_widget' );
		$field_name = $widget->get_field_name( 'pen_theme_animation_delay_widget' );
		?>
			<p>
				<label for="<?php echo esc_attr( $field_id ); ?>">
		<?php
		echo esc_html(
			sprintf(
				/* Translators: Just some words. */
				__( '%s:', 'pen' ),
				__( 'Animation Delay', 'pen' )
			)
		);
		?>
				</label>
				<select id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $field_name ); ?>">
		<?php
		$options = pen_animations_delay();
		foreach ( $options as $key => $label ) {
			?>
					<option value="<?php echo $key; /* phpcs:ignore */ ?>" <?php selected( $key, $animation_delay ); ?>>
			<?php
			echo esc_html( $label );
			?>
					</option>
			<?php
		}
		?>
				</select>
			</p>
		<?php
	}
}

if ( ! function_exists( 'pen_widgets_customization_save' ) ) {
	/**
	 * Saves widget customization.
	 *
	 * @global string $sidebar.
	 *
	 * @param array     $instance     The current widget settings.
	 * @param array     $new_instance The new widget settings.
	 * @param array     $old_instance The old widget settings.
	 * @param WP_Widget $widget       The current widget instance.
	 *
	 * @since Pen 1.0.0
	 * @return array
	 */
	function pen_widgets_customization_save( $instance, $new_instance, $old_instance, $widget ) {
		if ( ! empty( $new_instance['pen_theme_color_widget'] ) ) {
			$instance['pen_theme_color_widget'] = pen_sanitize_string( $new_instance['pen_theme_color_widget'] );
		}
		if ( ! empty( $new_instance['pen_theme_animation_widget'] ) ) {
			$instance['pen_theme_animation_widget'] = pen_sanitize_animation( $new_instance['pen_theme_animation_widget'] );
		}
		if ( ! empty( $new_instance['pen_theme_animation_delay_widget'] ) ) {
			$instance['pen_theme_animation_delay_widget'] = pen_sanitize_integer( $new_instance['pen_theme_animation_delay_widget'] );
		}
		return $instance;
	}
	add_filter( 'widget_update_callback', 'pen_widgets_customization_save', 10, 4 );
}

if ( ! function_exists( 'pen_widgets_custom_classes' ) ) {
	/**
	 * Custom class names for widgets.
	 *
	 * @global wp_registered_widgets $wp_registered_widgets
	 *
	 * @param array $params Widget parameters.
	 *
	 * @since Pen 1.0.0
	 * @return array
	 */
	function pen_widgets_custom_classes( $params ) {
		global $wp_registered_widgets;
		$widget_id   = $params[0]['widget_id'];
		$instance_id = $params[1]['number'];
		if ( ! is_object( $wp_registered_widgets[ $widget_id ]['callback'][0] ) ) {
			return;
		}

		$settings = $wp_registered_widgets[ $widget_id ]['callback'][0]->get_settings();

		$class = 'class="';

		if ( isset( $settings[ $instance_id ]['title'] ) && '' !== trim( $settings[ $instance_id ]['title'] ) ) {
			$class .= 'pen_widget_has_title ';
		}

		$current_color = '';
		if ( ! empty( $settings[ $instance_id ]['pen_theme_color_widget'] ) ) {
			$current_color = $settings[ $instance_id ]['pen_theme_color_widget'];
		}

		// This is for default widgets on fresh installations.
		if ( ! empty( $params[0]['id'] ) && ( ! $current_color || 'automatic' === $current_color ) ) {
			$variables = array(
				'option'     => 'color_scheme',
				'sidebar_id' => $params[0]['id'],
			);
			$color     = pen_widget_determine_default( $variables );
		}

		if ( empty( $color ) ) {
			$color = 'transparent';
		}

		if ( $current_color && 'automatic' !== $current_color ) {
			$color = $current_color;
		}
		$class .= 'pen_widget_' . $color . ' ';
		if ( false !== strpos( $color, '_flat' ) ) {
			$class .= 'pen_widget_' . str_replace( '_flat', '', $color ) . ' ';
		}
		if ( 'transparent' !== $color ) {
			$class .= 'pen_widget_not_transparent ';
		}

		$animation = 'fadeIn';
		if ( ! empty( $settings[ $instance_id ]['pen_theme_animation_widget'] ) ) {
			$animation = $settings[ $instance_id ]['pen_theme_animation_widget'];
		}
		if ( $animation && 'none' !== $animation ) {
			$class .= 'pen_animate_on_scroll ';
			$class .= 'pen_custom_animation_' . sanitize_html_class( $animation ) . ' ';

			if ( ! empty( $settings[ $instance_id ]['pen_theme_animation_delay_widget'] ) ) {
				$animation_delay = (int) $settings[ $instance_id ]['pen_theme_animation_delay_widget'];
				if ( (int) $animation_delay ) {
					$class .= 'pen_custom_animation_delay_' . $animation_delay . ' ';
				}
			}
		}

		$params[0]['before_widget'] = str_ireplace( 'class="', $class, $params[0]['before_widget'] );
		return $params;
	}
	add_filter( 'dynamic_sidebar_params', 'pen_widgets_custom_classes' );
}

if ( ! function_exists( 'pen_widget_determine_default' ) ) {
	/**
	 * Optimal default values for widget options.
	 *
	 * @param array $variables The variables.
	 *
	 * @since Pen 1.3.0
	 * @return string
	 */
	function pen_widget_determine_default( $variables ) {

		if ( empty( $variables['option'] ) || empty( $variables['sidebar_id'] ) ) {
			return;
		}

		if ( 'color_scheme' === $variables['option'] ) {
			$color_default          = 'transparent';
			$override_default_color = false;
			// These are transparent widget areas.
			if ( in_array( $variables['sidebar_id'], array( 'sidebar-top', 'sidebar-left', 'sidebar-right' ), true ) ) {
				$override_default_color = true;
			} elseif ( 'sidebar-bottom' === $variables['sidebar_id'] && pen_option_get( 'color_bottom_background_transparent' ) ) {
				$override_default_color = true;
			}
			if ( $override_default_color ) {
				$preset = pen_preset_get( 'color' );
				switch ( $preset ) {
					case 'preset_1':
						$color_default = 'light';
						break;
					case 'preset_2':
						$color_default = 'dark';
						break;
					case 'preset_3':
						$color_default = 'dark';
						break;
					case 'preset_4':
						$color_default = 'purple_deep';
						break;
					case 'preset_5':
						$color_default = 'blue_deep';
						break;
					case 'preset_6':
						$color_default = 'violet';
						break;
					case 'preset_7':
						$color_default = 'dark';
						break;
					case 'preset_8':
						$color_default = 'violet';
						break;
					case 'preset_9':
						$color_default = 'lime';
						break;
					case 'preset_10':
						$color_default = 'dark';
						break;
					case 'preset_11':
						$color_default = 'light_flat';
						break;
					case 'preset_12':
						$color_default = 'dark_flat';
						break;
					case 'preset_13':
						$color_default = 'dark_flat';
						break;
					case 'preset_14':
						$color_default = 'dark_flat';
						break;
					case 'preset_15':
						$color_default = 'dark_flat';
						break;
					default:
						$color_default = 'transparent';
				}
			}
			return $color_default;
		}
	}
}
