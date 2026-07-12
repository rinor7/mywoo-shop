<?php
/**
 * Home — lookbook / social grid.
 *
 * @package Base Theme
 */

defined( 'ABSPATH' ) || exit;

$shop = myshop_shop_url();

// Each tile: image URL, label, link.
$tiles = array();
$rows  = myshop_c( 'look_tiles', array() );

if ( $rows ) {
	foreach ( $rows as $row ) {
		if ( empty( $row['lt_image'] ) ) {
			continue;
		}
		$tiles[] = array( $row['lt_image'], $row['lt_label'], $row['lt_url'] ? $row['lt_url'] : $shop );
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
		$tiles[] = array( myshop_placeholder( $tile[0] ), $tile[1], $shop );
	}
}
?>

<section class="section lookbook">
	<div class="shop-container">

		<?php
		myshop_section_head(
			array(
				'eyebrow'   => myshop_c( 'look_eyebrow', __( '@myshop', 'base-theme' ) ),
				'title'     => myshop_c( 'look_title', __( 'Shop the look', 'base-theme' ) ),
				'sub'       => myshop_c( 'look_sub', __( 'Tag us and your photo could land here. We read every one.', 'base-theme' ) ),
				'link_url'  => $shop,
				'link_text' => __( 'Follow along', 'base-theme' ),
			)
		);
		?>

		<div class="look-grid">
			<?php foreach ( $tiles as $i => $tile ) : ?>
				<a class="look-tile reveal" href="<?php echo esc_url( $tile[2] ); ?>"
					style="--reveal-delay:<?php echo (int) ( $i * 60 ); ?>ms">
					<img src="<?php echo esc_url( $tile[0] ); ?>" alt="" loading="lazy" decoding="async">

					<span class="look-tile__overlay">
						<span class="look-tile__icon"><i class="fa-solid fa-bag-shopping" aria-hidden="true"></i></span>
						<span class="look-tile__label"><?php echo esc_html( $tile[1] ); ?></span>
					</span>
				</a>
			<?php endforeach; ?>
		</div>

	</div>
</section>
