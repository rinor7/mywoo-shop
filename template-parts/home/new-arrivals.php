<?php
/**
 * Home — new arrivals carousel.
 *
 * @package Base Theme
 */

defined( 'ABSPATH' ) || exit;

// Manual picks (Frontpage Content → New arrivals → Custom products) win when
// at least one is set; otherwise fall back to the latest arrivals.
$products = array();
$picks    = myshop_c( 'na_products', array() );

if ( ! empty( $picks ) && function_exists( 'wc_get_product' ) ) {
	foreach ( $picks as $row ) {
		$picked_id = isset( $row['na_product'] ) ? (int) $row['na_product'] : 0;
		if ( ! $picked_id ) {
			continue;
		}
		$picked_product = wc_get_product( $picked_id );
		if ( $picked_product ) {
			$products[] = myshop_normalize_product( $picked_product );
		}
	}
}

if ( empty( $products ) ) {
	$products = myshop_get_products(
		array(
			'limit' => 8,
			'type'  => 'recent',
		)
	);
}

if ( empty( $products ) ) {
	return;
}

// Admin's color/gradient (Frontpage → New arrivals) overrides the sitewide
// product-box background just for the cards in this section.
$card_bg_type = myshop_c( 'na_card_bg_type', '' );
$card_bg      = 'color' === $card_bg_type ? myshop_c( 'na_card_bg_color', '' ) : ( 'gradient' === $card_bg_type ? myshop_c( 'na_card_bg_css', '' ) : '' );
?>

<section class="section products">
	<div class="shop-container">

		<?php
		$btn_enabled = true;
		if ( function_exists( 'get_field' ) ) {
			$raw = get_field( 'na_btn_enabled', myshop_front_id() );
			if ( '' !== $raw && null !== $raw ) {
				$btn_enabled = (bool) $raw;
			}
		}

		// Label and URL travel together in one ACF "link" field — empty =
		// today's default ("Shop all new in" straight to the shop page).
		$btn_link   = myshop_c( 'na_btn_link', array() );
		$btn_url    = ! empty( $btn_link['url'] ) ? $btn_link['url'] : myshop_shop_url();
		$btn_label  = ! empty( $btn_link['title'] ) ? $btn_link['title'] : __( 'Shop all new in', 'base-theme' );
		$btn_target = ! empty( $btn_link['target'] ) ? $btn_link['target'] : '';

		myshop_section_head(
			array(
				'eyebrow'     => myshop_c( 'na_eyebrow' ),
				'title'       => myshop_c( 'na_title' ),
				'sub'         => myshop_c( 'na_sub' ),
				'link_url'    => $btn_enabled ? $btn_url : '',
				'link_text'   => $btn_enabled ? $btn_label : '',
				'link_target' => $btn_enabled ? $btn_target : '',
			)
		);
		?>

		<div class="products__carousel">
			<div class="swiper js-product-slider">
				<div class="swiper-wrapper">
					<?php foreach ( $products as $i => $product ) : ?>
						<div class="swiper-slide">
							<?php myshop_product_card( $product, $i, 'minimal', $card_bg ); ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<div class="products__progress">
			<span class="products__progress-bar js-product-progress"></span>
		</div>

	</div>
</section>
