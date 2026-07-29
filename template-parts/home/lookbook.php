<?php
/**
 * Home — lookbook / social grid.
 *
 * @package Base Theme
 */

defined( 'ABSPATH' ) || exit;

$shop = myshop_shop_url();

// Each tile: a custom lifestyle photo + a real product. Name, price and the
// link all come straight from the product — nothing to keep in sync by hand.
$tiles = array();
$rows  = myshop_c( 'look_tiles', array() );

if ( $rows && function_exists( 'wc_get_product' ) ) {
	foreach ( $rows as $row ) {
		if ( empty( $row['lt_image'] ) || empty( $row['lt_product'] ) ) {
			continue;
		}
		$product = wc_get_product( (int) $row['lt_product'] );
		if ( ! $product ) {
			continue;
		}
		$tiles[] = array(
			'image'      => $row['lt_image'],
			'name'       => $product->get_name(),
			'price_html' => $product->get_price_html(),
			'link'       => $product->get_permalink(),
		);
	}
}

if ( empty( $tiles ) ) {
	$defaults = array(
		array( 'texture-3', __( 'The Autumn Edit', 'base-theme' ) ),
		array( 'bag', __( 'Aurelia Leather Tote', 'base-theme' ) ),
		array( 'texture-1', __( 'In the workshop', 'base-theme' ) ),
		array( 'sunglasses', __( 'Solstice Sunglasses', 'base-theme' ) ),
		array( 'texture-4', __( 'Detail study', 'base-theme' ) ),
		array( 'lamp', __( 'Lumen Table Lamp', 'base-theme' ) ),
	);
	foreach ( $defaults as $tile ) {
		$tiles[] = array(
			'image'      => myshop_placeholder( $tile[0] ),
			'name'       => $tile[1],
			'price_html' => '',
			'link'       => $shop,
		);
	}
}

// Same true_false gotcha as the Categories/New arrivals buttons: myshop_c()
// treats `false` as "empty" and would fall back to shown-by-default even
// when explicitly turned off, so this one reads get_field() directly.
$btn_enabled = true;
if ( function_exists( 'get_field' ) ) {
	$raw = get_field( 'look_btn_enabled', myshop_front_id() );
	if ( '' !== $raw && null !== $raw ) {
		$btn_enabled = (bool) $raw;
	}
}

// Label and URL travel together in one ACF "link" field — empty =
// today's default ("Follow along" straight to the shop page).
$btn_link   = myshop_c( 'look_btn_link', array() );
$btn_url    = ! empty( $btn_link['url'] ) ? $btn_link['url'] : $shop;
$btn_label  = ! empty( $btn_link['title'] ) ? $btn_link['title'] : __( 'Follow along', 'base-theme' );
$btn_target = ! empty( $btn_link['target'] ) ? $btn_link['target'] : '';
?>

<section class="section lookbook">
	<div class="shop-container">

		<?php
		myshop_section_head(
			array(
				'eyebrow'     => myshop_c( 'look_eyebrow' ),
				'title'       => myshop_c( 'look_title' ),
				'sub'         => myshop_c( 'look_sub' ),
				'link_url'    => $btn_enabled ? $btn_url : '',
				'link_text'   => $btn_enabled ? $btn_label : '',
				'link_target' => $btn_enabled ? $btn_target : '',
			)
		);
		?>

		<div class="look-grid">
			<?php foreach ( $tiles as $i => $tile ) : ?>
				<a class="look-tile reveal" href="<?php echo esc_url( $tile['link'] ); ?>"
					style="--reveal-delay:<?php echo (int) ( $i * 60 ); ?>ms">
					<img src="<?php echo esc_url( $tile['image'] ); ?>" alt="" loading="lazy" decoding="async">

					<span class="look-tile__overlay">
						<span class="look-tile__icon"><i class="fa-solid fa-bag-shopping" aria-hidden="true"></i></span>
						<span class="look-tile__label"><?php echo esc_html( $tile['name'] ); ?></span>
						<?php if ( $tile['price_html'] ) : ?>
							<span class="look-tile__price"><?php echo wp_kses_post( $tile['price_html'] ); ?></span>
						<?php endif; ?>
					</span>
				</a>
			<?php endforeach; ?>
		</div>

	</div>
</section>
