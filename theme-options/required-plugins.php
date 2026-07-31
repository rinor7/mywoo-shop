<?php
/**
 * Required plugins.
 *
 * Locks the plugins the shop depends on so they can't be deactivated from
 * the admin panel (security / stability): the "Deactivate" row link is
 * removed, and if one gets switched off any other way (bulk action,
 * WP-CLI, REST) it's silently reactivated on the next admin page load.
 */

if ( ! is_admin() ) {
	return;
}

/**
 * Main plugin file (relative to wp-content/plugins) for each required plugin.
 */
function myshop_required_plugins(): array {
	return array(
		'contact-form-7/wp-contact-form-7.php',
		'secure-custom-fields/secure-custom-fields.php',
		'woocommerce/woocommerce.php',
		'yith-woocommerce-wishlist/init.php',
		'wordpress-seo/wp-seo.php',
	);
}

add_filter(
	'plugin_action_links',
	function ( array $actions, string $plugin_file ): array {
		if ( in_array( $plugin_file, myshop_required_plugins(), true ) ) {
			unset( $actions['deactivate'] );
		}

		return $actions;
	},
	10,
	2
);

add_action(
	'admin_init',
	function (): void {
		foreach ( myshop_required_plugins() as $plugin_file ) {
			if ( ! is_plugin_active( $plugin_file ) && file_exists( WP_PLUGIN_DIR . '/' . $plugin_file ) ) {
				activate_plugin( $plugin_file );
			}
		}
	}
);
