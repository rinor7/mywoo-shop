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
 * Reassurance line under the place-order button.
 */
function myshop_secure_note() {
	echo '<p class="checkout-secure-note"><i class="fa-solid fa-lock" aria-hidden="true"></i> '
		. esc_html__( 'All transactions are secure and encrypted.', 'base-theme' ) . '</p>';
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

			printf(
				'<label class="ship-card" for="%1$s">
					<input type="radio" name="shipping_method[%2$d]" data-index="%2$d" id="%1$s" value="%3$s" class="shipping_method" %4$s>
					<span class="ship-card__radio" aria-hidden="true"></span>
					<span class="ship-card__body">
						<span class="ship-card__name">%5$s</span>
						<span class="ship-card__eta">%6$s</span>
					</span>
					<span class="ship-card__price">%7$s</span>
				</label>',
				esc_attr( $id ),
				(int) $i,
				esc_attr( $rate->get_id() ),
				$checked, // phpcs:ignore WordPress.Security.EscapeOutput
				esc_html( $rate->get_label() ),
				esc_html( myshop_shipping_eta( $rate ) ),
				wp_kses_post( $price )
			);
		}

		echo '</div>';
	}
}

/**
 * Small ETA line per shipping method. Filterable per rate id.
 */
function myshop_shipping_eta( $rate ) {
	$method = $rate->get_method_id();

	$eta = 'free_shipping' === $method || false !== stripos( $rate->get_label(), 'standard' )
		? __( '3–5 business days', 'base-theme' )
		: __( '1–2 business days', 'base-theme' );

	if ( 'local_pickup' === $method ) {
		$eta = __( 'Ready today at the store', 'base-theme' );
	}

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
 * Payment method icons + secure badge (cart summary card).
 */
function myshop_payment_badges() {
	?>
	<ul class="pay-icons" aria-label="<?php esc_attr_e( 'Accepted payment methods', 'base-theme' ); ?>">
		<li><i class="fa-brands fa-cc-visa" aria-hidden="true"></i></li>
		<li><i class="fa-brands fa-cc-mastercard" aria-hidden="true"></i></li>
		<li><i class="fa-brands fa-cc-amex" aria-hidden="true"></i></li>
		<li><i class="fa-brands fa-cc-paypal" aria-hidden="true"></i></li>
		<li><i class="fa-brands fa-cc-apple-pay" aria-hidden="true"></i></li>
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
 * "Complete the ensemble" — cross-sells when set, newest products otherwise,
 * never items already in the cart. Rendered by the cart template.
 */
function myshop_cart_ensemble() {
	$in_cart = array();
	foreach ( WC()->cart->get_cart() as $item ) {
		$in_cart[] = $item['product_id'];
	}

	$products = array();

	// Real cross-sells first.
	$cross_ids = WC()->cart->get_cross_sells();
	if ( ! empty( $cross_ids ) ) {
		foreach ( array_slice( $cross_ids, 0, 4 ) as $id ) {
			$product = wc_get_product( $id );
			if ( $product && 'publish' === $product->get_status() ) {
				$products[] = myshop_normalize_product( $product );
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
	?>
	<section class="cart-ensemble">
		<h2 class="cart-ensemble__title"><?php esc_html_e( 'Complete the ensemble', 'base-theme' ); ?></h2>
		<div class="product-grid">
			<?php foreach ( $products as $i => $product ) : ?>
				<?php myshop_product_card( $product, $i ); ?>
			<?php endforeach; ?>
		</div>
	</section>
	<?php
}
