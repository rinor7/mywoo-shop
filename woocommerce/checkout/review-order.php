<?php
/**
 * Order review table — theme override.
 *
 * Item thumbnails with quantity bubbles, then subtotal / shipping / tax /
 * total rows. Shipping methods themselves render in the left column
 * (myshop_shipping_cards()); here only the chosen rate shows.
 *
 * The .woocommerce-checkout-review-order-table class is the AJAX fragment
 * anchor — everything inside re-renders on address/method changes.
 *
 * @package Base Theme
 * @version 5.2.0
 */

defined( 'ABSPATH' ) || exit;
?>
<table class="shop_table woocommerce-checkout-review-order-table co-review">

	<tbody class="co-review__items">
		<?php
		do_action( 'woocommerce_review_order_before_cart_contents' );

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

			if ( ! $_product || ! $_product->exists() || $cart_item['quantity'] <= 0 || ! apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				continue;
			}
			?>
			<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
				<td class="co-review__media">
					<span class="co-review__thumb">
						<?php echo $_product->get_image( 'woocommerce_thumbnail' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<span class="co-review__qty" aria-hidden="true"><?php echo esc_html( $cart_item['quantity'] ); ?></span>
					</span>
				</td>
				<td class="co-review__body">
					<span class="co-review__name"><?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) ); ?></span>
					<?php echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<span class="screen-reader-text"><?php echo esc_html( sprintf( _n( 'Quantity: %d', 'Quantity: %d', $cart_item['quantity'], 'base-theme' ), $cart_item['quantity'] ) ); ?></span>
				</td>
				<td class="co-review__price">
					<?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</td>
			</tr>
			<?php
		}

		do_action( 'woocommerce_review_order_after_cart_contents' );
		?>
	</tbody>

	<tfoot class="co-review__totals">

		<tr class="cart-subtotal">
			<th colspan="2"><?php esc_html_e( 'Subtotal', 'base-theme' ); ?></th>
			<td><?php wc_cart_totals_subtotal_html(); ?></td>
		</tr>

		<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
				<th colspan="2"><?php wc_cart_totals_coupon_label( $coupon ); ?></th>
				<td><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
			</tr>
		<?php endforeach; ?>

		<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
			<tr class="co-review__shipping">
				<th colspan="2">
					<?php esc_html_e( 'Shipping', 'base-theme' ); ?>
					<?php $chosen_label = myshop_chosen_shipping_label(); ?>
					<?php if ( $chosen_label ) : ?>
						<small><?php echo esc_html( $chosen_label ); ?></small>
					<?php endif; ?>
				</th>
				<td>
					<?php
					$shipping_total = WC()->cart->get_shipping_total() + array_sum( WC()->cart->get_shipping_taxes() );
					echo $chosen_label
						? wp_kses_post( wc_price( $shipping_total ) )
						: '<em>' . esc_html__( 'Calculated at next step', 'base-theme' ) . '</em>';
					?>
				</td>
			</tr>
		<?php endif; ?>

		<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
			<tr class="fee">
				<th colspan="2"><?php echo esc_html( $fee->name ); ?></th>
				<td><?php wc_cart_totals_fee_html( $fee ); ?></td>
			</tr>
		<?php endforeach; ?>

		<?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
			<?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
				<?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : ?>
					<tr class="tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
						<th colspan="2"><?php echo esc_html( $tax->label ); ?></th>
						<td><?php echo wp_kses_post( $tax->formatted_amount ); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr class="tax-total">
					<th colspan="2"><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></th>
					<td><?php wc_cart_totals_taxes_total_html(); ?></td>
				</tr>
			<?php endif; ?>
		<?php endif; ?>

		<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

		<tr class="order-total co-review__total">
			<th colspan="2"><?php esc_html_e( 'Total', 'base-theme' ); ?></th>
			<td><?php wc_cart_totals_order_total_html(); ?></td>
		</tr>

		<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>

	</tfoot>
</table>
