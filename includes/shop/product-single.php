<?php
/**
 * Single product page support.
 *
 * ACF "Product Story" fields for the editorial sections, spec-table helpers,
 * and the gallery data used by the custom PDP template.
 *
 * @package Base Theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Editorial fields per product — every field optional; sections that have no
 * content simply do not render.
 */
function myshop_register_product_story_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$half = array( 'wrapper' => array( 'width' => '50' ) );

	acf_add_local_field_group(
		array(
			'key'      => 'group_myshop_product_story',
			'title'    => __( 'Product Story', 'base-theme' ),
			'fields'   => array(
				myshop_f( 'ps_bg_enabled', __( 'Add custom background for this product?', 'base-theme' ), 'true_false', array(
					'ui'           => 1,
					'instructions' => __( 'Fills the gallery panel behind the product photo — meant for images with a transparent background. Leave off to use the default background.', 'base-theme' ),
				) ),
				myshop_f( 'ps_bg_type', __( 'Background type', 'base-theme' ), 'button_group', array(
					'choices'           => array(
						'color'    => __( 'Pick a color', 'base-theme' ),
						'gradient' => __( 'Custom CSS / gradient', 'base-theme' ),
					),
					'default_value'     => 'color',
					'conditional_logic' => array(
						array(
							array( 'field' => 'field_ms_ps_bg_enabled', 'operator' => '==', 'value' => '1' ),
						),
					),
				) ),
				myshop_f( 'ps_bg_color_1', __( 'Background color', 'base-theme' ), 'color_picker', array(
					'conditional_logic' => array(
						array(
							array( 'field' => 'field_ms_ps_bg_enabled', 'operator' => '==', 'value' => '1' ),
							array( 'field' => 'field_ms_ps_bg_type', 'operator' => '==', 'value' => 'color' ),
						),
					),
				) ),
				myshop_f( 'ps_bg_css', __( 'Linear gradient / custom CSS', 'base-theme' ), 'text', array(
					'placeholder'       => 'linear-gradient(135deg, #ff5f6d, #6a11cb)',
					'instructions'      => __( 'Paste any valid CSS `background` value — angle, more than two colors, a radial gradient, anything CSS accepts.', 'base-theme' ),
					'conditional_logic' => array(
						array(
							array( 'field' => 'field_ms_ps_bg_enabled', 'operator' => '==', 'value' => '1' ),
							array( 'field' => 'field_ms_ps_bg_type', 'operator' => '==', 'value' => 'gradient' ),
						),
					),
				) ),
				myshop_f( 'ps_quote_eyebrow', __( 'Editorial quote — eyebrow', 'base-theme' ), 'text', array( 'default_value' => __( 'Crafted for the modern professional', 'base-theme' ), 'instructions' => __( 'Small label above the editorial quote. Leave empty to hide it.', 'base-theme' ) ) ),
				myshop_f( 'ps_quote', __( 'Editorial quote', 'base-theme' ), 'textarea', array( 'rows' => 2 ) ),
				myshop_f( 'ps_stat1_value', __( 'Stat 1 — value (e.g. 2024)', 'base-theme' ), 'text', $half ),
				myshop_f( 'ps_stat1_label', __( 'Stat 1 — label (e.g. Inception)', 'base-theme' ), 'text', $half ),
				myshop_f( 'ps_stat2_value', __( 'Stat 2 — value (e.g. 99.9%)', 'base-theme' ), 'text', $half ),
				myshop_f( 'ps_stat2_label', __( 'Stat 2 — label (e.g. Purity)', 'base-theme' ), 'text', $half ),
				myshop_f( 'ps_a_eyebrow', __( 'Section A — eyebrow (01 — Core principles)', 'base-theme' ), 'text' ),
				myshop_f( 'ps_a_title', __( 'Section A — title ( additional info/section )', 'base-theme' ), 'text', $half ),
				myshop_f( 'ps_a_text', __( 'Section A — text', 'base-theme' ), 'textarea', array( 'rows' => 3 ) + $half ),
				myshop_f( 'ps_b_eyebrow', __( 'Section B — eyebrow (Technical story)', 'base-theme' ), 'text' ),
				myshop_f( 'ps_b_title', __( 'Section B — title ( additional info/section )', 'base-theme' ), 'text', $half ),
				myshop_f( 'ps_b_text', __( 'Section B — text', 'base-theme' ), 'textarea', array( 'rows' => 3 ) + $half ),
				myshop_f(
					'ps_perks',
					__( 'Product page perks (empty = the global list from Global Settings → Single Product / if you fill it will overwritte, icons included)', 'base-theme' ),
					'repeater',
					array(
						'layout'       => 'table',
						'button_label' => __( 'Add perk', 'base-theme' ),
						'max'          => 4,
						'sub_fields'   => array(
							myshop_f( 'perk_icon', __( 'Icon', 'base-theme' ), 'image', array(
								'return_format' => 'url',
								'preview_size'  => 'thumbnail',
								'instructions'  => __( 'Upload a small square icon (PNG/WebP work best). Leave empty to use the default rotating icon.', 'base-theme' ),
							) ),
							myshop_f( 'perk_text', __( 'Text', 'base-theme' ), 'text' ),
						),
					)
				),
				myshop_f(
					'ps_specs',
					__( 'Specifications (empty = product attributes + dimensions / if you fill it will overwritte)', 'base-theme' ),
					'repeater',
					array(
						'layout'       => 'table',
						'button_label' => __( 'Add row', 'base-theme' ),
						'sub_fields'   => array(
							myshop_f( 'spec_label', __( 'Label', 'base-theme' ), 'text' ),
							myshop_f( 'spec_value', __( 'Value', 'base-theme' ), 'text' ),
						),
					)
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'product',
					),
				),
			),
			'position' => 'normal',
			'active'   => true,
		)
	);
}
add_action( 'acf/init', 'myshop_register_product_story_fields' );

/**
 * Spec rows: ACF repeater first, otherwise product attributes plus
 * weight/dimensions from shipping data.
 *
 * @param WC_Product $product Product.
 * @return array[] [label, value]
 */
function myshop_product_specs( $product ) {
	$rows = array();

	if ( function_exists( 'get_field' ) ) {
		$acf = get_field( 'ps_specs', $product->get_id() );
		if ( $acf ) {
			foreach ( $acf as $row ) {
				if ( '' !== $row['spec_label'] && '' !== $row['spec_value'] ) {
					$rows[] = array( $row['spec_label'], $row['spec_value'] );
				}
			}
		}
	}

	if ( ! empty( $rows ) ) {
		return $rows;
	}

	foreach ( $product->get_attributes() as $attribute ) {
		if ( ! $attribute->get_visible() ) {
			continue;
		}

		$values = array();
		if ( $attribute->is_taxonomy() ) {
			$terms = wp_get_post_terms( $product->get_id(), $attribute->get_name(), array( 'fields' => 'names' ) );
			if ( ! is_wp_error( $terms ) ) {
				$values = $terms;
			}
		} else {
			$values = $attribute->get_options();
		}

		if ( $values ) {
			$rows[] = array( wc_attribute_label( $attribute->get_name() ), implode( ', ', $values ) );
		}
	}

	if ( $product->has_weight() ) {
		$rows[] = array( __( 'Weight', 'base-theme' ), wc_format_weight( $product->get_weight() ) );
	}

	if ( $product->has_dimensions() ) {
		$rows[] = array( __( 'Dimensions', 'base-theme' ), wc_format_dimensions( $product->get_dimensions( false ) ) );
	}

	if ( $product->get_sku() ) {
		$rows[] = array( __( 'SKU', 'base-theme' ), $product->get_sku() );
	}

	return $rows;
}

/**
 * All gallery image ids for the PDP slider (featured first, no duplicates).
 */
function myshop_product_gallery_ids( $product ) {
	$ids = array_merge(
		array( $product->get_image_id() ),
		$product->get_gallery_image_ids()
	);

	return array_values( array_unique( array_filter( array_map( 'intval', $ids ) ) ) );
}

/**
 * Only lets through what a color_picker field can actually produce
 * (hex, or rgb/rgba if opacity is ever enabled) before it reaches an
 * inline style attribute.
 */
function myshop_sanitize_css_color( $value ) {
	$value = trim( (string) $value );

	if ( preg_match( '/^#[0-9a-f]{3,8}$/i', $value ) || preg_match( '/^rgba?\([0-9.,%\s]+\)$/i', $value ) ) {
		return $value;
	}

	return '';
}

/**
 * Gallery panel background for products shot with a transparent
 * background. Checked in order:
 *  1. This product's own background (ACF "Product Story" fields
 *     ps_bg_enabled/ps_bg_type/ps_bg_color_1/ps_bg_css on the product
 *     edit screen) — always wins when set.
 *  2. The shop-wide default (Global Settings → Shop).
 *  3. '' — the CSS default (`--bone-2`) applies as-is.
 *
 * The fields live on the parent product post, but cart/order/wishlist
 * items carry the specific *variation* the customer picked — reading
 * get_id() there would look up the variation's own (empty) postmeta, so
 * variations resolve to their parent first.
 *
 * @param WC_Product $product Product (simple, variable, or variation).
 * @return string CSS `background` value, or '' for the default.
 */
function myshop_product_gallery_background( $product ) {
	if ( ! function_exists( 'get_field' ) ) {
		return '';
	}

	$post_id = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();

	if ( get_field( 'ps_bg_enabled', $post_id ) ) {
		$value = 'gradient' === get_field( 'ps_bg_type', $post_id )
			? trim( wp_strip_all_tags( (string) get_field( 'ps_bg_css', $post_id ) ) )
			: myshop_sanitize_css_color( get_field( 'ps_bg_color_1', $post_id ) );

		if ( $value ) {
			return $value;
		}
	}

	if ( function_exists( 'myshop_opt' ) && myshop_opt( 'shop_bg_enabled', 0 ) ) {
		$value = 'gradient' === myshop_opt( 'shop_bg_type', 'color' )
			? trim( wp_strip_all_tags( (string) myshop_opt( 'shop_bg_css', '' ) ) )
			: myshop_sanitize_css_color( myshop_opt( 'shop_bg_color', '' ) );

		if ( $value ) {
			return $value;
		}
	}

	return '';
}

/**
 * Inline `style` value for any thumbnail of this product — cart row,
 * checkout review, mini-cart drawer, order history, quick-view, product
 * cards, PDP gallery. `object-fit: contain` is bundled in so the photo's
 * transparent margins actually reveal the background instead of being
 * cropped away by whatever `object-fit: cover` the component normally uses.
 * Empty string (no attribute) when the product has no override.
 *
 * @param WC_Product $product Product.
 * @return string
 */
function myshop_product_thumb_bg_style( $product ) {
	$bg = $product ? myshop_product_gallery_background( $product ) : '';

	return $bg ? 'background: ' . $bg . '; object-fit: contain;' : '';
}

/**
 * "Complete the look": upsells, then related, normalised for pcards.
 */
function myshop_product_pairings( $product, $limit = 4 ) {
	$ids = $product->get_upsell_ids();

	if ( count( $ids ) < $limit ) {
		$ids = array_merge( $ids, wc_get_related_products( $product->get_id(), $limit * 2 ) );
	}

	$ids = array_values( array_unique( array_diff( array_map( 'intval', $ids ), array( $product->get_id() ) ) ) );

	$out = array();
	foreach ( $ids as $id ) {
		$related = wc_get_product( $id );
		if ( $related && 'publish' === $related->get_status() ) {
			$out[] = myshop_normalize_product( $related );
		}
		if ( count( $out ) >= $limit ) {
			break;
		}
	}

	return $out;
}
