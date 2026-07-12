<?php
/**
 * Home — customer reviews.
 *
 * @package Base Theme
 */

defined( 'ABSPATH' ) || exit;

$rows    = myshop_c( 'reviews', array() );
$reviews = array();

if ( $rows ) {
	foreach ( $rows as $row ) {
		if ( empty( $row['rv_quote'] ) ) {
			continue;
		}
		$reviews[] = array(
			'quote'   => $row['rv_quote'],
			'name'    => $row['rv_name'],
			'product' => $row['rv_product'],
			'rating'  => (float) $row['rv_rating'],
		);
	}
}

if ( empty( $reviews ) ) {
	$reviews = array(
		array(
			'quote'   => __( 'The tote arrived better than the photos. The leather already has a patina after a month and the stitching has not shifted at all.', 'base-theme' ),
			'name'    => __( 'Elena M.', 'base-theme' ),
			'product' => __( 'Aurelia Leather Tote', 'base-theme' ),
			'rating'  => 5,
		),
		array(
			'quote'   => __( 'I have owned three pairs of headphones at this price. These are the first I have not wanted to return. The cups actually fit.', 'base-theme' ),
			'name'    => __( 'Tomas R.', 'base-theme' ),
			'product' => __( 'Vale Wireless Headphones', 'base-theme' ),
			'rating'  => 5,
		),
		array(
			'quote'   => __( 'Ordered on Tuesday, wearing it Thursday. The packaging alone made it feel like a gift to myself.', 'base-theme' ),
			'name'    => __( 'Priya K.', 'base-theme' ),
			'product' => __( 'Reeve Cashmere Sweater', 'base-theme' ),
			'rating'  => 5,
		),
		array(
			'quote'   => __( 'Customer service replied in under an hour on a Sunday. That never happens. The chair is beautiful too.', 'base-theme' ),
			'name'    => __( 'Marcus L.', 'base-theme' ),
			'product' => __( 'Nordic Lounge Chair', 'base-theme' ),
			'rating'  => 4.5,
		),
	);
}
?>

<section class="section reviews">
	<div class="shop-container">

		<?php
		myshop_section_head(
			array(
				'eyebrow' => myshop_c( 'rev_eyebrow', __( 'Reviews', 'base-theme' ) ),
				'title'   => myshop_c( 'rev_title', __( 'Rated 4.9 by 2,400 customers', 'base-theme' ) ),
				'sub'     => myshop_c( 'rev_sub', __( 'We publish every review we receive — the four-star ones included.', 'base-theme' ) ),
				'center'  => true,
			)
		);
		?>

		<div class="reviews__carousel">
			<div class="swiper reviews__swiper js-reviews">
				<div class="swiper-wrapper">
					<?php foreach ( $reviews as $review ) : ?>
						<div class="swiper-slide">
							<figure class="review">
								<div class="review__stars"><?php echo myshop_stars( $review['rating'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>

								<blockquote class="review__quote"><?php echo esc_html( $review['quote'] ); ?></blockquote>

								<span class="review__product"><?php echo esc_html( $review['product'] ); ?></span>

								<figcaption class="review__foot">
									<span class="review__avatar" aria-hidden="true"><?php echo esc_html( mb_substr( $review['name'], 0, 1 ) ); ?></span>
									<span class="review__who">
										<strong><?php echo esc_html( $review['name'] ); ?></strong>
										<span class="review__verified">
											<i class="fa-solid fa-circle-check" aria-hidden="true"></i>
											<?php esc_html_e( 'Verified buyer', 'base-theme' ); ?>
										</span>
									</span>
								</figcaption>
							</figure>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<?php // Outside .swiper: its overflow:hidden would clip an absolutely-placed pagination. ?>
			<div class="reviews__pagination js-reviews-pagination"></div>
		</div>

	</div>
</section>
