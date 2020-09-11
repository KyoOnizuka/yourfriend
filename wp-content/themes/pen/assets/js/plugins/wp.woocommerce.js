/**
 * WooCommerce JavaScript.
 *
 * @package Pen
 */
;( function( $ ) {

	'use strict';

	var $page = $( '#page' );

	$( document ).ready(
		function() {

			pen_cart_header();

			$page.find( '.wc-block-grid__product-image' ).addClass( 'pen_image_thumbnail' );

			function pen_cart_header() {
				if ( $( '#pen_cart_header' ).length ) {
					var $header_cart = $( '#pen_cart_header' ),
					$cart_heading = $header_cart.find( 'a.cart-contents' ),
					$cart_content = $header_cart.find( '.pen_cart_content' );
					if ( pen_woocommerce_js.is_customize_preview ) {
						/* There seems to be no other way to make customizer respect the event.preventDefault() etc. */
						$cart_heading.attr( 'href', '#' );
					}
					if ( ! $cart_heading.hasClass( 'pen_processed' ) ) {
						$cart_heading.addClass( 'pen_processed' )
						.on( 'click', function( event ) {
							if ( $( '.widget_shopping_cart_content', $cart_content ).children().length ) {
								var $this = $( this );
								if ( ! $this.hasClass( 'pen_active' ) ) {
									$this.addClass( 'pen_active' );
									$cart_content.removeClass( 'pen_element_hidden' );
								} else {
									$this.removeClass( 'pen_active' );
									$cart_content.addClass( 'pen_element_hidden' );
								}
								event.preventDefault();
							}
						} );
					}
				}
			}

			$( document.body ).on( 'added_to_cart wc_cart_button_updated', function() {
				if ( $( '#pen_masonry.pen_masonry_applied' ).length ) {
					$( '#pen_masonry' ).masonry( 'layout' );
				}
				if ( $( '#pen_tiles' ).length ) {
					$( '#pen_tiles' ).pen_layout_tiles();
				}
			} );

		}
	);

})( jQuery );
