<?php
/**
 * Home — shop by category (editorial mosaic).
 *
 * One tall featured tile, one wide banner, two small tiles:
 *   ┌──────────┬───────────────┐
 *   │          │     wide      │
 *   │ featured ├───────┬───────┤
 *   │          │ small │ small │
 *   └──────────┴───────┴───────┘
 * Categories are ordered by product count; set images per term under
 * Products → Categories, otherwise the theme's line art fills in.
 *
 * @package Base Theme
 */

defined( 'ABSPATH' ) || exit;

$categories = myshop_get_categories( 4 );
if ( empty( $categories ) ) {
	return;
}

// Position in the mosaic by index: 0 featured, 1 wide, 2–3 small.
$variants = array( 'feature', 'wide', 'small', 'small' );

// Dark editorial gradients for tiles without a photo yet — one per position.
$tones = array(
	array( '#2c2f35', '#131417' ),
	array( '#4b3f33', '#27211a' ),
	array( '#3e4650', '#242930' ),
	array( '#584a3c', '#2f2820' ),
);
?>

<section class="section categories">
	<div class="shop-container">

		<?php
		$btn_enabled = true;
		if ( function_exists( 'get_field' ) ) {
			$raw = get_field( 'cat_btn_enabled', myshop_front_id() );
			if ( '' !== $raw && null !== $raw ) {
				$btn_enabled = (bool) $raw;
			}
		}

		// Label and URL travel together in one ACF "link" field — empty =
		// today's default ("All categories" straight to the shop page).
		$btn_link   = myshop_c( 'cat_btn_link', array() );
		$btn_url    = ! empty( $btn_link['url'] ) ? $btn_link['url'] : myshop_shop_url();
		$btn_label  = ! empty( $btn_link['title'] ) ? $btn_link['title'] : __( 'All categories', 'base-theme' );
		$btn_target = ! empty( $btn_link['target'] ) ? $btn_link['target'] : '';

		myshop_section_head(
			array(
				'eyebrow'     => myshop_c( 'cat_eyebrow' ),
				'title'       => myshop_c( 'cat_title' ),
				'sub'         => myshop_c( 'cat_sub' ),
				'link_url'    => $btn_enabled ? $btn_url : '',
				'link_text'   => $btn_enabled ? $btn_label : '',
				'link_target' => $btn_enabled ? $btn_target : '',
			)
		);
		?>

		<div class="cat-mosaic">
			<?php foreach ( array_slice( $categories, 0, 4 ) as $i => $cat ) : ?>
				<?php $variant = $variants[ $i ]; ?>
				<?php
				$bg_override = '';
				if ( ! empty( $cat['bg'] ) ) {
					$bg_override = 'gradient' === $cat['bg']['type'] ? $cat['bg']['css'] : $cat['bg']['color'];
				}
				?>

				<a class="cat-card cat-card--<?php echo esc_attr( $variant ); ?><?php echo empty( $cat['image'] ) ? ' cat-card--noimg' : ''; ?><?php echo $bg_override ? ' cat-card--custom-bg' : ''; ?> reveal"
					href="<?php echo esc_url( $cat['link'] ); ?>"
					style="--reveal-delay:<?php echo (int) ( $i * 80 ); ?>ms;--ca:<?php echo esc_attr( $tones[ $i ][0] ); ?>;--cb:<?php echo esc_attr( $tones[ $i ][1] ); ?><?php echo $bg_override ? ';--bg-override:' . esc_attr( $bg_override ) : ''; ?>">

					<span class="cat-card__media">
						<?php if ( ! empty( $cat['image'] ) ) : ?>
							<img class="cat-card__img" src="<?php echo esc_url( $cat['image'] ); ?>" alt="" loading="lazy" decoding="async">
						<?php else : ?>
							<img class="cat-card__art" src="<?php echo esc_url( $cat['art'] ); ?>" alt="" loading="lazy" decoding="async">
						<?php endif; ?>
					</span>

					<span class="cat-card__body">
						<strong class="cat-card__name"><?php echo esc_html( $cat['name'] ); ?></strong>
						<span class="cat-card__cta">
							<?php esc_html_e( 'Shop now', 'base-theme' ); ?>
							<i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
						</span>
					</span>
				</a>
			<?php endforeach; ?>
		</div>

		<!-- Mobile only: same cards and same feature/wide/small proportions
			 as the mosaic above (just laid out as slides instead of a grid) in
			 a peeking carousel like the New arrivals slider (.js-product-slider). -->
		<div class="cat-slider">
			<div class="swiper js-category-slider">
				<div class="swiper-wrapper">
					<?php foreach ( array_slice( $categories, 0, 4 ) as $i => $cat ) : ?>
						<?php $variant = $variants[ $i ]; ?>
						<?php
						$bg_override = '';
						if ( ! empty( $cat['bg'] ) ) {
							$bg_override = 'gradient' === $cat['bg']['type'] ? $cat['bg']['css'] : $cat['bg']['color'];
						}
						?>
						<div class="swiper-slide">
							<a class="cat-card cat-card--<?php echo esc_attr( $variant ); ?><?php echo empty( $cat['image'] ) ? ' cat-card--noimg' : ''; ?><?php echo $bg_override ? ' cat-card--custom-bg' : ''; ?>"
								href="<?php echo esc_url( $cat['link'] ); ?>"
								style="--ca:<?php echo esc_attr( $tones[ $i ][0] ); ?>;--cb:<?php echo esc_attr( $tones[ $i ][1] ); ?><?php echo $bg_override ? ';--bg-override:' . esc_attr( $bg_override ) : ''; ?>">

								<span class="cat-card__media">
									<?php if ( ! empty( $cat['image'] ) ) : ?>
										<img class="cat-card__img" src="<?php echo esc_url( $cat['image'] ); ?>" alt="" loading="lazy" decoding="async">
									<?php else : ?>
										<img class="cat-card__art" src="<?php echo esc_url( $cat['art'] ); ?>" alt="" loading="lazy" decoding="async">
									<?php endif; ?>
								</span>

								<span class="cat-card__body">
									<strong class="cat-card__name"><?php echo esc_html( $cat['name'] ); ?></strong>
									<span class="cat-card__cta">
										<?php esc_html_e( 'Shop now', 'base-theme' ); ?>
										<i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
									</span>
								</span>
							</a>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

	</div>
</section>
