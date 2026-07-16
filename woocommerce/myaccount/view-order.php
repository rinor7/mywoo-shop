<?php
/**
 * View Order — theme override.
 *
 * Order header with status pill, itemised panel, address cards and the
 * order-notes timeline. Replaces WooCommerce's plain tables.
 *
 * @package Base Theme
 * @version 10.6.0
 *
 * @var WC_Order $order
 * @var int      $order_id
 */

defined( 'ABSPATH' ) || exit;

$notes = $order->get_customer_order_notes();
?>

<div class="order-view">

	<header class="order-view__head">
		<div class="order-view__id">
			<a class="order-view__back" href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>">
				<i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
				<?php esc_html_e( 'Back to orders', 'base-theme' ); ?>
			</a>

			<h2 class="order-view__title">
				<?php
				/* translators: %s: order number */
				printf( esc_html__( 'Order #%s', 'base-theme' ), esc_html( $order->get_order_number() ) );
				?>
			</h2>

			<p class="order-view__date">
				<?php
				printf(
					/* translators: %s: order date */
					esc_html__( 'Placed on %s', 'base-theme' ),
					esc_html( wc_format_datetime( $order->get_date_created() ) )
				);
				?>
			</p>
		</div>

		<?php myshop_order_status_badge( $order ); ?>
	</header>

	<?php if ( $notes ) : ?>
		<section class="order-notes">
			<h3 class="order-notes__title"><?php esc_html_e( 'Order updates', 'woocommerce' ); ?></h3>
			<ol class="order-notes__list">
				<?php foreach ( $notes as $note ) : ?>
					<li class="order-notes__item">
						<time class="order-notes__date"><?php echo esc_html( date_i18n( get_option( 'date_format' ) . ', ' . get_option( 'time_format' ), strtotime( $note->comment_date ) ) ); ?></time>
						<div class="order-notes__text">
							<?php echo wp_kses_post( wpautop( wptexturize( $note->comment_content ) ) ); ?>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
		</section>
	<?php endif; ?>

	<?php myshop_order_items_panel( $order ); ?>

	<?php myshop_order_addresses( $order ); ?>

	<?php
	// Keep plugin hooks (downloads, subscriptions...) minus Woo's default tables.
	remove_action( 'woocommerce_view_order', 'woocommerce_order_details_table', 10 );
	do_action( 'woocommerce_view_order', $order_id );
	?>

</div>
