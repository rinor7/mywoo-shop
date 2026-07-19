<?php
/**
 * Single product content — editorial PDP.
 *
 * Split hero (gallery slider | purchase panel), sticky add-to-bag bar,
 * optional story sections from the "Product Story" fields, specification
 * table, and "Complete the look".
 *
 * @package Base Theme
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product || post_password_required() ) {
	echo get_the_password_form(); // phpcs:ignore WordPress.Security.EscapeOutput
	return;
}

$gallery_ids = myshop_product_gallery_ids( $product );
$specs       = myshop_product_specs( $product );
$pairings    = myshop_product_pairings( $product, 4 );

$terms   = get_the_terms( $product->get_id(), 'product_cat' );
$eyebrow = $product->is_on_sale()
	? __( 'Limited offer', 'base-theme' )
	: ( $terms && ! is_wp_error( $terms ) ? $terms[0]->name : __( 'The collection', 'base-theme' ) );

$quote  = function_exists( 'get_field' ) ? get_field( 'ps_quote', $product->get_id() ) : '';
$story  = array();
foreach ( array( 'ps_stat1_value', 'ps_stat1_label', 'ps_stat2_value', 'ps_stat2_label', 'ps_a_eyebrow', 'ps_a_title', 'ps_a_text', 'ps_b_eyebrow', 'ps_b_title', 'ps_b_text' ) as $ps_key ) {
	$story[ $ps_key ] = function_exists( 'get_field' ) ? get_field( $ps_key, $product->get_id() ) : '';
}
?>

<div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'pdp', $product ); ?>>

	<?php do_action( 'woocommerce_before_single_product' ); ?>

	<!-- ============ Hero ============ -->
	<section class="pdp-hero">

		<div class="pdp-gallery js-pdp" data-count="<?php echo esc_attr( count( $gallery_ids ) ); ?>">
			<?php if ( ! empty( $gallery_ids ) ) : ?>
				<div class="swiper pdp-gallery__main js-pdp-main">
					<div class="swiper-wrapper">
						<?php foreach ( $gallery_ids as $image_id ) : ?>
							<div class="swiper-slide">
								<?php echo wp_get_attachment_image( $image_id, 'woocommerce_single', false, array( 'class' => 'pdp-gallery__img' ) ); ?>
							</div>
						<?php endforeach; ?>
					</div>
				</div>

				<?php if ( count( $gallery_ids ) > 1 ) : ?>
					<div class="pdp-gallery__nav">
						<button type="button" class="pdp-gallery__arrow js-pdp-prev" aria-label="<?php esc_attr_e( 'Previous image', 'base-theme' ); ?>">
							<i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
						</button>
						<button type="button" class="pdp-gallery__arrow js-pdp-next" aria-label="<?php esc_attr_e( 'Next image', 'base-theme' ); ?>">
							<i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
						</button>
					</div>

					<div class="swiper pdp-gallery__thumbs js-pdp-thumbs">
						<div class="swiper-wrapper">
							<?php foreach ( $gallery_ids as $image_id ) : ?>
								<div class="swiper-slide">
									<?php echo wp_get_attachment_image( $image_id, 'woocommerce_gallery_thumbnail' ); ?>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>
			<?php else : ?>
				<img class="pdp-gallery__img" src="<?php echo esc_url( wc_placeholder_img_src( 'woocommerce_single' ) ); ?>" alt="">
			<?php endif; ?>
		</div>

		<div class="pdp-panel">
			<div class="pdp-panel__inner js-pdp-panel">

				<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>

				<h1 class="pdp-panel__title"><?php the_title(); ?></h1>

				<div class="pdp-panel__price price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>

				<?php if ( $product->get_short_description() ) : ?>
					<div class="pdp-panel__desc"><?php echo wp_kses_post( wpautop( $product->get_short_description() ) ); ?></div>
				<?php endif; ?>

				<div class="pdp-panel__form">
					<?php
					// Simple: qty + add. Variable: attribute selects + qty + add.
					// The stepper wraps Woo's own quantity input via CSS/JS.
					woocommerce_template_single_add_to_cart();
					?>
				</div>

				<?php $pdp_perks = myshop_pdp_perks(); ?>
				<?php if ( $pdp_perks ) : ?>
					<ul class="pdp-panel__meta">
						<?php foreach ( $pdp_perks as $perk ) : ?>
							<li><i class="fa-solid <?php echo esc_attr( $perk[0] ); ?>" aria-hidden="true"></i> <?php echo esc_html( $perk[1] ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

			</div>
		</div>
	</section>

	<!-- ============ Sticky add-to-bag bar ============ -->
	<div class="pdp-bar js-pdp-bar" hidden>
		<div class="shop-container pdp-bar__inner">
			<span class="pdp-bar__name"><?php the_title(); ?></span>

			<div class="pdp-bar__actions">
				<div class="qty-stepper pdp-bar__qty js-pdp-bar-qty">
					<button type="button" class="qty-stepper__btn" data-dir="-1" aria-label="<?php esc_attr_e( 'Decrease quantity', 'base-theme' ); ?>">&minus;</button>
					<span class="pdp-bar__qty-value js-pdp-bar-count">1</span>
					<button type="button" class="qty-stepper__btn" data-dir="1" aria-label="<?php esc_attr_e( 'Increase quantity', 'base-theme' ); ?>">+</button>
				</div>

				<button type="button" class="pdp-bar__add js-pdp-bar-add">
					<?php esc_html_e( 'Add to bag', 'base-theme' ); ?>
					<span class="pdp-bar__price">&mdash; <?php echo wp_kses_post( wc_price( wc_get_price_to_display( $product ) ) ); ?></span>
				</button>
			</div>
		</div>
	</div>

	<!-- ============ Editorial quote ============ -->
	<?php if ( $quote ) : ?>
		<section class="pdp-quote">
			<div class="shop-container">
				<span class="eyebrow eyebrow--center"><?php esc_html_e( 'Crafted for the modern professional', 'base-theme' ); ?></span>
				<blockquote class="pdp-quote__text">&ldquo;<?php echo esc_html( $quote ); ?>&rdquo;</blockquote>

				<?php if ( $story['ps_stat1_value'] || $story['ps_stat2_value'] ) : ?>
					<div class="pdp-quote__stats">
						<?php if ( $story['ps_stat1_value'] ) : ?>
							<div class="pdp-quote__stat">
								<strong><?php echo esc_html( $story['ps_stat1_value'] ); ?></strong>
								<span><?php echo esc_html( $story['ps_stat1_label'] ); ?></span>
							</div>
						<?php endif; ?>
						<?php if ( $story['ps_stat2_value'] ) : ?>
							<div class="pdp-quote__stat">
								<strong><?php echo esc_html( $story['ps_stat2_value'] ); ?></strong>
								<span><?php echo esc_html( $story['ps_stat2_label'] ); ?></span>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</section>
	<?php endif; ?>

	<!-- ============ Story A ============ -->
	<?php if ( $story['ps_a_title'] ) : ?>
		<section class="pdp-story">
			<div class="shop-container pdp-story__inner">
				<div class="pdp-story__copy reveal">
					<span class="eyebrow"><?php echo esc_html( $story['ps_a_eyebrow'] ? $story['ps_a_eyebrow'] : __( '01 — Core principles', 'base-theme' ) ); ?></span>
					<h2 class="pdp-story__title"><?php echo esc_html( $story['ps_a_title'] ); ?></h2>
					<p class="pdp-story__text"><?php echo esc_html( $story['ps_a_text'] ); ?></p>
				</div>
				<div class="pdp-story__media reveal">
					<?php
					$story_img = isset( $gallery_ids[1] ) ? $gallery_ids[1] : ( isset( $gallery_ids[0] ) ? $gallery_ids[0] : 0 );
					if ( $story_img ) {
						echo wp_get_attachment_image( $story_img, 'large' );
					}
					?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<!-- ============ Story B ============ -->
	<?php if ( $story['ps_b_title'] ) : ?>
		<section class="pdp-story pdp-story--alt">
			<div class="shop-container pdp-story__inner">
				<div class="pdp-story__copy reveal">
					<span class="eyebrow"><?php echo esc_html( $story['ps_b_eyebrow'] ? $story['ps_b_eyebrow'] : __( 'Technical story', 'base-theme' ) ); ?></span>
					<h2 class="pdp-story__title"><?php echo esc_html( $story['ps_b_title'] ); ?></h2>
					<p class="pdp-story__text"><?php echo esc_html( $story['ps_b_text'] ); ?></p>
				</div>
				<div class="pdp-story__media reveal">
					<?php
					$story_img_b = isset( $gallery_ids[2] ) ? $gallery_ids[2] : ( isset( $gallery_ids[0] ) ? $gallery_ids[0] : 0 );
					if ( $story_img_b ) {
						echo wp_get_attachment_image( $story_img_b, 'large' );
					}
					?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<!-- ============ Full description ============ -->
	<?php if ( get_the_content() ) : ?>
		<section class="pdp-desc">
			<div class="shop-container pdp-desc__inner">
				<?php the_content(); ?>
			</div>
		</section>
	<?php endif; ?>

	<!-- ============ Specifications ============ -->
	<?php if ( ! empty( $specs ) ) : ?>
		<section class="pdp-specs">
			<div class="shop-container">
				<span class="eyebrow"><?php esc_html_e( 'Document 02', 'base-theme' ); ?></span>
				<h2 class="pdp-specs__title"><?php esc_html_e( 'Specifications', 'base-theme' ); ?></h2>

				<dl class="pdp-specs__list">
					<?php foreach ( $specs as $spec ) : ?>
						<div class="pdp-specs__row reveal">
							<dt><?php echo esc_html( $spec[0] ); ?></dt>
							<dd><?php echo esc_html( $spec[1] ); ?></dd>
						</div>
					<?php endforeach; ?>
				</dl>
			</div>
		</section>
	<?php endif; ?>

	<!-- ============ Reviews (kept, restyled) ============ -->
	<?php if ( comments_open() || get_comments_number() ) : ?>
		<section class="pdp-reviews">
			<div class="shop-container">
				<?php comments_template(); ?>
			</div>
		</section>
	<?php endif; ?>

	<!-- ============ Complete the look ============ -->
	<?php if ( ! empty( $pairings ) ) : ?>
		<section class="pdp-pairings">
			<div class="shop-container">
				<div class="sec-head">
					<div class="sec-head__text">
						<span class="eyebrow"><?php esc_html_e( 'Curated pairings', 'base-theme' ); ?></span>
						<h2 class="sec-head__title"><?php esc_html_e( 'Complete the look', 'base-theme' ); ?></h2>
					</div>
					<a class="link-arrow" href="<?php echo esc_url( myshop_shop_url() ); ?>">
						<?php esc_html_e( 'Explore full collection', 'base-theme' ); ?>
						<i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
					</a>
				</div>

				<div class="product-grid">
					<?php foreach ( $pairings as $i => $pairing ) : ?>
						<?php myshop_product_card( $pairing, $i ); ?>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php do_action( 'woocommerce_after_single_product' ); ?>
</div>
