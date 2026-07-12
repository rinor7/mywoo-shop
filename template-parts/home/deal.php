<?php
/**
 * Home — deal of the week with a live countdown.
 *
 * The countdown ends at the coming Sunday midnight, so the demo never shows an
 * expired timer.
 *
 * @package Base Theme
 */

defined( 'ABSPATH' ) || exit;

$deal = null;

// Admin-picked product wins; otherwise the newest sale product (or demo).
$picked = (int) myshop_c( 'deal_product', 0 );
if ( $picked && function_exists( 'wc_get_product' ) ) {
	$product = wc_get_product( $picked );
	if ( $product && 'publish' === $product->get_status() ) {
		$deal = myshop_normalize_product( $product );
	}
}

if ( ! $deal ) {
	$deals = myshop_get_products(
		array(
			'limit' => 1,
			'type'  => 'sale',
		)
	);
	if ( empty( $deals ) ) {
		return;
	}
	$deal = $deals[0];
}

$ends = strtotime( (string) myshop_c( 'deal_ends', '' ) );
if ( ! $ends || $ends <= current_time( 'timestamp' ) ) {
	$ends = strtotime( 'next sunday 23:59:59', current_time( 'timestamp' ) );
}

$sold  = (int) myshop_c( 'deal_sold', 32 );
$total = max( $sold, (int) myshop_c( 'deal_total', 50 ), 1 );
?>

<section class="section deal grain">
	<div class="shop-container deal__inner">

		<div class="deal__media reveal">
			<div class="deal__art">
				<img src="<?php echo esc_url( $deal['image'] ); ?>" alt="" width="600" height="750" loading="lazy" decoding="async">
			</div>
			<?php if ( ! empty( $deal['badge'] ) ) : ?>
				<span class="deal__badge"><?php echo esc_html( $deal['badge']['label'] ); ?></span>
			<?php endif; ?>
		</div>

		<div class="deal__body reveal" style="--reveal-delay:120ms">
			<span class="eyebrow eyebrow--light"><?php esc_html_e( 'Deal of the week', 'base-theme' ); ?></span>

			<h2 class="deal__title"><?php echo esc_html( $deal['name'] ); ?></h2>

			<?php if ( ! empty( $deal['excerpt'] ) ) : ?>
				<p class="deal__text"><?php echo esc_html( $deal['excerpt'] ); ?></p>
			<?php endif; ?>

			<div class="deal__price price"><?php echo wp_kses_post( $deal['price_html'] ); ?></div>

			<div class="deal__timer js-countdown" data-ends="<?php echo esc_attr( $ends ); ?>">
				<div class="deal__unit"><strong class="js-cd-days">00</strong><span><?php esc_html_e( 'Days', 'base-theme' ); ?></span></div>
				<div class="deal__unit"><strong class="js-cd-hours">00</strong><span><?php esc_html_e( 'Hours', 'base-theme' ); ?></span></div>
				<div class="deal__unit"><strong class="js-cd-mins">00</strong><span><?php esc_html_e( 'Minutes', 'base-theme' ); ?></span></div>
				<div class="deal__unit"><strong class="js-cd-secs">00</strong><span><?php esc_html_e( 'Seconds', 'base-theme' ); ?></span></div>
			</div>

			<div class="deal__stock">
				<div class="deal__stock-track">
					<span class="deal__stock-bar" style="width:<?php echo esc_attr( round( $sold / $total * 100 ) ); ?>%"></span>
				</div>
				<p class="deal__stock-label">
					<?php
					printf(
						/* translators: 1: units sold, 2: total units */
						esc_html__( 'Sold: %1$d of %2$d — going fast', 'base-theme' ),
						(int) $sold,
						(int) $total
					);
					?>
				</p>
			</div>

			<div class="deal__cta">
				<?php if ( ! empty( $deal['purchasable'] ) ) : ?>
					<button type="button" class="btn btn--accent js-add-to-cart"
						data-product-id="<?php echo esc_attr( $deal['id'] ); ?>">
						<i class="fa-solid fa-bag-shopping" aria-hidden="true"></i>
						<span><?php esc_html_e( 'Add to bag', 'base-theme' ); ?></span>
					</button>
				<?php elseif ( empty( $deal['is_demo'] ) ) : ?>
					<a class="btn btn--accent" href="<?php echo esc_url( $deal['permalink'] ); ?>">
						<i class="fa-solid fa-sliders" aria-hidden="true"></i>
						<?php esc_html_e( 'Choose options', 'base-theme' ); ?>
					</a>
				<?php else : ?>
					<button type="button" class="btn btn--accent js-add-to-cart" data-demo="1"
						data-product-id="<?php echo esc_attr( $deal['id'] ); ?>">
						<i class="fa-solid fa-bag-shopping" aria-hidden="true"></i>
						<span><?php esc_html_e( 'Add to bag', 'base-theme' ); ?></span>
					</button>
				<?php endif; ?>
				<a class="btn btn--outline-light" href="<?php echo esc_url( $deal['permalink'] ); ?>">
					<?php esc_html_e( 'View details', 'base-theme' ); ?>
				</a>
			</div>
		</div>

	</div>
</section>
