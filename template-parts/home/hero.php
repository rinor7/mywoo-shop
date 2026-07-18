<?php
/**
 * Home — hero slider.
 *
 * Slides come from the "Frontpage Content → Hero" repeater when rows exist,
 * otherwise from the built-in demo slides below.
 *
 * @package Base Theme
 */

defined( 'ABSPATH' ) || exit;

$shop = myshop_shop_url();

$defaults = array(
	array(
		'eyebrow' => __( 'New Season', 'base-theme' ),
		'title'   => __( 'Objects made<br>to be lived with.', 'base-theme' ),
		'sub'     => __( 'A tightly edited collection of pieces built to outlast the season — and most of the ones after it.', 'base-theme' ),
		'image'   => myshop_placeholder( 'chair' ),
		'chip'    => __( 'From &euro;185', 'base-theme' ),
		'a'       => '#EFE8DC',
		'b'       => '#DACBB4',
		'btn1'    => array( __( 'Shop the collection', 'base-theme' ), $shop ),
		'btn2'    => array( __( 'Explore new in', 'base-theme' ), $shop ),
	),
	array(
		'eyebrow' => __( 'The Autumn Edit', 'base-theme' ),
		'title'   => __( 'Warmth,<br>in every layer.', 'base-theme' ),
		'sub'     => __( 'Two-ply cashmere, brushed wool and quiet colour. Made in small runs, restocked rarely.', 'base-theme' ),
		'image'   => myshop_placeholder( 'sweater' ),
		'chip'    => __( 'Only 40 made', 'base-theme' ),
		'a'       => '#E6EAEF',
		'b'       => '#C9D3DE',
		'btn1'    => array( __( 'Shop the collection', 'base-theme' ), $shop ),
		'btn2'    => array( __( 'Explore new in', 'base-theme' ), $shop ),
	),
	array(
		'eyebrow' => __( 'Sound', 'base-theme' ),
		'title'   => __( 'Quiet,<br>engineered.', 'base-theme' ),
		'sub'     => __( 'Forty hours of battery, adaptive cancelling and lambskin cups. The commute just got shorter.', 'base-theme' ),
		'image'   => myshop_placeholder( 'headphones' ),
		'chip'    => __( 'Free engraving', 'base-theme' ),
		'a'       => '#E9E7E4',
		'b'       => '#CFCCC6',
		'btn1'    => array( __( 'Shop the collection', 'base-theme' ), $shop ),
		'btn2'    => array( __( 'Explore new in', 'base-theme' ), $shop ),
	),
);

$rows   = myshop_c( 'hero_slides', array() );
$slides = array();

if ( $rows ) {
	// Rows without an image reuse the demo art in rotation.
	$art = array( 'chair', 'sweater', 'headphones', 'bag', 'watch', 'lamp' );

	foreach ( $rows as $i => $row ) {
		$slides[] = array(
			'eyebrow' => $row['hs_eyebrow'],
			'title'   => nl2br( esc_html( $row['hs_title'] ) ),
			'sub'     => $row['hs_text'],
			'image'   => $row['hs_image'] ? $row['hs_image'] : myshop_placeholder( $art[ $i % count( $art ) ] ),
			'chip'    => $row['hs_chip'],
			'a'       => $row['hs_color_a'] ? $row['hs_color_a'] : '#EFE8DC',
			'b'       => $row['hs_color_b'] ? $row['hs_color_b'] : '#DACBB4',
			'btn1'    => array( $row['hs_btn1_label'], $row['hs_btn1_url'] ? $row['hs_btn1_url'] : $shop ),
			'btn2'    => array( $row['hs_btn2_label'], $row['hs_btn2_url'] ? $row['hs_btn2_url'] : $shop ),
		);
	}
} else {
	$slides = $defaults;
}
?>

<section class="hero">
	<div class="hero__wrap">
		<div class="swiper hero__swiper js-hero">
			<div class="swiper-wrapper">

					<?php foreach ( $slides as $slide ) : ?>
						<div class="swiper-slide hero__slide grain" style="--slide-a:<?php echo esc_attr( $slide['a'] ); ?>;--slide-b:<?php echo esc_attr( $slide['b'] ); ?>">
							<?php // Background runs full width; content stays in the container. ?>
							<div class="shop-container hero__inner">

								<div class="hero__copy">
									<?php if ( $slide['eyebrow'] ) : ?>
										<span class="eyebrow"><?php echo esc_html( $slide['eyebrow'] ); ?></span>
									<?php endif; ?>

									<h1 class="hero__title"><?php echo wp_kses_post( $slide['title'] ); ?></h1>

									<?php if ( $slide['sub'] ) : ?>
										<p class="hero__sub"><?php echo esc_html( $slide['sub'] ); ?></p>
									<?php endif; ?>

									<div class="hero__cta">
										<?php if ( ! empty( $slide['btn1'][0] ) ) : ?>
											<a class="btn btn--primary" href="<?php echo esc_url( $slide['btn1'][1] ); ?>">
												<?php echo esc_html( $slide['btn1'][0] ); ?>
											</a>
										<?php endif; ?>
										<?php if ( ! empty( $slide['btn2'][0] ) ) : ?>
											<a class="btn btn--ghost" href="<?php echo esc_url( $slide['btn2'][1] ); ?>">
												<?php echo esc_html( $slide['btn2'][0] ); ?>
											</a>
										<?php endif; ?>
									</div>

									<ul class="hero__meta">
										<?php foreach ( array_slice( myshop_pdp_perks(), 0, 2 ) as $perk ) : ?>
											<li><i class="fa-solid <?php echo esc_attr( $perk[0] ); ?>" aria-hidden="true"></i> <?php echo esc_html( $perk[1] ); ?></li>
										<?php endforeach; ?>
									</ul>
								</div>

								<div class="hero__media">
									<div class="hero__art">
										<img src="<?php echo esc_url( $slide['image'] ); ?>" alt="" width="600" height="750" fetchpriority="high" decoding="async">
									</div>

									<?php if ( $slide['chip'] ) : ?>
										<span class="hero__chip hero__chip--price"><?php echo wp_kses_post( $slide['chip'] ); ?></span>
									<?php endif; ?>

									<span class="hero__chip hero__chip--rating">
										<?php echo myshop_stars( 5 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
										<em><?php esc_html_e( '2,400+ reviews', 'base-theme' ); ?></em>
									</span>
								</div>

							</div>
						</div>
					<?php endforeach; ?>

			</div>
		</div>

		<div class="hero__controls">
			<div class="shop-container hero__controls-inner">
				<div class="hero__pagination js-hero-pagination"></div>
				<div class="hero__arrows">
					<button type="button" class="hero__arrow js-hero-prev" aria-label="<?php esc_attr_e( 'Previous slide', 'base-theme' ); ?>">
						<i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
					</button>
					<button type="button" class="hero__arrow js-hero-next" aria-label="<?php esc_attr_e( 'Next slide', 'base-theme' ); ?>">
						<i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
					</button>
				</div>
			</div>
		</div>
	</div>
</section>
