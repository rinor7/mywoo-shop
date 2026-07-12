<?php
/**
 * Home — editorial promo banners.
 *
 * Copy and images come from "Frontpage Content → Promo banners".
 *
 * @package Base Theme
 */

defined( 'ABSPATH' ) || exit;

$shop = myshop_shop_url();
?>

<section class="section promo">
	<div class="shop-container">
		<div class="promo__grid">

			<a class="promo__card promo__card--lg reveal grain" href="<?php echo esc_url( myshop_c( 'promo1_url', $shop ) ); ?>"
				style="--a:#2A2F36;--b:#12151A">
				<span class="promo__art">
					<img src="<?php echo esc_url( myshop_c( 'promo1_image', myshop_placeholder( 'watch' ) ) ); ?>" alt="" loading="lazy" decoding="async">
				</span>
				<span class="promo__body">
					<span class="eyebrow eyebrow--light"><?php echo esc_html( myshop_c( 'promo1_eyebrow', __( 'Limited release', 'base-theme' ) ) ); ?></span>
					<span class="promo__title"><?php echo esc_html( myshop_c( 'promo1_title', __( 'The Meridian, back in stock', 'base-theme' ) ) ); ?></span>
					<span class="promo__text"><?php echo esc_html( myshop_c( 'promo1_text', __( 'Two hundred pieces. A 72-hour movement. No second run planned.', 'base-theme' ) ) ); ?></span>
					<span class="btn btn--light btn--sm"><?php echo esc_html( myshop_c( 'promo1_btn', __( 'Shop watches', 'base-theme' ) ) ); ?></span>
				</span>
			</a>

			<a class="promo__card promo__card--sm reveal grain" href="<?php echo esc_url( myshop_c( 'promo2_url', $shop ) ); ?>"
				style="--a:#F0E7DA;--b:#DCC7AB;--reveal-delay:100ms">
				<span class="promo__art">
					<img src="<?php echo esc_url( myshop_c( 'promo2_image', myshop_placeholder( 'candle' ) ) ); ?>" alt="" loading="lazy" decoding="async">
				</span>
				<span class="promo__body">
					<span class="eyebrow"><?php echo esc_html( myshop_c( 'promo2_eyebrow', __( 'Gifting', 'base-theme' ) ) ); ?></span>
					<span class="promo__title"><?php echo wp_kses_post( myshop_c( 'promo2_title', __( 'Under &euro;75', 'base-theme' ) ) ); ?></span>
					<span class="promo__text"><?php echo esc_html( myshop_c( 'promo2_text', __( 'Small things, wrapped well.', 'base-theme' ) ) ); ?></span>
					<span class="link-arrow"><?php echo esc_html( myshop_c( 'promo2_btn', __( 'Browse gifts', 'base-theme' ) ) ); ?> <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span>
				</span>
			</a>

		</div>
	</div>
</section>
