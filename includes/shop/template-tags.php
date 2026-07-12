<?php
/**
 * Storefront template tags.
 *
 * @package Base Theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Navigation shown until a menu is assigned to the "menu-1" location.
 *
 * Includes the Shop mega-panel. Assigning a real menu in Appearance → Menus
 * replaces this wholesale, mega-panel included.
 */
function myshop_nav_fallback() {
	$shop = myshop_shop_url();
	$cats = myshop_get_categories( 6 );
	?>
	<ul class="nav-list">
		<li class="menu-item has-mega js-mega">
			<a href="<?php echo esc_url( $shop ); ?>">
				<?php esc_html_e( 'Shop', 'base-theme' ); ?>
				<i class="fa-solid fa-chevron-down nav-list__caret" aria-hidden="true"></i>
			</a>

			<div class="mega">
				<div class="shop-container mega__inner">
					<div class="mega__col">
						<span class="mega__title"><?php esc_html_e( 'Categories', 'base-theme' ); ?></span>
						<ul>
							<?php foreach ( $cats as $cat ) : ?>
								<li><a href="<?php echo esc_url( $cat['link'] ); ?>"><?php echo esc_html( $cat['name'] ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</div>

					<div class="mega__col">
						<span class="mega__title"><?php esc_html_e( 'Collections', 'base-theme' ); ?></span>
						<ul>
							<li><a href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'New arrivals', 'base-theme' ); ?></a></li>
							<li><a href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'Best sellers', 'base-theme' ); ?></a></li>
							<li><a href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'Last chance', 'base-theme' ); ?></a></li>
							<li><a href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'Gift cards', 'base-theme' ); ?></a></li>
						</ul>
					</div>

					<div class="mega__col">
						<span class="mega__title"><?php esc_html_e( 'Help', 'base-theme' ); ?></span>
						<ul>
							<li><a href="<?php echo esc_url( home_url( '/contact' ) ); ?>"><?php esc_html_e( 'Contact us', 'base-theme' ); ?></a></li>
							<li><a href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'Shipping &amp; returns', 'base-theme' ); ?></a></li>
							<li><a href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'Size guide', 'base-theme' ); ?></a></li>
							<li><a href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'Track your order', 'base-theme' ); ?></a></li>
						</ul>
					</div>

					<a class="mega__promo" href="<?php echo esc_url( $shop ); ?>">
						<img src="<?php echo esc_url( myshop_placeholder( 'texture-3' ) ); ?>" alt="" loading="lazy">
						<span class="mega__promo-body">
							<span class="eyebrow"><?php esc_html_e( 'Featured', 'base-theme' ); ?></span>
							<span class="mega__promo-title"><?php esc_html_e( 'The Autumn Edit', 'base-theme' ); ?></span>
							<span class="link-arrow"><?php esc_html_e( 'Discover', 'base-theme' ); ?> <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span>
						</span>
					</a>
				</div>
			</div>
		</li>

		<li class="menu-item"><a href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'New In', 'base-theme' ); ?></a></li>
		<li class="menu-item"><a href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'Best Sellers', 'base-theme' ); ?></a></li>
		<li class="menu-item"><a href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'Sale', 'base-theme' ); ?></a></li>
		<li class="menu-item"><a href="<?php echo esc_url( home_url( '/blog' ) ); ?>"><?php esc_html_e( 'Journal', 'base-theme' ); ?></a></li>
	</ul>
	<?php
}

/**
 * Mobile drawer navigation, shown until a menu is assigned to "menu-1".
 */
function myshop_mobile_nav_fallback() {
	$shop = myshop_shop_url();
	$cats = myshop_get_categories( 6 );
	?>
	<ul class="menu-drawer__nav">
		<li class="menu-item"><a href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'New In', 'base-theme' ); ?></a></li>
		<li class="menu-item"><a href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'Best Sellers', 'base-theme' ); ?></a></li>
		<li class="menu-item"><a href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'Sale', 'base-theme' ); ?></a></li>
		<li class="menu-item"><a href="<?php echo esc_url( home_url( '/blog' ) ); ?>"><?php esc_html_e( 'Journal', 'base-theme' ); ?></a></li>
	</ul>

	<span class="menu-drawer__label"><?php esc_html_e( 'Categories', 'base-theme' ); ?></span>

	<ul class="menu-drawer__nav menu-drawer__nav--sub">
		<?php foreach ( $cats as $cat ) : ?>
			<li class="menu-item"><a href="<?php echo esc_url( $cat['link'] ); ?>"><?php echo esc_html( $cat['name'] ); ?></a></li>
		<?php endforeach; ?>
	</ul>
	<?php
}

/**
 * Section header: eyebrow + title + optional subtitle and a trailing link.
 *
 * @param array $args eyebrow, title, sub, link_url, link_text, center, light.
 */
function myshop_section_head( $args = array() ) {
	$a = wp_parse_args(
		$args,
		array(
			'eyebrow'   => '',
			'title'     => '',
			'sub'       => '',
			'link_url'  => '',
			'link_text' => '',
			'center'    => false,
			'light'     => false,
		)
	);

	$classes = 'sec-head reveal';
	$classes .= $a['center'] ? ' sec-head--center' : '';
	$classes .= $a['light'] ? ' sec-head--light' : '';
	?>
	<div class="<?php echo esc_attr( $classes ); ?>">
		<div class="sec-head__text">
			<?php if ( $a['eyebrow'] ) : ?>
				<span class="eyebrow<?php echo $a['light'] ? ' eyebrow--light' : ''; ?>"><?php echo esc_html( $a['eyebrow'] ); ?></span>
			<?php endif; ?>

			<?php if ( $a['title'] ) : ?>
				<h2 class="sec-head__title"><?php echo wp_kses_post( $a['title'] ); ?></h2>
			<?php endif; ?>

			<?php if ( $a['sub'] ) : ?>
				<p class="sec-head__sub"><?php echo wp_kses_post( $a['sub'] ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( $a['link_url'] && $a['link_text'] ) : ?>
			<a class="link-arrow<?php echo $a['light'] ? ' link-arrow--light' : ''; ?>" href="<?php echo esc_url( $a['link_url'] ); ?>">
				<?php echo esc_html( $a['link_text'] ); ?>
				<i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
			</a>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Product card.
 *
 * Renders identically for demo and real products. The full product payload is
 * embedded as JSON so quick-view can open instantly without a round trip.
 *
 * @param array $product Normalised product (see myshop_get_products()).
 * @param int   $index   Position in the grid, used to stagger the reveal.
 */
function myshop_product_card( $product, $index = 0 ) {
	$is_demo     = ! empty( $product['is_demo'] );
	$purchasable = ! empty( $product['purchasable'] );

	$payload = wp_json_encode(
		array(
			'id'       => $product['id'],
			'name'     => $product['name'],
			'category' => $product['category'],
			'price'    => $product['price_html'],
			'image'    => $product['image'],
			'rating'   => $product['rating'],
			'count'    => $product['rating_count'],
			'excerpt'  => isset( $product['excerpt'] ) ? $product['excerpt'] : '',
			'url'      => $product['permalink'],
			'demo'     => $is_demo,
			'buy'      => $purchasable,
		)
	);

	$delay = ( $index % 4 ) * 80;
	?>
	<article class="pcard reveal" style="--reveal-delay:<?php echo (int) $delay; ?>ms" data-product="<?php echo esc_attr( $payload ); ?>">
		<div class="pcard__media">
			<a class="pcard__link" href="<?php echo esc_url( $product['permalink'] ); ?>" tabindex="-1" aria-hidden="true">
				<img class="pcard__img" src="<?php echo esc_url( $product['image'] ); ?>" alt="" width="600" height="750" loading="lazy" decoding="async">
				<?php if ( ! empty( $product['image_hover'] ) && $product['image_hover'] !== $product['image'] ) : ?>
					<img class="pcard__img pcard__img--hover" src="<?php echo esc_url( $product['image_hover'] ); ?>" alt="" width="600" height="750" loading="lazy" decoding="async">
				<?php endif; ?>
			</a>

			<?php if ( ! empty( $product['badge'] ) ) : ?>
				<div class="pcard__badges">
					<span class="badge-pill badge-pill--<?php echo esc_attr( $product['badge']['type'] ); ?>">
						<?php echo esc_html( $product['badge']['label'] ); ?>
					</span>
				</div>
			<?php endif; ?>

			<?php
			// With YITH active, hearts render their saved state server-side.
			$in_wishlist = ! $is_demo
				&& function_exists( 'yith_wcwl_is_product_in_wishlist' )
				&& yith_wcwl_is_product_in_wishlist( $product['id'] );
			?>
			<button type="button" class="pcard__wish js-wishlist<?php echo $in_wishlist ? ' is-active' : ''; ?>"
				data-id="<?php echo esc_attr( $product['id'] ); ?>"
				aria-label="<?php esc_attr_e( 'Save to wishlist', 'base-theme' ); ?>">
				<i class="<?php echo $in_wishlist ? 'fa-solid' : 'fa-regular'; ?> fa-heart" aria-hidden="true"></i>
			</button>

			<div class="pcard__actions">
				<?php if ( $purchasable ) : ?>
					<button type="button" class="pcard__add js-add-to-cart"
						data-product-id="<?php echo esc_attr( $product['id'] ); ?>">
						<i class="fa-solid fa-bag-shopping" aria-hidden="true"></i>
						<span><?php esc_html_e( 'Add to cart', 'base-theme' ); ?></span>
					</button>
				<?php elseif ( ! $is_demo ) : ?>
					<?php // Variable/grouped products need options picked on the product page. ?>
					<a class="pcard__add" href="<?php echo esc_url( $product['permalink'] ); ?>">
						<i class="fa-solid fa-sliders" aria-hidden="true"></i>
						<span><?php esc_html_e( 'Choose options', 'base-theme' ); ?></span>
					</a>
				<?php else : ?>
					<button type="button" class="pcard__add js-add-to-cart" data-demo="1"
						data-product-id="<?php echo esc_attr( $product['id'] ); ?>">
						<i class="fa-solid fa-bag-shopping" aria-hidden="true"></i>
						<span><?php esc_html_e( 'Add to cart', 'base-theme' ); ?></span>
					</button>
				<?php endif; ?>

				<button type="button" class="pcard__quick js-quickview" aria-label="<?php esc_attr_e( 'Quick view', 'base-theme' ); ?>">
					<i class="fa-regular fa-eye" aria-hidden="true"></i>
				</button>
			</div>
		</div>

		<div class="pcard__body">
			<?php if ( $product['category'] ) : ?>
				<span class="pcard__cat"><?php echo esc_html( $product['category'] ); ?></span>
			<?php endif; ?>

			<h3 class="pcard__title">
				<a href="<?php echo esc_url( $product['permalink'] ); ?>"><?php echo esc_html( $product['name'] ); ?></a>
			</h3>

			<?php if ( $product['rating'] ) : ?>
				<div class="rating-row">
					<?php echo myshop_stars( $product['rating'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<span class="rating-row__count">(<?php echo (int) $product['rating_count']; ?>)</span>
				</div>
			<?php endif; ?>

			<div class="price"><?php echo wp_kses_post( $product['price_html'] ); ?></div>

			<?php if ( ! empty( $product['swatches'] ) ) : ?>
				<div class="pcard__swatches" aria-label="<?php esc_attr_e( 'Available colours', 'base-theme' ); ?>">
					<?php foreach ( $product['swatches'] as $hex ) : ?>
						<span class="pcard__swatch" style="--sw:<?php echo esc_attr( $hex ); ?>"></span>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</article>
	<?php
}
