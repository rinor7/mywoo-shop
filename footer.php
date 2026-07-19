<?php
/**
 * @package Base Theme
 */

$shop    = myshop_shop_url();
$account = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/' );
$is_woo  = function_exists( 'WC' );
?>

<footer id="footer-site" class="site-footer">
	<div class="shop-container">

		<div class="footer__top">

			<div class="footer__brand">
				<?php
				$footer_logo = get_theme_mod( 'footer_logo' );
				if ( $footer_logo ) :
					?>
					<a class="footer__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
						<img src="<?php echo esc_url( $footer_logo ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
					</a>
				<?php else : ?>
					<a class="footer__logo logo-text" href="<?php echo esc_url( home_url( '/' ) ); ?>">
						<?php bloginfo( 'name' ); ?>
					</a>
				<?php endif; ?>

				<?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
					<?php dynamic_sidebar( 'footer-1' ); ?>
				<?php endif; ?>
			</div>

			<div class="footer__cols">

				<div class="footer__col">
					<?php if ( is_active_sidebar( 'footer-2' ) ) : ?>
						<?php dynamic_sidebar( 'footer-2' ); ?>
					<?php endif; ?>
				</div>

				<div class="footer__col">
					<?php if ( is_active_sidebar( 'footer-3' ) ) : ?>
						<?php dynamic_sidebar( 'footer-3' ); ?>
					<?php endif; ?>
				</div>

				<div class="footer__col">
					<?php if ( is_active_sidebar( 'footer-4' ) ) : ?>
						<?php dynamic_sidebar( 'footer-4' ); ?>
					<?php endif; ?>
				</div>

				<div class="footer__col footer__col--contact">
					<?php if ( is_active_sidebar( 'footer-5' ) ) : ?>
						<?php dynamic_sidebar( 'footer-5' ); ?>
					<?php endif; ?>
				</div>

			</div>
		</div>

		<div class="footer__bottom">
			<p class="footer__copy">
				&copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'All rights reserved.', 'base-theme' ); ?>
			</p>

			<?php if ( function_exists( 'myshop_payment_icons' ) ) { myshop_payment_icons(); } ?>
		</div>

	</div>
</footer>

</div><!-- #page -->


<!-- ============ Global chrome ============ -->

<div class="backdrop js-backdrop" hidden></div>

<!-- Cart drawer -->
<aside id="cart-drawer" class="drawer cart-drawer js-cart-drawer" role="dialog" aria-modal="true"
	aria-label="<?php esc_attr_e( 'Shopping bag', 'base-theme' ); ?>" hidden>
	<header class="drawer__head">
		<h2 class="drawer__title"><?php esc_html_e( 'Your bag', 'base-theme' ); ?></h2>
		<button type="button" class="icon-btn js-drawer-close" aria-label="<?php esc_attr_e( 'Close', 'base-theme' ); ?>">
			<i class="fa-solid fa-xmark" aria-hidden="true"></i>
		</button>
	</header>

	<?php myshop_cart_drawer_content(); ?>
</aside>

<!-- Mobile menu drawer -->
<aside id="mobile-menu" class="drawer menu-drawer js-menu-drawer" role="dialog" aria-modal="true"
	aria-label="<?php esc_attr_e( 'Menu', 'base-theme' ); ?>" hidden>
	<header class="drawer__head">
		<span class="drawer__title"><?php esc_html_e( 'Menu', 'base-theme' ); ?></span>
		<button type="button" class="icon-btn js-drawer-close" aria-label="<?php esc_attr_e( 'Close', 'base-theme' ); ?>">
			<i class="fa-solid fa-xmark" aria-hidden="true"></i>
		</button>
	</header>

	<div class="menu-drawer__body">
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'menu-1',
				'menu_class'     => 'menu-drawer__nav',
				'container'      => false,
				'depth'          => 2,
				'fallback_cb'    => 'myshop_mobile_nav_fallback',
			)
		);
		?>

		<div class="menu-drawer__foot">
			<?php if ( function_exists( 'myshop_language_switcher' ) ) { myshop_language_switcher( 'drawer' ); } ?>

			<a class="btn btn--ghost btn--block" href="<?php echo esc_url( $account ); ?>">
				<i class="fa-regular fa-user" aria-hidden="true"></i>
				<?php esc_html_e( 'My account', 'base-theme' ); ?>
			</a>

			<?php
			// Same links the Social Icons widget renders in the footer.
			$drawer_socials = function_exists( 'myshop_social_links' ) ? myshop_social_links() : array();
			if ( $drawer_socials ) :
				?>
				<ul class="menu-drawer__social">
					<?php foreach ( $drawer_socials as $social ) : ?>
						<li>
							<a href="<?php echo esc_url( $social['url'] ); ?>" target="_blank" rel="noopener"
								aria-label="<?php echo esc_attr( $social['label'] ); ?>">
								<i class="fa-brands <?php echo esc_attr( $social['icon'] ); ?>" aria-hidden="true"></i>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</div>
</aside>

<!-- Search overlay -->
<div class="search-overlay js-search-overlay" role="dialog" aria-modal="true"
	aria-label="<?php esc_attr_e( 'Search', 'base-theme' ); ?>" hidden>
	<div class="shop-container search-overlay__inner">

		<button type="button" class="search-overlay__close icon-btn js-drawer-close" aria-label="<?php esc_attr_e( 'Close search', 'base-theme' ); ?>">
			<i class="fa-solid fa-xmark" aria-hidden="true"></i>
		</button>

		<span class="eyebrow"><?php esc_html_e( 'Search', 'base-theme' ); ?></span>

		<form class="search-overlay__form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<i class="fa-solid fa-magnifying-glass search-overlay__icon" aria-hidden="true"></i>

			<label class="screen-reader-text" for="shop-search"><?php esc_html_e( 'Search products', 'base-theme' ); ?></label>
			<input class="search-overlay__input js-search-input" type="search" id="shop-search" name="s"
				placeholder="<?php esc_attr_e( 'What are you looking for?', 'base-theme' ); ?>" autocomplete="off">

			<?php if ( $is_woo ) : ?>
				<input type="hidden" name="post_type" value="product">
			<?php endif; ?>

			<button type="submit" class="btn btn--primary"><?php esc_html_e( 'Search', 'base-theme' ); ?></button>
		</form>

		<?php
		// Chips come from Global Settings → Search Suggestions; none set = no block.
		$popular = function_exists( 'myshop_search_suggest_terms' ) ? myshop_search_suggest_terms() : array();
		if ( $popular ) :
			?>
			<div class="search-overlay__suggest">
				<?php $suggest_label = myshop_search_suggest_label(); ?>
				<?php if ( '' !== $suggest_label ) : ?>
					<span class="search-overlay__suggest-label"><?php echo esc_html( $suggest_label ); ?></span>
				<?php endif; ?>
				<ul>
					<?php
					foreach ( $popular as $term ) :
						$url = add_query_arg(
							array_filter(
								array(
									's'         => rawurlencode( $term ),
									'post_type' => $is_woo ? 'product' : null,
								)
							),
							home_url( '/' )
						);
						?>
						<li><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $term ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

	</div>
</div>

<!-- Quick view -->
<div class="modal quickview js-quickview-modal" role="dialog" aria-modal="true"
	aria-label="<?php esc_attr_e( 'Product quick view', 'base-theme' ); ?>" hidden>
	<div class="modal__panel">
		<button type="button" class="modal__close icon-btn js-drawer-close" aria-label="<?php esc_attr_e( 'Close', 'base-theme' ); ?>">
			<i class="fa-solid fa-xmark" aria-hidden="true"></i>
		</button>

		<div class="quickview__media">
			<img class="js-qv-image" src="" alt="" width="600" height="750">
		</div>

		<div class="quickview__body">
			<span class="pcard__cat js-qv-cat"></span>
			<h3 class="quickview__title js-qv-title"></h3>

			<div class="rating-row">
				<span class="js-qv-stars"></span>
				<span class="rating-row__count js-qv-count"></span>
			</div>

			<div class="price quickview__price js-qv-price"></div>
			<p class="quickview__text js-qv-excerpt"></p>

			<div class="quickview__actions">
				<div class="qty">
					<button type="button" class="qty__btn js-qty-minus" aria-label="<?php esc_attr_e( 'Decrease quantity', 'base-theme' ); ?>">&minus;</button>
					<input class="qty__input js-qty-input" type="number" value="1" min="1" inputmode="numeric"
						aria-label="<?php esc_attr_e( 'Quantity', 'base-theme' ); ?>">
					<button type="button" class="qty__btn js-qty-plus" aria-label="<?php esc_attr_e( 'Increase quantity', 'base-theme' ); ?>">+</button>
				</div>

				<button type="button" class="btn btn--primary js-add-to-cart js-qv-add">
					<i class="fa-solid fa-bag-shopping" aria-hidden="true"></i>
					<span><?php esc_html_e( 'Add to bag', 'base-theme' ); ?></span>
				</button>
			</div>

			<a class="link-arrow js-qv-link" href="#">
				<?php esc_html_e( 'View full details', 'base-theme' ); ?>
				<i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
			</a>
		</div>
	</div>
</div>

<!-- Mobile bottom bar -->
<?php
// Which tab the current page belongs to (search/bag are overlays — never "here").
$mb_home    = is_front_page();
$mb_shop    = $is_woo && ( is_shop() || is_product_category() || is_product_tag() || is_product() );
$mb_account = $is_woo && is_account_page();
$mb_bag     = $is_woo && ( is_cart() || is_checkout() );
?>
<nav class="mobile-bar" aria-label="<?php esc_attr_e( 'Quick navigation', 'base-theme' ); ?>">
	<a class="mobile-bar__item<?php echo $mb_home ? ' is-active' : ''; ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>"
		<?php echo $mb_home ? 'aria-current="page"' : ''; ?>>
		<i class="fa-solid fa-house" aria-hidden="true"></i>
		<span><?php esc_html_e( 'Home', 'base-theme' ); ?></span>
	</a>

	<a class="mobile-bar__item<?php echo $mb_shop ? ' is-active' : ''; ?>" href="<?php echo esc_url( $shop ); ?>"
		<?php echo $mb_shop ? 'aria-current="page"' : ''; ?>>
		<i class="fa-solid fa-grip" aria-hidden="true"></i>
		<span><?php esc_html_e( 'Shop', 'base-theme' ); ?></span>
	</a>

	<button type="button" class="mobile-bar__item js-search-open">
		<i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
		<span><?php esc_html_e( 'Search', 'base-theme' ); ?></span>
	</button>

	<a class="mobile-bar__item<?php echo $mb_account ? ' is-active' : ''; ?>" href="<?php echo esc_url( $account ); ?>"
		<?php echo $mb_account ? 'aria-current="page"' : ''; ?>>
		<i class="fa-regular fa-user" aria-hidden="true"></i>
		<span><?php esc_html_e( 'Account', 'base-theme' ); ?></span>
	</a>

	<button type="button" class="mobile-bar__item js-cart-open<?php echo $mb_bag ? ' is-active' : ''; ?>">
		<span class="mobile-bar__bag">
			<i class="fa-solid fa-bag-shopping" aria-hidden="true"></i>
			<?php myshop_cart_count_html(); ?>
		</span>
		<span><?php esc_html_e( 'Bag', 'base-theme' ); ?></span>
	</button>
</nav>

<button type="button" class="to-top js-to-top" aria-label="<?php esc_attr_e( 'Back to top', 'base-theme' ); ?>">
	<i class="fa-solid fa-arrow-up" aria-hidden="true"></i>
</button>

<div class="toast-stack js-toasts" aria-live="polite" aria-atomic="true"></div>

<?php wp_footer(); ?>
</body>

</html>
