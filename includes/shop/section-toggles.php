<?php
/**
 * Frontpage section toggles.
 *
 * Registers an ACF/SCF field group of on-off switches — one per homepage
 * section — against the Home page. Registered in PHP rather than acf-json so
 * it is live immediately with no "Sync" step, and travels with the theme.
 *
 * @package Base Theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * The homepage sections, in render order.
 *
 * Keys map to both the ACF field name (section_{key}) and the template part
 * (template-parts/home/{part}), so adding a section means adding one line here.
 *
 * @return array key => array( label, part )
 */
function myshop_home_sections() {
	return array(
		'hero'         => array( __( 'Hero slider', 'base-theme' ), 'hero' ),
		'usp'          => array( __( 'Trust bar', 'base-theme' ), 'usp' ),
		'new_arrivals' => array( __( 'New arrivals', 'base-theme' ), 'new-arrivals' ),
		'categories'   => array( __( 'Shop by category', 'base-theme' ), 'categories' ),
		'promo'        => array( __( 'Promo banners', 'base-theme' ), 'promo' ),
		'deal'         => array( __( 'Deal of the week', 'base-theme' ), 'deal' ),
		'bestsellers'  => array( __( 'Product tabs', 'base-theme' ), 'bestsellers' ),
		'brands'       => array( __( 'Makers marquee', 'base-theme' ), 'brands' ),
		'testimonials' => array( __( 'Reviews', 'base-theme' ), 'testimonials' ),
		'lookbook'     => array( __( 'Shop the look', 'base-theme' ), 'lookbook' ),
		'journal'      => array( __( 'Journal', 'base-theme' ), 'journal' ),
		'newsletter'   => array( __( 'Newsletter', 'base-theme' ), 'newsletter' ),
	);
}

/**
 * Is a section switched on?
 *
 * An unset value counts as ON. Without that, a homepage whose toggles have
 * never been saved (or a site with ACF disabled) would render empty.
 *
 * @param string   $key     Section key.
 * @param int|null $post_id Page to read from. Defaults to the current post.
 * @return bool
 */
function myshop_section_on( $key, $post_id = null ) {
	if ( ! function_exists( 'get_field' ) ) {
		return true;
	}

	$post_id = $post_id ? $post_id : get_the_ID();
	$value   = get_field( 'section_' . $key, $post_id );

	if ( null === $value || '' === $value ) {
		return true;
	}

	return (bool) $value;
}

/**
 * Register the field group.
 */
function myshop_register_section_toggles() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$fields = array();

	foreach ( myshop_home_sections() as $key => $section ) {
		$fields[] = array(
			'key'           => 'field_myshop_section_' . $key,
			'label'         => $section[0],
			'name'          => 'section_' . $key,
			'type'          => 'true_false',
			'default_value' => 1,
			'ui'            => 1,
			'ui_on_text'    => __( 'Shown', 'base-theme' ),
			'ui_off_text'   => __( 'Hidden', 'base-theme' ),
			'wrapper'       => array( 'width' => '33' ),
		);
	}

	acf_add_local_field_group(
		array(
			'key'         => 'group_myshop_home_sections',
			'title'       => __( 'Frontpage Sections', 'base-theme' ),
			'description' => __( 'Switch each homepage section on or off.', 'base-theme' ),
			'fields'      => $fields,
			// Either rule matches: the Home template, or whatever page is set as
			// the static front page.
			'location'    => array(
				array(
					array(
						'param'    => 'page_template',
						'operator' => '==',
						'value'    => 'frontpage.php',
					),
				),
				array(
					array(
						'param'    => 'page_type',
						'operator' => '==',
						'value'    => 'front_page',
					),
				),
			),
			'menu_order'  => 0,
			'position'    => 'normal',
			'style'       => 'default',
			'active'      => true,
		)
	);
}
add_action( 'acf/init', 'myshop_register_section_toggles' );
