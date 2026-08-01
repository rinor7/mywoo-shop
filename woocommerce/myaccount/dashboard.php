<?php
/**
 * Account dashboard — greeting, bento cards, curated products.
 *
 * Every string here (and each bento card's visibility) comes from
 * Global Settings → Account Dashboard — see theme-options/account-dashboard.php.
 *
 * @package Base Theme
 * @version 5.3.0
 */

defined( 'ABSPATH' ) || exit;

$dash_user = wp_get_current_user();
$snapshot  = myshop_account_orders_snapshot();

$orders_url    = wc_get_account_endpoint_url( 'orders' );
$addresses_url = wc_get_account_endpoint_url( 'edit-address' );
$edit_url      = wc_get_account_endpoint_url( 'edit-account' );

$first = $dash_user->first_name ? $dash_user->first_name : $dash_user->display_name;

$shipping = wc_get_account_formatted_address( 'shipping' );
if ( ! $shipping ) {
	$shipping = wc_get_account_formatted_address( 'billing' );
}

$greeting_title = myshop_account_greeting_title( $first );
$greeting_text  = myshop_account_greeting_text(
	array(
		'orders'    => $orders_url,
		'addresses' => $addresses_url,
		'account'   => $edit_url,
	)
);
?>

<?php if ( $greeting_title || $greeting_text ) : ?>
	<header class="account-hero">
		<?php if ( $greeting_title ) : ?>
			<h1 class="account-hero__title"><?php echo esc_html( $greeting_title ); ?></h1>
		<?php endif; ?>

		<?php if ( $greeting_text ) : ?>
			<p class="account-hero__text"><?php echo wp_kses_post( $greeting_text ); ?></p>
		<?php endif; ?>
	</header>
<?php endif; ?>

<div class="account-bento">

	<?php if ( myshop_account_card_enabled( 'acct_orders_enabled' ) ) : ?>
		<!-- Latest orders -->
		<article class="acard acard--orders">
			<div class="acard__top">
				<span class="acard__icon"><i class="fa-solid fa-bag-shopping" aria-hidden="true"></i></span>
				<?php if ( $snapshot['last'] ) : ?>
					<span class="acard__tag">
						<?php esc_html_e( 'Last order', 'base-theme' ); ?>
						#<?php echo esc_html( $snapshot['last']->get_order_number() ); ?>
					</span>
				<?php else : ?>
					<span class="acard__tag"><?php esc_html_e( 'Last 30 days', 'base-theme' ); ?></span>
				<?php endif; ?>
			</div>

			<?php $orders_title = myshop_account_dashboard_field( 'acct_orders_title' ); ?>
			<?php if ( $orders_title ) : ?>
				<h2 class="acard__title"><?php echo esc_html( $orders_title ); ?></h2>
			<?php endif; ?>

			<?php
			if ( $snapshot['count'] ) {
				$orders_text = myshop_account_dashboard_field( 'acct_orders_text' );
				$orders_text = $orders_text ? str_replace( '{count}', (int) $snapshot['count'], $orders_text ) : '';
			} else {
				$orders_text = myshop_account_dashboard_field( 'acct_orders_empty_text' );
			}
			?>
			<?php if ( $orders_text ) : ?>
				<p class="acard__text"><?php echo esc_html( $orders_text ); ?></p>
			<?php endif; ?>

			<div class="acard__foot">
				<?php $orders_btn = $snapshot['count'] ? myshop_account_dashboard_field( 'acct_orders_btn' ) : myshop_account_dashboard_field( 'acct_orders_empty_btn' ); ?>
				<?php if ( $orders_btn ) : ?>
					<a class="link-arrow" href="<?php echo esc_url( $snapshot['count'] ? $orders_url : myshop_shop_url() ); ?>">
						<?php echo esc_html( $orders_btn ); ?>
						<i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
					</a>
				<?php endif; ?>

				<?php if ( $snapshot['thumbs'] ) : ?>
					<span class="acard__thumbs">
						<?php foreach ( $snapshot['thumbs'] as $thumb ) : ?>
							<img src="<?php echo esc_url( $thumb ); ?>" alt="" width="44" height="44" loading="lazy">
						<?php endforeach; ?>
					</span>
				<?php endif; ?>
			</div>
		</article>
	<?php endif; ?>

	<?php if ( myshop_account_card_enabled( 'acct_security_enabled' ) ) : ?>
		<!-- Security -->
		<article class="acard acard--dark acard--security">
			<span class="acard__icon acard__icon--accent"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i></span>

			<?php $security_title = myshop_account_dashboard_field( 'acct_security_title' ); ?>
			<?php if ( $security_title ) : ?>
				<h2 class="acard__title"><?php echo esc_html( $security_title ); ?></h2>
			<?php endif; ?>

			<?php $security_text = myshop_account_dashboard_field( 'acct_security_text' ); ?>
			<?php if ( $security_text ) : ?>
				<p class="acard__text"><?php echo esc_html( str_replace( '{email}', $dash_user->user_email, $security_text ) ); ?></p>
			<?php endif; ?>

			<?php $security_btn = myshop_account_dashboard_field( 'acct_security_btn' ); ?>
			<?php if ( $security_btn ) : ?>
				<a class="acard__btn" href="<?php echo esc_url( $edit_url ); ?>"><?php echo esc_html( $security_btn ); ?></a>
			<?php endif; ?>
		</article>
	<?php endif; ?>

	<?php if ( myshop_account_card_enabled( 'acct_shipping_enabled' ) ) : ?>
		<!-- Default shipping -->
		<article class="acard acard--address">
			<span class="acard__icon"><i class="fa-solid fa-location-dot" aria-hidden="true"></i></span>

			<?php $shipping_title = myshop_account_dashboard_field( 'acct_shipping_title' ); ?>
			<?php if ( $shipping_title ) : ?>
				<h2 class="acard__title"><?php echo esc_html( $shipping_title ); ?></h2>
			<?php endif; ?>

			<?php if ( $shipping ) : ?>
				<address class="acard__address"><?php echo wp_kses_post( $shipping ); ?></address>
			<?php else : ?>
				<?php $shipping_empty_text = myshop_account_dashboard_field( 'acct_shipping_empty_text' ); ?>
				<?php if ( $shipping_empty_text ) : ?>
					<p class="acard__text"><?php echo esc_html( $shipping_empty_text ); ?></p>
				<?php endif; ?>
			<?php endif; ?>

			<div class="acard__foot">
				<?php $shipping_btn = $shipping ? myshop_account_dashboard_field( 'acct_shipping_btn' ) : myshop_account_dashboard_field( 'acct_shipping_empty_btn' ); ?>
				<?php if ( $shipping_btn ) : ?>
					<a class="link-arrow" href="<?php echo esc_url( $addresses_url ); ?>">
						<?php echo esc_html( $shipping_btn ); ?>
						<i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
					</a>
				<?php endif; ?>
			</div>
		</article>
	<?php endif; ?>

	<?php if ( myshop_account_card_enabled( 'acct_membership_enabled' ) ) : ?>
		<!-- Membership / perks -->
		<article class="acard acard--dark acard--perks grain">
			<?php $membership_eyebrow = myshop_account_dashboard_field( 'acct_membership_eyebrow' ); ?>
			<?php if ( $membership_eyebrow ) : ?>
				<span class="eyebrow eyebrow--light"><?php echo esc_html( $membership_eyebrow ); ?></span>
			<?php endif; ?>

			<?php $membership_title = myshop_account_dashboard_field( 'acct_membership_title' ); ?>
			<?php if ( $membership_title ) : ?>
				<h2 class="acard__title acard__title--lg"><?php echo esc_html( $membership_title ); ?></h2>
			<?php endif; ?>

			<?php
			$myshop_perk_lines = array_map(
				static function ( $perk ) {
					return $perk['text'];
				},
				myshop_pdp_perks()
			);

			if ( $myshop_perk_lines ) {
				$membership_text = myshop_account_dashboard_field( 'acct_membership_text' );
				$membership_text = $membership_text ? str_replace( '{perks}', implode( ', ', $myshop_perk_lines ), $membership_text ) : '';
			} else {
				$membership_text = myshop_account_dashboard_field( 'acct_membership_empty_text' );
			}
			?>
			<?php if ( $membership_text ) : ?>
				<p class="acard__text"><?php echo esc_html( $membership_text ); ?></p>
			<?php endif; ?>

			<?php $membership_btn = myshop_account_dashboard_field( 'acct_membership_btn' ); ?>
			<?php if ( $membership_btn ) : ?>
				<a class="acard__btn" href="<?php echo esc_url( myshop_shop_url() ); ?>"><?php echo esc_html( $membership_btn ); ?></a>
			<?php endif; ?>
		</article>
	<?php endif; ?>

</div>

<!-- Curated -->
<?php
$curated = array();
if ( myshop_account_card_enabled( 'acct_curated_enabled' ) ) {
	// Admin's explicit picks first, in the order they were set.
	$curated_picked_ids = function_exists( 'myshop_account_curated_product_ids' ) ? myshop_account_curated_product_ids() : array();
	foreach ( $curated_picked_ids as $id ) {
		$curated_product = wc_get_product( $id );
		if ( $curated_product && 'publish' === $curated_product->get_status() ) {
			$curated[] = myshop_normalize_product( $curated_product );
		}
		if ( count( $curated ) >= 5 ) {
			break;
		}
	}

	// No picks (or none still published) — fall back to current bestsellers.
	if ( ! $curated ) {
		$curated = myshop_get_products( array( 'limit' => 5, 'type' => 'bestseller' ) );
	}
}
?>
<?php if ( $curated ) : ?>
	<section class="account-curated">
		<div class="sec-head">
			<div class="sec-head__text">
				<?php $curated_eyebrow = myshop_account_dashboard_field( 'acct_curated_eyebrow' ); ?>
				<?php if ( $curated_eyebrow ) : ?>
					<span class="eyebrow"><?php echo esc_html( $curated_eyebrow ); ?></span>
				<?php endif; ?>

				<?php $curated_title = myshop_account_dashboard_field( 'acct_curated_title' ); ?>
				<?php if ( $curated_title ) : ?>
					<h2 class="sec-head__title account-curated__title"><?php echo esc_html( $curated_title ); ?></h2>
				<?php endif; ?>

				<?php $curated_subtitle = myshop_account_dashboard_field( 'acct_curated_subtitle' ); ?>
				<?php if ( $curated_subtitle ) : ?>
					<p class="sec-head__sub"><?php echo esc_html( $curated_subtitle ); ?></p>
				<?php endif; ?>
			</div>

			<?php
			$curated_btn_enabled = (bool) myshop_opt( 'acct_curated_btn_enabled', true );
			$curated_link        = myshop_opt( 'acct_curated_link', array() );
			$curated_url         = ! empty( $curated_link['url'] ) ? $curated_link['url'] : myshop_shop_url();
			$curated_label       = ! empty( $curated_link['title'] ) ? $curated_link['title'] : __( 'View all', 'base-theme' );
			$curated_target      = ! empty( $curated_link['target'] ) ? $curated_link['target'] : '';
			?>
			<?php if ( $curated_btn_enabled ) : ?>
				<a class="link-arrow" href="<?php echo esc_url( $curated_url ); ?>"<?php echo $curated_target ? ' target="' . esc_attr( $curated_target ) . '"' : ''; ?>>
					<?php echo esc_html( $curated_label ); ?>
					<i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
				</a>
			<?php endif; ?>
		</div>

		<div class="product-grid">
			<?php foreach ( $curated as $i => $curated_product ) : ?>
				<?php myshop_product_card( $curated_product, $i, 'minimal' ); ?>
			<?php endforeach; ?>
		</div>

		<?php
		// Same products again, in the New arrivals slider markup — CSS shows
		// only one of the two depending on viewport width (see .account-curated
		// in _product-card.scss).
		?>
		<div class="products__carousel">
			<div class="swiper js-product-slider">
				<div class="swiper-wrapper">
					<?php foreach ( $curated as $i => $curated_product ) : ?>
						<div class="swiper-slide">
							<?php myshop_product_card( $curated_product, $i, 'minimal' ); ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<div class="products__progress">
			<span class="products__progress-bar js-product-progress"></span>
		</div>
	</section>
<?php endif; ?>

<?php
do_action( 'woocommerce_account_dashboard' );
