<?php
/**
 * Blog post hero background — same enable/type/color/gradient pattern as
 * the per-product background fields (includes/shop/product-single.php),
 * just scoped to the 'post' post type instead.
 *
 * @package Base Theme
 */

defined( 'ABSPATH' ) || exit;

function myshop_register_post_hero_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'      => 'group_myshop_post_hero',
			'title'    => __( 'Post Hero', 'base-theme' ),
			'fields'   => array(
				myshop_f( 'post_hero_bg_enabled', __( 'Add custom background for this post\'s hero?', 'base-theme' ), 'true_false', array(
					'ui'           => 1,
					'instructions' => __( 'Leave off to use the default dark background.', 'base-theme' ),
				) ),
				myshop_f( 'post_hero_bg_type', __( 'Background type', 'base-theme' ), 'button_group', array(
					'choices'           => array(
						'color'    => __( 'Pick a color', 'base-theme' ),
						'gradient' => __( 'Custom CSS / gradient', 'base-theme' ),
					),
					'default_value'     => 'color',
					'conditional_logic' => array(
						array(
							array( 'field' => 'field_ms_post_hero_bg_enabled', 'operator' => '==', 'value' => '1' ),
						),
					),
				) ),
				myshop_f( 'post_hero_bg_color', __( 'Background color', 'base-theme' ), 'color_picker', array(
					'conditional_logic' => array(
						array(
							array( 'field' => 'field_ms_post_hero_bg_enabled', 'operator' => '==', 'value' => '1' ),
							array( 'field' => 'field_ms_post_hero_bg_type', 'operator' => '==', 'value' => 'color' ),
						),
					),
				) ),
				myshop_f( 'post_hero_bg_css', __( 'Linear gradient / custom CSS', 'base-theme' ), 'text', array(
					'placeholder'       => 'linear-gradient(135deg, #ff5f6d, #6a11cb)',
					'instructions'      => __( 'Paste any valid CSS `background` value — angle, more than two colors, a radial gradient, anything CSS accepts.', 'base-theme' ),
					'conditional_logic' => array(
						array(
							array( 'field' => 'field_ms_post_hero_bg_enabled', 'operator' => '==', 'value' => '1' ),
							array( 'field' => 'field_ms_post_hero_bg_type', 'operator' => '==', 'value' => 'gradient' ),
						),
					),
				) ),
			),
			'location' => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'post',
					),
				),
			),
			'position' => 'normal',
			'active'   => true,
		)
	);
}
add_action( 'acf/init', 'myshop_register_post_hero_fields' );

/**
 * CSS `background` value for a post's hero — '' falls back to the
 * default gradient already baked into the .single-hero CSS.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function myshop_post_hero_background( $post_id ) {
	if ( ! function_exists( 'get_field' ) || ! get_field( 'post_hero_bg_enabled', $post_id ) ) {
		return '';
	}

	if ( 'gradient' === get_field( 'post_hero_bg_type', $post_id ) ) {
		return trim( wp_strip_all_tags( (string) get_field( 'post_hero_bg_css', $post_id ) ) );
	}

	return function_exists( 'myshop_sanitize_css_color' ) ? myshop_sanitize_css_color( get_field( 'post_hero_bg_color', $post_id ) ) : '';
}
