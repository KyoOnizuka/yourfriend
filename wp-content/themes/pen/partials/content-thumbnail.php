<?php
/**
 * Template part for displaying the content featured image.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Pen
 */

if ( ! post_password_required() && ! is_attachment() && has_post_thumbnail() ) {

	$content_id = pen_post_id();

	$pen_is_singular = pen_is_singular();

	$view = $pen_is_singular ? 'content' : 'list';

	$thumbnail         = '';
	$thumbnail_display = get_post_meta( $content_id, 'pen_' . $view . '_thumbnail_display_override', true );
	if ( ! $thumbnail_display || 'default' === $thumbnail_display ) {
		$thumbnail_display = pen_option_get( $view . '_thumbnail_display' );
	}
	if ( $thumbnail_display && 'no' !== $thumbnail_display ) {
		$thumbnail = get_the_post_thumbnail( $content_id, pen_content_thumbnail_size( $view, $content_id ) );
	}

	if ( $thumbnail ) {
		$classes = pen_thumbnail_classes( $view, $content_id );
		if ( $pen_is_singular ) {
			?>
		<div class="<?php echo esc_attr( $classes ); ?>">
			<?php
			echo $thumbnail; /* phpcs:ignore */
			?>
		</div><!-- .post-thumbnail -->
			<?php
		} else {
			?>
		<a class="<?php echo esc_attr( $classes ); ?>" href="<?php the_permalink( $content_id ); ?>" aria-hidden="true" tabindex="-1">
			<?php
			echo $thumbnail; /* phpcs:ignore */
			?>
		</a>
			<?php
		}
	}
}
