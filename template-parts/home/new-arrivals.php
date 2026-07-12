<?php
/**
 * Home — new arrivals carousel.
 *
 * @package Base Theme
 */

defined( 'ABSPATH' ) || exit;

$products = myshop_get_products(
	array(
		'limit' => 8,
		'type'  => 'recent',
	)
);

if ( empty( $products ) ) {
	return;
}
?>

<section class="section products">
	<div class="shop-container">

		<?php
		myshop_section_head(
			array(
				'eyebrow'   => myshop_c( 'na_eyebrow', __( 'Just landed', 'base-theme' ) ),
				'title'     => myshop_c( 'na_title', __( 'New arrivals', 'base-theme' ) ),
				'sub'       => myshop_c( 'na_sub', __( 'The newest additions to the shelf. Small batches — when they are gone, they are gone.', 'base-theme' ) ),
				'link_url'  => myshop_shop_url(),
				'link_text' => __( 'Shop all new in', 'base-theme' ),
			)
		);
		?>

		<div class="products__carousel">
			<div class="swiper js-product-slider">
				<div class="swiper-wrapper">
					<?php foreach ( $products as $i => $product ) : ?>
						<div class="swiper-slide">
							<?php myshop_product_card( $product, $i ); ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<button type="button" class="products__arrow products__arrow--prev js-product-prev" aria-label="<?php esc_attr_e( 'Previous products', 'base-theme' ); ?>">
				<i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
			</button>
			<button type="button" class="products__arrow products__arrow--next js-product-next" aria-label="<?php esc_attr_e( 'Next products', 'base-theme' ); ?>">
				<i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
			</button>
		</div>

		<div class="products__progress">
			<span class="products__progress-bar js-product-progress"></span>
		</div>

	</div>
</section>
