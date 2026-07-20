<?php
/**
 * My Account customisation + YITH wishlist bridge.
 *
 * @package Base Theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Is YITH Wishlist available?
 *
 * YITH 4.x dropped the old yith_wcwl_get_wishlist_url() helper, so detect on
 * the plugin constant + main instance function (present in 3.x and 4.x).
 */
function myshop_yith_active() {
	return defined( 'YITH_WCWL' ) && ( function_exists( 'yith_wcwl' ) || function_exists( 'yith_wcwl_get_wishlist_url' ) );
}

/**
 * Wishlist landing URL ('' when the plugin is off).
 */
function myshop_wishlist_url() {
	if ( ! myshop_yith_active() ) {
		return '';
	}

	if ( function_exists( 'yith_wcwl_get_wishlist_url' ) ) {
		return yith_wcwl_get_wishlist_url();
	}

	$yith = yith_wcwl();
	return is_object( $yith ) && method_exists( $yith, 'get_wishlist_url' ) ? $yith->get_wishlist_url( 'view' ) : '';
}

/**
 * Is a product already saved? (YITH 4.x moved this onto the wishlists service.)
 */
function myshop_in_wishlist( $product_id ) {
	if ( function_exists( 'yith_wcwl_wishlists' ) ) {
		return (bool) yith_wcwl_wishlists()->is_product_in_wishlist( (int) $product_id );
	}
	if ( function_exists( 'yith_wcwl_is_product_in_wishlist' ) ) {
		return (bool) yith_wcwl_is_product_in_wishlist( (int) $product_id );
	}
	return false;
}

/**
 * Server-side wishlist count (0 when plugin off — the localStorage fallback
 * fills the bubble client-side instead).
 */
function myshop_wishlist_count() {
	if ( myshop_yith_active() && function_exists( 'yith_wcwl_count_all_products' ) ) {
		return (int) yith_wcwl_count_all_products();
	}
	return 0;
}

/**
 * Account menu: icons map is CSS-side; here we inject Wishlist after Orders
 * and relabel a couple of endpoints to match the design.
 */
function myshop_account_menu_items( $items ) {
	$items['edit-address'] = __( 'Addresses', 'base-theme' );
	$items['edit-account'] = __( 'Account Info', 'base-theme' );

	if ( ! myshop_yith_active() ) {
		return $items;
	}

	$out = array();
	foreach ( $items as $key => $label ) {
		$out[ $key ] = $label;
		if ( 'orders' === $key ) {
			$out['ms-wishlist'] = __( 'Wishlist', 'base-theme' );
		}
	}
	return $out;
}
add_filter( 'woocommerce_account_menu_items', 'myshop_account_menu_items' );

/**
 * The injected Wishlist item points at YITH's page.
 */
function myshop_account_menu_url( $url, $endpoint ) {
	if ( 'ms-wishlist' === $endpoint ) {
		return myshop_wishlist_url();
	}
	return $url;
}
add_filter( 'woocommerce_get_endpoint_url', 'myshop_account_menu_url', 10, 2 );

/**
 * "Member since 2023" line for the sidebar card.
 */
function myshop_member_since() {
	$user = wp_get_current_user();
	if ( ! $user->exists() ) {
		return '';
	}
	return sprintf(
		/* translators: %s: year of registration */
		esc_html__( 'Member since %s', 'base-theme' ),
		date_i18n( 'Y', strtotime( $user->user_registered ) )
	);
}

/**
 * Snapshot for the dashboard "Latest acquisitions" card.
 *
 * @return array{count:int,last:?WC_Order,thumbs:string[]}
 */
function myshop_account_orders_snapshot() {
	$orders = wc_get_orders(
		array(
			'customer' => get_current_user_id(),
			'limit'    => 3,
			'orderby'  => 'date',
			'order'    => 'DESC',
		)
	);

	$thumbs = array();
	if ( ! empty( $orders ) ) {
		foreach ( $orders[0]->get_items() as $item ) {
			$order_product = $item->get_product();
			if ( $order_product ) {
				$src = wp_get_attachment_image_url( $order_product->get_image_id(), 'woocommerce_gallery_thumbnail' );
				if ( $src ) {
					$thumbs[] = $src;
				}
			}
			if ( count( $thumbs ) >= 2 ) {
				break;
			}
		}
	}

	return array(
		'count'  => count( $orders ),
		'last'   => ! empty( $orders ) ? $orders[0] : null,
		'thumbs' => $thumbs,
	);
}

/**
 * Nextend Social Login only auto-places its buttons on wp-login.php in the
 * free version — WooCommerce's own My Account login form is a Pro-only
 * "location". Its shortcode still works standalone regardless, so hook it
 * onto the My Account login form directly instead of buying the addon.
 */
function myshop_account_social_login() {
	if ( ! shortcode_exists( 'nextend_social_login' ) ) {
		return;
	}
	?>
	<div class="account-social-login">
		<span class="account-social-login__divider"><?php esc_html_e( 'or', 'base-theme' ); ?></span>
		<?php echo do_shortcode( '[nextend_social_login]' ); ?>
	</div>
	<?php
}
add_action( 'woocommerce_login_form_end', 'myshop_account_social_login' );
