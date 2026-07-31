<?php
/**
 * Global Settings → Header Settings → the "Popular right now" chips inside
 * the search overlay. Fields live in the Header Settings tab, right after
 * Header Navigation Content (acf-json/group_683a451131cc8.json,
 * field_ms_search_suggest_label / field_ms_search_suggest_terms) — not
 * registered here, so there's no tab of their own.
 * One term per line; leave the box empty to hide the whole block.
 *
 * @package Base Theme
 */

defined( 'ABSPATH' ) || exit;

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
 * Heading above the chips — empty string hides the heading.
 *
 * @return string
 */
function myshop_search_suggest_label() {
	$label = function_exists( 'get_field' ) ? (string) get_field( 'search_suggest_label', 'option' ) : '';

	return trim( $label );
}

/**
 * wp_ajax: live results for the search overlay as the customer types —
 * first 4 matching products plus the total match count, so the overlay
 * can show a "View all N results" link to the real search results page.
 */
function myshop_live_search() {
	$term = isset( $_GET['term'] ) ? sanitize_text_field( wp_unslash( $_GET['term'] ) ) : '';

	if ( '' === $term || ! function_exists( 'wc_get_product' ) ) {
		wp_send_json(
			array(
				'products' => array(),
				'total'    => 0,
			)
		);
	}

	$query = new WP_Query(
		array(
			's'                   => $term,
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'posts_per_page'      => 4,
			'ignore_sticky_posts' => true,
		)
	);

	$products = array();
	foreach ( $query->posts as $post ) {
		$product = wc_get_product( $post->ID );
		if ( ! $product ) {
			continue;
		}

		$image_id = $product->get_image_id();
		$image    = $image_id ? wp_get_attachment_image_url( $image_id, 'thumbnail' ) : wc_placeholder_img_src( 'thumbnail' );

		$products[] = array(
			'title'     => esc_html( $product->get_name() ),
			'permalink' => esc_url( get_permalink( $product->get_id() ) ),
			'image'     => esc_url( $image ),
			'price'     => wp_kses_post( $product->get_price_html() ),
		);
	}

	wp_send_json(
		array(
			'products' => $products,
			'total'    => (int) $query->found_posts,
			'viewAll'  => esc_url( add_query_arg( array( 's' => rawurlencode( $term ), 'post_type' => 'product' ), home_url( '/' ) ) ),
		)
	);
}
add_action( 'wp_ajax_myshop_live_search', 'myshop_live_search' );
add_action( 'wp_ajax_nopriv_myshop_live_search', 'myshop_live_search' );
