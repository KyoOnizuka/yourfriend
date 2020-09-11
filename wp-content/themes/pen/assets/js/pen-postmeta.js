;( function() {

	jQuery( document ).ready(
		function( $ ) {

			if ( $( '#pen_configuration' ).length ) {

				if ( pen_function_exists( typeof areYouSure ) ) {
					$( '#pen_configuration' ).areYouSure( { message: pen_postmeta_js.text.unsaved_changes } );
				}

			} else {

				var $post_meta = $( '#pen_postmeta' );

				$( '#pen_postmeta_hint' ).hide();
				$( '#pen_meta_box h2' ).prepend( '<span class="pen_only">' + pen_postmeta_js.text.pen_theme + '</span>' )
				.find( '.pen_only' ).attr( 'title', $( '#pen_postmeta_hint' ).text() );

				$post_meta.find( '.postbox select' ).each(
					function() {
						var $select = $( this );
						pen_postmeta_select( $select, 'page_load' );
						$select.on(
							'change',
							function() {
								pen_postmeta_select( $select, 'change' );

								$select.closest( '.pen_postmeta_container' ).slideDown( 'fast' ).removeClass( 'closed' ).attr( 'aria-expanded', true )
								.closest( '.pen_postmeta_options' ).removeClass( 'closed' );

							}
						);
					}
				);

				function pen_postmeta_show( $select ) {

					var id = $select.attr( 'id' );

					if ( id === 'pen_content_thumbnail_display_override' ) {
						$select
						.closest( '.pen_postmeta_wrapper' )
						.siblings( '.pen_postmeta_wrapper' )
						.filter( ':not(:first-of-type)' )
						.animate( { opacity: 1 }, { queue: false, duration: 300 } );
						pen_postmeta_fade_in( $( '#pen_content_thumbnail_animation_reveal_override, #pen_content_thumbnail_animation_delay_reveal_override' ) );

					} else if ( id === 'pen_content_thumbnail_frame_override' && 'no' !== $( '#pen_content_thumbnail_display_override' ).val() ) {
						pen_postmeta_fade_in( $( '#pen_color_content_thumbnail_frame_override' ) );

					} else if ( id === 'pen_site_header_display_override' ) {
						pen_postmeta_fade_in( $( '#pen_sidebar_header_primary_display, #pen_sidebar_header_secondary_display' ) );

					} else if ( id === 'pen_site_footer_display_override' ) {
						pen_postmeta_fade_in( $( '#pen_sidebar_footer_top_display, #pen_sidebar_footer_left_display, #pen_sidebar_footer_right_display, #pen_sidebar_footer_bottom_display' ) );

					} else if ( id === 'pen_content_header_display_override' ) {
						pen_postmeta_fade_in( $( '#pen_content_title_display_override, #pen_content_title_alignment_override, #pen_content_header_alignment_override, #pen_content_background_image_content_title_dynamic_override' ) );

					} else if ( id === 'pen_content_title_display_override' && 'no' !== $( '#pen_content_header_display_override' ).val() ) {
						pen_postmeta_fade_in( $( '#pen_content_title_alignment_override' ) );

					} else if ( id === 'pen_content_author_display_override' ) {
						pen_postmeta_fade_in( $( '#pen_content_author_location_override' ) );

					} else if ( id === 'pen_content_date_display_override' ) {
						pen_postmeta_fade_in( $( '#pen_content_date_location_override' ) );

					} else if ( id === 'pen_content_category_display_override' ) {
						pen_postmeta_fade_in( $( '#pen_content_category_location_override' ) );

					} else if ( id === 'pen_content_share_display_override' ) {
						pen_postmeta_fade_in( $( '#pen_content_share_location_override' ) );

					} else if ( id === 'pen_content_search_display_override' ) {
						pen_postmeta_fade_in( $( '#pen_content_search_location_override, #pen_sidebar_search_top_display, #pen_sidebar_search_left_display, #pen_sidebar_search_right_display, #pen_sidebar_search_bottom_display' ) );

					} else if ( id === 'pen_content_header_alignment_override' ) {
						pen_postmeta_fade_in( $( '#pen_content_title_alignment_override' ) );

					} else if ( id === 'pen_list_thumbnail_display_override' ) {
						$select.closest( '.pen_postmeta_wrapper' )
						.siblings( '.pen_postmeta_wrapper' )
						.filter( ':not(:first-of-type)' )
						.animate( { opacity: 1 }, { queue: false, duration: 300 } );
						pen_postmeta_fade_in( $( '#pen_list_thumbnail_animation_reveal_override, #pen_list_thumbnail_animation_delay_reveal_override' ) );

					} else if ( id === 'pen_list_thumbnail_frame_override' && 'no' !== $( '#pen_list_thumbnail_display_override' ).val() ) {
						pen_postmeta_fade_in( $( '#pen_color_list_thumbnail_frame_override' ) );

					} else if ( id === 'pen_list_header_display_override' ) {
						pen_postmeta_fade_in( $( '#pen_list_title_display_override, #pen_list_title_alignment_override, #pen_list_header_alignment_override, #pen_list_background_image_content_title_dynamic_override' ) );

					} else if ( id === 'pen_list_title_display_override' && 'no' !== $( '#pen_list_header_display_override' ).val() ) {
						pen_postmeta_fade_in( $( '#pen_list_title_alignment_override' ) );

					} else if ( id === 'pen_list_header_alignment_override' ) {
						pen_postmeta_fade_in( $( '#pen_list_title_alignment_override' ) );

					} else if ( id === 'pen_list_author_display_override' ) {
						pen_postmeta_fade_in( $( '#pen_list_author_location_override' ) );

					} else if ( id === 'pen_list_date_display_override' ) {
						pen_postmeta_fade_in( $( '#pen_list_date_location_override' ) );

					} else if ( id === 'pen_list_category_display_override' ) {
						pen_postmeta_fade_in( $( '#pen_list_category_location_override' ) );
					}
				}

				function pen_postmeta_fade_in( $element ) {
					$element
					.closest( '.pen_postmeta_wrapper' )
					.stop( true, true )
					.animate( { opacity: 1 }, { queue: false, duration: 300 } );
				}

				function pen_postmeta_fade_out( $element ) {
					$element
					.closest( '.pen_postmeta_wrapper' )
					.stop( true, true )
					.animate( { opacity: 0.3 }, { queue: false, duration: 300 } );
				}

				function pen_postmeta_select( $select, event ) {
					var id        = $select.attr( 'id' ),
					value_current = $select.val(),
					value_default = ( event === 'page_load' ) ? $select.data( 'default' ) : false;

					if ( $( '#apply_' + id ).length ) {
						var value_new = $( '#apply_' + id ).val();
						if ( value_new != value_current ) {
							var $option = $( '#apply_' + id );
							$option.prop( 'disabled', false )
							.closest( 'tr' ).find( '.button' ).removeClass( 'disabled' );
							if ( pen_function_exists( typeof $( window ).pen_meta_import_parent_disabled ) ) {
								$( '#pen_post_customizations' ).pen_meta_import_parent_disabled();
							}
						}
					}

					if ( value_current === 'no' || value_current === 'none' || value_default === 'no' ) {

						if ( id === 'pen_content_thumbnail_display_override' ) {
							$select.closest( '.pen_postmeta_wrapper' )
							.siblings( '.pen_postmeta_wrapper' )
							.filter( ':not(:first-of-type)' )
							.animate( { opacity: 0.3 }, { queue: false, duration: 300 } );
							pen_postmeta_fade_out( $( '#pen_content_thumbnail_animation_reveal_override, #pen_content_thumbnail_animation_delay_reveal_override' ) );

						} else if ( id === 'pen_content_thumbnail_frame_override' ) {
							pen_postmeta_fade_out( $( '#pen_color_content_thumbnail_frame_override' ) );
							if ( 'no' === $( '#pen_content_thumbnail_display_override' ).val() ) {
								pen_postmeta_fade_out( $( '#pen_color_content_thumbnail_frame_override' ) );
							}

						} else if ( id === 'pen_site_header_display_override' ) {
							pen_postmeta_fade_out( $( '#pen_sidebar_header_primary_display, #pen_sidebar_header_secondary_display' ) );

						} else if ( id === 'pen_site_footer_display_override' ) {
							pen_postmeta_fade_out( $( '#pen_sidebar_footer_top_display, #pen_sidebar_footer_left_display, #pen_sidebar_footer_right_display, #pen_sidebar_footer_bottom_display' ) );

						} else if ( id === 'pen_content_header_display_override' ) {
							pen_postmeta_fade_out( $( '#pen_content_title_display_override, #pen_content_title_alignment_override, #pen_content_header_alignment_override, #pen_content_background_image_content_title_dynamic_override' ) );

						} else if ( id === 'pen_content_title_display_override' ) {
							pen_postmeta_fade_out( $( '#pen_content_title_alignment_override' ) );
							if ( 'no' === $( '#pen_content_header_display_override' ).val() ) {
								pen_postmeta_fade_out( $( '#pen_content_title_alignment_override' ) );
							}

						} else if ( id === 'pen_content_author_display_override' ) {
							pen_postmeta_fade_out( $( '#pen_content_author_location_override' ) );

						} else if ( id === 'pen_content_date_display_override' ) {
							pen_postmeta_fade_out( $( '#pen_content_date_location_override' ) );

						} else if ( id === 'pen_content_category_display_override' ) {
							pen_postmeta_fade_out( $( '#pen_content_category_location_override' ) );

						} else if ( id === 'pen_content_share_display_override' ) {
							pen_postmeta_fade_out( $( '#pen_content_share_location_override' ) );

						} else if ( id === 'pen_content_search_display_override' ) {
							pen_postmeta_fade_out( $( '#pen_content_search_location_override, #pen_sidebar_search_top_display, #pen_sidebar_search_left_display, #pen_sidebar_search_right_display, #pen_sidebar_search_bottom_display' ) );

						} else if ( id === 'pen_content_header_alignment_override' ) {
							pen_postmeta_fade_in( $( '#pen_content_title_alignment_override' ) );

						} else if ( id === 'pen_list_thumbnail_display_override' ) {
							$select.closest( '.pen_postmeta_wrapper' )
							.siblings( '.pen_postmeta_wrapper' )
							.filter( ':not(:first-of-type)' )
							.animate( { opacity: 0.3 }, { queue: false, duration: 300 } );
							pen_postmeta_fade_out( $( '#pen_list_thumbnail_animation_reveal_override, #pen_list_thumbnail_animation_delay_reveal_override' ) );

						} else if ( id === 'pen_list_thumbnail_frame_override' ) {
							pen_postmeta_fade_out( $( '#pen_color_list_thumbnail_frame_override' ) );
							if ( 'no' === $( '#pen_list_thumbnail_display_override' ).val() ) {
								pen_postmeta_fade_out( $( '#pen_color_list_thumbnail_frame_override' ) );
							}

						} else if ( id === 'pen_list_header_display_override' ) {
							pen_postmeta_fade_out( $( '#pen_list_title_display_override, #pen_list_title_alignment_override, #pen_list_header_alignment_override, #pen_list_background_image_content_title_dynamic_override' ) );

						} else if ( id === 'pen_list_title_display_override' ) {
							pen_postmeta_fade_out( $( '#pen_list_title_alignment_override' ) );
							if ( 'no' === $( '#pen_list_header_display_override' ).val() ) {
								pen_postmeta_fade_out( $( '#pen_list_title_alignment_override' ) );
							}

						} else if ( id === 'pen_list_header_alignment_override' ) {
							pen_postmeta_fade_in( $( '#pen_list_title_alignment_override' ) );

						} else if ( id === 'pen_list_author_display_override' ) {
							pen_postmeta_fade_out( $( '#pen_list_author_location_override' ) );

						} else if ( id === 'pen_list_date_display_override' ) {
							pen_postmeta_fade_out( $( '#pen_list_date_location_override' ) );

						} else if ( id === 'pen_list_category_display_override' ) {
							pen_postmeta_fade_out( $( '#pen_list_category_location_override' ) );
						}

					} else if ( value_current === 'yes' || value_current !== 'default' ) {
						pen_postmeta_show( $select );
					} else {
						pen_postmeta_show( $select );
					}

					if ( id === 'pen_content_search_location_override' ) {
						if ( value_current === 'header' || value_default === 'header' ) {
							pen_postmeta_fade_out( $( '#pen_sidebar_search_top_display, #pen_sidebar_search_left_display, #pen_sidebar_search_right_display, #pen_sidebar_search_bottom_display' ) );
						} else {
							pen_postmeta_fade_in( $( '#pen_sidebar_search_top_display, #pen_sidebar_search_left_display, #pen_sidebar_search_right_display, #pen_sidebar_search_bottom_display' ) );
						}
					}

					if ( value_current === 'no' || value_current === 'none' ) {
						$select.parent().addClass( 'pen_postmeta_disable' ).removeClass( 'pen_postmeta_enable' );
					} else if ( value_current === 'yes' || value_current != 'default' ) {
						$select.parent().removeClass( 'pen_postmeta_disable' ).addClass( 'pen_postmeta_enable' );
					} else {
						$select.parent().removeClass( 'pen_postmeta_enable pen_postmeta_disable' );
					}

				}

				$post_meta.find( '.pen_sidebars input' ).each(
					function() {
						var $checkbox = $( this );
						pen_postmeta_sidebars( $checkbox );
						$checkbox.on(
							'change',
							function() {
								pen_postmeta_sidebars( $checkbox );
								$checkbox.closest( '.pen_postmeta_container' ).slideDown( 'fast' ).removeClass( 'closed' ).attr( 'aria-expanded', true )
								.closest( '.pen_postmeta_options' ).removeClass( 'closed' );
							}
						);
					}
				);

				function pen_postmeta_sidebars( $checkbox ) {
					var $option = $( '#apply_' + $checkbox.attr( 'id' ) ),
					$button     = $option.closest( 'tr' ).find( '.button' );
					if ( $checkbox.is( ':checked' ) ) {
						$checkbox.parent().addClass( 'pen_postmeta_disable' );
						$option.prop( 'disabled', true ).removeAttr( 'checked' ).prop( 'checked', false ).trigger( 'change' );
						$button.addClass( 'disabled' );
					} else {
						$checkbox.parent().removeClass( 'pen_postmeta_disable' );
						$option.prop( 'disabled', false );
						$button.removeClass( 'disabled' );
						if ( pen_function_exists( typeof $( window ).pen_meta_import_parent_disabled ) ) {
							$( '#pen_post_customizations' ).pen_meta_import_parent_disabled();
						}
					}
				}

				$post_meta.find( 'h3' ).each(
					function() {
						var $h3    = $( this ),
						$container = $h3.next( '.pen_postmeta_container' );

						$h3.append( '<button type="button" class="handlediv button-link"><span class="screen-reader-text">' + pen_postmeta_js.text.toggle + '</span><span class="toggle-indicator" aria-hidden="true"></span></button>' );

						if ( ! $container.find( '.pen_postmeta_enable,.pen_postmeta_disable' ).length ) {
							$container.slideUp( 'fast' ).addClass( 'closed' ).attr( 'aria-expanded', false )
							.closest( '.pen_postmeta_options' ).addClass( 'closed' );
						}

						$h3.on(
							'click',
							function() {
								var $container = $( this ).closest( '.pen_postmeta_options' ).find( '.pen_postmeta_container' );
								pen_options_expanded( $container );
							}
						);
					}
				);

				function pen_options_expanded( $container ) {
					if ( $container.hasClass( 'closed' ) ) {
						$container.slideDown( 'fast' ).removeClass( 'closed' ).closest( '.pen_postmeta_options' ).removeClass( 'closed' )
						.prev( 'h3' ).find( '.handlediv' ).attr( 'aria-expanded', 'true' );
					} else {
						$container.slideUp( 'fast' ).addClass( 'closed' ).closest( '.pen_postmeta_options' ).addClass( 'closed' )
						.prev( 'h3' ).find( '.handlediv' ).attr( 'aria-expanded', 'false' );
					}
				}

			}

		}
	);

	function pen_function_exists( type_of ) {
		if ( type_of !== 'undefined' && type_of !== undefined && type_of !== null ) {
			return true;
		}
		return false;
	}

} )( jQuery );
