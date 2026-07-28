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
		myshop_f( 'cat_btn_enabled', __( 'Show "All categories" button', 'base-theme' ), 'true_false', array(
			'default_value' => 1,
			'ui'            => 1,
			'ui_on_text'    => __( 'Shown', 'base-theme' ),
			'ui_off_text'   => __( 'Hidden', 'base-theme' ),
		) + $third ),
		myshop_f( 'cat_btn_link', __( 'Button (empty = "All categories" → the shop page)', 'base-theme' ), 'link', $half ),
		myshop_f(
			'cat_picks',
			__( 'Pick specific categories, in order (empty = automatic top categories by product count)', 'base-theme' ),
			'repeater',
			array(
				'layout'       => 'block',
				'button_label' => __( 'Add category', 'base-theme' ),
				'max'          => 4,
				'sub_fields'   => array(
					myshop_f( 'cp_term', __( 'Category', 'base-theme' ), 'taxonomy', array(
						'taxonomy'      => 'product_cat',
						'field_type'    => 'select',
						'return_format' => 'id',
						'allow_null'    => 1,
						'multiple'      => 0,
					) + $half ),
					myshop_f( 'cp_icon', __( 'Custom icon/image', 'base-theme' ), 'image', array(
						'return_format' => 'url',
						'preview_size'  => 'thumbnail',
						'instructions'  => __( 'Overrides the category\'s own thumbnail (Products → Categories) when set.', 'base-theme' ),
					) + $half ),
					myshop_f( 'cp_bg_enabled', __( 'Custom background for this tile?', 'base-theme' ), 'true_false', array( 'ui' => 1 ) + $third ),
					myshop_f( 'cp_bg_type', __( 'Background type', 'base-theme' ), 'button_group', array(
						'choices'           => array(
							'color'    => __( 'Solid color', 'base-theme' ),
							'gradient' => __( 'Gradient / custom CSS', 'base-theme' ),
						),
						'default_value'     => 'color',
						'conditional_logic' => array( array( array( 'field' => 'field_ms_cp_bg_enabled', 'operator' => '==', 'value' => '1' ) ) ),
					) + $third ),
					myshop_f( 'cp_bg_color', __( 'Color', 'base-theme' ), 'color_picker', array(
						'conditional_logic' => array(
							array(
								array( 'field' => 'field_ms_cp_bg_enabled', 'operator' => '==', 'value' => '1' ),
								array( 'field' => 'field_ms_cp_bg_type', 'operator' => '==', 'value' => 'color' ),
							),
						),
					) + $third ),
					myshop_f( 'cp_bg_css', __( 'Gradient / custom CSS', 'base-theme' ), 'text', array(
						'placeholder'       => 'linear-gradient(135deg, #ff5f6d, #6a11cb)',
						'conditional_logic' => array(
							array(
								array( 'field' => 'field_ms_cp_bg_enabled', 'operator' => '==', 'value' => '1' ),
								array( 'field' => 'field_ms_cp_bg_type', 'operator' => '==', 'value' => 'gradient' ),
							),
						),
					) ),
				),
			)
		),

		myshop_tab( 'New arrivals' ),
		myshop_f( 'na_eyebrow', __( 'Eyebrow', 'base-theme' ), 'text', $third ),
		myshop_f( 'na_title', __( 'Title', 'base-theme' ), 'text', $third ),
		myshop_f( 'na_sub', __( 'Subtitle', 'base-theme' ), 'text', $third ),
		myshop_f( 'na_btn_enabled', __( 'Show "Shop all new in" button', 'base-theme' ), 'true_false', array(
			'default_value' => 1,
			'ui'            => 1,
			'ui_on_text'    => __( 'Shown', 'base-theme' ),
			'ui_off_text'   => __( 'Hidden', 'base-theme' ),
		) + $third ),
		myshop_f( 'na_btn_link', __( 'Button (empty = "Shop all new in" → the shop page)', 'base-theme' ), 'link', $half ),
		myshop_f(
			'na_products',
			__( 'Custom products (empty = automatically show the latest arrivals; add 4+ for a full carousel)', 'base-theme' ),
			'repeater',
			array(
				'layout'       => 'table',
				'button_label' => __( 'Add product', 'base-theme' ),
				'sub_fields'   => array(
					myshop_f( 'na_product', __( 'Product', 'base-theme' ), 'post_object', array(
						'post_type'     => array( 'product' ),
						'return_format' => 'id',
						'allow_null'    => 1,
					) ),
				),
			)
		),
		myshop_f( 'na_card_bg_type', __( 'Product box background type (empty = sitewide default)', 'base-theme' ), 'button_group', array(
			'choices' => array(
				'color'    => __( 'Solid color', 'base-theme' ),
				'gradient' => __( 'Gradient / custom CSS', 'base-theme' ),
			),
		) + $third ),
		myshop_f( 'na_card_bg_color', __( 'Color', 'base-theme' ), 'color_picker', array(
			'conditional_logic' => array( array( array( 'field' => 'field_ms_na_card_bg_type', 'operator' => '==', 'value' => 'color' ) ) ),
		) + $third ),
		myshop_f( 'na_card_bg_css', __( 'Gradient / custom CSS', 'base-theme' ), 'text', array(
			'placeholder'       => 'linear-gradient(150deg, #f4f2ed, #e7e3db)',
			'conditional_logic' => array( array( array( 'field' => 'field_ms_na_card_bg_type', 'operator' => '==', 'value' => 'gradient' ) ) ),
		) + $third ),

		/* ---- Promo ----
		   One tab per card — with both cards' fields flattened into a single
		   list, it was hard to tell where the large card's fields ended and
		   the small card's began, especially since bg_color/bg_css toggle
		   in and out of view and shift the row layout as you switch type. */
		myshop_tab( 'Promo — Large card' ),
		myshop_f( 'promo1_eyebrow', __( 'Eyebrow', 'base-theme' ), 'text', $third ),
		myshop_f( 'promo1_title', __( 'Title', 'base-theme' ), 'text', $third ),
		myshop_f( 'promo1_text', __( 'Text', 'base-theme' ), 'text', $third ),
		myshop_f( 'promo1_btn', __( 'Button label', 'base-theme' ), 'text', $half ),
		myshop_f( 'promo1_url', __( 'URL', 'base-theme' ), 'text', $half ),
		myshop_f( 'promo1_image', __( 'Image', 'base-theme' ), 'image', array( 'return_format' => 'url', 'preview_size' => 'medium' ) ),
		myshop_f( 'promo1_bg_type', __( 'Background type', 'base-theme' ), 'button_group', array(
			'choices'       => array(
				'color'    => __( 'Solid color', 'base-theme' ),
				'gradient' => __( 'Gradient / custom CSS', 'base-theme' ),
			),
			'default_value' => 'gradient',
		) + $third ),
		myshop_f( 'promo1_bg_color', __( 'Color', 'base-theme' ), 'color_picker', array(
			'conditional_logic' => array( array( array( 'field' => 'field_ms_promo1_bg_type', 'operator' => '==', 'value' => 'color' ) ) ),
		) + $third ),
		myshop_f( 'promo1_bg_css', __( 'Gradient / custom CSS (default: linear-gradient(130deg, #2A2F36, #12151A))', 'base-theme' ), 'text', array(
			'placeholder'       => 'linear-gradient(130deg, #2A2F36, #12151A)',
			'conditional_logic' => array( array( array( 'field' => 'field_ms_promo1_bg_type', 'operator' => '==', 'value' => 'gradient' ) ) ),
		) + $third ),

		myshop_tab( 'Promo — Small card' ),
		myshop_f( 'promo2_eyebrow', __( 'Eyebrow', 'base-theme' ), 'text', $third ),
		myshop_f( 'promo2_title', __( 'Title', 'base-theme' ), 'text', $third ),
		myshop_f( 'promo2_text', __( 'Text', 'base-theme' ), 'text', $third ),
		myshop_f( 'promo2_btn', __( 'Link label', 'base-theme' ), 'text', $half ),
		myshop_f( 'promo2_url', __( 'URL', 'base-theme' ), 'text', $half ),
		myshop_f( 'promo2_image', __( 'Image', 'base-theme' ), 'image', array( 'return_format' => 'url', 'preview_size' => 'medium' ) ),
		myshop_f( 'promo2_bg_type', __( 'Background type', 'base-theme' ), 'button_group', array(
			'choices'       => array(
				'color'    => __( 'Solid color', 'base-theme' ),
				'gradient' => __( 'Gradient / custom CSS', 'base-theme' ),
			),
			'default_value' => 'gradient',
		) + $third ),
		myshop_f( 'promo2_bg_color', __( 'Color', 'base-theme' ), 'color_picker', array(
			'conditional_logic' => array( array( array( 'field' => 'field_ms_promo2_bg_type', 'operator' => '==', 'value' => 'color' ) ) ),
		) + $third ),
		myshop_f( 'promo2_bg_css', __( 'Gradient / custom CSS (default: linear-gradient(130deg, #F0E7DA, #DCC7AB))', 'base-theme' ), 'text', array(
			'placeholder'       => 'linear-gradient(130deg, #F0E7DA, #DCC7AB)',
			'conditional_logic' => array( array( array( 'field' => 'field_ms_promo2_bg_type', 'operator' => '==', 'value' => 'gradient' ) ) ),
		) + $third ),

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
		myshop_f( 'deal_stock_text', __( 'Stock line', 'base-theme' ), 'text', array(
			'default_value' => __( 'Sold: {sold} of {total} — going fast', 'base-theme' ),
			'instructions'  => __( '{sold} and {total} are replaced with the numbers above. Leave empty to hide the line (the progress bar still shows).', 'base-theme' ),
		) ),
		myshop_f( 'deal_secondary_cta', __( 'Second button (empty = "View details" → the product page)', 'base-theme' ), 'link', array(
			'instructions' => __( 'Both the label and the URL come from this one field. Use it to point somewhere other than the product\'s own page — the shop, a category, anywhere.', 'base-theme' ),
		) ),
		myshop_f( 'deal_bg_type', __( 'Photo box — background type', 'base-theme' ), 'button_group', array(
			'choices'       => array(
				'color'    => __( 'Solid color', 'base-theme' ),
				'gradient' => __( 'Gradient / custom CSS', 'base-theme' ),
			),
			'default_value' => 'color',
			'instructions'  => __( 'The product photo is shown in full (not cropped), so this fills the space around it. Leave empty for plain white.', 'base-theme' ),
		) + $third ),
		myshop_f( 'deal_bg_color', __( 'Photo box — color', 'base-theme' ), 'color_picker', array(
			'conditional_logic' => array( array( array( 'field' => 'field_ms_deal_bg_type', 'operator' => '==', 'value' => 'color' ) ) ),
		) + $third ),
		myshop_f( 'deal_bg_css', __( 'Photo box — gradient / custom CSS', 'base-theme' ), 'text', array(
			'placeholder'       => 'linear-gradient(150deg, #f4f2ed, #e7e3db)',
			'conditional_logic' => array( array( array( 'field' => 'field_ms_deal_bg_type', 'operator' => '==', 'value' => 'gradient' ) ) ),
		) + $third ),

		/* ---- Product tabs ---- */
		myshop_tab( 'Product tabs' ),
		myshop_f( 'tabs_eyebrow', __( 'Eyebrow', 'base-theme' ), 'text', $half ),
		myshop_f( 'tabs_title', __( 'Title', 'base-theme' ), 'text', $half ),
		myshop_f( 'tabs_card_bg_type', __( 'Product box background type (empty = sitewide default)', 'base-theme' ), 'button_group', array(
			'choices' => array(
				'color'    => __( 'Solid color', 'base-theme' ),
				'gradient' => __( 'Gradient / custom CSS', 'base-theme' ),
			),
		) + $third ),
		myshop_f( 'tabs_card_bg_color', __( 'Color', 'base-theme' ), 'color_picker', array(
			'conditional_logic' => array( array( array( 'field' => 'field_ms_tabs_card_bg_type', 'operator' => '==', 'value' => 'color' ) ) ),
		) + $third ),
		myshop_f( 'tabs_card_bg_css', __( 'Gradient / custom CSS', 'base-theme' ), 'text', array(
			'placeholder'       => 'linear-gradient(150deg, #f4f2ed, #e7e3db)',
			'conditional_logic' => array( array( array( 'field' => 'field_ms_tabs_card_bg_type', 'operator' => '==', 'value' => 'gradient' ) ) ),
		) + $third ),

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
