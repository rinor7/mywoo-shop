<?php
/**
 * Global Settings → store UI content that used to be hardcoded:
 * announcement bar, free-shipping threshold, product-page perks,
 * accepted-payment icons, cart trust note. The admin values are the
 * only source — an emptied field simply hides that element.
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

		/* --- Announcement bar --- */
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

		/* --- Store UI --- */
		acf_add_local_field(
			array(
				'key'       => 'field_ms_tab_store_ui',
				'parent'    => $parent,
				'label'     => __( 'Store UI', 'base-theme' ),
				'type'      => 'tab',
				'placement' => 'top',
			)
		);

		acf_add_local_field(
			array(
				'key'           => 'field_ms_free_shipping_threshold',
				'parent'        => $parent,
				'name'          => 'free_shipping_threshold',
				'label'         => __( 'Free shipping threshold', 'base-theme' ),
				'type'          => 'number',
				'min'           => 0,
				'step'          => 1,
				'default_value' => 100,
				'instructions'  => __( 'Drives the cart-drawer progress bar and the "free delivery over …" texts. Set 0 to hide the progress bar.', 'base-theme' ),
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
				'key'          => 'field_ms_cart_secure_note',
				'parent'       => $parent,
				'name'         => 'cart_secure_note',
				'label'        => __( 'Cart trust note', 'base-theme' ),
				'type'         => 'text',
				'instructions' => __( 'Short reassurance line under the cart order summary (e.g. "Secure checkout guaranteed"). Leave empty to hide it.', 'base-theme' ),
			)
		);

		acf_add_local_field(
			array(
				'key'           => 'field_ms_nav_menu_content',
				'parent'        => $parent,
				'name'          => 'nav_menu_content',
				'label'         => __( 'Header navigation content', 'base-theme' ),
				'type'          => 'button_group',
				'choices'       => array(
					'search' => __( 'Search input', 'base-theme' ),
					'nav'    => __( 'Navigation menu', 'base-theme' ),
				),
				'default_value' => 'search',
				'instructions'  => __( 'What shows in place of the nav menu — the desktop header\'s middle nav bar, and the top of the mobile hamburger menu. With only a few pages, a quick search is more useful than a repeated nav list — switch back to the menu once there are more pages to link to.', 'base-theme' ),
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
 * Product-page perk lines with rotating icons.
 *
 * @return array[] [icon, text]
 */
function myshop_pdp_perks() {
	$icons = array( 'fa-truck-fast', 'fa-rotate-left', 'fa-lock', 'fa-headset' );
	$raw   = (string) myshop_opt( 'pdp_perks', '' );
	$lines = array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', $raw ) ) );

	$perks = array();

	foreach ( array_slice( array_values( $lines ), 0, 4 ) as $i => $line ) {
		$perks[] = array( $icons[ $i % count( $icons ) ], $line );
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
