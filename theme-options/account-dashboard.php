<?php
/**
 * Global Settings → Account Dashboard
 *
 * Every string on the "My account" dashboard (greeting, the four bento
 * cards, the curated-products strip) plus a per-card enable/disable
 * switch. Empty field = that piece of text (or the whole card) renders
 * nothing, same rule as the rest of the site — no code-level fallback
 * strings, only ACF default_value so untouched installs look unchanged.
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

		$parent = 'group_683a451131cc8';
		$half   = array( 'wrapper' => array( 'width' => '50' ) );

		// myshop_f() alone doesn't set 'parent' — it's meant for fields nested
		// inside a acf_add_local_field_group() 'fields' array, where parentage
		// is inferred from nesting. Here each field is registered individually
		// against the existing Global Settings group, so it needs 'parent'
		// explicitly or ACF has nowhere to attach it (and silently drops it).
		$field = function ( $name, $label, $type = 'text', $extra = array() ) use ( $parent ) {
			return array_merge( myshop_f( $name, $label, $type, $extra ), array( 'parent' => $parent ) );
		};

		acf_add_local_field(
			array(
				'key'       => 'field_ms_tab_account',
				'parent'    => $parent,
				'label'     => __( 'Account Dashboard', 'base-theme' ),
				'type'      => 'tab',
				'placement' => 'top',
			)
		);

		/* ---------- Greeting ---------- */

		acf_add_local_field(
			$field( 'acct_greeting_title', __( 'Greeting — title', 'base-theme' ), 'text', array(
				'default_value' => __( 'Welcome home, {name}.', 'base-theme' ),
				'instructions'  => __( 'Top of the account dashboard. {name} is replaced with the customer\'s first name. Leave empty to hide.', 'base-theme' ),
			) )
		);

		acf_add_local_field(
			$field( 'acct_greeting_text', __( 'Greeting — text', 'base-theme' ), 'textarea', array(
				'rows'          => 2,
				'default_value' => __( 'From your dashboard you can review your <a href="{orders_url}">recent orders</a>, manage your <a href="{addresses_url}">delivery addresses</a>, or refine your <a href="{account_url}">account details</a>.', 'base-theme' ),
				'instructions'  => __( 'Basic HTML allowed. Tokens available: {orders_url}, {addresses_url}, {account_url}. Leave empty to hide.', 'base-theme' ),
			) )
		);

		/* ---------- Card: Latest Acquisitions ---------- */

		acf_add_local_field(
			$field( 'acct_orders_enabled', __( 'Show "Latest Acquisitions" card', 'base-theme' ), 'true_false', array(
				'ui'            => 1,
				'default_value' => 1,
			) )
		);

		acf_add_local_field(
			$field( 'acct_orders_title', __( 'Title', 'base-theme' ), 'text', array(
				'default_value'     => __( 'Latest Acquisitions', 'base-theme' ),
				'conditional_logic' => array( array( array( 'field' => 'field_ms_acct_orders_enabled', 'operator' => '==', 'value' => '1' ) ) ),
			) )
		);

		acf_add_local_field(
			$field( 'acct_orders_text', __( 'Text — has orders', 'base-theme' ), 'text', array(
				'default_value'     => __( 'You have {count} recent orders in your history.', 'base-theme' ),
				'instructions'      => __( '{count} is replaced with the number of recent orders.', 'base-theme' ),
				'conditional_logic' => array( array( array( 'field' => 'field_ms_acct_orders_enabled', 'operator' => '==', 'value' => '1' ) ) ),
			) )
		);

		acf_add_local_field(
			$field( 'acct_orders_empty_text', __( 'Text — no orders yet', 'base-theme' ), 'text', array(
				'default_value'     => __( 'No orders yet — your first acquisition awaits.', 'base-theme' ),
				'conditional_logic' => array( array( array( 'field' => 'field_ms_acct_orders_enabled', 'operator' => '==', 'value' => '1' ) ) ),
			) )
		);

		acf_add_local_field(
			$field( 'acct_orders_btn', __( 'Button — has orders', 'base-theme' ), 'text', array_merge( $half, array(
				'default_value'     => __( 'View order history', 'base-theme' ),
				'conditional_logic' => array( array( array( 'field' => 'field_ms_acct_orders_enabled', 'operator' => '==', 'value' => '1' ) ) ),
			) ) )
		);

		acf_add_local_field(
			$field( 'acct_orders_empty_btn', __( 'Button — no orders yet', 'base-theme' ), 'text', array_merge( $half, array(
				'default_value'     => __( 'Start shopping', 'base-theme' ),
				'conditional_logic' => array( array( array( 'field' => 'field_ms_acct_orders_enabled', 'operator' => '==', 'value' => '1' ) ) ),
			) ) )
		);

		/* ---------- Card: Privacy & Security ---------- */

		acf_add_local_field(
			$field( 'acct_security_enabled', __( 'Show "Privacy & Security" card', 'base-theme' ), 'true_false', array(
				'ui'            => 1,
				'default_value' => 1,
			) )
		);

		acf_add_local_field(
			$field( 'acct_security_title', __( 'Title', 'base-theme' ), 'text', array_merge( $half, array(
				'default_value'     => __( 'Privacy & Security', 'base-theme' ),
				'conditional_logic' => array( array( array( 'field' => 'field_ms_acct_security_enabled', 'operator' => '==', 'value' => '1' ) ) ),
			) ) )
		);

		acf_add_local_field(
			$field( 'acct_security_btn', __( 'Button label', 'base-theme' ), 'text', array_merge( $half, array(
				'default_value'     => __( 'Security settings', 'base-theme' ),
				'conditional_logic' => array( array( array( 'field' => 'field_ms_acct_security_enabled', 'operator' => '==', 'value' => '1' ) ) ),
			) ) )
		);

		acf_add_local_field(
			$field( 'acct_security_text', __( 'Text', 'base-theme' ), 'text', array(
				'default_value'     => __( 'Signed in as {email}. Keep your password fresh and yours alone.', 'base-theme' ),
				'instructions'      => __( '{email} is replaced with the account email.', 'base-theme' ),
				'conditional_logic' => array( array( array( 'field' => 'field_ms_acct_security_enabled', 'operator' => '==', 'value' => '1' ) ) ),
			) )
		);

		/* ---------- Card: Default Shipping ---------- */

		acf_add_local_field(
			$field( 'acct_shipping_enabled', __( 'Show "Default Shipping" card', 'base-theme' ), 'true_false', array(
				'ui'            => 1,
				'default_value' => 1,
			) )
		);

		acf_add_local_field(
			$field( 'acct_shipping_title', __( 'Title', 'base-theme' ), 'text', array(
				'default_value'     => __( 'Default Shipping', 'base-theme' ),
				'conditional_logic' => array( array( array( 'field' => 'field_ms_acct_shipping_enabled', 'operator' => '==', 'value' => '1' ) ) ),
			) )
		);

		acf_add_local_field(
			$field( 'acct_shipping_empty_text', __( 'Text — no address saved', 'base-theme' ), 'text', array(
				'default_value'     => __( 'No address saved yet.', 'base-theme' ),
				'conditional_logic' => array( array( array( 'field' => 'field_ms_acct_shipping_enabled', 'operator' => '==', 'value' => '1' ) ) ),
			) )
		);

		acf_add_local_field(
			$field( 'acct_shipping_btn', __( 'Button — has address', 'base-theme' ), 'text', array_merge( $half, array(
				'default_value'     => __( 'Manage addresses', 'base-theme' ),
				'conditional_logic' => array( array( array( 'field' => 'field_ms_acct_shipping_enabled', 'operator' => '==', 'value' => '1' ) ) ),
			) ) )
		);

		acf_add_local_field(
			$field( 'acct_shipping_empty_btn', __( 'Button — no address', 'base-theme' ), 'text', array_merge( $half, array(
				'default_value'     => __( 'Add address', 'base-theme' ),
				'conditional_logic' => array( array( array( 'field' => 'field_ms_acct_shipping_enabled', 'operator' => '==', 'value' => '1' ) ) ),
			) ) )
		);

		/* ---------- Card: Membership ---------- */

		acf_add_local_field(
			$field( 'acct_membership_enabled', __( 'Show "Membership" card', 'base-theme' ), 'true_false', array(
				'ui'            => 1,
				'default_value' => 1,
			) )
		);

		acf_add_local_field(
			$field( 'acct_membership_eyebrow', __( 'Eyebrow', 'base-theme' ), 'text', array_merge( $half, array(
				'default_value'     => __( 'Membership', 'base-theme' ),
				'conditional_logic' => array( array( array( 'field' => 'field_ms_acct_membership_enabled', 'operator' => '==', 'value' => '1' ) ) ),
			) ) )
		);

		acf_add_local_field(
			$field( 'acct_membership_title', __( 'Title', 'base-theme' ), 'text', array_merge( $half, array(
				'default_value'     => __( 'The Inner Circle', 'base-theme' ),
				'conditional_logic' => array( array( array( 'field' => 'field_ms_acct_membership_enabled', 'operator' => '==', 'value' => '1' ) ) ),
			) ) )
		);

		acf_add_local_field(
			$field( 'acct_membership_text', __( 'Text — perks set', 'base-theme' ), 'text', array(
				'default_value'     => __( '{perks} and first access to limited releases — yours with every order.', 'base-theme' ),
				'instructions'      => __( '{perks} is replaced with the perk list from Global Settings → Single Product ("Product page perks"), comma-separated.', 'base-theme' ),
				'conditional_logic' => array( array( array( 'field' => 'field_ms_acct_membership_enabled', 'operator' => '==', 'value' => '1' ) ) ),
			) )
		);

		acf_add_local_field(
			$field( 'acct_membership_empty_text', __( 'Text — no perks set', 'base-theme' ), 'text', array(
				'default_value'     => __( 'First access to limited releases — yours with every order.', 'base-theme' ),
				'conditional_logic' => array( array( array( 'field' => 'field_ms_acct_membership_enabled', 'operator' => '==', 'value' => '1' ) ) ),
			) )
		);

		acf_add_local_field(
			$field( 'acct_membership_btn', __( 'Button label', 'base-theme' ), 'text', array(
				'default_value'     => __( 'Explore the collection', 'base-theme' ),
				'conditional_logic' => array( array( array( 'field' => 'field_ms_acct_membership_enabled', 'operator' => '==', 'value' => '1' ) ) ),
			) )
		);

		/* ---------- Curated products ---------- */

		acf_add_local_field(
			$field( 'acct_curated_enabled', __( 'Show "Curated for you" section', 'base-theme' ), 'true_false', array(
				'ui'            => 1,
				'default_value' => 1,
				'instructions'  => __( 'Also hides itself automatically if there are no bestseller products to show.', 'base-theme' ),
			) )
		);

		acf_add_local_field(
			$field( 'acct_curated_title', __( 'Title', 'base-theme' ), 'text', array_merge( $half, array(
				'default_value'     => __( 'Curated for you', 'base-theme' ),
				'conditional_logic' => array( array( array( 'field' => 'field_ms_acct_curated_enabled', 'operator' => '==', 'value' => '1' ) ) ),
			) ) )
		);

		acf_add_local_field(
			$field( 'acct_curated_btn', __( 'Button label', 'base-theme' ), 'text', array_merge( $half, array(
				'default_value'     => __( 'View all', 'base-theme' ),
				'conditional_logic' => array( array( array( 'field' => 'field_ms_acct_curated_enabled', 'operator' => '==', 'value' => '1' ) ) ),
			) ) )
		);

		acf_add_local_field(
			$field( 'acct_curated_subtitle', __( 'Subtitle', 'base-theme' ), 'text', array(
				'default_value'     => __( 'Hand-selected pieces from the current collection.', 'base-theme' ),
				'conditional_logic' => array( array( array( 'field' => 'field_ms_acct_curated_enabled', 'operator' => '==', 'value' => '1' ) ) ),
			) )
		);

		acf_add_local_field(
			$field( 'acct_curated_products', __( '"Curated for you" — pick specific products', 'base-theme' ), 'relationship', array(
				'post_type'         => array( 'product' ),
				'filters'           => array( 'search' ),
				'return_format'     => 'id',
				'instructions'      => __( 'Always show these products, in this order. Leave empty to keep the automatic pick (current bestsellers).', 'base-theme' ),
				'conditional_logic' => array( array( array( 'field' => 'field_ms_acct_curated_enabled', 'operator' => '==', 'value' => '1' ) ) ),
			) )
		);
	}
);

/* ---------- Helpers ---------- */

function myshop_account_dashboard_field( $name, $default = '' ) {
	return function_exists( 'myshop_opt' ) ? (string) myshop_opt( $name, $default ) : $default;
}

function myshop_account_greeting_title( $name ) {
	$text = myshop_account_dashboard_field( 'acct_greeting_title' );
	return $text ? str_replace( '{name}', $name, $text ) : '';
}

function myshop_account_greeting_text( $urls ) {
	$text = myshop_account_dashboard_field( 'acct_greeting_text' );
	if ( ! $text ) {
		return '';
	}
	return str_replace(
		array( '{orders_url}', '{addresses_url}', '{account_url}' ),
		array( $urls['orders'], $urls['addresses'], $urls['account'] ),
		$text
	);
}

function myshop_account_card_enabled( $name ) {
	return (bool) ( function_exists( 'myshop_opt' ) ? myshop_opt( $name, 1 ) : 1 );
}

/**
 * Manually-picked products for the account dashboard's "Curated for you"
 * strip. Empty = let the caller fall back to its automatic pick.
 *
 * @return int[] Product IDs.
 */
function myshop_account_curated_product_ids() {
	$ids = myshop_opt( 'acct_curated_products', array() );

	return is_array( $ids ) ? array_map( 'intval', $ids ) : array();
}
