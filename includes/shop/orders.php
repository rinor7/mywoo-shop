<?php
/**
 * Order presentation helpers.
 *
 * Shared by the order-received (thank you) page and the My Account
 * view-order screen so both render the same design.
 *
 * @package Base Theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Status pill. Colours map in scss/shop/_orders.scss.
 *
 * @param WC_Order $order Order.
 */
function myshop_order_status_badge( $order ) {
	$status = $order->get_status();
	printf(
		'<span class="order-status order-status--%1$s"><i aria-hidden="true"></i>%2$s</span>',
		esc_attr( $status ),
		esc_html( wc_get_order_status_name( $status ) )
	);
}

/**
 * Items + totals card.
 *
 * @param WC_Order $order Order.
 */
function myshop_order_items_panel( $order ) {
	$items = $order->get_items();
	?>
	<section class="order-panel">
		<header class="order-panel__head">
			<h2 class="order-panel__title"><?php esc_html_e( 'Order details', 'base-theme' ); ?></h2>
			<span class="order-panel__count">
				<?php
				printf(
					/* translators: %d: number of items */
					esc_html( _n( '%d item', '%d items', $order->get_item_count(), 'base-theme' ) ),
					(int) $order->get_item_count()
				);
				?>
			</span>
		</header>

		<ul class="order-panel__items">
			<?php
			foreach ( $items as $item ) :
				$product   = $item->get_product();
				$permalink = $product ? $product->get_permalink( $item ) : '';
				$thumbnail = $product ? $product->get_image( 'woocommerce_gallery_thumbnail', array( 'class' => 'order-panel__img' ) ) : '';
				$meta_html = wc_display_item_meta( $item, array( 'echo' => false ) );
				?>
				<li class="order-panel__item">
					<span class="order-panel__media">
						<?php if ( $permalink && $thumbnail ) : ?>
							<a href="<?php echo esc_url( $permalink ); ?>" tabindex="-1"><?php echo wp_kses_post( $thumbnail ); ?></a>
						<?php else : ?>
							<?php echo wp_kses_post( $thumbnail ); ?>
						<?php endif; ?>
						<span class="order-panel__qty"><?php echo (int) $item->get_quantity(); ?></span>
					</span>

					<span class="order-panel__info">
						<?php if ( $permalink ) : ?>
							<a class="order-panel__name" href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $item->get_name() ); ?></a>
						<?php else : ?>
							<span class="order-panel__name"><?php echo esc_html( $item->get_name() ); ?></span>
						<?php endif; ?>

						<?php if ( $meta_html ) : ?>
							<span class="order-panel__meta"><?php echo wp_kses_post( $meta_html ); ?></span>
						<?php endif; ?>

						<span class="order-panel__each">
							<?php
							printf(
								/* translators: 1: quantity, 2: unit price */
								esc_html__( 'Qty %1$d × %2$s', 'base-theme' ),
								(int) $item->get_quantity(),
								wp_kses_post( wc_price( $order->get_item_total( $item, false, true ), array( 'currency' => $order->get_currency() ) ) )
							);
							?>
						</span>
					</span>

					<span class="order-panel__total"><?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>

		<dl class="order-panel__totals">
			<?php
			foreach ( $order->get_order_item_totals() as $key => $total ) :
				if ( 'payment_method' === $key ) {
					continue;
				}
				?>
				<div class="order-panel__row<?php echo 'order_total' === $key ? ' order-panel__row--grand' : ''; ?>">
					<dt><?php echo esc_html( $total['label'] ); ?></dt>
					<dd><?php echo wp_kses_post( $total['value'] ); ?></dd>
				</div>
			<?php endforeach; ?>
		</dl>
	</section>
	<?php
}

/**
 * Billing / shipping / payment cards.
 *
 * @param WC_Order $order Order.
 */
function myshop_order_addresses( $order ) {
	$billing  = $order->get_formatted_billing_address();
	$shipping = $order->get_formatted_shipping_address();
	?>
	<div class="order-cards">

		<?php if ( $billing ) : ?>
			<div class="order-card">
				<h3 class="order-card__title">
					<i class="fa-solid fa-location-dot" aria-hidden="true"></i>
					<?php esc_html_e( 'Billing address', 'base-theme' ); ?>
				</h3>
				<address class="order-card__body">
					<?php echo wp_kses_post( $billing ); ?>
				</address>

				<?php if ( $order->get_billing_phone() || $order->get_billing_email() ) : ?>
					<ul class="order-card__contact">
						<?php if ( $order->get_billing_phone() ) : ?>
							<li><i class="fa-solid fa-phone" aria-hidden="true"></i><?php echo esc_html( $order->get_billing_phone() ); ?></li>
						<?php endif; ?>
						<?php if ( $order->get_billing_email() ) : ?>
							<li><i class="fa-regular fa-envelope" aria-hidden="true"></i><?php echo esc_html( $order->get_billing_email() ); ?></li>
						<?php endif; ?>
					</ul>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( $shipping && $shipping !== $billing ) : ?>
			<div class="order-card">
				<h3 class="order-card__title">
					<i class="fa-solid fa-truck-fast" aria-hidden="true"></i>
					<?php esc_html_e( 'Shipping address', 'base-theme' ); ?>
				</h3>
				<address class="order-card__body">
					<?php echo wp_kses_post( $shipping ); ?>
				</address>
			</div>
		<?php endif; ?>

		<?php if ( $order->get_payment_method_title() ) : ?>
			<div class="order-card">
				<h3 class="order-card__title">
					<i class="fa-regular fa-credit-card" aria-hidden="true"></i>
					<?php esc_html_e( 'Payment', 'base-theme' ); ?>
				</h3>
				<p class="order-card__body"><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></p>
			</div>
		<?php endif; ?>

	</div>
	<?php
}
