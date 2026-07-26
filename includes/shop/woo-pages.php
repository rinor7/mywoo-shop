<?php
/**
 * Cart + checkout page customisation.
 *
 * Backs the template overrides in woocommerce/cart and woocommerce/checkout:
 * field trimming, Shopify-style single address flow, shipping-method cards,
 * and the "Complete the ensemble" recommendation row.
 *
 * @package Base Theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Checkout fields: single clean address form.
 * Email renders in its own "Contact information" block (see form-checkout.php).
 */
function myshop_checkout_fields( $fields ) {
	unset( $fields['billing']['billing_company'] );
	unset( $fields['billing']['billing_phone'] );

	$fields['billing']['billing_email']['priority'] = 5;

	$fields['billing']['billing_first_name']['priority'] = 10;
	$fields['billing']['billing_last_name']['priority']  = 20;

	$fields['billing']['billing_address_1']['priority']    = 30;
	$fields['billing']['billing_address_1']['label']       = __( 'Address', 'base-theme' );
	$fields['billing']['billing_address_1']['placeholder'] = '';

	$fields['billing']['billing_address_2']['priority'] = 40;
	$fields['billing']['billing_address_2']['label']    = __( 'Apartment, suite, etc. (optional)', 'base-theme' );
	$fields['billing']['billing_address_2']['label_class'] = array();
	$fields['billing']['billing_address_2']['placeholder'] = '';

	$fields['billing']['billing_city']['priority']     = 50;
	$fields['billing']['billing_country']['priority']  = 60;
	$fields['billing']['billing_postcode']['priority'] = 70;
	$fields['billing']['billing_postcode']['label']    = __( 'ZIP Code', 'base-theme' );
	$fields['billing']['billing_state']['priority']    = 80;

	return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'myshop_checkout_fields', 20 );

/**
 * One address form: ship to the billing address, no second block.
 */
function myshop_ship_to_billing_only() {
	return 'billing_only';
}
add_filter( 'pre_option_woocommerce_ship_to_destination', 'myshop_ship_to_billing_only' );

// No order-notes textarea.
add_filter( 'woocommerce_enable_order_notes_field', '__return_false' );

/**
 * Submit button label.
 */
function myshop_order_button_text() {
	return __( 'Complete Purchase', 'base-theme' );
}
add_filter( 'woocommerce_order_button_text', 'myshop_order_button_text' );

/**
 * The coupon field lives inside the order summary card instead of the
 * default "Have a coupon?" toggle above the form.
 */
function myshop_move_checkout_coupon() {
	remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );

	// archive-product.php places these itself — unhook the defaults so
	// breadcrumb, count, sorting and pagination render exactly once.
	remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
	remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );
}
add_action( 'init', 'myshop_move_checkout_coupon' );

/**
 * Reassurance line under the place-order button — same field/text as the
 * cart page's own note (Global Settings), so the two can't drift apart.
 */
function myshop_secure_note() {
	$note = function_exists( 'myshop_opt' ) ? trim( (string) myshop_opt( 'cart_secure_note', '' ) ) : '';
	if ( '' === $note ) {
		return;
	}

	echo '<p class="checkout-secure-note"><i class="fa-solid fa-lock" aria-hidden="true"></i> ' . esc_html( $note ) . '</p>';
}
add_action( 'woocommerce_review_order_after_submit', 'myshop_secure_note' );

/**
 * Shipping methods as selectable cards (checkout left column).
 *
 * Same input name/values as WooCommerce's own list, so the core checkout JS
 * picks changes up and refreshes the order review.
 */
function myshop_shipping_cards() {
	if ( ! WC()->cart->needs_shipping() || ! WC()->cart->show_shipping() ) {
		return;
	}

	$packages = WC()->shipping()->get_packages();

	foreach ( $packages as $i => $package ) {
		$chosen = isset( WC()->session->chosen_shipping_methods[ $i ] ) ? WC()->session->chosen_shipping_methods[ $i ] : '';

		if ( empty( $package['rates'] ) ) {
			echo '<p class="ship-cards__none">' . esc_html__( 'Enter your address to see shipping options.', 'base-theme' ) . '</p>';
			continue;
		}

		echo '<div class="ship-cards">';

		foreach ( $package['rates'] as $rate ) {
			$id      = 'shipping_method_' . $i . '_' . sanitize_title( $rate->get_id() );
			$checked = $chosen === $rate->get_id() ? ' checked="checked"' : '';

			$price = ( 0 < (float) $rate->get_cost() )
				? wc_price( (float) $rate->get_cost() + (float) $rate->get_shipping_tax() )
				: wc_price( 0 );

			$eta      = myshop_shipping_eta( $rate );
			$eta_html = $eta ? '<span class="ship-card__eta">' . esc_html( $eta ) . '</span>' : '';

			printf(
				'<label class="ship-card" for="%1$s">
					<input type="radio" name="shipping_method[%2$d]" data-index="%2$d" id="%1$s" value="%3$s" class="shipping_method" %4$s>
					<span class="ship-card__radio" aria-hidden="true"></span>
					<span class="ship-card__body">
						<span class="ship-card__name">%5$s</span>
						%6$s
					</span>
					<span class="ship-card__price">%7$s</span>
				</label>',
				esc_attr( $id ),
				(int) $i,
				esc_attr( $rate->get_id() ),
				$checked, // phpcs:ignore WordPress.Security.EscapeOutput
				esc_html( $rate->get_label() ),
				$eta_html, // phpcs:ignore WordPress.Security.EscapeOutput
				wp_kses_post( $price )
			);
		}

		echo '</div>';
	}
}

/**
 * Small ETA line per shipping method (Global Settings → Shop), matched by
 * the method's real type — not guessed from its label text, which broke
 * as soon as a method was named anything other than what the guess
 * expected. Empty string ('') when nothing's set for that method, so the
 * caller can just skip rendering the line entirely.
 */
function myshop_shipping_eta( $rate ) {
	$method = $rate->get_method_id();

	$field_by_method = array(
		'flat_rate'     => 'shipping_eta_flat_rate',
		'free_shipping' => 'shipping_eta_free_shipping',
	);

	$eta = isset( $field_by_method[ $method ] ) && function_exists( 'myshop_opt' )
		? myshop_opt( $field_by_method[ $method ], '' )
		: '';

	return apply_filters( 'myshop_shipping_eta', $eta, $rate );
}

/**
 * The shipping line shown in the order summary (methods render on the left).
 */
function myshop_chosen_shipping_label() {
	$chosen = WC()->session ? WC()->session->get( 'chosen_shipping_methods' ) : array();
	if ( empty( $chosen ) ) {
		return '';
	}

	foreach ( WC()->shipping()->get_packages() as $i => $package ) {
		if ( isset( $chosen[ $i ], $package['rates'][ $chosen[ $i ] ] ) ) {
			return $package['rates'][ $chosen[ $i ] ]->get_label();
		}
	}

	return '';
}

/**
 * Checkout shipping row, while no method has resolved yet: the free-shipping
 * progress message (Global Settings → Shop "Free shipping progress message"
 * — the same field and number driving the cart drawer's bar) when that's
 * specifically why nothing's available — the cart hasn't reached the real
 * WooCommerce Free Shipping minimum yet. '' otherwise, so the caller falls
 * back to its own generic "still calculating" text.
 */
function myshop_checkout_shipping_placeholder() {
	if ( ! function_exists( 'myshop_free_shipping_threshold' ) || ! function_exists( 'WC' ) || ! WC()->cart ) {
		return '';
	}

	$threshold = myshop_free_shipping_threshold();
	if ( $threshold <= 0 ) {
		return '';
	}

	$remaining = $threshold - (float) WC()->cart->get_subtotal();
	if ( $remaining <= 0 ) {
		return '';
	}

	$text = function_exists( 'myshop_cart_progress_text' ) ? myshop_cart_progress_text() : '';

	return $text ? str_replace( '{amount}', myshop_price_html( $remaining ), $text ) : '';
}

/**
 * Payment method icons + secure badge (cart summary card).
 */
function myshop_payment_badges() {
	$chosen = function_exists( 'myshop_opt' )
		? (array) myshop_opt( 'payment_icons', array( 'visa', 'mastercard', 'amex', 'paypal', 'apple-pay' ) )
		: array( 'visa', 'mastercard', 'amex', 'paypal', 'apple-pay' );

	if ( ! $chosen ) {
		return;
	}
	?>
	<ul class="pay-icons" aria-label="<?php esc_attr_e( 'Accepted payment methods', 'base-theme' ); ?>">
		<?php foreach ( $chosen as $slug ) : ?>
			<li><i class="fa-brands fa-cc-<?php echo esc_attr( sanitize_html_class( $slug ) ); ?>" aria-hidden="true"></i></li>
		<?php endforeach; ?>
	</ul>
	<?php
}

/**
 * Category pill bar for the shop/category archives — quick filtering without
 * a sidebar. Current term (or "All" on the main shop) renders active.
 */
function myshop_shop_filterbar() {
	// Top-level categories only — child terms (Men/Shirts, Women/Shirts)
	// would surface as confusing duplicate pills.
	$terms = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => true,
			'parent'     => 0,
			'orderby'    => 'count',
			'order'      => 'DESC',
			'number'     => 10,
			'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
		)
	);

	if ( empty( $terms ) || is_wp_error( $terms ) ) {
		return;
	}

	$current = is_tax( 'product_cat' ) ? get_queried_object_id() : 0;
	?>
	<nav class="shop-pills" aria-label="<?php esc_attr_e( 'Product categories', 'base-theme' ); ?>">
		<a class="shop-pills__pill<?php echo $current ? '' : ' is-active'; ?>" href="<?php echo esc_url( myshop_shop_url() ); ?>">
			<?php esc_html_e( 'All', 'base-theme' ); ?>
		</a>

		<?php foreach ( $terms as $term ) : ?>
			<a class="shop-pills__pill<?php echo $current === $term->term_id ? ' is-active' : ''; ?>"
				href="<?php echo esc_url( get_term_link( $term ) ); ?>">
				<?php echo esc_html( $term->name ); ?>
				<span class="shop-pills__count"><?php echo (int) $term->count; ?></span>
			</a>
		<?php endforeach; ?>
	</nav>
	<?php
}

/**
 * WooCommerce's own woocommerce_page_title() appends "– Page N" whenever
 * get_query_var('paged') is truthy — but the catalog ordering dropdown
 * resubmits with a hidden paged=1 field on every sort change (to reset
 * pagination), and "1" is truthy, so sorting on page 1 wrongly showed
 * "– Page 1" even though the user never left the first page.
 */
function myshop_fix_search_title_page_suffix( $title ) {
	if ( is_search() && (int) get_query_var( 'paged' ) <= 1 ) {
		/* translators: %s: search query */
		$title = sprintf( __( 'Search results: &ldquo;%s&rdquo;', 'woocommerce' ), get_search_query() );
	}

	return $title;
}
add_filter( 'woocommerce_page_title', 'myshop_fix_search_title_page_suffix' );

/**
 * Every search box on this site already restricts to products
 * (post_type=product, set as a hidden field on every search form) — a
 * bare /?s= URL that bypasses those forms has no post_type, so it falls
 * through to WordPress's own plain search.php (unstyled, and this theme
 * has no content-search.php, so results render blank).
 *
 * Forcing the same post_type via pre_get_posts doesn't work here — by
 * the time that fires, WP_Query::parse_query() has already computed and
 * locked in is_post_type_archive() from the ORIGINAL request, and that's
 * what WooCommerce's template loader checks to pick archive-product.php.
 * The `request` filter runs earlier (in WP::parse_request(), before the
 * main query is even built), so setting post_type here lets that flag
 * compute correctly from the start — same result as every other search
 * on the site, regardless of entry point.
 */
function myshop_force_product_search( $vars ) {
	if ( isset( $vars['s'] ) && ! isset( $vars['post_type'] ) ) {
		$vars['post_type'] = 'product';
	}

	return $vars;
}
add_filter( 'request', 'myshop_force_product_search' );

/**
 * "Complete the ensemble" — manually-picked products when set (Global
 * Settings → Cart), otherwise cross-sells, otherwise newest products.
 * Never items already in the cart. Rendered by the cart template.
 */
function myshop_cart_ensemble() {
	$in_cart = array();
	foreach ( WC()->cart->get_cart() as $item ) {
		$in_cart[] = $item['product_id'];
	}

	$products = array();

	// Admin's explicit picks first, in the order they were set.
	$picked_ids = function_exists( 'myshop_cart_ensemble_product_ids' ) ? myshop_cart_ensemble_product_ids() : array();
	foreach ( $picked_ids as $id ) {
		$product = wc_get_product( $id );
		if ( $product && 'publish' === $product->get_status() && ! in_array( $id, $in_cart, true ) ) {
			$products[] = myshop_normalize_product( $product );
		}
		if ( count( $products ) >= 4 ) {
			break;
		}
	}

	// Real cross-sells next.
	if ( empty( $products ) ) {
		$cross_ids = WC()->cart->get_cross_sells();
		if ( ! empty( $cross_ids ) ) {
			foreach ( array_slice( $cross_ids, 0, 4 ) as $id ) {
				$product = wc_get_product( $id );
				if ( $product && 'publish' === $product->get_status() ) {
					$products[] = myshop_normalize_product( $product );
				}
			}
		}
	}

	if ( empty( $products ) ) {
		$pool = myshop_get_products(
			array(
				'limit' => 8,
				'type'  => 'recent',
			)
		);
		foreach ( $pool as $candidate ) {
			if ( ! in_array( $candidate['id'], $in_cart, true ) ) {
				$products[] = $candidate;
			}
			if ( count( $products ) >= 4 ) {
				break;
			}
		}
	}

	if ( empty( $products ) ) {
		return;
	}

	$title = function_exists( 'myshop_cart_ensemble_title' ) ? myshop_cart_ensemble_title() : '';
	?>
	<section class="cart-ensemble">
		<?php if ( $title ) : ?>
			<h2 class="cart-ensemble__title"><?php echo esc_html( $title ); ?></h2>
		<?php endif; ?>
		<div class="product-grid">
			<?php foreach ( $products as $i => $product ) : ?>
				<?php myshop_product_card( $product, $i ); ?>
			<?php endforeach; ?>
		</div>
	</section>
	<?php
}
