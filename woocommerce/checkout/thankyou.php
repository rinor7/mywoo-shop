<?php
/**
 * Thankyou page — theme override.
 *
 * Confirmation hero, order meta chips, itemised summary and address cards.
 * Gateway hooks (payment instructions etc.) still fire; only WooCommerce's
 * default order-details table is swapped for the theme's own panel.
 *
 * @package Base Theme
 * @version 8.1.0
 *
 * @var WC_Order $order
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="woocommerce-order order-confirm">

	<?php if ( $order ) : ?>

		<?php do_action( 'woocommerce_before_thankyou', $order->get_id() ); ?>

		<?php if ( $order->has_status( 'failed' ) ) : ?>

			<header class="order-confirm__head order-confirm__head--failed">
				<span class="order-confirm__mark order-confirm__mark--failed">
					<i class="fa-solid fa-xmark" aria-hidden="true"></i>
				</span>
				<h1 class="order-confirm__title"><?php esc_html_e( 'Payment failed', 'base-theme' ); ?></h1>
				<p class="order-confirm__sub">
					<?php esc_html_e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce' ); ?>
				</p>

				<div class="order-confirm__actions">
					<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="btn btn--primary"><?php esc_html_e( 'Try again', 'base-theme' ); ?></a>
					<?php if ( is_user_logged_in() ) : ?>
						<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="btn btn--ghost"><?php esc_html_e( 'My account', 'woocommerce' ); ?></a>
					<?php endif; ?>
				</div>
			</header>

		<?php else : ?>

			<header class="order-confirm__head">
				<span class="order-confirm__mark">
					<i class="fa-solid fa-check" aria-hidden="true"></i>
				</span>

				<span class="eyebrow"><?php esc_html_e( 'Order confirmed', 'base-theme' ); ?></span>

				<h1 class="order-confirm__title">
					<?php
					$first_name = $order->get_billing_first_name();
					if ( $first_name ) {
						/* translators: %s: customer first name */
						printf( esc_html__( 'Thank you, %s!', 'base-theme' ), esc_html( $first_name ) );
					} else {
						esc_html_e( 'Thank you!', 'base-theme' );
					}
					?>
				</h1>

				<p class="order-confirm__sub">
					<?php
					if ( $order->get_billing_email() ) {
						printf(
							/* translators: %s: billing email */
							esc_html__( 'Your order has been received. A confirmation is on its way to %s.', 'base-theme' ),
							'<strong>' . esc_html( $order->get_billing_email() ) . '</strong>'
						); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					} else {
						esc_html_e( 'Your order has been received.', 'base-theme' );
					}
					?>
				</p>
			</header>

			<ul class="order-confirm__meta">
				<li>
					<span class="order-confirm__meta-label"><?php esc_html_e( 'Order number', 'base-theme' ); ?></span>
					<strong>#<?php echo esc_html( $order->get_order_number() ); ?></strong>
				</li>
				<li>
					<span class="order-confirm__meta-label"><?php esc_html_e( 'Date', 'base-theme' ); ?></span>
					<strong><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></strong>
				</li>
				<li>
					<span class="order-confirm__meta-label"><?php esc_html_e( 'Total', 'base-theme' ); ?></span>
					<strong><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></strong>
				</li>
				<?php if ( $order->get_payment_method_title() ) : ?>
					<li>
						<span class="order-confirm__meta-label"><?php esc_html_e( 'Payment', 'base-theme' ); ?></span>
						<strong><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></strong>
					</li>
				<?php endif; ?>
			</ul>

			<div class="order-confirm__grid">
				<div class="order-confirm__main">
					<?php myshop_order_items_panel( $order ); ?>
				</div>

				<aside class="order-confirm__aside">
					<?php myshop_order_addresses( $order ); ?>

					<div class="order-confirm__actions">
						<a class="btn btn--primary btn--block" href="<?php echo esc_url( myshop_shop_url() ); ?>">
							<?php esc_html_e( 'Continue shopping', 'base-theme' ); ?>
						</a>
						<?php if ( is_user_logged_in() ) : ?>
							<a class="btn btn--ghost btn--block" href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>">
								<?php esc_html_e( 'View my orders', 'base-theme' ); ?>
							</a>
						<?php endif; ?>
					</div>
				</aside>
			</div>

		<?php endif; ?>

		<?php
		// Gateway-specific extras (bank details, pay-later instructions...).
		do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() );

		// Plugins hooked here still run — the default details table is ours now.
		remove_action( 'woocommerce_thankyou', 'woocommerce_order_details_table', 10 );
		do_action( 'woocommerce_thankyou', $order->get_id() );
		?>

	<?php else : ?>

		<header class="order-confirm__head">
			<span class="order-confirm__mark">
				<i class="fa-solid fa-check" aria-hidden="true"></i>
			</span>
			<h1 class="order-confirm__title"><?php esc_html_e( 'Thank you!', 'base-theme' ); ?></h1>
			<p class="order-confirm__sub">
				<?php echo esc_html( apply_filters( 'woocommerce_thankyou_order_received_text', __( 'Thank you. Your order has been received.', 'woocommerce' ), null ) ); ?>
			</p>
			<div class="order-confirm__actions">
				<a class="btn btn--primary" href="<?php echo esc_url( myshop_shop_url() ); ?>">
					<?php esc_html_e( 'Continue shopping', 'base-theme' ); ?>
				</a>
			</div>
		</header>

	<?php endif; ?>

</div>
