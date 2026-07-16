<?php
/**
 * Checkout form — theme override.
 *
 * Left: contact → address → shipping method → payment.
 * Right: order summary card with inline coupon.
 *
 * The core checkout JS keeps working because the key anchors survive:
 * form.checkout, #customer_details, .shipping_method inputs, #payment and
 * .woocommerce-checkout-review-order-table (both replaced by AJAX fragments).
 *
 * @package Base Theme
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_checkout_form', $checkout );

// Registration required and not logged in — Woo's message applies.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}

$fields = $checkout->get_checkout_fields( 'billing' );
?>

<form name="checkout" method="post" class="checkout woocommerce-checkout myshop-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data" aria-label="<?php echo esc_attr( __( 'Checkout', 'woocommerce' ) ); ?>">

	<div class="myshop-checkout__grid">

		<div class="myshop-checkout__main" id="customer_details">

			<!-- Contact -->
			<section class="co-section">
				<header class="co-section__head">
					<h2 class="co-section__title"><?php esc_html_e( 'Contact Information', 'base-theme' ); ?></h2>
					<?php if ( ! is_user_logged_in() ) : ?>
						<span class="co-section__aside">
							<?php esc_html_e( 'Already have an account?', 'base-theme' ); ?>
							<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php esc_html_e( 'Log in', 'base-theme' ); ?></a>
						</span>
					<?php endif; ?>
				</header>

				<?php
				if ( isset( $fields['billing_email'] ) ) {
					woocommerce_form_field( 'billing_email', $fields['billing_email'], $checkout->get_value( 'billing_email' ) );
				}
				?>
			</section>

			<!-- Address -->
			<section class="co-section">
				<header class="co-section__head">
					<h2 class="co-section__title"><?php esc_html_e( 'Shipping Address', 'base-theme' ); ?></h2>
				</header>

				<div class="woocommerce-billing-fields__field-wrapper co-address">
					<?php
					foreach ( $fields as $key => $field ) {
						if ( 'billing_email' === $key ) {
							continue;
						}
						woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
					}
					?>
				</div>

				<?php
				// Account creation fields, when guest checkout allows signup.
				if ( ! is_user_logged_in() && $checkout->is_registration_enabled() ) {
					$account_fields = $checkout->get_checkout_fields( 'account' );
					if ( $account_fields ) {
						echo '<div class="co-account">';
						if ( ! $checkout->is_registration_required() ) {
							?>
							<p class="form-row form-row-wide create-account">
								<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
									<input class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" id="createaccount" <?php checked( ( true === $checkout->get_value( 'createaccount' ) || ( true === apply_filters( 'woocommerce_create_account_default_checked', false ) ) ), true ); ?> type="checkbox" name="createaccount" value="1">
									<span><?php esc_html_e( 'Create an account?', 'woocommerce' ); ?></span>
								</label>
							</p>
							<?php
						}
						echo '<div class="create-account">';
						foreach ( $account_fields as $key => $field ) {
							woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
						}
						echo '</div></div>';
					}
				}

				do_action( 'woocommerce_checkout_after_customer_details' );
				?>
			</section>

			<!-- Shipping method -->
			<?php if ( WC()->cart->needs_shipping() ) : ?>
				<section class="co-section co-section--shipping">
					<header class="co-section__head">
						<h2 class="co-section__title"><?php esc_html_e( 'Shipping Method', 'base-theme' ); ?></h2>
					</header>
					<?php myshop_shipping_cards(); ?>
				</section>
			<?php endif; ?>

			<!-- Payment -->
			<section class="co-section co-section--payment">
				<header class="co-section__head">
					<h2 class="co-section__title"><?php esc_html_e( 'Payment', 'base-theme' ); ?></h2>
					<span class="co-section__lock"><i class="fa-solid fa-lock" aria-hidden="true"></i></span>
				</header>

				<?php woocommerce_checkout_payment(); ?>
			</section>

		</div>

		<aside class="myshop-checkout__aside">
			<div class="checkout-summary">
				<h2 class="checkout-summary__title"><?php esc_html_e( 'Order Summary', 'base-theme' ); ?></h2>

				<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

				<div id="order_review" class="woocommerce-checkout-review-order">
					<?php do_action( 'woocommerce_checkout_order_review_ms' ); ?>
					<?php wc_get_template( 'checkout/review-order.php' ); ?>
				</div>

				<?php if ( wc_coupons_enabled() ) : ?>
					<?php
					// NOT a <form>: this sits inside form.checkout, and browsers drop
					// nested form tags — the Apply button would submit the whole
					// checkout. main.js applies the code via wc-ajax instead.
					?>
					<div class="checkout-summary__coupon js-checkout-coupon">
						<div class="checkout-summary__coupon-msg js-coupon-msg" aria-live="polite"></div>
						<div class="checkout-summary__coupon-row">
							<label class="screen-reader-text" for="coupon_code"><?php esc_html_e( 'Coupon code', 'base-theme' ); ?></label>
							<input type="text" name="coupon_code" class="input-text" placeholder="<?php esc_attr_e( 'Gift card or discount code', 'base-theme' ); ?>" id="coupon_code" value="">
							<button type="button" class="checkout-summary__apply js-coupon-apply"><?php esc_html_e( 'Apply', 'base-theme' ); ?></button>
						</div>
					</div>
				<?php endif; ?>

				<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
			</div>
		</aside>

	</div>
</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
