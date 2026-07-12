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
				myshop_f( 'ps_quote', __( 'Editorial quote', 'base-theme' ), 'textarea', array( 'rows' => 2 ) ),
				myshop_f( 'ps_stat1_value', __( 'Stat 1 — value (e.g. 2024)', 'base-theme' ), 'text', $half ),
				myshop_f( 'ps_stat1_label', __( 'Stat 1 — label (e.g. Inception)', 'base-theme' ), 'text', $half ),
				myshop_f( 'ps_stat2_value', __( 'Stat 2 — value (e.g. 99.9%)', 'base-theme' ), 'text', $half ),
				myshop_f( 'ps_stat2_label', __( 'Stat 2 — label (e.g. Purity)', 'base-theme' ), 'text', $half ),
				myshop_f( 'ps_a_eyebrow', __( 'Section A — eyebrow (01 — Core principles)', 'base-theme' ), 'text' ),
				myshop_f( 'ps_a_title', __( 'Section A — title', 'base-theme' ), 'text', $half ),
				myshop_f( 'ps_a_text', __( 'Section A — text', 'base-theme' ), 'textarea', array( 'rows' => 3 ) + $half ),
				myshop_f( 'ps_b_eyebrow', __( 'Section B — eyebrow (Technical story)', 'base-theme' ), 'text' ),
				myshop_f( 'ps_b_title', __( 'Section B — title', 'base-theme' ), 'text', $half ),
				myshop_f( 'ps_b_text', __( 'Section B — text', 'base-theme' ), 'textarea', array( 'rows' => 3 ) + $half ),
				myshop_f(
					'ps_specs',
					__( 'Specifications (empty = product attributes + dimensions)', 'base-theme' ),
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
