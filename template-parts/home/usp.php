<?php
/**
 * Home — trust / service bar.
 *
 * Items come from "Frontpage Content → Trust bar" — no rows, no section.
 *
 * @package Base Theme
 */

defined( 'ABSPATH' ) || exit;

$rows  = myshop_c( 'usp_items', array() );
$items = array();

if ( $rows ) {
	foreach ( $rows as $row ) {
		$items[] = array(
			$row['usp_icon'] ? $row['usp_icon'] : 'fa-circle-check',
			$row['usp_title'],
			$row['usp_text'],
		);
	}
}

if ( ! $items ) {
	return;
}
?>

<section class="usp">
	<div class="shop-container">
		<ul class="usp__list">
			<?php foreach ( $items as $i => $item ) : ?>
				<li class="usp__item reveal" style="--reveal-delay:<?php echo (int) ( $i * 70 ); ?>ms">
					<span class="usp__icon"><i class="fa-solid <?php echo esc_attr( $item[0] ); ?>" aria-hidden="true"></i></span>
					<span class="usp__body">
						<strong class="usp__title"><?php echo esc_html( $item[1] ); ?></strong>
						<span class="usp__text"><?php echo wp_kses_post( $item[2] ); ?></span>
					</span>
				</li>
			<?php endforeach; ?>
		</ul>

		<!-- Mobile only: same items, laid out as slides instead of stacking —
			 swipeable row like the other mobile carousels (.js-usp-slider), not
			 an auto-scrolling marquee, so the trust copy stays readable. No
			 progress bar here — only 4 short items, the peeking next item is
			 signal enough that there's more to swipe to. -->
		<div class="products__carousel">
			<div class="swiper js-usp-slider">
				<div class="swiper-wrapper">
					<?php foreach ( $items as $i => $item ) : ?>
						<div class="swiper-slide">
							<span class="usp__item">
								<span class="usp__icon"><i class="fa-solid <?php echo esc_attr( $item[0] ); ?>" aria-hidden="true"></i></span>
								<span class="usp__body">
									<strong class="usp__title"><?php echo esc_html( $item[1] ); ?></strong>
									<span class="usp__text"><?php echo wp_kses_post( $item[2] ); ?></span>
								</span>
							</span>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
</section>
