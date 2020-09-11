<?php
/**
 * Template part for displaying the content footer for search results.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Pen
 */

$content_id      = pen_post_id();
$pen_is_singular = pen_is_singular();

ob_start();
// Hide tags for pages.
if ( 'page' !== get_post_type() ) {
	/* Translators: used between list items, there is a space after the comma */
	$tags_list = get_the_tag_list();

	if ( $tags_list ) {
		$classes_tag = array(
			'tags-links',
			pen_class_lists( 'tags_display_override', $content_id ),
		);
		$classes_tag = implode( ' ', array_filter( $classes_tag ) );
		printf(
			'<span class="%1$s">%2$s</span>',
			esc_attr( $classes_tag ),
			sprintf(
				'<span class="pen_heading_tags">%1$s</span>%2$s',
				esc_html__( 'Tagged', 'pen' ),
				$tags_list /* phpcs:ignore */
			)
		);
	}
}

if ( ! $pen_is_singular && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
	$classes_comment = array(
		'comments-link',
		pen_class_lists( 'button_comment_display_override', $content_id ),
	);
	$classes_comment = implode( ' ', array_filter( $classes_comment ) );
	?>
	<span class="<?php echo esc_attr( $classes_comment ); ?>">
	<?php
	comments_popup_link(
		sprintf(
			wp_kses(
				/* Translators: Content title */
				__( 'Leave a Comment<span class="screen-reader-text"> on %s</span>', 'pen' ),
				array(
					'span' => array(
						'class' => array(),
					),
				)
			),
			wp_kses_post( get_the_title() )
		)
	);
	?>
	</span>
	<?php
}

if ( ! $pen_is_singular ) {
	$edit_link = pen_option_get( 'list_button_edit_display' ) ? get_edit_post_link( $content_id ) : '';
	if ( $edit_link ) {
		$classes_edit = array(
			'edit-link',
			pen_class_lists( 'button_edit_display_override', $content_id ),
		);
		$classes_edit = implode( ' ', array_filter( $classes_edit ) );
		?>
	<span class="<?php echo esc_attr( $classes_edit ); ?>">
		<a href="<?php echo esc_url( $edit_link ); ?>" class="pen_button post-edit-link">
			<?php
			echo wp_kses_post(
				sprintf(
					'%s%s',
					esc_html__( 'Edit', 'pen' ),
					the_title(
						'<span class="pen_element_hidden"> &rarr; "',
						'"</span>',
						false
					)
				)
			);
			?>
		</a>
	</span>
		<?php
	}
}
$post_footer = trim( ob_get_clean() );
$entry_meta  = trim( pen_html_content_information( 'footer', $content_id ) );
if ( $post_footer || $entry_meta ) {
	$classes_footer = array(
		'entry-footer',
		'pen_content_footer',
		pen_class_lists( 'footer_display_override', $content_id ),
	);
	$classes_footer = implode( ' ', array_filter( $classes_footer ) );
	?>
	<footer class="<?php echo esc_attr( $classes_footer ); ?>">
		<div class="pen_actions">
	<?php
	echo $post_footer; /* phpcs:ignore */
	?>
		</div>
	<?php
	echo $entry_meta; /* phpcs:ignore */
	?>
	</footer><!-- .pen_content_footer -->
	<?php
}
