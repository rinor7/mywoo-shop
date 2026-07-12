<?php
/**
 * Product loop item — routes through the theme's card renderer so shop
 * archives, sliders and the frontpage all share one card.
 *
 * @package Base Theme
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

static $myshop_loop_index = 0;

myshop_product_card( myshop_normalize_product( $product ), $myshop_loop_index++ );
