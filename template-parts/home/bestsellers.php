<?php
/**
 * Home — tabbed product grid.
 *
 * All three panels are rendered server-side and toggled with JS, so there is no
 * loading state and the content is crawlable.
 *
 * @package Base Theme
 */

defined( 'ABSPATH' ) || exit;

$tabs = array(
	'best'  => array(
		'label' => __( 'Best sellers', 'base-theme' ),
		'type'  => 'bestseller',
	),
	'new'   => array(
		'label' => __( 'New in', 'base-theme' ),
		'type'  => 'recent',
	),
	'sale'  => array(
		'label' => __( 'On sale', 'base-theme' ),
		'type'  => 'sale',
	),
);

// Drop any tab with nothing to show rather than rendering an empty grid.
foreach ( $tabs as $key => $tab ) {
	$tabs[ $key ]['products'] = myshop_get_products(
		array(
			'limit' => 8,
			'type'  => $tab['type'],
		)
	);

	if ( empty( $tabs[ $key ]['products'] ) ) {
		unset( $tabs[ $key ] );
	}
}

if ( empty( $tabs ) ) {
	return;
}

$first = array_key_first( $tabs );

// Admin's color/gradient (Frontpage → Product tabs) overrides the sitewide
// product-box background just for the cards in this section.
$card_bg_type = myshop_c( 'tabs_card_bg_type', '' );
$card_bg      = 'color' === $card_bg_type ? myshop_c( 'tabs_card_bg_color', '' ) : ( 'gradient' === $card_bg_type ? myshop_c( 'tabs_card_bg_css', '' ) : '' );
?>

<section class="section section--bone tabs">
	<div class="shop-container">

		<?php
		$tabs_eyebrow = myshop_c( 'tabs_eyebrow' );
		$tabs_title   = myshop_c( 'tabs_title' );
		?>
		<div class="tabs__head reveal">
			<?php if ( $tabs_eyebrow || $tabs_title ) : ?>
				<div class="sec-head__text">
					<?php if ( $tabs_eyebrow ) : ?>
						<span class="eyebrow"><?php echo esc_html( $tabs_eyebrow ); ?></span>
					<?php endif; ?>
					<?php if ( $tabs_title ) : ?>
						<h2 class="sec-head__title"><?php echo esc_html( $tabs_title ); ?></h2>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="tabs__nav" role="tablist">
				<?php foreach ( $tabs as $key => $tab ) : ?>
					<button type="button"
						class="tabs__btn js-tab<?php echo $key === $first ? ' is-active' : ''; ?>"
						data-tab="<?php echo esc_attr( $key ); ?>"
						role="tab"
						aria-selected="<?php echo $key === $first ? 'true' : 'false'; ?>">
						<?php echo esc_html( $tab['label'] ); ?>
					</button>
				<?php endforeach; ?>
			</div>
		</div>

		<?php foreach ( $tabs as $key => $tab ) : ?>
			<div class="tabs__panel js-tab-panel<?php echo $key === $first ? ' is-active' : ''; ?>"
				data-panel="<?php echo esc_attr( $key ); ?>"
				role="tabpanel"
				<?php echo $key === $first ? '' : 'hidden'; ?>>

				<div class="product-grid">
					<?php foreach ( $tab['products'] as $i => $product ) : ?>
						<?php myshop_product_card( $product, $i, 'minimal', $card_bg ); ?>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endforeach; ?>

		<div class="tabs__foot reveal">
			<a class="btn btn--ghost" href="<?php echo esc_url( myshop_shop_url() ); ?>">
				<?php esc_html_e( 'View all products', 'base-theme' ); ?>
			</a>
		</div>

	</div>
</section>
