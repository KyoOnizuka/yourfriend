<?php
/**
 * Template functions.
 *
 * @package Pen
 */

defined( 'ABSPATH' ) || die();

if ( ! function_exists( 'pen_html_logo' ) ) {
	/**
	 * Displays the custom logo.
	 * Does nothing if the custom logo is not available.
	 *
	 * @param string $location   Location.
	 * @param int    $content_id Content ID.
	 *
	 * @since Pen 1.0.0
	 * @return string
	 */
	function pen_html_logo( $location = 'header', $content_id = null ) {

		// For maximum compatibility.
		if ( is_numeric( $location ) ) {
			$content_id = $location;
			$location   = 'header';
		}

		if ( is_null( $content_id ) ) {
			$content_id = pen_post_id();
		}

		if ( 'header' !== $location ) {
			$location = 'header';
		}
		if ( ! pen_option_get( $location . '_logo_display' ) ) {
			return;
		}

		$logo_url       = '';
		$logo_url_light = pen_option_get( $location . '_logo_light' );
		if ( $logo_url_light ) {
			$dark  = false;
			$color = new \Pen_Theme\Color( pen_option_get( 'color_header_background_primary' ) );
			if ( $color->isDark() ) {
				$dark = true;
				// If it's a vertical gradient.
			} elseif ( 'to bottom' === pen_option_get( 'color_header_background_angle' ) ) {
				$color = new \Pen_Theme\Color( pen_option_get( 'color_header_background_secondary' ) );
				if ( $color->isDark() ) {
					$dark = true;
				}
			}
			if ( $dark ) {
				/**
				 * Although we can analyze images to determine whether they
				 * are dark or light but it is very resource intensive,
				 * so we just ignore the light-colored logo when there is
				 * a background image.
				 * In later versions we may let the user choose this, especially
				 * if we decide to add it to the plugin.
				 */
				$header_image = get_header_image();
				if ( $header_image ) {
					$dark = false;
				} else {
					$background_dynamic = get_post_meta( $content_id, 'pen_content_background_image_header_dynamic_override', true );
					if ( ! $background_dynamic || 'default' === $background_dynamic ) {
						$background_dynamic = pen_option_get( 'background_image_header_dynamic' );
					}
					if ( 'featured_image' === $background_dynamic && $content_id ) {
						$image_dynamic = get_the_post_thumbnail_url();
						if ( $image_dynamic ) {
							$dark = false;
						}
					}
				}
			}
			if ( $dark ) {
				$logo_url = $logo_url_light;
			}
		}

		if ( ! $logo_url ) {
			if ( function_exists( 'get_custom_logo' ) ) {
				$logo = trim( get_custom_logo() );
			} else {
				return;
			}
		} else {
			$id = attachment_url_to_postid( $logo_url );

			$attributes = array(
				'class'    => 'custom-logo',
				'itemprop' => 'logo',
			);

			$alt = get_post_meta( $id, '_wp_attachment_image_alt', true );
			if ( ! $alt ) {
				$attributes['alt'] = get_bloginfo( 'name', 'display' );
			}
			$logo = sprintf(
				'<a href="%1$s" class="custom-logo-link" rel="home" itemprop="url">%2$s</a>',
				esc_url( is_multisite() ? network_home_url( '/' ) : home_url( '/' ) ),
				wp_get_attachment_image( $id, 'full', false, $attributes )
			);
		}

		if ( $logo ) {
			$classes = array(
				'pen_logo',
				pen_class_animation( $location . '_logo', false, $content_id ),
			);
			$classes = implode( ' ', array_filter( $classes ) );
			ob_start();
			?>
		<span class="<?php echo esc_attr( $classes ); ?>">
			<?php
			echo $logo; /* phpcs:ignore */
			?>
		</span>
			<?php
			return ob_get_clean();
		}

	}
}

if ( ! function_exists( 'pen_html_site_title' ) ) {
	/**
	 * Displays the site title.
	 * Does nothing if the custom logo is not available.
	 *
	 * @param string $location   Location.
	 * @param int    $content_id Content ID.
	 *
	 * @since Pen 1.3.0
	 * @return string
	 */
	function pen_html_site_title( $location, $content_id ) {

		if ( ! in_array( $location, array( 'header', 'footer' ), true ) ) {
			$location = 'header';
		}

		$description              = false;
		$site_description_display = pen_option_get( $location . '_sitedescription_display' );
		if ( $site_description_display ) {
			$description = get_bloginfo( 'description', 'display' );
		}

		$site_title_display = pen_option_get( 'header_sitetitle_display' );
		$classes_sitetitle  = array(
			$description ? 'pen_has_description' : '',
			'pen_sitetitle_' . ( sanitize_html_class( $site_title_display ) ? 'show' : 'hide' ),
		);
		$classes_sitetitle  = implode( ' ', array_filter( $classes_sitetitle ) );

		$url_home = is_multisite() ? network_home_url( false ) : home_url( false );

		ob_start();
		?>
		<span class="pen_site_name">
			<a href="<?php echo esc_url( $url_home ); ?>" id="site-title" class="<?php echo esc_attr( $classes_sitetitle ); ?>" rel="home">
		<?php
		$classes_title = array(
			'site-title',
			( ! $site_title_display ) ? 'pen_element_hidden' : '',
			pen_class_animation( $location . '_sitetitle', false, $content_id ),
		);
		$classes_title = implode( ' ', array_filter( $classes_title ) );
		?>
				<span class="<?php echo esc_attr( $classes_title ); ?>">
		<?php
		bloginfo( 'name' );
		?>
				</span>
		<?php
		$description = wp_strip_all_tags( $description );
		if ( 200 < strlen( $description ) ) {
			$description = substr( $description, 0, 100 ) . ' &hellip;'; /* phpcs:ignore */
		}
		if ( $description || is_customize_preview() ) {
			$classes_description = array(
				'site-description',
				pen_class_animation( $location . '_sitedescription', false, $content_id ),
			);
			$classes_description = implode( ' ', array_filter( $classes_description ) );
			// CSS "margin" relies on :not(:empty) here hence no indentation, newlines, etc.
			?>
				<span class="<?php echo esc_attr( $classes_description ); ?>"><?php echo $description; /* phpcs:ignore */ ?></span>
			<?php
		}
		?>
			</a>
		</span>
		<?php
		return ob_get_clean();
	}
}

if ( ! function_exists( 'pen_html_connect' ) ) {
	/**
	 * Generates markup for the social network links.
	 *
	 * @param string $location   The location of the social network links (for now it can be header or footer).
	 * @param int    $content_id Content ID.
	 *
	 * @since Pen 1.0.0
	 * @return string
	 */
	function pen_html_connect( $location, $content_id = null ) {
		// For maximum compatibility.
		if ( is_null( $content_id ) ) {
			$content_id = pen_post_id();
		}

		ob_start();

		$rss_url = pen_option_get( 'rss' );
		if ( $rss_url ) {
			$rss_display = pen_option_get( 'rss_' . $location . '_display' );
			if ( $rss_display ) {
				$rss_url = explode( '|', $rss_url );
				foreach ( $rss_url as $rss_url ) {
					?>
			<li class="pen_rss" title="<?php esc_attr_e( 'Subscribe to RSS', 'pen' ); ?>">
				<a href="<?php echo esc_url( $rss_url ); ?>">
					<span class="pen_element_hidden">
					<?php
					esc_html_e( 'RSS', 'pen' );
					?>
					</span>
				</a>
			</li>
					<?php
				}
			}
		}

		$email = pen_option_get( 'email' );
		if ( $email ) {
			$email_display = pen_option_get( 'email_' . $location . '_display' );
			if ( $email_display ) {
				$email = explode( '|', $email );
				foreach ( $email as $email ) {
					if ( false !== strpos( $email, '@' ) ) {
						$email = 'mailto:' . antispambot( $email );
					}
					?>
			<li class="pen_email" title="<?php esc_attr_e( 'E-mail', 'pen' ); ?>">
				<a href="<?php echo esc_url( $email ); ?>">
					<span class="pen_element_hidden">
					<?php
					esc_html_e( 'E-mail', 'pen' );
					?>
					</span>
				</a>
			</li>
					<?php
				}
			}
		}

		$facebook_url = pen_option_get( 'facebook' );
		if ( $facebook_url ) {
			$facebook_display = pen_option_get( 'facebook_' . $location . '_display' );
			if ( $facebook_display ) {
				$facebook_url = explode( '|', $facebook_url );
				foreach ( $facebook_url as $facebook_url ) {
					?>
			<li class="pen_facebook" title="<?php esc_attr_e( 'Facebook', 'pen' ); ?>">
				<a href="<?php echo esc_url( $facebook_url ); ?>">
					<span class="pen_element_hidden">
					<?php
					esc_html_e( 'Facebook', 'pen' );
					?>
					</span>
				</a>
			</li>
					<?php
				}
			}
		}

		$instagram_url = pen_option_get( 'instagram' );
		if ( $instagram_url ) {
			$instagram_display = pen_option_get( 'instagram_' . $location . '_display' );
			if ( $instagram_display ) {
				$instagram_url = explode( '|', $instagram_url );
				foreach ( $instagram_url as $instagram_url ) {
					?>
			<li class="pen_instagram" title="<?php esc_attr_e( 'Instagram', 'pen' ); ?>">
				<a href="<?php echo esc_url( $instagram_url ); ?>">
					<span class="pen_element_hidden">
					<?php
					esc_html_e( 'Instagram', 'pen' );
					?>
					</span>
				</a>
			</li>
					<?php
				}
			}
		}

		$mewe_url = pen_option_get( 'mewe' );
		if ( $mewe_url ) {
			$mewe_display = pen_option_get( 'mewe_' . $location . '_display' );
			if ( $mewe_display ) {
				$mewe_url = explode( '|', $mewe_url );
				foreach ( $mewe_url as $mewe_url ) {
					?>
			<li class="pen_mewe" title="<?php esc_attr_e( 'MeWe', 'pen' ); ?>">
				<a href="<?php echo esc_url( $mewe_url ); ?>">
					<span class="pen_element_hidden">
					<?php
					esc_html_e( 'MeWe', 'pen' );
					?>
					</span>
				</a>
			</li>
					<?php
				}
			}
		}

		$vk_url = pen_option_get( 'vk' );
		if ( $vk_url ) {
			$vk_display = pen_option_get( 'vk_' . $location . '_display' );
			if ( $vk_display ) {
				$vk_url = explode( '|', $vk_url );
				foreach ( $vk_url as $vk_url ) {
					?>
			<li class="pen_vk" title="<?php esc_attr_e( 'VK', 'pen' ); ?>">
				<a href="<?php echo esc_url( $vk_url ); ?>">
					<span class="pen_element_hidden">
					<?php
					esc_html_e( 'VK', 'pen' );
					?>
					</span>
				</a>
			</li>
					<?php
				}
			}
		}

		$pinterest_url = pen_option_get( 'pinterest' );
		if ( $pinterest_url ) {
			$pinterest_display = pen_option_get( 'pinterest_' . $location . '_display' );
			if ( $pinterest_display ) {
				$pinterest_url = explode( '|', $pinterest_url );
				foreach ( $pinterest_url as $pinterest_url ) {
					?>
			<li class="pen_pinterest" title="<?php esc_attr_e( 'Pinterest', 'pen' ); ?>">
				<a href="<?php echo esc_url( $pinterest_url ); ?>">
					<span class="pen_element_hidden">
					<?php
					esc_html_e( 'Pinterest', 'pen' );
					?>
					</span>
				</a>
			</li>
					<?php
				}
			}
		}

		$twitter_url = pen_option_get( 'twitter' );
		if ( $twitter_url ) {
			$twitter_display = pen_option_get( 'twitter_' . $location . '_display' );
			if ( $twitter_display ) {
				$twitter_url = explode( '|', $twitter_url );
				foreach ( $twitter_url as $twitter_url ) {
					?>
			<li class="pen_twitter" title="<?php esc_attr_e( 'Twitter', 'pen' ); ?>">
				<a href="<?php echo esc_url( $twitter_url ); ?>">
					<span class="pen_element_hidden">
					<?php
					esc_html_e( 'Twitter', 'pen' );
					?>
					</span>
				</a>
			</li>
					<?php
				}
			}
		}

		$linkedin_url = pen_option_get( 'linkedin' );
		if ( $linkedin_url ) {
			$linkedin_display = pen_option_get( 'linkedin_' . $location . '_display' );
			if ( $linkedin_display ) {
				$linkedin_url = explode( '|', $linkedin_url );
				foreach ( $linkedin_url as $linkedin_url ) {
					?>
			<li class="pen_linkedin" title="<?php esc_attr_e( 'LinkedIn', 'pen' ); ?>">
				<a href="<?php echo esc_url( $linkedin_url ); ?>">
					<span class="pen_element_hidden">
					<?php
					esc_html_e( 'LinkedIn', 'pen' );
					?>
					</span>
				</a>
			</li>
					<?php
				}
			}
		}

		$bitbucket_url = pen_option_get( 'bitbucket' );
		if ( $bitbucket_url ) {
			$bitbucket_display = pen_option_get( 'bitbucket_' . $location . '_display' );
			if ( $bitbucket_display ) {
				$bitbucket_url = explode( '|', $bitbucket_url );
				foreach ( $bitbucket_url as $bitbucket_url ) {
					?>
			<li class="pen_bitbucket" title="<?php esc_attr_e( 'Bitbucket', 'pen' ); ?>">
				<a href="<?php echo esc_url( $bitbucket_url ); ?>">
					<span class="pen_element_hidden">
					<?php
					esc_html_e( 'Bitbucket', 'pen' );
					?>
					</span>
				</a>
			</li>
					<?php
				}
			}
		}

		$flickr_url = pen_option_get( 'flickr' );
		if ( $flickr_url ) {
			$flickr_display = pen_option_get( 'flickr_' . $location . '_display' );
			if ( $flickr_display ) {
				$flickr_url = explode( '|', $flickr_url );
				foreach ( $flickr_url as $flickr_url ) {
					?>
			<li class="pen_flickr" title="<?php esc_attr_e( 'Flickr', 'pen' ); ?>">
				<a href="<?php echo esc_url( $flickr_url ); ?>">
					<span class="pen_element_hidden">
					<?php
					esc_html_e( 'Flickr', 'pen' );
					?>
					</span>
				</a>
			</li>
					<?php
				}
			}
		}

		$github_url = pen_option_get( 'github' );
		if ( $github_url ) {
			$github_display = pen_option_get( 'github_' . $location . '_display' );
			if ( $github_display ) {
				$github_url = explode( '|', $github_url );
				foreach ( $github_url as $github_url ) {
					?>
			<li class="pen_github" title="<?php esc_attr_e( 'GitHub', 'pen' ); ?>">
				<a href="<?php echo esc_url( $github_url ); ?>">
					<span class="pen_element_hidden">
					<?php
					esc_html_e( 'GitHub', 'pen' );
					?>
					</span>
				</a>
			</li>
					<?php
				}
			}
		}

		$telegram_url = pen_option_get( 'telegram' );
		if ( $telegram_url ) {
			$telegram_display = pen_option_get( 'telegram_' . $location . '_display' );
			if ( $telegram_display ) {
				$telegram_url = explode( '|', $telegram_url );
				foreach ( $telegram_url as $telegram_url ) {
					?>
			<li class="pen_telegram" title="<?php esc_attr_e( 'Telegram', 'pen' ); ?>">
				<a href="<?php echo esc_url( $telegram_url ); ?>">
					<span class="pen_element_hidden">
					<?php
					esc_html_e( 'Telegram', 'pen' );
					?>
					</span>
				</a>
			</li>
					<?php
				}
			}
		}

		$whatsapp_url = pen_option_get( 'whatsapp' );
		if ( $whatsapp_url ) {
			$whatsapp_display = pen_option_get( 'whatsapp_' . $location . '_display' );
			if ( $whatsapp_display ) {
				$whatsapp_url = explode( '|', $whatsapp_url );
				foreach ( $whatsapp_url as $whatsapp_url ) {
					?>
			<li class="pen_whatsapp" title="<?php esc_attr_e( 'WhatsApp', 'pen' ); ?>">
				<a href="<?php echo esc_attr( $whatsapp_url ); ?>">
					<span class="pen_element_hidden">
					<?php
					esc_html_e( 'WhatsApp', 'pen' );
					?>
					</span>
				</a>
			</li>
					<?php
				}
			}
		}

		$skype_url = pen_option_get( 'skype' );
		if ( $skype_url ) {
			$skype_display = pen_option_get( 'skype_' . $location . '_display' );
			if ( $skype_display ) {
				$skype_url = explode( '|', $skype_url );
				foreach ( $skype_url as $skype_url ) {
					?>
			<li class="pen_skype" title="<?php esc_attr_e( 'Skype', 'pen' ); ?>">
				<a href="<?php echo esc_attr( $skype_url ); ?>">
					<span class="pen_element_hidden">
					<?php
					esc_html_e( 'Skype', 'pen' );
					?>
					</span>
				</a>
			</li>
					<?php
				}
			}
		}

		$slack_url = pen_option_get( 'slack' );
		if ( $slack_url ) {
			$slack_display = pen_option_get( 'slack_' . $location . '_display' );
			if ( $slack_display ) {
				$slack_url = explode( '|', $slack_url );
				foreach ( $slack_url as $slack_url ) {
					?>
			<li class="pen_slack" title="<?php esc_attr_e( 'Slack', 'pen' ); ?>">
				<a href="<?php echo esc_url( $slack_url ); ?>">
					<span class="pen_element_hidden">
					<?php
					esc_html_e( 'Slack', 'pen' );
					?>
					</span>
				</a>
			</li>
					<?php
				}
			}
		}

		$youtube_url = pen_option_get( 'youtube' );
		if ( $youtube_url ) {
			$youtube_display = pen_option_get( 'youtube_' . $location . '_display' );
			if ( $youtube_display ) {
				$youtube_url = explode( '|', $youtube_url );
				foreach ( $youtube_url as $youtube_url ) {
					?>
			<li class="pen_youtube" title="<?php esc_attr_e( 'YouTube', 'pen' ); ?>">
				<a href="<?php echo esc_attr( $youtube_url ); ?>">
					<span class="pen_element_hidden">
					<?php
					esc_html_e( 'YouTube', 'pen' );
					?>
					</span>
				</a>
			</li>
					<?php
				}
			}
		}

		$shop_url = pen_option_get( 'shop' );
		if ( $shop_url ) {
			$shop_display = pen_option_get( 'shop_' . $location . '_display' );
			if ( $shop_display ) {
				$shop_url = explode( '|', $shop_url );
				foreach ( $shop_url as $shop_url ) {
					?>
			<li class="pen_shop" title="<?php esc_attr_e( 'Shop Now', 'pen' ); ?>">
				<a href="<?php echo esc_attr( $shop_url ); ?>">
					<span class="pen_element_hidden">
					<?php
					esc_html_e( 'Shop', 'pen' );
					?>
					</span>
				</a>
			</li>
					<?php
				}
			}
		}

		$vimeo_url = pen_option_get( 'vimeo' );
		if ( $vimeo_url ) {
			$vimeo_display = pen_option_get( 'vimeo_' . $location . '_display' );
			if ( $vimeo_display ) {
				$vimeo_url = explode( '|', $vimeo_url );
				foreach ( $vimeo_url as $vimeo_url ) {
					?>
			<li class="pen_vimeo" title="<?php esc_attr_e( 'Vimeo', 'pen' ); ?>">
				<a href="<?php echo esc_attr( $vimeo_url ); ?>">
					<span class="pen_element_hidden">
					<?php
					esc_html_e( 'Vimeo', 'pen' );
					?>
					</span>
				</a>
			</li>
					<?php
				}
			}
		}

		$output = trim( ob_get_clean() );
		if ( $output ) {
			$classes = array(
				'pen_social_networks',
				pen_class_animation( 'social_' . $location, false, $content_id ),
			);
			$classes = implode( ' ', array_filter( $classes ) );
			ob_start();
			?>
	<div class="<?php echo esc_attr( $classes ); ?>">
		<ul>
			<?php
			echo wp_kses_post( $output );
			?>
		</ul>
	</div><!-- .pen_social_networks -->
			<?php
			return ob_get_clean();
		}
	}
}

if ( ! function_exists( 'pen_html_search_box' ) ) {
	/**
	 * Generates markup for the search box.
	 *
	 * @param int $content_id Content ID.
	 *
	 * @since Pen 1.0.0
	 * @return string
	 */
	function pen_html_search_box( $content_id = null ) {
		// For maximum compatibility.
		if ( is_null( $content_id ) ) {
			$content_id = pen_post_id();
		}

		$search_display = get_post_meta( $content_id, 'pen_content_search_display_override', true );
		if ( ! $search_display || 'default' === $search_display ) {
			$search_display = pen_option_get( 'search_display' );
		}
		if ( $search_display && 'no' !== $search_display ) {
			return trim( get_search_form( false ) );
		}
		return false;
	}
}

if ( ! function_exists( 'pen_html_menu' ) ) {
	/**
	 * Generates HTML for the navigation menus.
	 *
	 * @param string $menu_id The menu ID.
	 *
	 * @since Pen 1.3.0
	 * @return string
	 */
	function pen_html_menu( $menu_id ) {
		$menu_html = '';
		if ( 'primary' === $menu_id ) {
			$variables = array(
				'theme_location' => 'primary',
				'menu_id'        => 'primary-menu',
				'menu_class'     => 'menu',
				'echo'           => false,
				'fallback_cb'    => 'pen_html_navigation_fallback',
			);
			$menu_html = trim( wp_nav_menu( $variables ) );
		} elseif ( 'secondary' === $menu_id ) {
			// Does not have a content-level visibility option.
			if ( pen_option_get( 'footer_menu_display' ) ) {
				$variables = array(
					'theme_location' => 'secondary',
					'menu_id'        => 'secondary-menu',
					'menu_class'     => 'menu',
					'echo'           => false,
					'fallback_cb'    => 'pen_html_footer_menu_fallback',
				);
				$menu_html = trim( wp_nav_menu( $variables ) );
			}
		}
		return $menu_html;
	}
}

if ( ! function_exists( 'pen_html_navigation_main' ) ) {
	/**
	 * Generates HTML for the main navigation menus.
	 *
	 * @param string $menu_id    Menu ID.
	 * @param int    $content_id Content ID.
	 *
	 * @since Pen 1.3.0
	 * @return string
	 */
	function pen_html_navigation_main( $menu_id, $content_id ) {
		$navigation_display = get_post_meta( $content_id, 'pen_navigation_display_override', true );
		if ( ! $navigation_display || 'default' === $navigation_display ) {
			$navigation_display = pen_option_get( 'navigation_display' );
		}
		if ( ! $navigation_display || 'no' === $navigation_display ) {
			$navigation_display = false;
		} else {
			$navigation_display = true;
		}

		if ( $navigation_display || 'never' !== pen_option_get( 'navigation_mobile_display' ) ) {
			$menu_html = pen_html_menu( $menu_id );
			if ( $menu_html ) {
				$is_customize_preview = is_customize_preview();
				ob_start();
				if ( PEN_THEME_SMALLSCREEN || $is_customize_preview ) {
					pen_sidebar_get( 'sidebar-mobile-menu-top', $content_id );
				}

				$hover             = pen_option_get( 'navigation_hover' );
				$arrows            = pen_option_get( 'navigation_arrows' );
				$separator         = pen_option_get( 'navigation_separator' );
				$separator_submenu = pen_option_get( 'navigation_separator_submenu' );

				$classes_navigation = array(
					'main-navigation',
					( ! $navigation_display ) ? 'pen_element_hidden' : '',
					'pen_hover_' . ( $hover ? $hover : 'none' ),
					'pen_arrows_' . ( $arrows ? $arrows : 'none' ),
					'pen_separator_' . ( $separator ? $separator : 'none' ),
					'pen_separator_submenu_' . ( $separator_submenu ? $separator_submenu : 'none' ),
					pen_class_animation( 'navigation_bar', false, $content_id ),
				);
				$classes_navigation = implode( ' ', array_filter( $classes_navigation ) );
				?>
		<nav id="pen_navigation" class="<?php echo esc_attr( $classes_navigation ); ?>" role="navigation" aria-label="<?php esc_attr_e( 'Header Menu', 'pen' ); ?>">
				<?php
				$classes_container = array(
					'pen_container',
					pen_class_animation( 'navigation', false, $content_id ),
				);
				$classes_container = implode( ' ', array_filter( $classes_container ) );
				?>
			<div class="<?php echo esc_attr( $classes_container ); ?>">
				<?php
				echo $menu_html; /* phpcs:ignore */
				?>
			</div>
				<?php
				pen_html_jump_menu( 'navigation', $content_id );
				?>
		</nav>
				<?php
				if ( PEN_THEME_SMALLSCREEN || $is_customize_preview ) {
					pen_sidebar_get( 'sidebar-mobile-menu-bottom', $content_id );
				}
				return ob_get_clean();
			}
		}
	}
}

if ( ! function_exists( 'pen_html_navigation_fallback' ) ) {
	/**
	 * Fallback navigation menu.
	 *
	 * @since Pen 1.0.8
	 * @return string
	 */
	function pen_html_navigation_fallback() {
		if ( current_user_can( 'edit_theme_options' ) ) {
			$content_id = pen_post_id();
			ob_start();
			?>
	<ul id="primary-menu" class="menu">
		<li class="pen_menu_create" title="<?php esc_attr_e( 'This is a shortcut link for users with theme customization permission, invisible for the rest.', 'pen' ); ?>">
			<?php
			if ( is_customize_preview() ) {
				$url        = '#';
				$attributes = ' class="pen_customizer_shortcut" data-type="panel" data-target="nav_menus"';
			} else {
				$url        = esc_url( self_admin_url( 'nav-menus.php' ) );
				$attributes = '';
			}
			printf(
				'<a href="%1$s"%2$s>%3$s</a>',
				esc_attr( $url ),
				$attributes, /* phpcs:ignore */
				esc_html__( 'Create a menu?', 'pen' )
			);
			?>
		</li>
	</ul>
			<?php
			pen_html_jump_menu( 'navigation', pen_post_id() );

			return ob_get_clean();
		}
	}
}

if ( ! function_exists( 'pen_html_footer_menu_fallback' ) ) {
	/**
	 * Fallback footer menu.
	 *
	 * @since Pen 1.0.8
	 * @return string
	 */
	function pen_html_footer_menu_fallback() {
		if ( current_user_can( 'edit_theme_options' ) ) {
			ob_start();
			?>
	<ul id="secondary-menu" class="menu">
		<li class="pen_menu_create" title="<?php esc_attr_e( 'This is a shortcut link for users with theme customization permission, invisible for the rest.', 'pen' ); ?>">
			<?php
			if ( is_customize_preview() ) {
				$url        = '#';
				$attributes = ' class="pen_customizer_shortcut" data-type="panel" data-target="nav_menus"';
			} else {
				$url        = esc_url( self_admin_url( 'nav-menus.php' ) );
				$attributes = '';
			}
			printf(
				'<a href="%1$s"%2$s>%3$s</a>',
				esc_attr( $url ),
				$attributes, /* phpcs:ignore */
				esc_html__( 'Create a menu?', 'pen' )
			);
			?>
		</li>
	</ul>
			<?php
			return ob_get_clean();
		}
	}
}

if ( ! function_exists( 'pen_html_content_information' ) ) {
	/**
	 * Prints HTML with meta information for the current post-date/time and author.
	 *
	 * @param string $location   The selected location for the element.
	 * @param int    $content_id Content ID.
	 *
	 * @since Pen 1.0.0
	 * @return string
	 */
	function pen_html_content_information( $location, $content_id = null ) {

		if ( 'page' === get_post_type() ) {
			return;
		}

		// For maximum compatibility.
		if ( is_null( $content_id ) ) {
			$content_id = pen_post_id();
		}

		if ( ! in_array( (string) $location, array( 'header', 'footer' ), true ) ) {
			$location = 'header';
		}

		$pen_is_singular = pen_is_singular();

		$view = $pen_is_singular ? 'content' : 'list';

		// Hide category for pages.
		$categories_list   = '';
		$category_location = get_post_meta( $content_id, 'pen_' . $view . '_category_location_override', true );
		if ( ! $category_location || 'default' === $category_location ) {
			$category_location = pen_option_get( $view . '_category_location' );
		}

		if ( $location === $category_location || ( ! $category_location && 'header' === $location ) ) {
			/* Translators: used between list items, there is a space after the comma */
			$categories_list = get_the_category_list( '||' );
			if ( pen_option_get( 'pen_' . $view . '_category_only_first' ) && false !== strpos( $categories_list, '||' ) ) {
				$categories_list = explode( '||', $categories_list );
				$categories_list = $categories_list[0];
			}
			$categories_list = str_replace( '||', _x( ', ', 'Separates category links.', 'pen' ), $categories_list );
			if ( $categories_list ) {
				$categories_list = sprintf(
					'<span class="cat-links %1$s"><span class="pen_element_hidden">%2$s</span>%3$s</span>',
					pen_class_lists( 'category_display_override', $content_id ),
					sprintf(
						/* Translators: Just some words. */
						__( '%s:', 'pen' ),
						__( 'Categories', 'pen' )
					),
					$categories_list
				); /* phpcs:ignore */
			}
		}

		$posted_on     = '';
		$date_location = get_post_meta( $content_id, 'pen_' . $view . '_date_location_override', true );
		if ( ! $date_location || 'default' === $date_location ) {
			$date_location = pen_option_get( $view . '_date_location' );
		}

		if ( $location === $date_location || ( ! $date_location && 'header' === $location ) ) {
			$time_string = sprintf(
				'<time class="entry-date published updated" datetime="%1$s">%2$s</time>',
				esc_attr( get_the_date( DATE_W3C ) ),
				esc_html( get_the_date() )
			);
			if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
				$time_string = sprintf(
					'<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>',
					esc_attr( get_the_date( DATE_W3C ) ),
					esc_html( get_the_date() ),
					esc_attr( get_the_modified_date( DATE_W3C ) ),
					esc_html( get_the_modified_date() )
				);
			}

			$posted_on = sprintf(
				/* Translators: Publish date. */
				esc_html_x( 'Posted on %s', 'Date', 'pen' ),
				sprintf(
					'<a href="%1$s" rel="bookmark">%2$s</a>',
					esc_url( get_permalink() ),
					$time_string
				)
			);
			$posted_on = sprintf(
				'<span class="posted-on %1$s">%2$s</span>',
				pen_class_lists( 'date_display_override', $content_id ),
				$posted_on
			);
		}

		$byline          = '';
		$author_location = get_post_meta( $content_id, 'pen_' . $view . '_author_location_override', true );
		if ( ! $author_location || 'default' === $author_location ) {
			$author_location = pen_option_get( $view . '_author_location' );
		}
		if ( $location === $author_location || ( ! $author_location && 'header' === $location ) ) {
			$byline = sprintf(
				/* Translators: Author's name. */
				esc_html_x( 'by %s', 'Author', 'pen' ),
				sprintf(
					'<span class="author vcard"><a class="url fn n" href="%1$s">%2$s</a></span>',
					esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
					esc_html( get_the_author() )
				)
			);
			$byline = sprintf(
				'<span class="byline %1$s"> %2$s</span>',
				pen_class_lists( 'author_display_override', $content_id ),
				$byline
			);
		}

		$output = trim( $posted_on . $byline . $categories_list );

		if ( $output ) {

			$classes = array(
				'entry-meta',
				'pen_separator_' . pen_option_get( 'content_details_separator' ),
			);

			$classes = implode( ' ', $classes );

			ob_start();
			?>
		<div class="<?php echo esc_attr( $classes ); ?>">
			<?php
			echo $output; /* phpcs:ignore */
			?>
		</div>
			<?php
			return ob_get_clean();
		}
	}
}

if ( ! function_exists( 'pen_html_author' ) ) {
	/**
	 * Generates author profile.
	 *
	 * @param array $variables  Profile parameteres.
	 * @param int   $content_id Content ID.
	 *
	 * @since Pen 1.0.0
	 * @return void
	 */
	function pen_html_author( $variables = array(), $content_id = null ) {
		// For maximum compatibility.
		if ( is_null( $content_id ) ) {
			$content_id = pen_post_id();
		}

		$view    = pen_is_singular() ? 'content' : 'list';
		$display = get_post_meta( $content_id, 'pen_' . $view . '_profile_display_override', true );
		if ( ! $display || 'default' === $display ) {
			$display = pen_option_get( $view . '_profile_display' );
		}

		if ( ! $display || 'no' === $display ) {
			return;
		}

		$avatar      = get_avatar( get_the_author_meta( 'email' ), '90' );
		$archive_url = get_author_posts_url( get_the_author_meta( 'ID' ) );
		$user_url    = get_the_author_meta( 'user_url' );
		$avatar_link = pen_option_get( $view . '_author_avatar_link' );

		if ( 'website' === $avatar_link ) {
			$avatar_url = $user_url;
		} else {
			$avatar_url = $archive_url;
		}
		$add_link = ( $user_url && ( ! isset( $variables['add_url'] ) || $variables['add_url'] ) ) ? true : false;

		$classes = array(
			'pen_author_profile',
			pen_class_animation( $view . '_author', false, $content_id ),
			$avatar ? 'pen_has_avatar' : '',
		);
		$classes = implode( ' ', array_filter( $classes ) );
		?>
	<div class="<?php echo esc_attr( $classes ); ?>">
		<?php
		if ( $avatar && ( ! isset( $variables['add_avatar'] ) || $variables['add_avatar'] ) ) {
			?>
		<div class="pen_author_avatar">
			<?php
			if ( $add_link ) {
				?>
			<a href="<?php echo esc_url( $avatar_url ); ?>">
				<?php
			}

			echo $avatar; /* phpcs:ignore */

			if ( $add_link ) {
				?>
			</a>
				<?php
			}
			?>
		</div>
			<?php
		}

		$description = wp_kses_post( get_the_author_meta( 'description' ) );
		$classes     = trim(
			implode(
				' ',
				array(
					'pen_author_about',
					( ! $description ) ? 'pen_no_description' : '',
				)
			)
		);
		?>
		<div class="<?php echo esc_attr( $classes ); ?>">
		<?php
		if ( 'list' === $view ) {
			?>
			<h3>
			<?php
		} else {
			?>
			<h2>
			<?php
		}

		$author_name_link = pen_option_get( $view . '_author_name_link' );
		if ( 'website' === $author_name_link ) {
			the_author_link();
		} else {
			printf(
				'<a href="%1$s" rel="author internal">%2$s</a>',
				esc_url( $archive_url ),
				esc_html( get_the_author() )
			);
		}

		if ( 'list' === $view ) {
			?>
			</h3>
			<?php
		} else {
			?>
			</h2>
			<?php
		}

		ob_start();
		if ( $user_url && $add_link ) {
			$site_name = wp_parse_url( $user_url );
			if ( isset( $site_name['host'] ) ) {
				$site_name = $site_name['host'];
			} else {
				$site_name = $user_url;
			}
			?>
			<a href="<?php echo esc_url( $user_url ); ?>" class="pen_author_url">
			<?php
			echo esc_html( $site_name );
			?>
			</a>
			<?php
		}

		if ( $description ) {
			?>
			<p>
			<?php
			echo $description; /* phpcs:ignore */
			?>
			</p>
			<?php
		}

		$about = trim( ob_get_clean() );
		if ( $about ) {
			?>
			<div>
			<?php
			echo $about; /* phpcs:ignore */
			?>
			</div>
			<?php
		}
		?>
		</div>
	</div>
		<?php
	}
}

if ( ! function_exists( 'pen_html_share' ) ) {
	/**
	 * Social sharing buttons.
	 *
	 * @global object $post
	 *
	 * @param string $location   The selected location.
	 * @param int    $content_id Content ID.
	 *
	 * @since Pen 1.0.0
	 * @return string
	 */
	function pen_html_share( $location, $content_id = null ) {
		// For maximum compatibility.
		if ( is_null( $content_id ) ) {
			$content_id = pen_post_id();
		}

		ob_start();

		if ( ! pen_is_singular() ) {
			return;
		}
		if ( ! in_array( (string) $location, array( 'header', 'content', 'footer' ), true ) ) {
			$location = 'header';
		}
		$share_location = get_post_meta( $content_id, 'pen_content_share_location_override', true );
		if ( ! $share_location || 'default' === $share_location ) {
			$share_location = pen_option_get( 'content_share_location' );
		}
		if ( $share_location !== $location ) {
			return;
		}

		$display = get_post_meta( $content_id, 'pen_content_share_display_override', true );
		if ( ! $display || 'default' === $display ) {
			$display = pen_option_get( 'content_share_display' );
		}
		if ( ! $display || 'no' === $display ) {
			return;
		}

		global $post;
		$url   = rawurlencode( esc_url( get_permalink( $content_id ) ) );
		$title = rawurlencode( $post->post_title );

		$url_home = is_multisite() ? network_home_url( false ) : home_url( false );

		$url_facebook = sprintf( 'https://www.facebook.com/sharer/sharer.php?u=%1$s', $url );
		$url_twitter  = sprintf( 'https://twitter.com/intent/tweet?text=%2$s&url=%1$s', $url, $title );
		$url_linkedin = sprintf( 'https://www.linkedin.com/cws/share?url=%1$s&original_referer=%2$s', $url, $url_home );
		?>
		<div class="pen_share">
			<h4>
		<?php
		esc_html_e( 'Share this!', 'pen' );
		?>
			</h4>
			<ul>
				<li class="pen_facebook">
		<?php
		$link_title = sprintf(
			/* Translators: Social network name, e.g. Facebook. */
			__( 'Share on %s', 'pen' ),
			__( 'Facebook', 'pen' )
		);
		?>
					<a href="<?php echo esc_url( $url_facebook ); ?>" title="<?php echo esc_attr( $link_title ); ?>" target="_blank" class="pen_button pen_button_share">
						<span>
		<?php
		esc_html_e( 'Facebook', 'pen' );
		?>
						</span>
					</a>
				</li>
				<li class="pen_twitter">
		<?php
		$link_title = sprintf(
			/* Translators: Social network name, e.g. Facebook. */
			__( 'Share on %s', 'pen' ),
			__( 'Twitter', 'pen' )
		);
		?>
					<a href="<?php echo esc_url( $url_twitter ); ?>" title="<?php echo esc_attr( $link_title ); ?>" target="_blank" class="pen_button pen_button_share">
						<span>
		<?php
		esc_html_e( 'Twitter', 'pen' );
		?>
						</span>
					</a>
				</li>
				<li class="pen_linkedin">
		<?php
		$link_title = sprintf(
			/* Translators: Social network name, e.g. Facebook. */
			__( 'Share on %s', 'pen' ),
			__( 'LinkedIn', 'pen' )
		);
		?>
					<a href="<?php echo esc_url( $url_linkedin ); ?>" title="<?php echo esc_attr( $link_title ); ?>" target="_blank" class="pen_button pen_button_share">
						<span>
		<?php
		esc_html_e( 'LinkedIn', 'pen' );
		?>
						</span>
					</a>
				</li>
			</ul>
		</div><!-- .pen_share -->
		<?php
		return ob_get_clean();
	}
}

if ( ! function_exists( 'pen_html_configuration_overview' ) ) {
	/**
	 * Displays an overview of the post meta settings.
	 *
	 * @param int $content_id Content ID.
	 *
	 * @since Pen 1.0.0
	 * @return string
	 */
	function pen_html_configuration_overview( $content_id = null ) {
		// For maximum compatibility.
		if ( is_null( $content_id ) ) {
			$content_id = pen_post_id();
		}

		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		ob_start();
		$overview_list     = array();
		$overview_content  = array();
		$overview_sidebars = array();
		$customize_display = false;
		$edit_post_display = false;

		$options_list = pen_post_meta_options( 'list' );

		foreach ( $options_list as $option => $label ) {
			$value = get_post_meta( $content_id, $option, true );
			if ( $value && 'default' !== $value ) {
				$edit_post_display        = true;
				$overview_list[ $option ] = array(
					'status' => ( 'no' === $value ) ? 'disabled' : 'enabled',
					'label'  => $label,
					'value'  => $value,
					'help'   => '',
				);
			}
		}

		$options_content = pen_post_meta_options( 'content' );

		foreach ( $options_content as $option => $label ) {
			$value = get_post_meta( $content_id, $option, true );
			if ( $value && 'default' !== $value ) {
				$edit_post_display           = true;
				$overview_content[ $option ] = array(
					'status' => ( 'no' === $value ) ? 'disabled' : 'enabled',
					'label'  => $label,
					'value'  => $value,
					'help'   => '',
				);
			}
		}

		$options_sidebars = pen_post_meta_options( 'sidebar' );

		$is_homepage = ( is_front_page() && pen_is_singular() ) ? true : false;

		foreach ( $options_sidebars as $sidebar => $name ) {
			if ( $is_homepage ) {
				if ( pen_option_get( str_replace( 'pen_', 'pen_front_', $sidebar ) ) ) {
					$customize_display             = true;
					$overview_sidebars[ $sidebar ] = array(
						'status' => 'disabled',
						'label'  => sprintf(
							/* Translators: Sidebar name. */
							esc_html__( '(If on homepage) "%s"', 'pen' ),
							$name
						),
						'value'  => __( 'Hide', 'pen' ),
						'help'   => sprintf(
							/* Translators: Path to the settings page. */
							__( 'You can change this through %s', 'pen' ),
							sprintf(
								'%1$s &rarr; %2$s &rarr; %3$s &rarr; %4$s.',
								__( 'Appearance', 'pen' ),
								__( 'Customize', 'pen' ),
								__( 'Front Page', 'pen' ),
								__( 'Sidebars', 'pen' )
							)
						),
					);
				}
			}
			if ( get_post_meta( $content_id, $sidebar, true ) ) {
				$edit_post_display = true;
				$sidebar_id        = str_replace( array( 'pen_sidebar_', '_display', '_' ), array( '', '', '-' ), $sidebar );
				$sidebars          = pen_sidebars();

				if ( in_array( $sidebar_id, array( 'left', 'right' ), true ) ) {
					$label = $sidebars[ $sidebar_id ]['name'];
				} else {
					$label = sprintf(
						/* Translators: Sidebar name. */
						__( '"%s" Widget Area', 'pen' ),
						$sidebars[ $sidebar_id ]['name']
					);
				}

				$overview_sidebars[ $sidebar ] = array(
					'status' => 'disabled',
					'label'  => $label,
					'value'  => __( 'Hide', 'pen' ),
					'help'   => '',
				);
			}
		}
		if ( empty( $overview_list ) && empty( $overview_content ) && empty( $overview_sidebars ) ) {
			ob_end_clean();
			return;
		}
		?>
		<div class="pen_options_overview" id="pen_post_overview_<?php echo esc_attr( $content_id ); ?>">
			<h3>
		<?php
		esc_html_e( 'Content Settings', 'pen' );
		?>
			</h3>
			<p class="pen_overview_content_title">
				<strong>
					<span class="pen_element_hidden">
		<?php
		echo esc_html(
			sprintf(
				'%s: ',
				__( 'Title', 'pen' )
			)
		);
		?>
					</span>
		<?php
		the_title();
		?>
				</strong>
			</p>
			<div class="pen_table_wrapper">
				<table>
		<?php
		if ( ! empty( $overview_list ) ) {
			?>
					<tr>
						<th scope="col" colspan="2">
			<?php
			esc_html_e( 'List Views', 'pen' );
			?>
						</th>
					</tr>
			<?php
			foreach ( $overview_list as $option_id => $item ) {
				$is_visibility = ( strpos( $option_id, 'display' ) !== false ) ? true : false;
				?>
					<tr class="pen_option_<?php echo esc_attr( $item['status'] ); ?>" title="<?php echo esc_attr( $item['help'] ); ?>">
				<?php
				$value = str_replace( '_', ' ', $item['value'] );
				if ( '#000000' === $value ) {
					$value = __( 'Dark', 'pen' );
				} elseif ( '#ffffff' === $value ) {
					$value = __( 'Light', 'pen' );
				}
				?>
						<td class="pen_overview_item">
				<?php
				echo esc_html( $item['label'] );
				?>
						</td>
						<td class="pen_overview_value">
				<?php
				if ( 'yes' === $value ) {
					if ( $is_visibility ) {
						esc_html_e( 'Show', 'pen' );
					} else {
						esc_html_e( 'Yes', 'pen' );
					}
				} elseif ( 'no' === $value ) {
					if ( $is_visibility ) {
						esc_html_e( 'Hide', 'pen' );
					} else {
						esc_html_e( 'No', 'pen' );
					}
				} else {
					echo esc_html( $value );
				}
				?>
						</td>
					</tr>
				<?php
			}
		}

		if ( $overview_content || $overview_sidebars ) {
			?>
					<tr>
						<th scope="col" colspan="2">
			<?php
			esc_html_e( 'Full Content View', 'pen' );
			?>
						</th>
					</tr>
			<?php
			foreach ( $overview_content as $option_id => $item ) {
				$is_visibility = ( strpos( $option_id, 'display' ) !== false ) ? true : false;
				?>
					<tr class="pen_option_<?php echo esc_attr( $item['status'] ); ?>" title="<?php echo esc_attr( $item['help'] ); ?>">
				<?php
				$value = str_replace( array( '_', 'preset ' ), array( ' ', 'style ' ), $item['value'] );
				if ( '#000000' === $value ) {
					$value = __( 'Dark', 'pen' );
				} elseif ( '#ffffff' === $value ) {
					$value = __( 'Light', 'pen' );
				}
				?>
						<td class="pen_overview_item">
				<?php
				echo esc_html( $item['label'] );
				?>
						</td>
						<td class="pen_overview_value">
				<?php
				if ( 'yes' === $value ) {
					if ( $is_visibility ) {
						esc_html_e( 'Show', 'pen' );
					} else {
						esc_html_e( 'Yes', 'pen' );
					}
				} elseif ( 'no' === $value ) {
					if ( $is_visibility ) {
						esc_html_e( 'Hide', 'pen' );
					} else {
						esc_html_e( 'No', 'pen' );
					}
				} else {
					echo esc_html( $value );
				}
				?>
						</td>
					</tr>
				<?php
			}
		}

		foreach ( $overview_sidebars as $item ) {
			?>
				<tr class="pen_option_<?php echo esc_attr( $item['status'] ); ?>" title="<?php echo esc_attr( $item['help'] ); ?>">
					<td class="pen_overview_item">
			<?php
			echo esc_html( $item['label'] );
			?>
					</td>
					<td class="pen_overview_value">
			<?php
			echo esc_html( $item['value'] );
			?>
					</td>
				</tr>
			<?php
		}
		?>
			</table>
		</div>
		<?php
		if ( $edit_post_display && ! is_customize_preview() ) {
			$url_content_edit = get_edit_post_link( $content_id );
			?>
		<p>
			<a href="<?php echo esc_url( $url_content_edit ); ?>" class="pen_button">
			<?php
			esc_html_e( 'Edit', 'pen' );
			?>
			</a>
		</p>
			<?php
		}

		if ( isset( $url_content_edit ) ) {
			$url_content_edit .= '#pen_meta_box';
			?>
		<p>
			<a href="<?php echo esc_url( $url_content_edit ); ?>" class="pen_button">
			<?php
			echo esc_html(
				sprintf(
					/* Translators: Just some words. */
					__( '%1$s - %2$s', 'pen' ),
					__( 'Pen', 'pen' ),
					__( 'Options', 'pen' )
				)
			);
			?>
			</a>
		</p>
			<?php
		}

		if ( $customize_display ) {
			$url_customize = wp_customize_url();
			if ( ! is_admin() ) {
				$content_id = pen_post_id();
				if ( $content_id ) {
					$url_customize = add_query_arg( 'pen_content_id', $content_id, wp_customize_url() );
				}
			}
			?>
		<p>
			<a href="<?php echo esc_url( $url_customize ); ?>" class="pen_button">
			<?php
			esc_html_e( 'Edit defaults', 'pen' );
			?>
			</a>
		</p>
			<?php
		}
		?>
	</div>
		<?php
		return ob_get_clean();
	}
}

if ( ! function_exists( 'pen_html_jump_menu' ) ) {
	/**
	 * Jump menus for easier access to various parts of the backend.
	 *
	 * @param string $element    Layout section or template part.
	 * @param int    $content_id Content ID.
	 *
	 * @since Pen 1.0.8
	 * @return void
	 */
	function pen_html_jump_menu( $element, $content_id = null ) {
		// For maximum compatibility.
		if ( is_null( $content_id ) ) {
			$content_id = pen_post_id();
		}

		$menu = pen_jump_menu( $element, $content_id );
		if ( ! $menu ) {
			return;
		}

		$heading_text = wp_kses_post(
			sprintf(
				/* Translators: layout section name, like Footer, Header, etc. */
				__( '%1$s: %2$s', 'pen' ),
				__( 'Customize', 'pen' ),
				sprintf(
					'<span class="pen_jump_menu_name">%s</span>',
					esc_html( $menu['name'] )
				)
			)
		);
		$heading_title = htmlspecialchars( wp_strip_all_tags( str_replace( '"', '', htmlspecialchars_decode( $heading_text ) ) ), ENT_NOQUOTES, 'UTF-8' );

		// This menu has to be hidden when JavaScript is disabled (too many links) unless it's a screen-reader.
		?>
		<div id="pen_jump_menu_<?php echo esc_attr( $element ); ?>" class="pen_jump_menu clearfix pen_element_hidden">
			<div class="pen_menu_wrapper clearfix pen_element_hidden">
				<strong class="pen_jump_menu_title" title="<?php echo esc_attr( $heading_title ); ?>">
		<?php
		echo $heading_text; /* phpcs:ignore */
		?>
				</strong>
		<?php
		echo pen_html_jump_menu_items( $menu['items'], 'ul' ); /* phpcs:ignore */
		?>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'pen_html_jump_menu_items' ) ) {
	/**
	 * Renders jump menu items.
	 *
	 * @param array  $menu      The menu items.
	 * @param string $list_type List type.
	 *
	 * @since Pen 1.3.4
	 * @return string
	 */
	function pen_html_jump_menu_items( $menu, $list_type = 'comma' ) {
		if ( ! $menu ) {
			return;
		}

		ob_start();

		$current     = 1;
		$items_count = false;
		if ( 'ul' === $list_type ) {
			?>
				<ul>
			<?php
		} else {
			$items_count = count( $menu );
		}
		foreach ( $menu as $target => $label ) {
			if ( 'ul' === $list_type ) {
				?>
					<li>
				<?php
			}
			if ( filter_var( $target, FILTER_VALIDATE_URL ) ) {
				printf(
					'<a href="%1$s">%2$s</a>',
					esc_url( $target ),
					wp_kses_post( $label )
				);
			} else {

				$container = explode( ',', $target );

				$container_type = $container[0];
				$container_name = $container[1];

				$generic = array( // Also in the pen_url_customizer().
					'background_image',
					'header_image',
					'nav_menus',
					'title_tagline',
					'widgets',
					'woocommerce',
				);
				if ( ! in_array( $container_name, $generic, true ) && false === strpos( $container_name, 'sidebar-widgets-' ) ) {
					$container_name = 'pen_' . $container_type . '_' . $container_name;
				}

				printf(
					'<a href="%1$s" class="pen_customizer_shortcut"%2$s>%3$s</a>',
					esc_url( pen_url_customizer( $target ) ), // No need to esc_url.
					sprintf(
						' data-type="%1$s" data-target="%2$s"',
						esc_attr( $container_type ),
						esc_attr( $container_name )
					),
					wp_kses_post( $label )
				);

			}
			if ( 'ul' === $list_type ) {
				?>
					</li>
				<?php
			} else {
				printf(
					'%s',
					esc_html( $items_count >= (int) ( $current + 1 ) ? _x( ', ', 'Separates category links.', 'pen' ) : __( '.', 'pen' ) )
				);
			}
			$current++;
		}
		if ( 'ul' === $list_type ) {
			?>
				</ul>
			<?php
		}
		return ob_get_clean();
	}
}

if ( ! function_exists( 'pen_html_pagination_content' ) ) {
	/**
	 * Inline content pagination.
	 *
	 * @param int $content_id Content ID.
	 *
	 * @since Pen 1.0.6
	 * @return void
	 */
	function pen_html_pagination_content( $content_id = null ) {
		// For maximum compatibility.
		if ( is_null( $content_id ) ) {
			$content_id = pen_post_id();
		}

		ob_start();
		if ( function_exists( 'wp_pagenavi' ) ) {
			wp_pagenavi(
				array(
					'type' => 'multipart',
				)
			);
		} else {
			wp_link_pages(
				array(
					'before'         => sprintf(
						'<div class="page-links %1$s"><span class="pen_element_hidden">%2$s</span>',
						pen_class_animation( 'content_pager', false, $content_id ),
						esc_html(
							sprintf(
								/* Translators: Just some words. */
								__( '%s:', 'pen' ),
								__( 'Maximum Width', 'pen' )
							)
						)
					),
					'after'          => '</div>',
					'next_or_number' => 'next',
				)
			);
		}
		$pagination = trim( ob_get_clean() );
		if ( $pagination ) {
			?>
		<div class="pen_content_pagination">
			<?php
			echo $pagination; /* phpcs:ignore */
			?>
		</div>
			<?php
		}
	}
}

if ( ! function_exists( 'pen_html_button_users' ) ) {
	/**
	 * Returns HTML for a button to the user account profile.
	 *
	 * @param string $location   Location.
	 * @param int    $content_id Content ID.
	 *
	 * @since Pen 1.2.8
	 * @return string
	 */
	function pen_html_button_users( $location = 'header', $content_id = null ) {

		if ( ! in_array( $location, array( 'header', 'footer' ), true ) ) {
			$location = 'header';
		}

		if ( is_null( $content_id ) ) {
			$content_id = pen_post_id();
		}

		$user_logged_in = is_user_logged_in();

		$button_users_display = pen_option_get( 'button_users_' . $location . '_display' );

		$visible = false;
		if ( 'always' === $button_users_display ) {
			$visible = true;
		}
		if ( 'logged_in' === $button_users_display && $user_logged_in ) {
			$visible = true;
		}
		if ( 'visitors' === $button_users_display && ! $user_logged_in ) {
			$visible = true;
		}

		if ( ! $visible ) {
			return;
		}

		ob_start();

		if ( $user_logged_in ) {
			if ( PEN_THEME_HAS_WOOCOMMERCE ) {
				$url_account = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );
			} else {
				if ( ! current_user_can( 'edit_user' ) ) {
					return;
				}
				$url_account = get_edit_user_link();
			}
			?>
		<a class="pen_button" href="<?php echo esc_attr( $url_account ); ?>" title="<?php esc_attr_e( 'My Account', 'pen' ); ?>">
			<?php
			esc_html_e( 'My Account', 'pen' );
			?>
		</a>
			<?php
		} elseif ( pen_option_get( 'encourage_register' ) ) {

			$text    = pen_option_get( 'button_users_header_text_register' );
			$choices = array(
				'free_registration' => __( 'Free Registration', 'pen' ),
				'login_register'    => sprintf(
					/* Translators: Just some words. */
					__( '%1$s <span>/</span> %2$s', 'pen' ),
					__( 'Login', 'pen' ),
					__( 'Register', 'pen' )
				),
				'register'          => __( 'Register', 'pen' ),
				'register_today'    => __( 'Register Today', 'pen' ),
				'sign_in'           => __( 'Sign in', 'pen' ),
				'sign_up'           => __( 'Sign up', 'pen' ),
				'subscribe'         => __( 'Subscribe', 'pen' ),
				'subscribe_today'   => __( 'Subscribe Today', 'pen' ),
			);
			if ( ! empty( $choices[ $text ] ) ) {
				$text = $choices[ $text ];
			} else {
				$text = sprintf(
					/* Translators: Just some words. */
					__( '%1$s <span>/</span> %2$s', 'pen' ),
					__( 'Login', 'pen' ),
					__( 'Register', 'pen' )
				);
			}

			$url_register = wp_kses_post( pen_option_get( 'button_users_' . $location . '_url' ) );
			if ( ! $url_register ) {
				if ( PEN_THEME_HAS_WOOCOMMERCE ) {
					$url_register = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );
				} else {
					$url_register = wp_registration_url();
				}
			}
			?>
		<a class="pen_button" href="<?php echo esc_attr( $url_register ); ?>" title="<?php echo esc_attr( $text ); ?>">
			<?php
			echo wp_kses_post( $text );
			?>
		</a>
			<?php
		}
		return ob_get_clean();
	}
}

if ( ! function_exists( 'pen_html_id_layout' ) ) {
	/**
	 * Determines a proper HTML ID for the <div> containing posts.
	 *
	 * @param int $content_id Content ID.
	 *
	 * @since Pen 1.3.0
	 * @return string
	 */
	function pen_html_id_layout( $content_id ) {
		$content_list_type = pen_list_type( $content_id );
		switch ( $content_list_type ) {
			case 'masonry':
				$html_id = 'pen_masonry';
				break;
			case 'tiles':
				$html_id = 'pen_tiles';
				break;
			default:
				$html_id = 'pen_plain';
		}
		return $html_id;
	}
}

if ( ! function_exists( 'pen_html_phone' ) ) {
	/**
	 * Generates HTML markup for the phone number.
	 *
	 * @param string $location   Location.
	 * @param int    $content_id Content ID.
	 *
	 * @since Pen 1.3.0
	 * @return string
	 */
	function pen_html_phone( $location, $content_id ) {
		if ( ! in_array( $location, array( 'header', 'footer' ), true ) ) {
			$location = 'header';
		}
		$phone = pen_option_get( 'phone' );
		if ( ! pen_option_get( 'phone_' . $location . '_display' ) ) {
			return;
		}
		$classes_phone = array(
			'pen_phone',
			pen_class_animation( 'phone_' . $location, false, $content_id ),
		);
		$classes_phone = implode( ' ', array_filter( $classes_phone ) );
		ob_start();
		?>
		<div id="pen_<?php echo esc_attr( $location ); ?>_phone" class="<?php echo esc_attr( $classes_phone ); ?>">
			<a href="tel:<?php echo esc_attr( $phone ); ?>">
		<?php
		$classes_phone_label = array(
			'pen_phone_label',
			( ! pen_option_get( 'phone_' . $location . '_label_display' ) ) ? 'pen_element_hidden' : '',
		);
		$classes_phone_label = implode( ' ', array_filter( $classes_phone_label ) );
		?>
				<span class="<?php echo esc_attr( $classes_phone_label ); ?>">
		<?php
		$text    = pen_option_get( 'phone_' . $location . '_label_text' );
		$choices = array(
			'call_me'              => __( 'Call me:', 'pen' ),
			'call_now'             => __( 'Call Now:', 'pen' ),
			'call_us'              => __( 'Call us:', 'pen' ),
			'direct_line'          => __( 'Direct Line:', 'pen' ),
			'for_more_information' => __( 'For more information:', 'pen' ),
			'give_me_a_call'       => __( 'Give me a call:', 'pen' ),
			'give_us_a_call'       => __( 'Give us a call:', 'pen' ),
			'lets_talk'            => __( "Let's talk!", 'pen' ),
			'phone'                => sprintf(
				/* Translators: Just some words. */
				__( '%s:', 'pen' ),
				__( 'Phone', 'pen' )
			),
			'phone_number'         => __( 'Phone Number:', 'pen' ),
			'talk_to_an_expert'    => __( 'Talk to an expert:', 'pen' ),
			'talk_to_an_operator'  => __( 'Talk to an operator:', 'pen' ),
			'tel'                  => __( 'Tel:', 'pen' ),
			'toll_free'            => __( 'Toll Free:', 'pen' ),
		);
		if ( ! empty( $choices[ $text ] ) ) {
			$text = $choices[ $text ];
		} else {
			$text = __( 'Phone Number:', 'pen' );
		}

		echo esc_html( $text );
		?>
				</span>
				<span>
		<?php
		echo esc_html( $phone );
		?>
				</span>
			</a>
		</div>
		<?php
		return ob_get_clean();
	}
}

if ( ! function_exists( 'pen_html_loading_spinner' ) ) {
	/**
	 * The "Loading..." splash screen.
	 *
	 * @since Pen 1.3.0
	 * @return void
	 */
	function pen_html_loading_spinner() {
		?>
		<div class="pen_loading clearfix" role="alert">
			<div class="pen_icon">
			</div>
			<div class="pen_text">
		<?php
		$text    = pen_option_get( 'loading_spinner_text' );
		$choices = array(
			'loading'     => __( 'Loading...', 'pen' ),
			'please_wait' => __( 'Please wait...', 'pen' ),
			'site_title'  => get_bloginfo( 'name', 'display' ),
		);
		if ( ! empty( $choices[ $text ] ) ) {
			echo esc_html( $choices[ $text ] );
		} else {
			esc_html_e( 'Loading...', 'pen' );
		}

		if ( 'site_title' === $text ) {
			?>
				<p class="pen_element_hidden">
			<?php
			esc_html_e( 'Loading...', 'pen' );
			?>
				</p>
			<?php
		}
		?>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'pen_html_back_to_top' ) ) {
	/**
	 * Generates HTML for the "Back to top" link.
	 *
	 * @since Pen 1.3.0
	 * @return void
	 */
	function pen_html_back_to_top() {
		?>
		<a id="pen_back" href="#page" title="<?php esc_attr_e( 'Back to top', 'pen' ); ?>"<?php echo ( ! pen_option_get( 'footer_back_to_top_display' ) ) ? ' class="pen_element_hidden"' : ''; /* phpcs:ignore */ ?>>
			<span class="pen_element_hidden">
		<?php
		esc_html_e( 'Back to top', 'pen' );
		?>
			</span>
		</a>
		<?php
	}
}
