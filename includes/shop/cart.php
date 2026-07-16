<?php
/**
 * Cart drawer + AJAX fragments.
 *
 * Both fragments below are registered on woocommerce_add_to_cart_fragments, so
 * WooCommerce's own wc-ajax endpoints (add_to_cart / remove_from_cart) return
 * fresh markup for the header count and the drawer. No custom AJAX handler and
 * no dependency on WooCommerce's frontend JS.
 *
 * @package Base Theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Items currently in the cart.
 */
function myshop_cart_count() {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return 0;
	}
	return (int) WC()->cart->get_cart_contents_count();
}

/**
 * Header cart bubble. Always rendered (hidden at zero) so the fragment
 * selector still has an element to swap.
 */
function myshop_cart_count_html() {
	$count = myshop_cart_count();
	printf(
		'<span class="icon-btn__count js-cart-count%s">%d</span>',
		$count ? '' : ' is-empty',
		$count
	);
}

/**
 * Free-shipping progress bar.
 */
function myshop_cart_progress() {
	$threshold = myshop_free_shipping_threshold();
	if ( $threshold <= 0 || ! function_exists( 'WC' ) || ! WC()->cart ) {
		return;
	}

	$subtotal  = (float) WC()->cart->get_subtotal();
	$remaining = max( 0, $threshold - $subtotal );
	$percent   = $threshold > 0 ? min( 100, ( $subtotal / $threshold ) * 100 ) : 0;
	?>
	<div class="cart-progress">
		<p class="cart-progress__label">
			<?php if ( $remaining > 0 ) : ?>
				<?php
				printf(
					/* translators: %s: formatted amount remaining */
					esc_html__( 'Spend %s more for free shipping', 'base-theme' ),
					wp_kses_post( myshop_price_html( $remaining ) )
				);
				?>
			<?php else : ?>
				<i class="fa-solid fa-circle-check" aria-hidden="true"></i>
				<?php esc_html_e( 'You have unlocked free shipping', 'base-theme' ); ?>
			<?php endif; ?>
		</p>
		<div class="cart-progress__track">
			<span class="cart-progress__bar" style="width:<?php echo esc_attr( round( $percent ) ); ?>%"></span>
		</div>
	</div>
	<?php
}

/**
 * Drawer body: line items, totals and the checkout actions.
 */
function myshop_cart_drawer_content() {
	$has_cart = function_exists( 'WC' ) && WC()->cart && ! WC()->cart->is_empty();
	?>
	<div class="cart-drawer__content js-cart-drawer-content">
		<?php if ( ! $has_cart ) : ?>

			<div class="cart-empty">
				<span class="cart-empty__icon"><i class="fa-solid fa-bag-shopping" aria-hidden="true"></i></span>
				<h3 class="cart-empty__title"><?php esc_html_e( 'Your bag is empty', 'base-theme' ); ?></h3>
				<p class="cart-empty__text"><?php esc_html_e( 'Once you add something, it will show up here.', 'base-theme' ); ?></p>
				<a class="btn btn--primary btn--block js-drawer-close" href="<?php echo esc_url( myshop_shop_url() ); ?>">
					<?php esc_html_e( 'Start shopping', 'base-theme' ); ?>
				</a>
			</div>

		<?php else : ?>

			<?php myshop_cart_progress(); ?>

			<ul class="cart-list">
				<?php
				foreach ( WC()->cart->get_cart() as $key => $item ) {
					$product = $item['data'];
					if ( ! $product || ! $product->exists() || $item['quantity'] <= 0 ) {
						continue;
					}

					$image_id = $product->get_image_id();
					$image    = $image_id
						? wp_get_attachment_image_url( $image_id, 'woocommerce_thumbnail' )
						: wc_placeholder_img_src( 'woocommerce_thumbnail' );
					?>
					<li class="cart-item">
						<a class="cart-item__media" href="<?php echo esc_url( $product->get_permalink() ); ?>">
							<img src="<?php echo esc_url( $image ); ?>" alt="" width="80" height="100" loading="lazy">
						</a>

						<div class="cart-item__body">
							<a class="cart-item__title" href="<?php echo esc_url( $product->get_permalink() ); ?>">
								<?php echo esc_html( $product->get_name() ); ?>
							</a>
							<span class="cart-item__meta">
								<?php echo wp_kses_post( wc_price( $product->get_price() ) ); ?>
							</span>

							<div class="cart-item__controls">
								<div class="drawer-qty" data-key="<?php echo esc_attr( $key ); ?>">
									<button type="button" class="drawer-qty__btn js-drawer-qty" data-dir="-1"
										aria-label="<?php esc_attr_e( 'Decrease quantity', 'base-theme' ); ?>">&minus;</button>
									<span class="drawer-qty__val" data-qty="<?php echo (int) $item['quantity']; ?>"><?php echo (int) $item['quantity']; ?></span>
									<button type="button" class="drawer-qty__btn js-drawer-qty" data-dir="1"
										aria-label="<?php esc_attr_e( 'Increase quantity', 'base-theme' ); ?>">+</button>
								</div>

								<span class="cart-item__price">
									<?php echo wp_kses_post( WC()->cart->get_product_subtotal( $product, $item['quantity'] ) ); ?>
								</span>
							</div>
						</div>

						<button type="button" class="cart-item__remove js-cart-remove"
							data-key="<?php echo esc_attr( $key ); ?>"
							aria-label="<?php esc_attr_e( 'Remove item', 'base-theme' ); ?>">
							<i class="fa-solid fa-xmark" aria-hidden="true"></i>
						</button>
					</li>
					<?php
				}
				?>
			</ul>

			<div class="cart-foot">
				<div class="cart-foot__row">
					<span><?php esc_html_e( 'Subtotal', 'base-theme' ); ?></span>
					<strong><?php echo wp_kses_post( WC()->cart->get_cart_subtotal() ); ?></strong>
				</div>
				<p class="cart-foot__note"><?php esc_html_e( 'Shipping and taxes calculated at checkout.', 'base-theme' ); ?></p>

				<a class="btn btn--primary btn--block" href="<?php echo esc_url( wc_get_checkout_url() ); ?>">
					<?php esc_html_e( 'Checkout', 'base-theme' ); ?>
				</a>
				<a class="btn btn--ghost btn--block" href="<?php echo esc_url( wc_get_cart_url() ); ?>">
					<?php esc_html_e( 'View bag', 'base-theme' ); ?>
				</a>
			</div>

		<?php endif; ?>
	</div>
	<?php
}

/**
 * wc-ajax: change a line's quantity from the drawer.
 * Same response shape as add_to_cart/remove_from_cart (fragments + hash),
 * so main.js reuses its fragment swap.
 */
function myshop_set_cart_qty() {
	$key = isset( $_POST['cart_item_key'] ) ? wc_clean( wp_unslash( $_POST['cart_item_key'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$qty = isset( $_POST['quantity'] ) ? max( 0, (int) $_POST['quantity'] ) : null; // phpcs:ignore WordPress.Security.NonceVerification.Missing

	if ( '' === $key || null === $qty || ! WC()->cart || ! WC()->cart->get_cart_item( $key ) ) {
		wp_send_json( array( 'error' => true ) );
	}

	WC()->cart->set_quantity( $key, $qty, true );

	WC_AJAX::get_refreshed_fragments();
}
add_action( 'wc_ajax_myshop_set_qty', 'myshop_set_cart_qty' );

/**
 * Register both fragments with WooCommerce.
 */
function myshop_cart_fragments( $fragments ) {
	ob_start();
	myshop_cart_count_html();
	$fragments['span.js-cart-count'] = ob_get_clean();

	ob_start();
	myshop_cart_drawer_content();
	$fragments['div.js-cart-drawer-content'] = ob_get_clean();

	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'myshop_cart_fragments' );
