/**
 * Front-end JavaScript.
 *
 * @package Pen
 */

;( function( $ ) {

	'use strict';

	$( 'html' ).removeClass( 'no-js' ).addClass( 'js' );

	var $window = $( window ),
		$body = $( 'body' ),
		$page = $( '#page' ),
		$page_wrapper = $page.children( '.pen_wrapper' ),
		$menu_primary = $( 'ul#primary-menu' ).length ? $( 'ul#primary-menu' ) : false;

	if ( $body.hasClass( 'pen_loading_spinner' ) ) {
		$page_wrapper.addClass( 'pen_hidden' );
	}

	$( document ).ready(
		function() {

			pen_trianglify();
			pen_shards();

			if ( $body.hasClass( 'pen_width_narrow' ) || ( pen_function_exists( typeof Modernizr ) && Modernizr.mq( 'only all and (max-width:728px)' ) ) ) {
				if ( pen_js.font_resize.site_title === 'dynamic' && pen_function_exists( typeof $window.fitText ) ) {
					$( '#site-title' ).fitText( 1, { minFontSize: 20, maxFontSize: 48 } );
					$window.on(
						'resize orientationchange',
						function() {
							$window.trigger( 'resize.fittext' );
						}
					);
				}
				if ( pen_js.font_resize.site_title === 'resize' ) {
					var $site_title = $( '#pen_header #pen_site_title a .site-title' );
					$site_title.pen_font_resize( parseInt( $site_title.css( 'font-size' ) ) );
					$window.on(
						'resize orientationchange',
						function() {
							if ( pen_function_exists( typeof Modernizr ) && Modernizr.mq( 'only all and (max-width:728px)' ) ) {
								$site_title.pen_font_resize( parseInt( $site_title.css( 'font-size' ) ) );
							} else {
								$site_title.css( { fontSize: '' } );
							}
						}
					);
				}
			}

			if ( pen_function_exists( typeof autosize ) ) {
				autosize( $page.find( 'textarea' ) );
			}

			if ( $menu_primary ) {
				if ( pen_js.navigation_pointer_event !== 'click' && pen_function_exists( typeof $window.superfish ) ) {
					$menu_primary.superfish(
						{
							animation: pen_js.navigation_easing,
							speed: parseInt( pen_js.animation_navigation_speed ),
							cssArrows: pen_js.navigation_arrows
						}
					);
				} else if ( pen_function_exists( typeof $window.superclick ) ) {
					$menu_primary.superclick(
						{
							animation: pen_js.navigation_easing,
							speed: parseInt( pen_js.animation_navigation_speed ),
							cssArrows: pen_js.navigation_arrows
						}
					);
				}
				if ( pen_js.navigation_mobile !== 'never' ) {
					$menu_primary.pen_navigation_mobile();
				}
			}

			$( '.search-form' ).on(
				'submit',
				function( event ) {
					var $search = $( this );
					if ( pen_text_trim( $search.find( '.search-field' ).val() ) === '' ) {
						alert( pen_js.text.enter_keyword );
						event.preventDefault();
					}
				}
			);

			if ( '.pen_options_overview' ) {
				$( '#primary' ).find( '.pen_options_overview' ).each(
					function() {
						var $overview = $( this );
						if ( $body.hasClass( 'pen_singular' ) ) {
							$page.append( $overview );
						}
						var overview_id = $overview.attr( 'id' ),
							toggle_id = overview_id + '_toggle';
						$overview.addClass( 'pen_off_screen' )
						.prepend( '<a href="#" class="pen_close">' + pen_js.text.close + '</a>' )
						.before( '<a href="#" id="' + toggle_id + '" class="pen_options_overview_toggle pen_button pen_visible" title="' + pen_js.text.overview_options_post + '">' + pen_js.text.overview_options_post + '</a>' )
						.find( '.pen_close' ).on(
							'click',
							function( event ) {
								$( '#' + toggle_id ).toggleClass( 'pen_visible' );
								$overview.toggleClass( 'pen_visible' );
								event.preventDefault();
							}
						);
						$( '#' + toggle_id ).on(
							'click',
							function( event ) {
								$( this ).toggleClass( 'pen_visible' );
								$overview.toggleClass( 'pen_visible' );
								event.preventDefault();
							}
						);
					}
				);
			}

			$( '#pen_back' ).hide().on(
				'click',
				function ( event ) {
					$( 'html, body' ).animate( { scrollTop: 0 }, { queue: false, duration: 1000 } );
					event.preventDefault();
				}
			);

			pen_sticky_header();

			pen_sticky_navigation_mobile();

			if ( pen_js.infinite_scrolling && pen_js.url_page_next && $body.hasClass( 'pen_multiple' ) ) {

				var page_next = parseInt( pen_js.page_current ),
					url_page_next = pen_js.url_page_next,
					page_title = $( 'title' ).text(),
					$articles = $page.find( '.pen_articles article' );

				$articles.each( function() {
					$( this )
					.data( 'page-url', window.location.href )
					.data( 'page-title', page_title );
				} );

				if ( pen_function_exists( typeof $( window ).waypoint ) ) {
					$articles.waypoint( {
						handler: function( direction ) {
							var $item = $( this.element );
							$( 'title' ).html( $item.data( 'page-title' ) );
							if ( history.pushState ) {
								window.history.replaceState( window.location.href, $( 'title' ).text(), $item.data( 'page-url' ) );
							}
						},
						offset: '90%'
					} );
				}

				$window.on(
					'scroll',
					function() {
						if ( $window.scrollTop() === $( document ).height() - $window.height() ) {
							if ( page_next > parseInt( pen_js.pages_total ) ) {
								return false;
							} else {
								if ( $page.hasClass( 'pen_wait' ) || ! url_page_next ) {
									return false;
								}
								$page.children( '.pen_loading' ).fadeIn( 100, function() { $page.addClass( 'pen_wait' ); } );
								$.post( {
									url: url_page_next,
									success: function ( html ) {
										if ( html ) {
											$page.children( '.pen_loading' ).fadeOut( 100, function() { $page.removeClass( 'pen_wait' ); } );
											var $last_article = $page.find( '.pen_article' ).last(),
												$html = $( '<div />' ).prepend( html ),
												page_title = $html.find( 'title' ).text(),
												$articles  = $html.find( '.pen_articles article' ),
												$link_previous = $html.find( '#pen_pager .nav-previous > a' ),
												url_page_current = url_page_next;

											if ( $link_previous.length ) {
												if ( history.pushState ) {
													window.history.pushState( {}, page_title, url_page_current );
												}
												url_page_next = $link_previous.attr( 'href' );
											} else {
												url_page_next = false;
											}

											if ( $articles.length ) {
												$last_article.after( $articles );

												$articles.each( function() {
													$( this )
													.data( 'page-url', url_page_current )
													.data( 'page-title', page_title );
												} );

												if ( pen_function_exists( typeof $( window ).waypoint ) ) {
													$articles.waypoint( {
														handler: function( direction ) {
															var $item = $( this.element );
															$( 'title' ).html( $item.data( 'page-title' ) );
															if ( history.pushState ) {
																window.history.replaceState( window.location.href, $( 'title' ).text(), url_page_current );
															}
														},
														offset: '90%'
													} );
												}

												var $header = $( '#pen_header' ),
													offset_top = $last_article.offset().top;
												if ( pen_function_exists( typeof Modernizr ) && Modernizr.mq( 'only all and (max-width:728px)' ) ) {
													offset_top += $last_article.outerHeight( true );
												} else {
													offset_top -= 40;
												}
												if ( $( '#wpadminbar' ).length ) {
													offset_top -= $( '#wpadminbar' ).outerHeight( true );
												}
												if ( $header.hasClass( 'pen_header_sticked' ) ) {
													offset_top -= $header.outerHeight( true );
												}
												$( 'html, body' ).animate( { scrollTop: offset_top }, { queue: false, duration: 1000 } );

												if ( $( '#pen_masonry.pen_masonry_applied' ).length ) {
													$( '#pen_masonry' ).masonry( 'destroy' ).pen_layout_masonry();
													/* pen_content_height() does nothing on mobile. */
													$page.pen_content_height();
												}
												if ( $( '#pen_tiles' ).length ) {
													$( '#pen_tiles' ).pen_layout_tiles();
												}
												if ( $( '#pen_plain' ).length ) {
													$( '#pen_plain' ).pen_layout_plain();
												}
											} else {
												alert( pen_js.text.no_more_content );
											}
										}
									}
								} );
							}
							page_next++;
						}
					}
				);
			}

			$page.find( '.pen_warning_upgrade' ).each( function() {
				var $warning = $( this );
				$warning.addClass( 'pen_element_hidden' ).attr( 'aria-hidden', 'true' )
				.before( '<button class="pen_warning_toggle">' + pen_js.text.button_warning + '</button>' )
				.prev( '.pen_warning_toggle' ).on( 'click', function() {
					var $toggle = $( this );
					$warning.on( 'click', function() {
						$( this ).addClass( 'pen_element_hidden' ).attr( 'aria-hidden', 'true' );
						$toggle.after( $warning );
					} );
					if ( $warning.hasClass( 'pen_element_hidden' ) ) {
						$page.append( $warning );
						$warning.removeClass( 'pen_element_hidden' ).attr( 'aria-hidden', 'false' );
					} else {
						$warning.addClass( 'pen_element_hidden' ).attr( 'aria-hidden', 'true' );
						$toggle.after( $warning );
					}
				} );
			} );

		}
	);

	$window.on(
		'load',
		function() {
			var $main = $( '#main' );

			if ( $body.hasClass( 'pen_loading_spinner' ) ) {
				$page.children( '.pen_loading' ).fadeOut(
					100,
					function() {
						$page_wrapper.removeClass( 'pen_hidden' )
						.css( { display: 'none', visibility: 'visible' } ).fadeIn( 100 );
					}
				);
			}

			if ( $( '#pen_masonry' ).length ) {
				$( '#pen_masonry' ).pen_layout_masonry();
			}
			if ( $( '#pen_tiles' ).length ) {
				$( '#pen_tiles' ).pen_layout_tiles();
			}
			if ( $( '#pen_plain' ).length ) {
				$( '#pen_plain' ).pen_layout_plain();
			}

			if ( pen_js.animation_comments ) {
				var $comments = $( '#comments .comment-list' );
				if ( $comments.length ) {
					$comments.children( 'li' )
					.addClass( 'pen_animate_on_scroll pen_custom_animation_' + pen_js.animation_comments );
					if ( pen_js.animation_delay_comments ) {
						$comments.children( 'li' ).attr( 'delay', pen_js.animation_delay_comments );
					}
				}
			}

			pen_animation( $page.find( '.pen_animate_on_scroll' ), 'automatic' );

			$page.pen_content_height();

			$page.find( '.pen_jump_menu' ).each(
				function() {
					pen_jump_menu( $( this ) );
				}
			);

		}
	);

	$window.on(
		'resize orientationchange',
		function() {
			if ( $menu_primary && pen_js.navigation_mobile !== 'never' ) {
				$menu_primary.pen_navigation_mobile();
			}
			$page.pen_content_height();
			if ( $( '#pen_masonry.pen_masonry_applied' ).length ) {
				$( '#pen_masonry' ).pen_layout_masonry();
			}
			if ( $( '#pen_tiles' ).length ) {
				$( '#pen_tiles' ).pen_layout_tiles();
			}
		}
	);

	$window.on(
		'scroll',
		function() {
			if ( $( this ).scrollTop() > 400 ) {
				$( '#pen_back' ).fadeIn( 200 );
			} else {
				$( '#pen_back' ).fadeOut( 200 );
			}
		}
	);

	function pen_sticky_header() {
		if ( pen_js.header_sticky && $( '#pen_header' ).length && ! $body.hasClass( 'pen_site_header_hide' ) ) {
			var mobile = false,
				tablet = false,
				layout_boxed = $body.hasClass( 'pen_width_boxed' ) ? true : false,
				layout_narrow = $body.hasClass( 'pen_width_narrow' ) ? true : false,
				$wpadminbar = $( '#wpadminbar' ),
				$header = $( '#pen_header' );

			$window.on(
				'load resize orientationchange pen_update_sticky_header',
				function() {
					if ( ! $body.hasClass( 'pen_width_narrow' ) ) {
						if ( pen_function_exists( typeof Modernizr ) ) {
							mobile = Modernizr.mq( 'only all and (max-width:728px)' ) ? true : false;
							tablet = Modernizr.mq( 'only all and (max-width:1024px)' ) ? true : false;
						}
					}
					var header_top = 0,
						header_offset_left = 0,
						admin_toolbar_height = 0,
						mobile_menu_height = 0,
						header_height = $header.removeClass( 'pen_header_sticked' ).outerHeight( true );

					if ( $wpadminbar.length ) {
						var admin_toolbar_height = $wpadminbar.outerHeight( true );
						header_top += admin_toolbar_height;
					}
					var $mobile_menu = $( '#pen_navigation_mobile' );
					if ( $mobile_menu.length ) {
						var mobile_menu_height = $mobile_menu.outerHeight( true );
						header_top += mobile_menu_height;
					}
					if ( layout_boxed || layout_narrow ) {
						var header_offset = $page_wrapper.offset();
						if ( header_offset ) {
							header_offset_left = header_offset.left;
						}
						$header.css( { width: $( '#pen_section' ).outerWidth( true ) } );
					}
					if ( mobile || tablet || ( $window.outerHeight() / 2 ) < $header.outerHeight( true ) ) {
						$header.removeClass( 'pen_header_sticked' ).css( { left: '', position: '', top: '' } );
						$body.removeClass( 'pen_header_sticked' );
            $page_wrapper.css( { paddingTop: '' } );
					} else {
						$header.css( { left: header_offset_left, position: 'fixed', top: header_top } );
						$page_wrapper.css( { paddingTop: header_height + mobile_menu_height } );
					}

					$window.on(
						'scroll',
						function() {
							if ( $window.scrollTop() ) {
								$header.addClass( 'pen_header_sticked' );
								var header_top_dynamic = 0;
								if ( $wpadminbar.length && $wpadminbar.css( 'position' ) === 'fixed' ) {
									header_top_dynamic += admin_toolbar_height;
								}
								if ( $mobile_menu.length && $mobile_menu.css( 'position' ) === 'fixed' ) {
									header_top_dynamic += mobile_menu_height;
								}
								if ( $header.css( 'position' ) === 'fixed' ) {
									$header.css( { top: header_top_dynamic } );
								}
								$body.addClass( 'pen_header_sticked' );
							} else {
								$header.removeClass( 'pen_header_sticked' )
								.css( { top: ( $header.css( 'position' ) === 'fixed' ) ? header_top : 0 } );
								$body.removeClass( 'pen_header_sticked' );
							}
						}
					);
				}
			);
		}
	}

	function pen_sticky_navigation_mobile() {
		if ( pen_js.navigation_mobile_sticky ) {
			var $wpadminbar = $( '#wpadminbar' ),
				$header = $( '#pen_header' );

			$window.on(
				'load resize orientationchange pen_update_sticky_navigation_mobile',
				function() {
					var $navigation_mobile = $( '#pen_navigation_mobile' );
					if ( $navigation_mobile.length ) {
						var navigation_mobile_top = 0,
							admin_toolbar_height = 0,
							navigation_mobile_height = $navigation_mobile.removeClass( 'pen_navigation_mobile_sticked' ).outerHeight( true );
						if ( $wpadminbar.length ) {
							admin_toolbar_height = $wpadminbar.outerHeight( true );
						}
						$navigation_mobile.css( { left: 0, position: 'fixed', top: admin_toolbar_height } );
						$page_wrapper.css( {
							paddingTop: navigation_mobile_height + ( ( $header.css( 'position' ) === 'fixed' ) ? $header.outerHeight( true ) : 0 )
						} );

						$window.on(
							'scroll',
							function() {
								if ( $window.scrollTop() ) {
									$navigation_mobile.addClass( 'pen_navigation_mobile_sticked' );
									if ( pen_function_exists( typeof Modernizr ) ) {
										var breakpoint = '';
                    if ( pen_js.navigation_mobile === 'mobile' ) {
                      breakpoint = ' and (max-width:728px)';
										} else if ( pen_js.navigation_mobile === 'tablet' || pen_js.navigation_mobile === 'mobile_tablet' ) {
											breakpoint = ' and (max-width:1024px)';
										}
										if ( Modernizr.mq( 'only all' + breakpoint ) ) {
											$navigation_mobile.css( {
												top: ( $wpadminbar.css( 'position' ) === 'fixed' ) ? admin_toolbar_height : 0
											} );
										}
									}
									$body.addClass( 'pen_navigation_mobile_sticked' );
								} else {
									$navigation_mobile.removeClass( 'pen_navigation_mobile_sticked' ).css( { top: admin_toolbar_height } );
									$body.removeClass( 'pen_navigation_mobile_sticked' );
								}
							}
						);
					}
				}
			);
		}
	}

	$.fn.extend(
		{
			pen_font_resize: function( font_size ) {
				var $element = this,
					parent_width = $element.parent().outerWidth( false ),
					element_width = $element.css( { position: 'fixed', whiteSspace: 'nowrap' } ).outerWidth( true );
					$element.css( { position: 'relative' } );
					font_size = font_size - 2;
				if ( font_size > 12 && element_width > parent_width ) {
					$element.animate( { fontSize: font_size } )
					.end().pen_font_resize( font_size );
				}
			}
		}
	);

	$.fn.extend(
		{
			pen_navigation_mobile: function() {

				var visible = false,
					$menu = this,
					$menu_mobile = $menu.clone(), /* $menu is a <ul> */
					$navigation = $menu.closest( 'nav' ),
					$navigation_mobile = $( '#pen_navigation_mobile' ),
					$wpadminbar = $( '#wpadminbar' );

				if ( pen_js.navigation_mobile === 'always' ) {
					visible = true;
				} else if ( pen_function_exists( typeof Modernizr ) ) {
					if ( pen_js.navigation_mobile === 'mobile' ) {
						visible = Modernizr.mq( 'only all and (max-width:728px)' ) ? true : false;
					} else if ( pen_js.navigation_mobile === 'mobile_tablet' ) {
						visible = Modernizr.mq( 'only all and (max-width:1024px)' ) ? true : false;
					}
				}

				if ( ! visible ) {
					if ( $navigation_mobile.length ) {
						if ( $navigation_mobile.find( '#pen_mobile_menu_top' ).length ) {
							$menu.closest( 'nav' ).before( $( '#pen_mobile_menu_top' ) );
						}
						if ( $navigation_mobile.find( '#pen_mobile_menu_bottom' ).length ) {
							$menu.closest( 'nav' ).after( $( '#pen_mobile_menu_bottom' ) );
						}
						if ( $navigation_mobile.find( '#pen_jump_menu_navigation' ).length ) {
							$menu.closest( 'nav' ).append( $( '#pen_jump_menu_navigation' ) );
							$( '#pen_jump_menu_navigation' ).removeClass( 'pen_element_hidden' )
							.attr( 'aria-hidden', 'false' );
						}

						$navigation_mobile.remove();
						if ( pen_js.navigation_mobile_sticky ) {
							$page_wrapper.css( { paddingTop: '' } );
						}
						$window.trigger( 'pen_update_sticky_navigation_mobile' );
						$navigation.show();
						if ( $page_wrapper.hasClass( 'pen_element_hidden' ) ) {
							$page_wrapper.hide().removeClass( 'pen_element_hidden' ).stop( true, true ).fadeIn( 200 );
						}
					}

					return;
				}

				$menu_mobile.attr( 'id', 'primary-menu-mobile' )
				.removeClass( function ( index, css ) {
					return ( css.match( /(^|\s)sf-\S+/g ) || [] ).join( ' ' );
				} )
				.find( '*' ).each( function() {
					$( this ).removeClass( function ( index, css ) {
						return ( css.match( /(^|\s)sf-\S+/g ) || [] ).join( ' ' );
					} );
				} )
				.end()
				.addClass( 'pen_element_hidden pen_collapsed' ).attr( 'aria-hidden', 'true' )
				.find( 'li ul' )
				.addClass( 'pen_element_hidden pen_collapsed' ).attr( 'aria-hidden', 'true' );

				if ( ! $( '#pen_navigation_mobile' ).length ) {
					$page.prepend( '<div id="pen_navigation_mobile" />' )
					.children( '#pen_navigation_mobile' )
					.addClass( 'pen_collapsed' )
					.prepend( '<div id="pen_navigation_mobile_wrapper" />' )
					.children( '#pen_navigation_mobile_wrapper' )
					.prepend( '<nav role="navigation" />' )
					.children( 'nav' )
					.attr( 'class', $navigation.attr( 'class' ) )
					.removeClass( 'pen_element_hidden' )
					.attr( 'aria-label', $navigation.attr( 'aria-label' ) )
					.prepend( $menu_mobile );

					var $navigation_mobile = $( '#pen_navigation_mobile' );

					$navigation_mobile.prepend( '<a id="pen_navigation_mobile_toggle" href="' + pen_js.url_home + '"><span class="pen_text">' + pen_js.text.menu + '</span><span class="pen_icon"><span></span><span></span><span></span><span></span></span></a>' );

					if ( $( '#pen_mobile_menu_top' ).length ) {
						var $mobile_menu_top = $( '#pen_mobile_menu_top' );
						$mobile_menu_top.addClass( 'pen_element_hidden' ).attr( 'aria-hidden', 'true' );
						$navigation_mobile.find( 'nav' ).before( $mobile_menu_top );
					}
					if ( $( '#pen_mobile_menu_bottom' ).length ) {
						var $mobile_menu_bottom = $( '#pen_mobile_menu_bottom' );
						$mobile_menu_bottom.addClass( 'pen_element_hidden' ).attr( 'aria-hidden', 'true' );
						$navigation_mobile.find( 'nav' ).after( $mobile_menu_bottom );
					}
					if ( $( '#pen_jump_menu_navigation' ).length ) {
						$navigation_mobile.find( 'nav' ).append( $( '#pen_jump_menu_navigation' ) );
						$( '#pen_jump_menu_navigation' ).addClass( 'pen_element_hidden' )
						.attr( 'aria-hidden', 'true' );
					}
				} else {
					var $navigation_mobile = $( '#pen_navigation_mobile' );
				}

				/* The mobile menu is there, so no need to instead apply the pen_element_hidden class. */
				$navigation.hide();

				var $navigation_mobile_toggle = $( '#pen_navigation_mobile_toggle' );

				if ( ! pen_js.text.menu ) {
					$navigation_mobile_toggle.find( '.pen_text' ).addClass( 'pen_element_hidden' );
				}

				$navigation_mobile_toggle.on( 'click', function( event ) {
					if ( $menu_mobile.hasClass( 'pen_collapsed' ) ) {
						$( this ).addClass( 'pen_active' );
						$menu_mobile.hide()
						.removeClass( 'pen_collapsed pen_element_hidden' )
						.addClass( 'pen_expanded' )
						.stop( true, true )
						.animate( pen_js.navigation_easing, pen_js.animation_navigation_speed )
						.attr( 'aria-hidden', 'false' );
						if ( $mobile_menu_top ) {
							$mobile_menu_top.hide().removeClass( 'pen_element_hidden' )
							.fadeIn( 200 ).attr( 'aria-hidden', 'false' );
						}
						if ( $mobile_menu_bottom ) {
							$mobile_menu_bottom.hide().removeClass( 'pen_element_hidden' )
							.fadeIn( 200 ).attr( 'aria-hidden', 'false' );
						}
						if ( $wpadminbar.length && ! pen_js.navigation_mobile_sticky ) {
							$navigation_mobile.css( { paddingTop: $wpadminbar.outerHeight( true ) } );
						}
						$navigation_mobile.addClass( 'pen_expanded' ).removeClass( 'pen_collapsed' );
						if ( $( '#pen_jump_menu_navigation' ).length ) {
							$( '#pen_jump_menu_navigation' ).removeClass( 'pen_element_hidden' )
							.attr( 'aria-hidden', 'false' );
						}
						$page_wrapper.stop( true, true ).fadeOut( 200,
							function() {
								$( this ).addClass( 'pen_element_hidden' ).show();
							}
						);
					} else {
						$( this ).removeClass( 'pen_active' );
						$menu_mobile
						.stop( true, true )
						.animate( { height: 'hide' }, pen_js.animation_navigation_speed, function() {
							$( this )
							.removeClass( 'pen_expanded' )
							.addClass( 'pen_collapsed pen_element_hidden' ).show()
							.attr( 'aria-hidden', 'true' );
						} );
						if ( $mobile_menu_top ) {
							$mobile_menu_top.addClass( 'pen_element_hidden' ).attr( 'aria-hidden', 'true' );
						}
						if ( $mobile_menu_bottom ) {
							$mobile_menu_bottom.addClass( 'pen_element_hidden' ).attr( 'aria-hidden', 'true' );
						}
						$navigation_mobile.removeClass( 'pen_expanded' ).addClass( 'pen_collapsed' );
						if ( $wpadminbar.length && ! pen_js.navigation_mobile_sticky ) {
							$navigation_mobile.css( { paddingTop: '' } );
						}
						if ( $( '#pen_jump_menu_navigation' ).length ) {
							$( '#pen_jump_menu_navigation' ).addClass( 'pen_element_hidden' )
							.attr( 'aria-hidden', 'true' );
						}
						$page_wrapper.hide().removeClass( 'pen_element_hidden' ).stop( true, true ).fadeIn( 200 );
					}
					event.preventDefault();
				} );

				$menu_mobile.find( 'a' ).each( function() {
					var $link = $( this ),
						$parent = $link.closest( 'li' ),
						$child = $parent.children( 'ul' );
					if ( $child.length ) {
						if ( pen_js.navigation_mobile_parents_include && $parent.children( 'a' ).attr( 'href' ) !== '#' ) {
							$child.prepend( $( '<li class="pen_duplicate" />' ).prepend( $parent.children( 'a' ).clone() ) );
						}
						$parent.addClass( 'pen_parent pen_collapsed' );
						$link.on( 'click', function( event ) {
							if ( $parent.hasClass( 'pen_collapsed' ) ) {
								$parent.addClass( 'pen_expanded' ).removeClass( 'pen_collapsed' );
								$child.hide()
								.removeClass( 'pen_collapsed pen_element_hidden' )
								.addClass( 'pen_expanded' )
								.stop( true, true )
								.animate( pen_js.navigation_easing, pen_js.animation_navigation_speed )
								.attr( 'aria-hidden', 'false' );
							} else {
								$parent.addClass( 'pen_collapsed' ).removeClass( 'pen_expanded' );
								$child.hide()
								.removeClass( 'pen_expanded pen_element_hidden' )
								.addClass( 'pen_collapsed' )
								.stop( true, true )
								.animate( { height: 'hide' }, pen_js.animation_navigation_speed )
								.attr( 'aria-hidden', 'false' );
							}
							event.preventDefault();
						} );
					}
				} );

				if ( $navigation_mobile && pen_js.is_customize_preview ) {
					$navigation_mobile.find( 'li.pen_parent > a' ).attr( 'href', '#' );
				}

			}
		}
	);

	$.fn.extend(
		{
			pen_content_height: function() {
				var leftHeight = 0,
					rightHeight = 0,
					$content = $( '#content' ),
					$left = $( '#pen_left' ),
					$right = $( '#pen_right' );
				if ( pen_function_exists( typeof Modernizr ) && Modernizr.mq( 'only all and (max-width:728px)' ) ) {
					$content.css( { minHeight: '' } );
					return;
				}
				if ( $left.length ) {
					leftHeight = $left.outerHeight( true );
				}
				if ( $right.length ) {
					rightHeight = $right.outerHeight( true );
				}
				var contentHeight = Math.max( leftHeight, rightHeight );
				if ( contentHeight ) {
					contentHeight += parseInt( $content.css( 'padding-bottom' ) );
					$content.css( 'min-height', contentHeight + 30 );
				}
			}
		}
	);

	$.fn.extend(
		{
			pen_layout_masonry: function() {
				var $list = this;
				if ( pen_function_exists( typeof $window.masonry ) ) {
					$list.masonry(
						{
							itemSelector: '.pen_article',
							percentPosition: true,
							transitionDuration: 0
						}
					).addClass( 'pen_masonry_applied' );
					if ( pen_function_exists( typeof $window.imagesLoaded ) ) {
						$list.imagesLoaded(
							function() {
								$list.masonry( 'layout' );
								$page.pen_content_height();
								if ( pen_function_exists( typeof pen_animation ) ) {
									var $main = $( '#main' ),
										$items = $main.find( '.pen_article' ),
										$thumbnails = $main.find( '.pen_image_thumbnail' );
									pen_animation( $items, pen_js.animation_list );
									pen_animation( $thumbnails, pen_js.animation_list_thumbnails );
								}
							}
						);
					}
					setTimeout(
						function() {
							$list.masonry( 'layout' );
							$page.pen_content_height();
						},
						3000
					);
				}
			}
		}
	);

	$.fn.extend(
		{
			pen_layout_plain: function() {
				var $list = this,
					$main = $( '#main' );
				if ( pen_function_exists( typeof pen_animation ) ) {
					var $thumbnails = $main.find( '.pen_image_thumbnail' );
					if ( $body.hasClass( 'pen_multiple' ) ) {
						var $items = $main.find( '.pen_article' );
						pen_animation( $items, pen_js.animation_list );
						pen_animation( $thumbnails, pen_js.animation_list_thumbnails );
					} else {
						pen_animation( $main, pen_js.animation_content );
						pen_animation( $thumbnails, pen_js.animation_content_thumbnails );
					}
				}
			}
		}
	);

	$.fn.extend(
		{
			pen_layout_tiles: function() {
				if ( $body.hasClass( 'pen_width_narrow' ) ) {
					return false;
				}
				var $list = this,
					$page = $( '#page' );
				if ( pen_function_exists( typeof Modernizr ) && Modernizr.mq( 'only all and (max-width:728px)' ) ) {
					$list.find( '.pen_article' ).css( { minHeight: '' } );
				} else {
					if ( pen_function_exists( typeof $window.imagesLoaded ) ) {
						$list.imagesLoaded(
							function() {
								var $articles = $list.find( '.pen_article' ),
									height = 0;
								$articles.each( function() {
									height = Math.max( height, $( this ).outerHeight( true ) );
								} );
								$articles.css( 'min-height', height );
								$page.pen_content_height();
							}
						);
					} else {
						/**
						 * Since we're only using it on window.load and window.resize it may
						 * work without imagesLoaded() too.
						 */
						var $articles = $list.find( '.pen_article' ),
							height = 0;
						$articles.each( function() {
							height = Math.max( height, $( this ).outerHeight( true ) );
						} );
						$articles.css( 'min-height', height );
						$page.pen_content_height();
					}
				}
				if ( pen_function_exists( typeof pen_animation ) ) {
					var $main = $( '#main' ),
						$items = $main.find( '.pen_article' ),
						$thumbnails = $main.find( '.pen_image_thumbnail' );
					pen_animation( $items, pen_js.animation_list );
					pen_animation( $thumbnails, pen_js.animation_list_thumbnails );
				}
				/* pen_content_height() does nothing on mobile. */
				setTimeout(
					function() {
						$page.pen_content_height();
					},
					5000
				);

			}
		}
	);

})( jQuery );

function pen_animation( $items, animation ) {
	var animation_offset = '90%';

	$items = $items.filter( '.pen_animate_on_scroll' );

	if ( pen_function_exists( typeof jQuery( window ).waypoint ) && animation ) {
		$items.not( '.animate__animated' ).addClass( 'animate__animated' ).css( 'visibility', 'hidden' );

		for ( var i = 0; i < $items.length; i++ ) {
			var $item = $items.eq(i);
			if ( $item.closest( '#pen_footer, #pen_bottom' ).length ) {
				animation_offset = '99%';
			}
			$item.waypoint(
				{
					handler: function( direction ) {
						var timer,
							$item = jQuery( this.element ),
							add_animation = '';

						var animation_delay = 0,
							custom_animation_delay = this.element.className.match( /(^|\s)pen_custom_animation_delay_\S+/g );
						if ( custom_animation_delay && custom_animation_delay[0] ) {
							animation_delay = ( 1000 * parseInt( jQuery.trim( custom_animation_delay[0].replace( 'pen_custom_animation_delay_', '' ) ) ) );
						}

						var custom_animation = this.element.className.match( /(^|\s)pen_custom_animation_\S+/g );
						if ( custom_animation && custom_animation[0] ) {
							add_animation = jQuery.trim( custom_animation[0].replace( 'pen_custom_animation_', 'animate__' ) );
						} else {
							add_animation = 'animate__' + animation;
						}

						timer = setTimeout( function() {
							if ( ! $item.hasClass( add_animation ) ) {
								$item.addClass( add_animation ).css( 'visibility', 'visible' );
							}
						}, animation_delay ? animation_delay : 1 );

					},
					offset: animation_offset
				}
			);
		}
	}
}

function pen_shards() {
	if ( pen_function_exists( typeof jQuery( window ).shards ) ) {
		var $body = jQuery( 'body' ),
		background_image = $body.css( 'background-image' );
		if ( pen_js.shards_colors && background_image && background_image === 'none' ) {
			$body.prepend( '<div id="shards" style="left:0;height:100%;position:fixed;top:0;visibility:hidden;width:100%;" />' );
			var $shards = jQuery( '#shards' );
			$shards.shards( pen_js.shards_colors[0], pen_js.shards_colors[1], [0,0,0,0.2], 20, .8, 2, .15, true );
			var background = $shards.css( 'background-image' );
			$shards.remove();
			$body.addClass( 'pen_shards' ).removeClass( 'custom-background' );
			jQuery( 'head' ).append( "<style type=\"text/css\">body.pen_shards:before{background-image:" + background + " !important;content:'';left:0;height:100%;position:fixed;top:0;width:100%;will-change:transform;z-index:-1; }</style>" );
		}
	}
}

function pen_trianglify() {
	if ( pen_function_exists( typeof Trianglify ) ) {
		var $body = jQuery( 'body' ),
		background_image = $body.css( 'background-image' );
		if ( pen_js.trianglify_colors && background_image && background_image === 'none' ) {
			var pattern = Trianglify(
				{
					height: window.innerHeight,
					width: window.innerWidth,
					x_colors: pen_js.trianglify_colors,
					y_colors: 'match_x',
					cell_size: 80
				}
			);
			var svg = jQuery( '<div />' ).prepend( pattern.svg() ).html();
			svg = '<?xml version="1.0" ?>' + svg.replace( '<svg', '<svg xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none"' );
			var dataURI = "data:image/svg+xml;base64, " + window.btoa( unescape( encodeURIComponent( svg ) ) );
			$body.addClass( 'pen_trianglify' ).removeClass( 'custom-background' );
			jQuery( 'head' ).append( "<style type=\"text/css\">body.pen_trianglify:before{background-image:url('" + dataURI + "') !important;background-size:cover !important;content:'';left:0;height:100%;position:fixed;top:0;width:100%;will-change:transform;z-index:-1; }</style>" );
		}
	}
}

function pen_jump_menu( $menu ) {
	var $heading = $menu.find( 'strong.pen_jump_menu_title' ),
	name = jQuery.trim( $heading.children( 'span.pen_jump_menu_name' ).text() ),
	title = jQuery.trim( $heading.attr( 'title' ) );
	$menu.prepend( '<button type="button" class="pen_toggle pen_collapsed" title="' + title + '"><span class="pen_element_hidden">' + pen_js.text.expand_collapse + '</span><span class="pen_caption pen_element_hidden">' + name + '</span></button>' )
	.find( '.pen_menu_wrapper' ).attr( 'aria-hidden', true )
	.end().removeClass( 'pen_element_hidden' ).attr( 'aria-hidden', false )
	.children( 'strong.pen_jump_menu_title' ).prepend( '<span class="pen_only" title="' + pen_js.text.theme_specific + '">' + pen_js.text.pen_theme + '</span>&nbsp;' );
	var $toggle = $menu.find( '.pen_toggle' ),
		timer;
	if ( $menu.attr( 'id' ) === 'pen_jump_menu_color_schemes' || $menu.attr( 'id' ) === 'pen_jump_menu_font_presets' ) {
		$toggle.find( '.pen_caption' ).removeClass( 'pen_element_hidden' );
	}
	$menu.find( 'ul li a' ).each(
		function() {
			jQuery( this ).attr( 'title', jQuery.trim( jQuery( this ).text() ) ).attr( 'tabindex', '-1' );
		}
	);
	$toggle.on(
		'click',
		function() {
			var $wrapper = jQuery( '.pen_menu_wrapper', $menu );
			clearTimeout( timer );
			if ( $toggle.hasClass( 'pen_expanded' ) ) {
				$toggle.removeClass( 'pen_expanded' ).addClass( 'pen_collapsed' );
				$wrapper.addClass( 'pen_element_hidden' ).attr( 'aria-hidden', true )
				.find( 'ul li a' ).attr( 'tabindex', '-1' );
			} else {
				$toggle.addClass( 'pen_expanded' ).removeClass( 'pen_collapsed' );
				$wrapper.find( 'ul li a' ).removeAttr( 'tabindex' )
				.end().removeClass( 'pen_element_hidden' ).attr( 'aria-hidden', false )
				.on(
					'mouseleave',
					function() {
						clearTimeout( timer );
						timer = setTimeout(
							function() {
								$wrapper.stop( true, true ).animate(
									{ opacity: 0 },
									{
										duration: 2000,
										queue: false,
										complete: function() {
											 $toggle.trigger( 'click' );
										}
									}
								);
							},
							30000
						);
					}
				).on(
					'mouseenter',
					function() {
						$wrapper.stop( true, true ).animate( { opacity: 1 }, { duration: 200, queue: false } );
						clearTimeout( timer );
					}
				);
			}
		}
	);
}

function pen_function_exists( type_of ) {
	if ( type_of !== 'undefined' && type_of !== undefined && type_of !== null ) {
		return true;
	}
	return false;
}

function pen_text_trim( input ) {
	if ( ! input ) {
		return input;
	}
	var output = jQuery.trim( input.replace( /\s/g, ' ' ) );
	return output;
}
