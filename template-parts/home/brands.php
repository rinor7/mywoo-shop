<?php
/**
 * Home — maker partners marquee.
 *
 * Names come from "Frontpage Content → Makers marquee" (one per line).
 *
 * @package Base Theme
 */

defined( 'ABSPATH' ) || exit;

$names  = (string) myshop_c( 'brands_names', '' );
$brands = array_values( array_filter( array_map( 'trim', explode( "\n", $names ) ) ) );

if ( empty( $brands ) ) {
	$brands = array( 'Aurelia', 'Meridian', 'Vale', 'Lumen', 'Nordic', 'Terra', 'Kestrel', 'Solstice' );
}
?>

<section class="section--tight brands">
	<div class="shop-container">
		<p class="brands__label"><?php echo esc_html( myshop_c( 'brands_label', __( 'The makers we work with', 'base-theme' ) ) ); ?></p>
	</div>

	<div class="brands__marquee js-marquee">
		<div class="brands__track">
			<?php // Rendered twice so the CSS loop has no visible seam. ?>
			<?php for ( $pass = 0; $pass < 2; $pass++ ) : ?>
				<ul class="brands__list" <?php echo 1 === $pass ? 'aria-hidden="true"' : ''; ?>>
					<?php foreach ( $brands as $brand ) : ?>
						<li class="brands__item"><?php echo esc_html( $brand ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endfor; ?>
		</div>
	</div>
</section>
