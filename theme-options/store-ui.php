<?php
/**
 * Global Settings → store UI content that used to be hardcoded:
 * announcement bar, free-shipping threshold, product-page perks,
 * accepted-payment icons. Every helper falls back to the previous
 * built-in values, so empty settings never blank the storefront.
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
				'instructions' => __( 'One message per line — they rotate automatically. Leave empty to use the built-in defaults.', 'base-theme' ),
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
				'instructions' => __( 'One perk per line, shown under the add-to-bag button (e.g. "Free delivery over CHF 100"). Leave empty for the defaults.', 'base-theme' ),
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

	if ( $lines ) {
		return array_slice( array_values( $lines ), 0, 6 );
	}

	// Built-in defaults; the shipping line follows the configured threshold.
	$threshold = myshop_free_shipping_threshold();
	$messages  = array();

	if ( $threshold > 0 && function_exists( 'wc_price' ) ) {
		$messages[] = sprintf(
			/* translators: %s: formatted threshold amount */
			__( 'Complimentary shipping on orders over %s', 'base-theme' ),
			wp_strip_all_tags( wc_price( $threshold, array( 'decimals' => 0 ) ) )
		);
	}

	$messages[] = __( '30-day returns — no questions asked', 'base-theme' );
	$messages[] = __( 'New season has landed. Up to 20% off selected pieces', 'base-theme' );

	return $messages;
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

	if ( ! $lines ) {
		$threshold = myshop_free_shipping_threshold();
		$lines     = array(
			$threshold > 0 && function_exists( 'wc_price' )
				? sprintf(
					/* translators: %s: formatted threshold amount */
					__( 'Free delivery over %s', 'base-theme' ),
					wp_strip_all_tags( wc_price( $threshold, array( 'decimals' => 0 ) ) )
				)
				: __( 'Fast, tracked delivery', 'base-theme' ),
			__( '30-day returns, no questions asked', 'base-theme' ),
		);
	}

	$perks = array();

	foreach ( array_slice( array_values( $lines ), 0, 4 ) as $i => $line ) {
		$perks[] = array( $icons[ $i % count( $icons ) ], $line );
	}

	return $perks;
}

/**
 * Accepted-payment icon row (footer, cart summary).
 */
function myshop_payment_icons() {
	$chosen = (array) myshop_opt( 'payment_icons', array( 'visa', 'mastercard', 'amex', 'paypal', 'apple-pay' ) );

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
