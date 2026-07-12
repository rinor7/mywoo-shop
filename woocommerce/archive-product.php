<?php
/**
 * Product archive (shop + category/tag) — theme override.
 *
 * Breadcrumb, title + result count with the sorting select alongside,
 * the theme's product cards in a 4-up grid, square pagination.
 *
 * @package Base Theme
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

// Opens <main class="woo-main"><div class="shop-container"> (see shop/setup.php).
do_action( 'woocommerce_before_main_content' );

woocommerce_breadcrumb(
	array(
		'delimiter'   => '<span class="shop-crumbs__sep">/</span>',
		'wrap_before' => '<nav class="shop-crumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'base-theme' ) . '">',
		'wrap_after'  => '</nav>',
	)
);
?>

<header class="shop-head">
	<div class="shop-head__text">
		<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
			<h1 class="shop-head__title"><?php woocommerce_page_title(); ?></h1>
		<?php endif; ?>

		<?php
		do_action( 'woocommerce_archive_description' );
		woocommerce_result_count();
		?>
	</div>

	<div class="shop-head__tools">
		<?php woocommerce_catalog_ordering(); ?>
	</div>
</header>

<?php myshop_shop_filterbar(); ?>

<?php
if ( woocommerce_product_loop() ) {

	do_action( 'woocommerce_before_shop_loop' );

	echo '<div class="product-grid product-grid--cards shop-grid">';

	if ( wc_get_loop_prop( 'total' ) ) {
		while ( have_posts() ) {
			the_post();
			do_action( 'woocommerce_shop_loop' );
			wc_get_template_part( 'content', 'product' );
		}
	}

	echo '</div>';

	do_action( 'woocommerce_after_shop_loop' );

	woocommerce_pagination();

} else {
	do_action( 'woocommerce_no_products_found' );
}

do_action( 'woocommerce_after_main_content' );

get_footer( 'shop' );
