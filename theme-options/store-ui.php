<?php
/**
 * Global Settings → content that used to be hardcoded: announcement bar,
 * header nav content, free-shipping threshold, product-page perks,
 * accepted-payment icons, cart trust note, "Complete the look" copy.
 * The admin values are the only source — an emptied field simply hides
 * that element (see myshop_opt() below); nothing falls back to text
 * baked into this file.
 *
 * Tabs, top to bottom: Global Settings + Header Settings (native ACF
 * field group, see acf-json/group_683a451131cc8.json — header nav
 * content lives there too, alongside Header Sticky) then, appended
 * here: Announcement Bar, Shop, Single Product.
 *
 * To add a new setting: put it under the existing tab that matches its
 * topic, or add a new `'type' => 'tab'` field following the pattern
 * below to start another one — ACF tabs are flat markers, not nested,
 * so "sub-tabs" aren't a thing; a new top-level tab is the equivalent.
 *
 * @package Base Theme
 */

defined( 'ABSPATH' ) || exit;

/* ---------- Fields (appended to the Global Settings group) ---------- */
add_action(
	'acf/init',
	function () {
		if ( ! function_exists( 'acf_add_local_field' ) ) {
			return;
		}

		$parent = 'group_683a451131cc8';

		/* ============ Tab: Announcement Bar ============ */
		acf_add_local_field(
			array(
				'key'       => 'field_ms_tab_announce',
				'parent'    => $parent,
				'label'     => __( 'Announcement Bar', 'base-theme' ),
				'type'      => 'tab',
				'placement' => 'top',
			)
		);

		acf_add_local_field(
			array(
				'key'           => 'field_ms_announce_enabled',
				'parent'        => $parent,
				'name'          => 'announce_enabled',
				'label'         => __( 'Show announcement bar', 'base-theme' ),
				'type'          => 'true_false',
				'ui'            => 1,
				'default_value' => 1,
			)
		);

		acf_add_local_field(
			array(
				'key'          => 'field_ms_announce_messages',
				'parent'       => $parent,
				'name'         => 'announce_messages',
				'label'        => __( 'Messages', 'base-theme' ),
				'type'         => 'textarea',
				'rows'         => 4,
				'instructions' => __( 'One message per line — they rotate automatically. Leave empty to hide the bar.', 'base-theme' ),
			)
		);

		/* ============ Tab: Shop ============ */
		acf_add_local_field(
			array(
				'key'       => 'field_ms_tab_shop',
				'parent'    => $parent,
				'label'     => __( 'Shop', 'base-theme' ),
				'type'      => 'tab',
				'placement' => 'top',
			)
		);

		acf_add_local_field(
			array(
				'key'           => 'field_ms_cart_progress_text',
				'parent'        => $parent,
				'name'          => 'cart_progress_text',
				'label'         => __( 'Free shipping progress message', 'base-theme' ),
				'type'          => 'text',
				'default_value' => __( 'Spend {amount} more for free shipping', 'base-theme' ),
				'instructions'  => __( 'Shown in two places while the cart is under the free shipping minimum: the cart drawer\'s progress bar, and the Shipping row on the checkout page (in place of "Calculated at next step") if no shipping method has resolved yet for that reason. The amount itself comes straight from WooCommerce\'s own "Free Shipping" method (Settings → Shipping → your zone) — not a separate number here, so the two can never disagree. Disable/remove that method and this message stops showing on its own, everywhere. Use {amount} where the remaining amount should go; leave empty to hide the text (the cart drawer\'s bar still reflects progress; checkout falls back to "Calculated at next step").', 'base-theme' ),
			)
		);

		acf_add_local_field(
			array(
				'key'           => 'field_ms_cart_progress_complete_text',
				'parent'        => $parent,
				'name'          => 'cart_progress_complete_text',
				'label'         => __( 'Free shipping unlocked message', 'base-theme' ),
				'type'          => 'text',
				'default_value' => __( 'You have unlocked free shipping', 'base-theme' ),
				'instructions'  => __( 'Shown in the cart drawer once the free shipping threshold is met. Use {amount} where the qualifying threshold should go (e.g. "with an order of {amount}"). Leave empty to hide it.', 'base-theme' ),
			)
		);

		acf_add_local_field(
			array(
				'key'           => 'field_ms_cart_shipping_note',
				'parent'        => $parent,
				'name'          => 'cart_shipping_note',
				'label'         => __( 'Cart page — shipping address note', 'base-theme' ),
				'type'          => 'text',
				'default_value' => __( 'You can update this at checkout.', 'base-theme' ),
				'instructions'  => __( 'Shown right after "Shipping to [address]." on the cart page, reassuring the customer that address isn\'t final. Leave empty to hide it.', 'base-theme' ),
			)
		);

		acf_add_local_field(
			array(
				'key'           => 'field_ms_payment_icons',
				'parent'        => $parent,
				'name'          => 'payment_icons',
				'label'         => __( 'Accepted payment icons', 'base-theme' ),
				'type'          => 'checkbox',
				'choices'       => array(
					'visa'       => 'Visa',
					'mastercard' => 'Mastercard',
					'amex'       => 'American Express',
					'paypal'     => 'PayPal',
					'apple-pay'  => 'Apple Pay',
					'stripe'     => 'Stripe',
					'discover'   => 'Discover',
				),
				'default_value' => array( 'visa', 'mastercard', 'amex', 'paypal', 'apple-pay' ),
				'layout'        => 'horizontal',
			)
		);

		acf_add_local_field(
			array(
				'key'          => 'field_ms_shipping_eta_flat_rate',
				'parent'       => $parent,
				'name'         => 'shipping_eta_flat_rate',
				'label'        => __( 'Delivery estimate — Flat rate', 'base-theme' ),
				'type'         => 'text',
				'instructions' => __( 'Shown next to WooCommerce\'s "Flat rate" shipping method on checkout (e.g. "1–2 business days"). Matched by that method\'s type, not its display name, so renaming the method in WooCommerce doesn\'t break this. Leave empty to hide the estimate for this method.', 'base-theme' ),
			)
		);

		acf_add_local_field(
			array(
				'key'          => 'field_ms_shipping_eta_free_shipping',
				'parent'       => $parent,
				'name'         => 'shipping_eta_free_shipping',
				'label'        => __( 'Delivery estimate — Free shipping', 'base-theme' ),
				'type'         => 'text',
				'instructions' => __( 'Shown next to WooCommerce\'s "Free shipping" method on checkout (e.g. "3–5 business days"). Leave empty to hide the estimate for this method.', 'base-theme' ),
			)
		);

		acf_add_local_field(
			array(
				'key'           => 'field_ms_coupon_placeholder',
				'parent'        => $parent,
				'name'          => 'coupon_placeholder',
				'label'         => __( 'Coupon field placeholder', 'base-theme' ),
				'type'          => 'text',
				'default_value' => __( 'Coupon code', 'base-theme' ),
				'instructions'  => __( 'Placeholder text for the coupon input, shared by both the cart page and checkout so they always say the same thing. Leave empty for no placeholder text.', 'base-theme' ),
			)
		);

		acf_add_local_field(
			array(
				'key'          => 'field_ms_shop_bg_enabled',
				'parent'       => $parent,
				'name'         => 'shop_bg_enabled',
				'label'        => __( 'Add a default gallery background for every product', 'base-theme' ),
				'type'         => 'true_false',
				'ui'           => 1,
				'instructions' => __( 'Applies to every product\'s gallery panel. A product with its own background set (Product Story fields on that product\'s edit screen) overrides this and shows its own instead.', 'base-theme' ),
			)
		);

		acf_add_local_field(
			array(
				'key'               => 'field_ms_shop_bg_type',
				'parent'            => $parent,
				'name'              => 'shop_bg_type',
				'label'             => __( 'Background type', 'base-theme' ),
				'type'              => 'button_group',
				'choices'           => array(
					'color'    => __( 'Pick a color', 'base-theme' ),
					'gradient' => __( 'Custom CSS / gradient', 'base-theme' ),
				),
				'default_value'     => 'color',
				'conditional_logic' => array(
					array(
						array( 'field' => 'field_ms_shop_bg_enabled', 'operator' => '==', 'value' => '1' ),
					),
				),
			)
		);

		acf_add_local_field(
			array(
				'key'               => 'field_ms_shop_bg_color',
				'parent'            => $parent,
				'name'              => 'shop_bg_color',
				'label'             => __( 'Background color', 'base-theme' ),
				'type'              => 'color_picker',
				'conditional_logic' => array(
					array(
						array( 'field' => 'field_ms_shop_bg_enabled', 'operator' => '==', 'value' => '1' ),
						array( 'field' => 'field_ms_shop_bg_type', 'operator' => '==', 'value' => 'color' ),
					),
				),
			)
		);

		acf_add_local_field(
			array(
				'key'               => 'field_ms_shop_bg_css',
				'parent'            => $parent,
				'name'              => 'shop_bg_css',
				'label'             => __( 'Linear gradient / custom CSS', 'base-theme' ),
				'type'              => 'text',
				'placeholder'       => 'linear-gradient(135deg, #ff5f6d, #6a11cb)',
				'instructions'      => __( 'Paste any valid CSS `background` value — angle, more than two colors, a radial gradient, anything CSS accepts.', 'base-theme' ),
				'conditional_logic' => array(
					array(
						array( 'field' => 'field_ms_shop_bg_enabled', 'operator' => '==', 'value' => '1' ),
						array( 'field' => 'field_ms_shop_bg_type', 'operator' => '==', 'value' => 'gradient' ),
					),
				),
			)
		);

		/* ============ Tab: Cart ============ */
		acf_add_local_field(
			array(
				'key'       => 'field_ms_tab_cart',
				'parent'    => $parent,
				'label'     => __( 'Cart', 'base-theme' ),
				'type'      => 'tab',
				'placement' => 'top',
			)
		);

		acf_add_local_field(
			array(
				'key'           => 'field_ms_cart_title',
				'parent'        => $parent,
				'name'          => 'cart_title',
				'label'         => __( 'Cart page title', 'base-theme' ),
				'type'          => 'text',
				'default_value' => __( 'Your Bag', 'base-theme' ),
				'instructions'  => __( 'Heading at the top of the cart page. Leave empty to hide it.', 'base-theme' ),
			)
		);

		acf_add_local_field(
			array(
				'key'           => 'field_ms_cart_subtitle',
				'parent'        => $parent,
				'name'          => 'cart_subtitle',
				'label'         => __( 'Cart page subtitle', 'base-theme' ),
				'type'          => 'text',
				'default_value' => __( 'Review your selected pieces before checking out.', 'base-theme' ),
				'instructions'  => __( 'Line under the cart page title. Leave empty to hide it.', 'base-theme' ),
			)
		);

		acf_add_local_field(
			array(
				'key'           => 'field_ms_cart_secure_note',
				'parent'        => $parent,
				'name'          => 'cart_secure_note',
				'label'         => __( 'Secure checkout note', 'base-theme' ),
				'type'          => 'text',
				'default_value' => __( 'All transactions are secure and encrypted.', 'base-theme' ),
				'instructions'  => __( 'Short reassurance line shown in two places: under the cart page order summary, and under the "Place order" button at checkout. Leave empty to hide it in both.', 'base-theme' ),
			)
		);

		acf_add_local_field(
			array(
				'key'           => 'field_ms_cart_ensemble_title',
				'parent'        => $parent,
				'name'          => 'cart_ensemble_title',
				'label'         => __( '"Complete the ensemble" title', 'base-theme' ),
				'type'          => 'text',
				'default_value' => __( 'Complete the ensemble', 'base-theme' ),
				'instructions'  => __( 'Heading above the recommended-products row on the cart page. Leave empty to hide it (the products still show).', 'base-theme' ),
			)
		);

		acf_add_local_field(
			array(
				'key'           => 'field_ms_cart_ensemble_products',
				'parent'        => $parent,
				'name'          => 'cart_ensemble_products',
				'label'         => __( '"Complete the ensemble" — pick specific products', 'base-theme' ),
				'type'          => 'relationship',
				'post_type'     => array( 'product' ),
				'filters'       => array( 'search' ),
				'return_format' => 'id',
				'instructions'  => __( 'Always show these products in the cart-page recommendation row, in this order. Leave empty to keep the automatic pick (the cart\'s cross-sells, or — failing that — recent products).', 'base-theme' ),
			)
		);

		acf_add_local_field(
			array(
				'key'           => 'field_ms_cart_auto_update',
				'parent'        => $parent,
				'name'          => 'cart_auto_update',
				'label'         => __( 'Calculate cart totals automatically', 'base-theme' ),
				'type'          => 'true_false',
				'ui'            => 1,
				'default_value' => 0,
				'instructions'  => __( 'When enabled, changing a quantity on the cart page recalculates totals on its own (shortly after you stop clicking/typing) and the "Update bag" button is hidden. When disabled (default), quantity changes just enable the button — the customer clicks it to recalculate, same as stock WooCommerce.', 'base-theme' ),
			)
		);

		acf_add_local_field(
			array(
				'key'           => 'field_ms_quickview_link_label',
				'parent'        => $parent,
				'name'          => 'quickview_link_label',
				'label'         => __( 'Quick-view "view full details" label', 'base-theme' ),
				'type'          => 'text',
				'default_value' => __( 'View full details', 'base-theme' ),
				'instructions'  => __( 'Link at the bottom of the quick-view popup (the one opened from the eye icon on a product card). This one popup is shared across the whole site — shop, search, homepage sections, cart, and the "Complete the look" area on product pages. Leave empty to hide the link.', 'base-theme' ),
			)
		);

		/* ============ Tab: Single Product ============ */
		acf_add_local_field(
			array(
				'key'       => 'field_ms_tab_single_product',
				'parent'    => $parent,
				'label'     => __( 'Single Product', 'base-theme' ),
				'type'      => 'tab',
				'placement' => 'top',
			)
		);

		acf_add_local_field(
			array(
				'key'          => 'field_ms_pdp_perks',
				'parent'       => $parent,
				'name'         => 'pdp_perks',
				'label'        => __( 'Product page perks', 'base-theme' ),
				'type'         => 'textarea',
				'rows'         => 4,
				'instructions' => __( 'One perk per line, shown under the add-to-bag button (e.g. "Free delivery over CHF 100"). Leave empty to hide the list.', 'base-theme' ),
			)
		);

		acf_add_local_field(
			array(
				'key'           => 'field_ms_pairings_eyebrow',
				'parent'        => $parent,
				'name'          => 'pairings_eyebrow',
				'label'         => __( '"Complete the look" eyebrow', 'base-theme' ),
				'type'          => 'text',
				'default_value' => __( 'Curated pairings', 'base-theme' ),
				'instructions'  => __( 'Small label above the related-products heading near the bottom of every product page. Leave empty to hide it.', 'base-theme' ),
			)
		);

		acf_add_local_field(
			array(
				'key'           => 'field_ms_pairings_title',
				'parent'        => $parent,
				'name'          => 'pairings_title',
				'label'         => __( '"Complete the look" title', 'base-theme' ),
				'type'          => 'text',
				'default_value' => __( 'Complete the look', 'base-theme' ),
				'instructions'  => __( 'Heading for the related-products section near the bottom of every product page. Leave empty to hide it.', 'base-theme' ),
			)
		);

		acf_add_local_field(
			array(
				'key'           => 'field_ms_pairings_enabled',
				'parent'        => $parent,
				'name'          => 'pairings_enabled',
				'label'         => __( '"Complete the look" — show button', 'base-theme' ),
				'type'          => 'true_false',
				'default_value' => 1,
				'ui'            => 1,
				'ui_on_text'    => __( 'Shown', 'base-theme' ),
				'ui_off_text'   => __( 'Hidden', 'base-theme' ),
			)
		);

		acf_add_local_field(
			array(
				'key'          => 'field_ms_pairings_link',
				'parent'       => $parent,
				'name'         => 'pairings_link',
				'label'        => __( '"Complete the look" — button (empty = "Explore full collection" → the shop page)', 'base-theme' ),
				'type'         => 'link',
				'instructions' => __( 'Both the label and the URL come from this one field. Leave empty to keep the default label pointing at the shop.', 'base-theme' ),
			)
		);

		acf_add_local_field(
			array(
				'key'           => 'field_ms_desc_eyebrow',
				'parent'        => $parent,
				'name'          => 'desc_eyebrow',
				'label'         => __( '"Full description" eyebrow', 'base-theme' ),
				'type'          => 'text',
				'default_value' => __( 'Document 01', 'base-theme' ),
				'instructions'  => __( 'Small label above the full description on the product page. Leave empty to hide it.', 'base-theme' ),
			)
		);

		acf_add_local_field(
			array(
				'key'           => 'field_ms_desc_title',
				'parent'        => $parent,
				'name'          => 'desc_title',
				'label'         => __( '"Full description" title', 'base-theme' ),
				'type'          => 'text',
				'default_value' => __( 'Overview', 'base-theme' ),
				'instructions'  => __( 'Heading above the full description on the product page. Leave empty to hide it.', 'base-theme' ),
			)
		);

		acf_add_local_field(
			array(
				'key'           => 'field_ms_specs_eyebrow',
				'parent'        => $parent,
				'name'          => 'specs_eyebrow',
				'label'         => __( '"Specifications" eyebrow', 'base-theme' ),
				'type'          => 'text',
				'default_value' => __( 'Document 02', 'base-theme' ),
				'instructions'  => __( 'Small label above the specifications table on the product page. Leave empty to hide it.', 'base-theme' ),
			)
		);

		acf_add_local_field(
			array(
				'key'           => 'field_ms_specs_title',
				'parent'        => $parent,
				'name'          => 'specs_title',
				'label'         => __( '"Specifications" title', 'base-theme' ),
				'type'          => 'text',
				'default_value' => __( 'Specifications', 'base-theme' ),
				'instructions'  => __( 'Heading above the specifications table on the product page. Leave empty to hide it.', 'base-theme' ),
			)
		);
	}
);

/* ---------- Helpers ---------- */

/**
 * Option value with a default for "never saved yet" (null) and "cleared".
 */
function myshop_opt( $name, $default ) {
	$value = function_exists( 'get_field' ) ? get_field( $name, 'option' ) : null;

	if ( null === $value || '' === $value || array() === $value ) {
		return $default;
	}

	return $value;
}

/**
 * Announcement bar messages (empty array = bar hidden).
 *
 * @return string[]
 */
function myshop_announce_messages() {
	if ( ! myshop_opt( 'announce_enabled', 1 ) ) {
		return array();
	}

	$raw   = (string) myshop_opt( 'announce_messages', '' );
	$lines = array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', $raw ) ) );

	return array_slice( array_values( $lines ), 0, 6 );
}

/**
 * Product-page perk lines with rotating icons — global default, unless
 * $product has its own ps_perks rows (Product Story fields), which
 * replace the global list entirely, icons included. Per-product rows can
 * upload a custom icon image instead of using the default FA rotation.
 *
 * @param WC_Product|null $product Product to check for a per-product override.
 * @return array[] Each: [ 'icon' => fa-class|'', 'image' => url|'', 'text' => string ]
 */
function myshop_pdp_perks( $product = null ) {
	$icons = array( 'fa-truck-fast', 'fa-rotate-left', 'fa-lock', 'fa-headset' );

	if ( $product && function_exists( 'get_field' ) ) {
		$rows = get_field( 'ps_perks', $product->get_id() );
		if ( $rows ) {
			$perks = array();
			foreach ( $rows as $row ) {
				if ( '' === $row['perk_text'] ) {
					continue;
				}
				$image   = ! empty( $row['perk_icon'] ) ? $row['perk_icon'] : '';
				$perks[] = array(
					'icon'  => $image ? '' : $icons[ count( $perks ) % count( $icons ) ],
					'image' => $image,
					'text'  => $row['perk_text'],
				);
			}
			if ( $perks ) {
				return array_slice( $perks, 0, 4 );
			}
		}
	}

	$raw   = (string) myshop_opt( 'pdp_perks', '' );
	$lines = array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', $raw ) ) );

	$perks = array();

	foreach ( array_slice( array_values( $lines ), 0, 4 ) as $i => $line ) {
		$perks[] = array(
			'icon'  => $icons[ $i % count( $icons ) ],
			'image' => '',
			'text'  => $line,
		);
	}

	return $perks;
}

/**
 * What replaces the nav menu — the desktop header's middle nav bar, and
 * the top of the mobile hamburger menu: 'search' (default) or 'nav'.
 */
function myshop_nav_menu_content() {
	return myshop_opt( 'nav_menu_content', 'search' );
}

/**
 * "Header Sticky" (Global Settings → Header Settings): keep the header
 * pinned at all times instead of the default hide-on-scroll-down behaviour.
 */
function myshop_header_always_sticky() {
	$value = myshop_opt( 'sticky', array() );

	return is_array( $value ) && in_array( 'Enable', $value, true );
}

/**
 * "Calculate cart totals automatically" (Global Settings → Cart): skip the
 * manual "Update bag" click and recalculate on quantity change instead.
 */
function myshop_cart_auto_update() {
	return (bool) myshop_opt( 'cart_auto_update', 0 );
}

/**
 * Cart page title + subtitle (Global Settings → Cart). Empty field = not
 * shown, no fallback text.
 */
function myshop_cart_title() {
	return myshop_opt( 'cart_title', '' );
}

function myshop_cart_subtitle() {
	return myshop_opt( 'cart_subtitle', '' );
}

/**
 * Free-shipping progress messages (cart drawer): "under threshold" and
 * "unlocked" text, editable in Global Settings → Shop. Empty field = not
 * shown, no fallback text.
 */
function myshop_cart_progress_text() {
	return myshop_opt( 'cart_progress_text', '' );
}

function myshop_cart_progress_complete_text() {
	return myshop_opt( 'cart_progress_complete_text', '' );
}

/**
 * Reassurance note after "Shipping to [address]." on the cart page (Global
 * Settings → Shop). Empty field = not shown, no fallback text.
 */
function myshop_cart_shipping_note() {
	return myshop_opt( 'cart_shipping_note', '' );
}

/**
 * Coupon field placeholder (Global Settings → Shop), shared by the cart
 * page and checkout so they never say different things.
 */
function myshop_coupon_placeholder() {
	return myshop_opt( 'coupon_placeholder', '' );
}

/**
 * "Complete the ensemble" (cart page): title, editable in Global Settings
 * → Cart. Empty field = not shown, no fallback text.
 */
function myshop_cart_ensemble_title() {
	return myshop_opt( 'cart_ensemble_title', '' );
}

/**
 * Manually-picked products for "Complete the ensemble" (Global Settings →
 * Cart). Empty = let myshop_cart_ensemble() fall back to its automatic pick.
 *
 * @return int[] Product IDs.
 */
function myshop_cart_ensemble_product_ids() {
	$ids = myshop_opt( 'cart_ensemble_products', array() );

	return is_array( $ids ) ? array_map( 'intval', $ids ) : array();
}

/**
 * "Complete the look" section (product pages): eyebrow, title, button —
 * editable in Global Settings → Single Product. Empty eyebrow/title field =
 * not shown, no fallback text. Label and URL travel together in one ACF
 * "link" field — empty = "Shop all" straight to the shop page.
 */
function myshop_pairings_eyebrow() {
	return myshop_opt( 'pairings_eyebrow', '' );
}

function myshop_pairings_title() {
	return myshop_opt( 'pairings_title', '' );
}

function myshop_pairings_enabled() {
	return (bool) myshop_opt( 'pairings_enabled', true );
}

function myshop_pairings_link() {
	return myshop_opt( 'pairings_link', array() );
}

/**
 * Full description section (product pages): eyebrow + title, editable in
 * Global Settings → Single Product. Empty field = not shown, no fallback text.
 */
function myshop_desc_eyebrow() {
	return myshop_opt( 'desc_eyebrow', '' );
}

function myshop_desc_title() {
	return myshop_opt( 'desc_title', '' );
}

/**
 * Specifications section (product pages): eyebrow + title, editable in
 * Global Settings → Single Product. Empty field = not shown, no fallback text.
 */
function myshop_specs_eyebrow() {
	return myshop_opt( 'specs_eyebrow', '' );
}

function myshop_specs_title() {
	return myshop_opt( 'specs_title', '' );
}

/**
 * First letter of the logged-in user's name, for the account icon's
 * avatar — empty string when logged out (fall back to the plain person
 * icon there). Prefers first_name so it matches myshop_user_first_name()
 * below instead of falling back to display_name/user_login (which can be
 * a different string entirely, e.g. "admin" while first_name is "Rinor").
 */
function myshop_user_initial() {
	if ( ! is_user_logged_in() ) {
		return '';
	}

	$user = wp_get_current_user();
	$name = $user->first_name ? $user->first_name : ( $user->display_name ? $user->display_name : $user->user_login );

	return $name ? strtoupper( mb_substr( $name, 0, 1 ) ) : '';
}

/**
 * Logged-in user's first name, for the account button's desktop pill
 * label — empty string when logged out or no first name is set.
 */
function myshop_user_first_name() {
	if ( ! is_user_logged_in() ) {
		return '';
	}

	$user = wp_get_current_user();

	return $user->first_name ? $user->first_name : '';
}

/**
 * Quick-view popup's "View full details" link label (site-wide — shared
 * by every product card's eye icon, not just the single-product page).
 */
function myshop_quickview_link_label() {
	return myshop_opt( 'quickview_link_label', '' );
}

/**
 * Accepted-payment icon row (footer, cart summary).
 */
function myshop_payment_icons() {
	$chosen = (array) myshop_opt( 'payment_icons', array() );

	if ( ! $chosen ) {
		return;
	}
	?>
	<ul class="footer__pay" aria-label="<?php esc_attr_e( 'Accepted payment methods', 'base-theme' ); ?>">
		<?php foreach ( $chosen as $slug ) : ?>
			<li><i class="fa-brands fa-cc-<?php echo esc_attr( sanitize_html_class( $slug ) ); ?>" aria-hidden="true"></i></li>
		<?php endforeach; ?>
	</ul>
	<?php
}
