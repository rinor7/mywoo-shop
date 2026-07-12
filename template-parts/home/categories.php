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
		myshop_section_head(
			array(
				'eyebrow'   => myshop_c( 'cat_eyebrow', __( 'Browse', 'base-theme' ) ),
				'title'     => myshop_c( 'cat_title', __( 'Shop by category', 'base-theme' ) ),
				'sub'       => myshop_c( 'cat_sub', __( 'Six edits, each kept deliberately small. Everything here earned its place.', 'base-theme' ) ),
				'link_url'  => myshop_shop_url(),
				'link_text' => __( 'All categories', 'base-theme' ),
			)
		);
		?>

		<div class="cat-mosaic">
			<?php foreach ( array_slice( $categories, 0, 4 ) as $i => $cat ) : ?>
				<?php $variant = $variants[ $i ]; ?>

				<a class="cat-card cat-card--<?php echo esc_attr( $variant ); ?><?php echo empty( $cat['image'] ) ? ' cat-card--noimg' : ''; ?> reveal"
					href="<?php echo esc_url( $cat['link'] ); ?>"
					style="--reveal-delay:<?php echo (int) ( $i * 80 ); ?>ms;--ca:<?php echo esc_attr( $tones[ $i ][0] ); ?>;--cb:<?php echo esc_attr( $tones[ $i ][1] ); ?>">

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

	</div>
</section>
