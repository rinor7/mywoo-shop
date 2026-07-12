<?php
/**
 * Cart page — theme override.
 *
 * Product table + coupon/update row on the left, order summary card on the
 * right, "Complete the ensemble" recommendations underneath.
 *
 * @package Base Theme
 * @version 7.9.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' );
?>

<div class="myshop-cart">

	<header class="myshop-cart__head">
		<h1 class="myshop-cart__title"><?php esc_html_e( 'Shopping Cart', 'base-theme' ); ?></h1>
		<p class="myshop-cart__sub"><?php esc_html_e( 'Review your selected pieces before checking out.', 'base-theme' ); ?></p>
	</header>

	<div class="myshop-cart__grid">

		<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">

			<table class="shop_table cart-table" cellspacing="0">
				<thead>
					<tr>
						<th class="cart-table__h cart-table__h--product"><?php esc_html_e( 'Product', 'base-theme' ); ?></th>
						<th class="cart-table__h"><?php esc_html_e( 'Price', 'base-theme' ); ?></th>
						<th class="cart-table__h"><?php esc_html_e( 'Quantity', 'base-theme' ); ?></th>
						<th class="cart-table__h cart-table__h--total"><?php esc_html_e( 'Total', 'base-theme' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php do_action( 'woocommerce_before_cart_contents' ); ?>

					<?php
					foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
						$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
						$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

						if ( ! $_product || ! $_product->exists() || $cart_item['quantity'] <= 0 || ! apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
							continue;
						}

						$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
						?>
						<tr class="woocommerce-cart-form__cart-item cart-row <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

							<td class="cart-row__product">
								<?php
								echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput
									'woocommerce_cart_item_remove_link',
									sprintf(
										'<a href="%s" class="cart-row__remove" aria-label="%s" data-product_id="%s" data-product_sku="%s"><i class="fa-solid fa-xmark" aria-hidden="true"></i></a>',
										esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
										esc_attr( sprintf( __( 'Remove %s from cart', 'base-theme' ), wp_strip_all_tags( $_product->get_name() ) ) ),
										esc_attr( $product_id ),
										esc_attr( $_product->get_sku() )
									),
									$cart_item_key
								);
								?>

								<span class="cart-row__media">
									<?php
									$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'woocommerce_thumbnail' ), $cart_item, $cart_item_key );
									if ( $product_permalink ) {
										printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // phpcs:ignore WordPress.Security.EscapeOutput
									} else {
										echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput
									}
									?>
								</span>

								<span class="cart-row__info">
									<span class="cart-row__name">
										<?php
										$name = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
										if ( $product_permalink ) {
											printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), wp_kses_post( $name ) );
										} else {
											echo wp_kses_post( $name );
										}
										?>
									</span>

									<?php
									// Variation / item meta.
									echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput

									$terms = get_the_terms( $product_id, 'product_cat' );
									if ( $terms && ! is_wp_error( $terms ) ) {
										echo '<span class="cart-row__meta">' . esc_html( $terms[0]->name ) . '</span>';
									}

									do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );
									?>
								</span>
							</td>

							<td class="cart-row__price" data-title="<?php esc_attr_e( 'Price', 'base-theme' ); ?>">
								<?php echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							</td>

							<td class="cart-row__qty" data-title="<?php esc_attr_e( 'Quantity', 'base-theme' ); ?>">
								<div class="qty-stepper">
									<button type="button" class="qty-stepper__btn js-cart-qty" data-dir="-1" aria-label="<?php esc_attr_e( 'Decrease quantity', 'base-theme' ); ?>">&minus;</button>
									<?php
									if ( $_product->is_sold_individually() ) {
										$min_quantity = 1;
										$max_quantity = 1;
									} else {
										$min_quantity = 0;
										$max_quantity = $_product->get_max_purchase_quantity();
									}

									echo woocommerce_quantity_input( // phpcs:ignore WordPress.Security.EscapeOutput
										array(
											'input_name'   => "cart[{$cart_item_key}][qty]",
											'input_value'  => $cart_item['quantity'],
											'max_value'    => $max_quantity,
											'min_value'    => $min_quantity,
											'product_name' => $_product->get_name(),
										),
										$_product,
										false
									);
									?>
									<button type="button" class="qty-stepper__btn js-cart-qty" data-dir="1" aria-label="<?php esc_attr_e( 'Increase quantity', 'base-theme' ); ?>">+</button>
								</div>
							</td>

							<td class="cart-row__total" data-title="<?php esc_attr_e( 'Total', 'base-theme' ); ?>">
								<?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							</td>
						</tr>
						<?php
					}
					?>

					<?php do_action( 'woocommerce_cart_contents' ); ?>
					<?php do_action( 'woocommerce_after_cart_contents' ); ?>
				</tbody>
			</table>

			<div class="cart-actions">
				<?php if ( wc_coupons_enabled() ) : ?>
					<div class="cart-actions__coupon">
						<label class="screen-reader-text" for="coupon_code"><?php esc_html_e( 'Coupon code', 'base-theme' ); ?></label>
						<input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon code', 'base-theme' ); ?>">
						<button type="submit" class="cart-actions__apply" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'base-theme' ); ?>"><?php esc_html_e( 'Apply', 'base-theme' ); ?></button>
						<?php do_action( 'woocommerce_cart_coupon' ); ?>
					</div>
				<?php endif; ?>

				<button type="submit" class="cart-actions__update" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'base-theme' ); ?>"><?php esc_html_e( 'Update cart', 'base-theme' ); ?></button>

				<?php do_action( 'woocommerce_cart_actions' ); ?>
				<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
			</div>

			<?php do_action( 'woocommerce_after_cart_table' ); ?>
		</form>

		<aside class="cart-summary">
			<div class="cart-summary__card">
				<h2 class="cart-summary__title"><?php esc_html_e( 'Order Summary', 'base-theme' ); ?></h2>
				<?php woocommerce_cart_totals(); ?>
				<?php myshop_payment_badges(); ?>
			</div>

			<div class="cart-summary__secure">
				<i class="fa-solid fa-shield-halved" aria-hidden="true"></i>
				<?php esc_html_e( 'Secure checkout guaranteed', 'base-theme' ); ?>
			</div>
		</aside>

	</div>

	<?php myshop_cart_ensemble(); ?>

</div>

<?php do_action( 'woocommerce_after_cart' ); ?>
