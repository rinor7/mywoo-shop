<?php
/**
 * 404 — the "Go back" button returns to the previous page when the visitor
 * came from this site (handled in main.js); otherwise it links home.
 *
 * @package Base Theme
 */

get_header();
?>

<main id="primary" class="site-404">
	<section class="fof">
		<div class="shop-container fof__inner">
			<span class="eyebrow"><?php esc_html_e( 'Error 404', 'base-theme' ); ?></span>

			<h1 class="fof__code">404</h1>

			<p class="fof__text">
				<?php esc_html_e( 'This page doesn&rsquo;t exist or has moved. The good stuff is still where you left it.', 'base-theme' ); ?>
			</p>

			<div class="fof__actions">
				<a class="btn btn--primary js-go-back" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
					<?php esc_html_e( 'Go back', 'base-theme' ); ?>
				</a>

				<a class="btn btn--ghost" href="<?php echo esc_url( function_exists( 'myshop_shop_url' ) ? myshop_shop_url() : home_url( '/' ) ); ?>">
					<?php esc_html_e( 'Browse the shop', 'base-theme' ); ?>
				</a>
			</div>
		</div>
	</section>
</main>

<?php get_footer(); ?>
