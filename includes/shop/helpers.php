<?php
/**
 * Shop data layer.
 *
 * Everything the storefront renders goes through myshop_get_products() and
 * myshop_get_categories(). Both return the same normalised shape whether the
 * data comes from real WooCommerce products or from the built-in demo set, so
 * templates never branch on which one is in play. Publish real products and the
 * demo content disappears on its own — no template edits.
 *
 * @package Base Theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * URL for a bundled placeholder graphic.
 */
function myshop_placeholder( $slug ) {
	return get_template_directory_uri() . '/assets/img/placeholders/' . $slug . '.svg';
}

/**
 * Shop page URL, with a safe fallback before WooCommerce pages exist.
 */
function myshop_shop_url() {
	if ( function_exists( 'wc_get_page_permalink' ) ) {
		$url = wc_get_page_permalink( 'shop' );
		if ( $url ) {
			return $url;
		}
	}
	return home_url( '/' );
}

/**
 * Format a price the way the store is configured to (currency, decimals,
 * separators) even for demo products.
 */
function myshop_price_html( $price, $regular = 0 ) {
	if ( function_exists( 'wc_price' ) ) {
		if ( $regular && $regular > $price ) {
			return wc_format_sale_price( $regular, $price );
		}
		return wc_price( $price );
	}

	$out = '<span class="amount">&euro;' . number_format( (float) $price, 2 ) . '</span>';
	if ( $regular && $regular > $price ) {
		$out = '<del>&euro;' . number_format( (float) $regular, 2 ) . '</del> <ins>' . $out . '</ins>';
	}
	return $out;
}

/**
 * Star rating markup.
 */
function myshop_stars( $rating ) {
	$rating = (float) $rating;
	$out    = '<span class="stars" aria-label="' . esc_attr( sprintf( __( 'Rated %s out of 5', 'base-theme' ), $rating ) ) . '">';

	for ( $i = 1; $i <= 5; $i++ ) {
		if ( $rating >= $i ) {
			$out .= '<i class="fa-solid fa-star"></i>';
		} elseif ( $rating > $i - 1 ) {
			$out .= '<i class="fa-solid fa-star-half-stroke"></i>';
		} else {
			$out .= '<i class="fa-solid fa-star stars__empty"></i>';
		}
	}

	return $out . '</span>';
}

/**
 * The demo catalogue.
 *
 * Deliberately category-agnostic (bags, audio, furniture, apparel…) so the
 * layout can be judged before a product type is chosen. `tags` drives which
 * demo products answer a "new" / "sale" / "bestseller" request.
 */
function myshop_demo_catalogue() {
	$textures = array( 'texture-1', 'texture-2', 'texture-3', 'texture-4' );

	$items = array(
		array( 'Aurelia Leather Tote', 'Bags', 248, 310, 'bag', 4.8, 42, array( 'new', 'sale' ), array( '#2b2b2e', '#b07b4f', '#d8d2c7' ) ),
		array( 'Meridian Automatic Watch', 'Watches', 1190, 0, 'watch', 5.0, 18, array( 'new', 'best' ), array( '#1f2933', '#c9a227', '#e5e4e2' ) ),
		array( 'Vale Wireless Headphones', 'Audio', 329, 0, 'headphones', 4.6, 96, array( 'new', 'best' ), array( '#101114', '#f2efe9' ) ),
		array( 'Lumen Table Lamp', 'Lighting', 185, 0, 'lamp', 4.7, 24, array( 'new' ), array( '#b07b4f', '#2f7d5d' ) ),
		array( 'Nordic Lounge Chair', 'Furniture', 890, 1120, 'chair', 4.9, 31, array( 'sale', 'best' ), array( '#8a6a4f', '#4a4f57' ) ),
		array( 'Terra Ceramic Vase', 'Home', 76, 0, 'vase', 4.5, 57, array( 'new' ), array( '#c96f4a', '#eae5dc' ) ),
		array( 'Hana Silk Scarf', 'Accessories', 120, 160, 'scarf', 4.4, 12, array( 'sale' ), array( '#b3402f', '#2b5f75', '#e8c17a' ) ),
		array( 'Kestrel Running Sneaker', 'Footwear', 165, 0, 'sneaker', 4.6, 128, array( 'new', 'best' ), array( '#f2efe9', '#101114', '#b3402f' ) ),
		array( 'Solstice Sunglasses', 'Eyewear', 210, 0, 'sunglasses', 4.8, 39, array( 'best' ), array( '#3b3128', '#101114' ) ),
		array( 'Amber Soy Candle', 'Home', 42, 56, 'candle', 4.9, 214, array( 'sale', 'best' ), array( '#e8c17a', '#f2efe9' ) ),
		array( 'Onyx Travel Backpack', 'Bags', 275, 0, 'backpack', 4.7, 63, array( 'new' ), array( '#101114', '#4a4f57' ) ),
		array( 'Reeve Cashmere Sweater', 'Apparel', 340, 0, 'sweater', 4.8, 47, array( 'best' ), array( '#d8d2c7', '#2b5f75', '#101114' ) ),
	);

	$copy = array(
		'bag'        => 'Full-grain Italian leather, hand-finished edges and a suede-lined interior that softens with wear.',
		'watch'      => '38mm brushed-steel case, sapphire crystal and a 72-hour in-house automatic movement.',
		'headphones' => 'Adaptive noise cancelling, 40-hour battery and memory-foam cups wrapped in lambskin.',
		'lamp'       => 'Hand-blown opal glass on a solid brass stem, dimmable down to a warm 2200K glow.',
		'chair'      => 'Steam-bent oak frame with a shearling seat — built for long evenings, not showrooms.',
		'vase'       => 'Wheel-thrown stoneware in a reactive glaze, so no two pieces come out alike.',
		'scarf'      => 'Featherweight mulberry silk with hand-rolled hems, printed in a 90cm painterly repeat.',
		'sneaker'    => 'Engineered knit upper on a carbon-plated midsole. 218 grams of quiet speed.',
		'sunglasses' => 'Acetate frames polished for three days, fitted with polarised Zeiss lenses.',
		'candle'     => 'Amber, cedar and a whisper of smoke. Sixty hours of clean-burning soy wax.',
		'backpack'   => 'Water-resistant coated canvas, a padded 16" laptop bay and a lie-flat travel opening.',
		'sweater'    => 'Two-ply Grade-A Mongolian cashmere, fully-fashioned so the collar keeps its shape.',
	);

	$products = array();

	foreach ( $items as $i => $item ) {
		list( $name, $cat, $price, $regular, $art, $rating, $count, $tags, $swatches ) = $item;

		$badge = null;
		if ( $regular > $price ) {
			$badge = array(
				'label' => '-' . round( ( 1 - $price / $regular ) * 100 ) . '%',
				'type'  => 'sale',
			);
		} elseif ( in_array( 'new', $tags, true ) ) {
			$badge = array(
				'label' => __( 'New', 'base-theme' ),
				'type'  => 'new',
			);
		} elseif ( in_array( 'best', $tags, true ) ) {
			$badge = array(
				'label' => __( 'Best Seller', 'base-theme' ),
				'type'  => 'best',
			);
		}

		$products[] = array(
			'id'           => 'demo-' . ( $i + 1 ),
			'name'         => $name,
			'category'     => $cat,
			'permalink'    => myshop_shop_url(),
			'price_html'   => myshop_price_html( $price, $regular ),
			'image'        => myshop_placeholder( $art ),
			'image_hover'  => myshop_placeholder( $textures[ $i % 4 ] ),
			'rating'       => $rating,
			'rating_count' => $count,
			'badge'        => $badge,
			'swatches'     => $swatches,
			'excerpt'      => isset( $copy[ $art ] ) ? $copy[ $art ] : '',
			'in_stock'     => true,
			'purchasable'  => false,
			'is_demo'      => true,
			'tags'         => $tags,
		);
	}

	return $products;
}

/**
 * Turn a WC_Product into the shape the templates expect.
 */
function myshop_normalize_product( $product ) {
	$image_id = $product->get_image_id();
	$image    = $image_id
		? wp_get_attachment_image_url( $image_id, 'woocommerce_thumbnail' )
		: wc_placeholder_img_src( 'woocommerce_thumbnail' );

	// Second gallery image drives the hover swap; fall back to the main image.
	$gallery     = $product->get_gallery_image_ids();
	$image_hover = ! empty( $gallery )
		? wp_get_attachment_image_url( $gallery[0], 'woocommerce_thumbnail' )
		: $image;

	$badge = null;
	if ( ! $product->is_in_stock() ) {
		$badge = array(
			'label' => __( 'Sold Out', 'base-theme' ),
			'type'  => 'out',
		);
	} elseif ( $product->is_on_sale() ) {
		$regular = (float) $product->get_regular_price();
		$sale    = (float) $product->get_sale_price();
		$label   = ( $regular > 0 && $sale > 0 )
			? '-' . round( ( 1 - $sale / $regular ) * 100 ) . '%'
			: __( 'Sale', 'base-theme' );
		$badge   = array(
			'label' => $label,
			'type'  => 'sale',
		);
	} elseif ( $product->get_featured() ) {
		$badge = array(
			'label' => __( 'Featured', 'base-theme' ),
			'type'  => 'best',
		);
	} elseif ( strtotime( $product->get_date_created() ) > strtotime( '-30 days' ) ) {
		$badge = array(
			'label' => __( 'New', 'base-theme' ),
			'type'  => 'new',
		);
	}

	$terms    = get_the_terms( $product->get_id(), 'product_cat' );
	$category = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : '';

	return array(
		'id'           => $product->get_id(),
		'name'         => $product->get_name(),
		'category'     => $category,
		'permalink'    => $product->get_permalink(),
		'price_html'   => $product->get_price_html(),
		'image'        => $image,
		'image_hover'  => $image_hover,
		'rating'       => (float) $product->get_average_rating(),
		'rating_count' => (int) $product->get_review_count(),
		'badge'        => $badge,
		'swatches'     => array(),
		'excerpt'      => wp_strip_all_tags( $product->get_short_description() ),
		'in_stock'     => $product->is_in_stock(),
		'is_demo'      => false,
		'purchasable'  => $product->is_purchasable() && $product->is_in_stock() && $product->is_type( 'simple' ),
		'add_to_cart'  => $product->add_to_cart_url(),
		'tags'         => array(),
	);
}

/**
 * Fetch products for a section.
 *
 * @param array $args {
 *     @type int    $limit Number of products.
 *     @type string $type  recent|featured|sale|bestseller.
 * }
 * @return array Normalised products.
 */
function myshop_get_products( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'limit' => 8,
			'type'  => 'recent',
		)
	);

	$products = array();

	// Set the myshop_force_demo filter to true to ignore the real catalogue and
	// show the demo set instead — useful while the shop is still being dressed.
	if ( ! apply_filters( 'myshop_force_demo', false ) && function_exists( 'wc_get_products' ) ) {
		$query = array(
			'status'  => 'publish',
			'limit'   => $args['limit'],
			'orderby' => 'date',
			'order'   => 'DESC',
		);

		switch ( $args['type'] ) {
			case 'featured':
				$query['featured'] = true;
				break;

			case 'sale':
				$on_sale = wc_get_product_ids_on_sale();
				// An empty include[] would return the whole catalogue, so bail
				// to the demo set instead of silently showing everything.
				if ( empty( $on_sale ) ) {
					$query = null;
					break;
				}
				$query['include'] = $on_sale;
				break;

			case 'bestseller':
				$query['orderby']  = 'meta_value_num';
				$query['meta_key'] = 'total_sales'; // phpcs:ignore WordPress.DB.SlowDBQuery
				$query['order']    = 'DESC';
				break;
		}

		if ( $query ) {
			foreach ( wc_get_products( $query ) as $product ) {
				$products[] = myshop_normalize_product( $product );
			}
		}
	}

	if ( ! empty( $products ) ) {
		return $products;
	}

	// No catalogue yet — serve the demo set filtered to match the request.
	$demo = myshop_demo_catalogue();
	$map  = array(
		'featured'   => 'best',
		'bestseller' => 'best',
		'sale'       => 'sale',
		'recent'     => 'new',
	);
	$tag  = isset( $map[ $args['type'] ] ) ? $map[ $args['type'] ] : '';

	if ( $tag ) {
		$filtered = array_values(
			array_filter(
				$demo,
				function ( $p ) use ( $tag ) {
					return in_array( $tag, $p['tags'], true );
				}
			)
		);
		if ( count( $filtered ) >= 4 ) {
			$demo = $filtered;
		}
	}

	return array_slice( $demo, 0, $args['limit'] );
}

/**
 * True when the storefront is running on demo data.
 */
function myshop_is_demo() {
	$products = myshop_get_products( array( 'limit' => 1 ) );
	return empty( $products ) || ! empty( $products[0]['is_demo'] );
}

/**
 * Product categories — real ones when they exist, otherwise demo tiles.
 */
function myshop_get_categories( $limit = 6 ) {
	$categories = array();

	if ( taxonomy_exists( 'product_cat' ) ) {
		$terms = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => true,
				'number'     => $limit,
				'orderby'    => 'count',
				'order'      => 'DESC',
				'exclude'    => array( get_option( 'default_product_cat' ) ),
			)
		);

		if ( $terms && ! is_wp_error( $terms ) ) {
			// Terms without a usable thumbnail fall back to placeholder art —
			// wp_get_attachment_image_url() returns false for a stale
			// thumbnail_id, which would otherwise render a broken <img>.
			// The cat- variants are transparent, so they sit on the tile's own
			// gradient instead of showing a pasted-on rectangle.
			$fallback_art = array( 'cat-bag', 'cat-watch', 'cat-headphones', 'cat-lamp', 'cat-sweater', 'cat-sneaker' );

			foreach ( $terms as $i => $term ) {
				$thumb_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
				$image    = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'large' ) : '';

				$categories[] = array(
					'name'  => $term->name,
					'count' => $term->count,
					'link'  => get_term_link( $term ),
					'image' => $image ? $image : '',
					'art'   => $image ? '' : myshop_placeholder( $fallback_art[ $i % count( $fallback_art ) ] ),
				);
			}
		}
	}

	if ( ! empty( $categories ) ) {
		return $categories;
	}

	$demo = array(
		array( 'Bags', 24, 'cat-bag' ),
		array( 'Watches', 18, 'cat-watch' ),
		array( 'Audio', 12, 'cat-headphones' ),
		array( 'Home & Living', 36, 'cat-lamp' ),
		array( 'Apparel', 42, 'cat-sweater' ),
		array( 'Footwear', 27, 'cat-sneaker' ),
	);

	$categories = array();
	foreach ( array_slice( $demo, 0, $limit ) as $item ) {
		$categories[] = array(
			'name'  => $item[0],
			'count' => $item[1],
			'link'  => myshop_shop_url(),
			'image' => '',
			'art'   => myshop_placeholder( $item[2] ),
		);
	}

	return $categories;
}

/**
 * Free-shipping threshold used by the cart drawer progress bar.
 */
function myshop_free_shipping_threshold() {
	return (float) apply_filters( 'myshop_free_shipping_threshold', 100 );
}
