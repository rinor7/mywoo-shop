<?php
/**
 * WooCommerce theme setup.
 *
 * Declares support, sizes the product images to a 4:5 editorial crop and swaps
 * WooCommerce's default page wrappers for the theme's own container.
 *
 * @package Base Theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Theme support.
 */
function myshop_theme_support() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );

	add_theme_support(
		'woocommerce',
		array(
			'thumbnail_image_width' => 600,
			'single_image_width'    => 1000,
			'product_grid'          => array(
				'default_rows'    => 3,
				'min_rows'        => 1,
				'default_columns' => 4,
				'min_columns'     => 2,
				'max_columns'     => 5,
			),
		)
	);

	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'myshop_theme_support' );

/**
 * Force a 4:5 portrait crop on catalogue thumbnails — the proportion premium
 * fashion/lifestyle stores use. Without this the crop follows whatever the
 * customer uploaded and grids go ragged.
 */
function myshop_thumbnail_size( $size ) {
	return array(
		'width'  => 600,
		'height' => 750,
		'crop'   => 1,
	);
}
add_filter( 'woocommerce_get_image_size_thumbnail', 'myshop_thumbnail_size' );

/**
 * Replace WooCommerce's wrappers with the theme's own.
 */
function myshop_woocommerce_wrappers() {
	remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
	remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
	remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
}
add_action( 'init', 'myshop_woocommerce_wrappers' );

function myshop_wrapper_start() {
	echo '<main id="primary" class="site-main woo-main"><div class="shop-container">';
}
add_action( 'woocommerce_before_main_content', 'myshop_wrapper_start', 10 );

function myshop_wrapper_end() {
	echo '</div></main>';
}
add_action( 'woocommerce_after_main_content', 'myshop_wrapper_end', 10 );

/**
 * Products per page on the catalogue.
 */
function myshop_products_per_page() {
	return 12;
}
add_filter( 'loop_shop_per_page', 'myshop_products_per_page', 20 );
