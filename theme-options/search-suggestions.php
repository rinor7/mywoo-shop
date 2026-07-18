<?php
/**
 * Global Settings → Search Suggestions
 *
 * Own field group on the Global Settings options page for the
 * "Popular right now" chips inside the search overlay.
 * One term per line; leave the box empty to hide the whole block.
 *
 * @package Base Theme
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'acf/init',
	function () {
		if ( ! function_exists( 'acf_add_local_field' ) ) {
			return;
		}

		// Appended to the existing "Global Settings" group (acf-json) so they
		// show as a third tab there, next to Header Settings.
		$parent = 'group_683a451131cc8';

		acf_add_local_field(
			array(
				'key'       => 'field_ms_tab_search_suggest',
				'parent'    => $parent,
				'label'     => __( 'Search Suggestions', 'base-theme' ),
				'type'      => 'tab',
				'placement' => 'top',
			)
		);

		acf_add_local_field(
			array(
				'key'          => 'field_ms_search_suggest_label',
				'parent'       => $parent,
				'name'         => 'search_suggest_label',
				'label'        => __( 'Heading', 'base-theme' ),
				'type'         => 'text',
				'placeholder'  => __( 'Popular right now', 'base-theme' ),
				'instructions' => __( 'Shown above the quick-search chips in the search overlay.', 'base-theme' ),
			)
		);

		acf_add_local_field(
			array(
				'key'          => 'field_ms_search_suggest_terms',
				'parent'       => $parent,
				'name'         => 'search_suggest_terms',
				'label'        => __( 'Search terms', 'base-theme' ),
				'type'         => 'textarea',
				'rows'         => 6,
				'instructions' => __( 'One per line. Each becomes a chip that searches your products for that phrase. Leave empty to hide the block.', 'base-theme' ),
			)
		);
	}
);

/**
 * The configured suggestion terms as a clean array (may be empty).
 *
 * @return string[]
 */
function myshop_search_suggest_terms() {
	if ( ! function_exists( 'get_field' ) ) {
		return array();
	}

	$raw   = (string) get_field( 'search_suggest_terms', 'option' );
	$terms = array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', $raw ) ) );

	return array_slice( array_values( $terms ), 0, 8 );
}

/**
 * Heading above the chips, with the built-in default.
 *
 * @return string
 */
function myshop_search_suggest_label() {
	$label = function_exists( 'get_field' ) ? (string) get_field( 'search_suggest_label', 'option' ) : '';

	return '' !== trim( $label ) ? $label : __( 'Popular right now', 'base-theme' );
}
