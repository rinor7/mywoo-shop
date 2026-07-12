<?php
/**
 * @package Standard
 */

function standard_scripts_and_style() {
	wp_enqueue_style( 'base-theme-style', get_stylesheet_uri() );
	wp_enqueue_style( 'base-theme-bootstrap-style', get_template_directory_uri() . '/assets/css/libs/bootstrap.min.css', array(), null );
	wp_enqueue_style( 'base-theme-swiper-style', get_template_directory_uri() . '/assets/css/libs/swiper.css', array(), null );
	wp_enqueue_style( 'base-theme-awesome-style', get_template_directory_uri() . '/assets/css/libs/fontawesome/css/all.css', array(), null );

	// Theme styles load last so they win over Bootstrap and WooCommerce defaults.
	wp_enqueue_style(
		'base-theme-main-style',
		get_template_directory_uri() . '/assets/css/style.min.css',
		array( 'base-theme-bootstrap-style' ),
		filemtime( get_template_directory() . '/assets/css/style.min.css' )
	);

	wp_enqueue_script( 'base-theme-jquery-js', get_template_directory_uri() . '/assets/js/libs/jquery.js', array(), null, false );
	wp_enqueue_script( 'bootstrap-js', get_template_directory_uri() . '/assets/js/libs/bootstrap.min.js', array(), null, true );
	wp_enqueue_script( 'base-theme-swiper-js', get_template_directory_uri() . '/assets/js/libs/swiper.js', array(), null, true );
	wp_enqueue_script(
		'base-theme-main-js',
		get_template_directory_uri() . '/assets/js/main.min.js',
		array( 'base-theme-jquery-js', 'base-theme-swiper-js' ),
		filemtime( get_template_directory() . '/assets/js/main.min.js' ),
		true
	);

	wp_localize_script(
		'base-theme-main-js',
		'MyShop',
		array(
			// WooCommerce's own AJAX endpoints; '%%endpoint%%' is swapped in JS.
			'wcAjax'      => class_exists( 'WC_AJAX' ) ? WC_AJAX::get_endpoint( '%%endpoint%%' ) : '',
			'cartUrl'     => function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '',
			'checkoutUrl' => function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : '',
			// YITH wishlist bridge ('' / false when the plugin is off).
			'yith'        => function_exists( 'myshop_yith_active' ) ? myshop_yith_active() : false,
			'wishlistUrl' => function_exists( 'myshop_wishlist_url' ) ? myshop_wishlist_url() : '',
			'yithAdd'     => function_exists( 'myshop_yith_active' ) && myshop_yith_active()
				? esc_url_raw( add_query_arg( 'add_to_wishlist', '__ID__', home_url( '/' ) ) )
				: '',
			'i18n'        => array(
				'added'         => __( 'Added to your bag', 'base-theme' ),
				'removed'       => __( 'Item removed', 'base-theme' ),
				'error'         => __( 'Something went wrong. Please try again.', 'base-theme' ),
				'demo'          => __( 'Demo product — publish products in WooCommerce to enable checkout.', 'base-theme' ),
				'saved'         => __( 'Saved to wishlist', 'base-theme' ),
				'unsaved'       => __( 'Removed from wishlist', 'base-theme' ),
				'addToBag'      => __( 'Add to bag', 'base-theme' ),
				'chooseOptions' => __( 'Choose options', 'base-theme' ),
			),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'standard_scripts_and_style' );

// Storefront layer. Self-contained: nothing here depends on includes/blocks or
// includes/flexible, so those can be removed without touching the shop.
require get_template_directory() . '/includes/shop/setup.php';
require get_template_directory() . '/includes/shop/helpers.php';
require get_template_directory() . '/includes/shop/template-tags.php';
require get_template_directory() . '/includes/shop/cart.php';
require get_template_directory() . '/includes/shop/section-toggles.php';
require get_template_directory() . '/includes/shop/frontpage-fields.php';
require get_template_directory() . '/includes/shop/woo-pages.php';
require get_template_directory() . '/includes/shop/product-single.php';
require get_template_directory() . '/includes/shop/account.php';

// Include files from the 'theme-options' directory
require get_template_directory() . '/theme-options/global-colors.php';
require get_template_directory() . '/theme-options/post-types.php';
require get_template_directory() . '/theme-options/menus.php';
require get_template_directory() . '/theme-options/site-identity.php';
require get_template_directory() . '/theme-options/acf-relocate.php';
require get_template_directory() . '/theme-options/widgets.php';
require get_template_directory() . '/theme-options/enable-php-on-widgets.php';
require get_template_directory() . '/theme-options/container-admin-customize.php';
// require get_template_directory() . '/theme-options/acf-navigation-background.php';
require get_template_directory() . '/theme-options/general-functions.php';
require get_template_directory() . '/theme-options/taxonomies.php';
require get_template_directory() . '/theme-options/update-alt-tags.php';
require get_template_directory() . '/theme-options/global-email-phone.php';
