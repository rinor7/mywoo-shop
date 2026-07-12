<?php
/**
 * Frontpage content fields.
 *
 * One field group, one tab per homepage section. Every field is optional:
 * templates fall back to the built-in copy through myshop_c(), so an empty
 * field never blanks a section — it just shows the default.
 *
 * @package Base Theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * The page the frontpage fields are stored on.
 */
function myshop_front_id() {
	$id = (int) get_option( 'page_on_front' );
	return $id ? $id : (int) get_the_ID();
}

/**
 * Frontpage content value with fallback.
 *
 * @param string $name    Field name.
 * @param mixed  $default Returned when the field is empty or ACF is inactive.
 * @return mixed
 */
function myshop_c( $name, $default = '' ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $default;
	}

	$value = get_field( $name, myshop_front_id() );

	if ( null === $value || '' === $value || array() === $value || false === $value ) {
		return $default;
	}

	return $value;
}

/**
 * Field definition shorthands.
 */
function myshop_f( $name, $label, $type = 'text', $extra = array() ) {
	return array_merge(
		array(
			'key'   => 'field_ms_' . $name,
			'name'  => $name,
			'label' => $label,
			'type'  => $type,
		),
		$extra
	);
}

function myshop_tab( $label ) {
	return array(
		'key'       => 'field_ms_tab_' . sanitize_key( $label ),
		'label'     => $label,
		'type'      => 'tab',
		'placement' => 'left',
	);
}

/**
 * Register the group.
 */
function myshop_register_frontpage_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$half  = array( 'wrapper' => array( 'width' => '50' ) );
	$third = array( 'wrapper' => array( 'width' => '33' ) );

	$fields = array(

		/* ---- Hero ---- */
		myshop_tab( 'Hero' ),
		myshop_f(
			'hero_slides',
			__( 'Slides (empty = demo slides)', 'base-theme' ),
			'repeater',
			array(
				'layout'       => 'block',
				'button_label' => __( 'Add slide', 'base-theme' ),
				'sub_fields'   => array(
					myshop_f( 'hs_eyebrow', __( 'Eyebrow', 'base-theme' ), 'text', $third ),
					myshop_f( 'hs_chip', __( 'Floating chip (e.g. From €185)', 'base-theme' ), 'text', $third ),
					myshop_f( 'hs_image', __( 'Image', 'base-theme' ), 'image', array( 'return_format' => 'url', 'preview_size' => 'medium' ) + $third ),
					myshop_f( 'hs_title', __( 'Title (line breaks kept)', 'base-theme' ), 'textarea', array( 'rows' => 2 ) + $half ),
					myshop_f( 'hs_text', __( 'Text', 'base-theme' ), 'textarea', array( 'rows' => 2 ) + $half ),
					myshop_f( 'hs_btn1_label', __( 'Button 1 label', 'base-theme' ), 'text', $third ),
					myshop_f( 'hs_btn1_url', __( 'Button 1 URL', 'base-theme' ), 'text', $third ),
					myshop_f( 'hs_color_a', __( 'Background start', 'base-theme' ), 'color_picker', array( 'default_value' => '#EFE8DC' ) + $third ),
					myshop_f( 'hs_btn2_label', __( 'Button 2 label', 'base-theme' ), 'text', $third ),
					myshop_f( 'hs_btn2_url', __( 'Button 2 URL', 'base-theme' ), 'text', $third ),
					myshop_f( 'hs_color_b', __( 'Background end', 'base-theme' ), 'color_picker', array( 'default_value' => '#DACBB4' ) + $third ),
				),
			)
		),

		/* ---- Trust bar ---- */
		myshop_tab( 'Trust bar' ),
		myshop_f(
			'usp_items',
			__( 'Items (empty = defaults)', 'base-theme' ),
			'repeater',
			array(
				'layout'       => 'table',
				'button_label' => __( 'Add item', 'base-theme' ),
				'sub_fields'   => array(
					myshop_f( 'usp_icon', __( 'Font Awesome icon (fa-truck-fast)', 'base-theme' ), 'text' ),
					myshop_f( 'usp_title', __( 'Title', 'base-theme' ), 'text' ),
					myshop_f( 'usp_text', __( 'Text', 'base-theme' ), 'text' ),
				),
			)
		),

		/* ---- Section headers ---- */
		myshop_tab( 'Categories' ),
		myshop_f( 'cat_eyebrow', __( 'Eyebrow', 'base-theme' ), 'text', $third ),
		myshop_f( 'cat_title', __( 'Title', 'base-theme' ), 'text', $third ),
		myshop_f( 'cat_sub', __( 'Subtitle', 'base-theme' ), 'text', $third ),

		myshop_tab( 'New arrivals' ),
		myshop_f( 'na_eyebrow', __( 'Eyebrow', 'base-theme' ), 'text', $third ),
		myshop_f( 'na_title', __( 'Title', 'base-theme' ), 'text', $third ),
		myshop_f( 'na_sub', __( 'Subtitle', 'base-theme' ), 'text', $third ),

		/* ---- Promo ---- */
		myshop_tab( 'Promo banners' ),
		myshop_f( 'promo1_eyebrow', __( 'Large card — eyebrow', 'base-theme' ), 'text', $third ),
		myshop_f( 'promo1_title', __( 'Large card — title', 'base-theme' ), 'text', $third ),
		myshop_f( 'promo1_text', __( 'Large card — text', 'base-theme' ), 'text', $third ),
		myshop_f( 'promo1_btn', __( 'Large card — button label', 'base-theme' ), 'text', $third ),
		myshop_f( 'promo1_url', __( 'Large card — URL', 'base-theme' ), 'text', $third ),
		myshop_f( 'promo1_image', __( 'Large card — image', 'base-theme' ), 'image', array( 'return_format' => 'url', 'preview_size' => 'medium' ) + $third ),
		myshop_f( 'promo2_eyebrow', __( 'Small card — eyebrow', 'base-theme' ), 'text', $third ),
		myshop_f( 'promo2_title', __( 'Small card — title', 'base-theme' ), 'text', $third ),
		myshop_f( 'promo2_text', __( 'Small card — text', 'base-theme' ), 'text', $third ),
		myshop_f( 'promo2_btn', __( 'Small card — link label', 'base-theme' ), 'text', $third ),
		myshop_f( 'promo2_url', __( 'Small card — URL', 'base-theme' ), 'text', $third ),
		myshop_f( 'promo2_image', __( 'Small card — image', 'base-theme' ), 'image', array( 'return_format' => 'url', 'preview_size' => 'medium' ) + $third ),

		/* ---- Deal ---- */
		myshop_tab( 'Deal of the week' ),
		myshop_f(
			'deal_product',
			__( 'Product (empty = newest sale product)', 'base-theme' ),
			'post_object',
			array(
				'post_type'     => array( 'product' ),
				'return_format' => 'id',
				'allow_null'    => 1,
				'wrapper'       => array( 'width' => '40' ),
			)
		),
		myshop_f( 'deal_ends', __( 'Ends (empty = next Sunday)', 'base-theme' ), 'date_time_picker', array( 'return_format' => 'Y-m-d H:i:s', 'wrapper' => array( 'width' => '24' ) ) ),
		myshop_f( 'deal_sold', __( 'Units sold', 'base-theme' ), 'number', array( 'default_value' => 32, 'wrapper' => array( 'width' => '18' ) ) ),
		myshop_f( 'deal_total', __( 'Units total', 'base-theme' ), 'number', array( 'default_value' => 50, 'wrapper' => array( 'width' => '18' ) ) ),

		/* ---- Product tabs ---- */
		myshop_tab( 'Product tabs' ),
		myshop_f( 'tabs_eyebrow', __( 'Eyebrow', 'base-theme' ), 'text', $half ),
		myshop_f( 'tabs_title', __( 'Title', 'base-theme' ), 'text', $half ),

		/* ---- Makers ---- */
		myshop_tab( 'Makers marquee' ),
		myshop_f( 'brands_label', __( 'Label above the marquee', 'base-theme' ), 'text', $half ),
		myshop_f( 'brands_names', __( 'Names — one per line (empty = defaults)', 'base-theme' ), 'textarea', array( 'rows' => 4 ) + $half ),

		/* ---- Reviews ---- */
		myshop_tab( 'Reviews' ),
		myshop_f( 'rev_eyebrow', __( 'Eyebrow', 'base-theme' ), 'text', $third ),
		myshop_f( 'rev_title', __( 'Title', 'base-theme' ), 'text', $third ),
		myshop_f( 'rev_sub', __( 'Subtitle', 'base-theme' ), 'text', $third ),
		myshop_f(
			'reviews',
			__( 'Reviews (empty = demo reviews)', 'base-theme' ),
			'repeater',
			array(
				'layout'       => 'block',
				'button_label' => __( 'Add review', 'base-theme' ),
				'sub_fields'   => array(
					myshop_f( 'rv_quote', __( 'Quote', 'base-theme' ), 'textarea', array( 'rows' => 2 ) ),
					myshop_f( 'rv_name', __( 'Name', 'base-theme' ), 'text', $third ),
					myshop_f( 'rv_product', __( 'Product', 'base-theme' ), 'text', $third ),
					myshop_f( 'rv_rating', __( 'Rating (0–5)', 'base-theme' ), 'number', array( 'default_value' => 5, 'min' => 0, 'max' => 5, 'step' => '0.5' ) + $third ),
				),
			)
		),

		/* ---- Lookbook ---- */
		myshop_tab( 'Shop the look' ),
		myshop_f( 'look_eyebrow', __( 'Eyebrow', 'base-theme' ), 'text', $third ),
		myshop_f( 'look_title', __( 'Title', 'base-theme' ), 'text', $third ),
		myshop_f( 'look_sub', __( 'Subtitle', 'base-theme' ), 'text', $third ),
		myshop_f(
			'look_tiles',
			__( 'Tiles (empty = demo tiles)', 'base-theme' ),
			'repeater',
			array(
				'layout'       => 'table',
				'button_label' => __( 'Add tile', 'base-theme' ),
				'sub_fields'   => array(
					myshop_f( 'lt_image', __( 'Image', 'base-theme' ), 'image', array( 'return_format' => 'url', 'preview_size' => 'thumbnail' ) ),
					myshop_f( 'lt_label', __( 'Label', 'base-theme' ), 'text' ),
					myshop_f( 'lt_url', __( 'URL', 'base-theme' ), 'text' ),
				),
			)
		),

		/* ---- Journal ---- */
		myshop_tab( 'Journal' ),
		myshop_f( 'j_eyebrow', __( 'Eyebrow', 'base-theme' ), 'text', $half ),
		myshop_f( 'j_title', __( 'Title', 'base-theme' ), 'text', $half ),

		/* ---- Newsletter ---- */
		myshop_tab( 'Newsletter' ),
		myshop_f( 'nl_eyebrow', __( 'Eyebrow', 'base-theme' ), 'text', $half ),
		myshop_f( 'nl_title', __( 'Title', 'base-theme' ), 'text', $half ),
		myshop_f( 'nl_text', __( 'Text', 'base-theme' ), 'textarea', array( 'rows' => 2 ) + $half ),
		myshop_f( 'nl_note', __( 'Small note under the form', 'base-theme' ), 'text', $half ),
	);

	acf_add_local_field_group(
		array(
			'key'        => 'group_myshop_home_content',
			'title'      => __( 'Frontpage Content', 'base-theme' ),
			'fields'     => $fields,
			'location'   => array(
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
			'menu_order' => 1,
			'position'   => 'normal',
			'active'     => true,
		)
	);
}
add_action( 'acf/init', 'myshop_register_frontpage_fields' );
