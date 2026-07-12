<?php
/**
 * Account dashboard — greeting, bento cards, curated products.
 *
 * @package Base Theme
 * @version 4.4.0
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
?>

<header class="account-hero">
	<h1 class="account-hero__title">
		<?php
		printf(
			/* translators: %s: customer first name */
			esc_html__( 'Welcome home, %s.', 'base-theme' ),
			esc_html( $first )
		);
		?>
	</h1>

	<p class="account-hero__text">
		<?php
		printf(
			wp_kses_post(
				/* translators: 1: orders url, 2: addresses url, 3: account url */
				__( 'From your dashboard you can review your <a href="%1$s">recent orders</a>, manage your <a href="%2$s">delivery addresses</a>, or refine your <a href="%3$s">account details</a>.', 'base-theme' )
			),
			esc_url( $orders_url ),
			esc_url( $addresses_url ),
			esc_url( $edit_url )
		);
		?>
	</p>
</header>

<div class="account-bento">

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

		<h2 class="acard__title"><?php esc_html_e( 'Latest Acquisitions', 'base-theme' ); ?></h2>

		<p class="acard__text">
			<?php
			if ( $snapshot['count'] ) {
				printf(
					/* translators: %d: number of recent orders */
					esc_html( _n( 'You have %d recent order in your history.', 'You have %d recent orders in your history.', $snapshot['count'], 'base-theme' ) ),
					(int) $snapshot['count']
				);
			} else {
				esc_html_e( 'No orders yet — your first acquisition awaits.', 'base-theme' );
			}
			?>
		</p>

		<div class="acard__foot">
			<a class="link-arrow" href="<?php echo esc_url( $snapshot['count'] ? $orders_url : myshop_shop_url() ); ?>">
				<?php $snapshot['count'] ? esc_html_e( 'View order history', 'base-theme' ) : esc_html_e( 'Start shopping', 'base-theme' ); ?>
				<i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
			</a>

			<?php if ( $snapshot['thumbs'] ) : ?>
				<span class="acard__thumbs">
					<?php foreach ( $snapshot['thumbs'] as $thumb ) : ?>
						<img src="<?php echo esc_url( $thumb ); ?>" alt="" width="44" height="44" loading="lazy">
					<?php endforeach; ?>
				</span>
			<?php endif; ?>
		</div>
	</article>

	<!-- Security -->
	<article class="acard acard--dark acard--security">
		<span class="acard__icon acard__icon--accent"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i></span>

		<h2 class="acard__title"><?php esc_html_e( 'Privacy & Security', 'base-theme' ); ?></h2>

		<p class="acard__text">
			<?php
			printf(
				/* translators: %s: account email */
				esc_html__( 'Signed in as %s. Keep your password fresh and yours alone.', 'base-theme' ),
				esc_html( $dash_user->user_email )
			);
			?>
		</p>

		<a class="acard__btn" href="<?php echo esc_url( $edit_url ); ?>"><?php esc_html_e( 'Security settings', 'base-theme' ); ?></a>
	</article>

	<!-- Default shipping -->
	<article class="acard acard--address">
		<span class="acard__icon"><i class="fa-solid fa-location-dot" aria-hidden="true"></i></span>

		<h2 class="acard__title"><?php esc_html_e( 'Default Shipping', 'base-theme' ); ?></h2>

		<?php if ( $shipping ) : ?>
			<address class="acard__address"><?php echo wp_kses_post( $shipping ); ?></address>
		<?php else : ?>
			<p class="acard__text"><?php esc_html_e( 'No address saved yet.', 'base-theme' ); ?></p>
		<?php endif; ?>

		<div class="acard__foot">
			<a class="link-arrow" href="<?php echo esc_url( $addresses_url ); ?>">
				<?php $shipping ? esc_html_e( 'Manage addresses', 'base-theme' ) : esc_html_e( 'Add address', 'base-theme' ); ?>
				<i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
			</a>
		</div>
	</article>

	<!-- Membership / perks -->
	<article class="acard acard--dark acard--perks grain">
		<span class="eyebrow eyebrow--light"><?php esc_html_e( 'Membership', 'base-theme' ); ?></span>

		<h2 class="acard__title acard__title--lg"><?php esc_html_e( 'The Inner Circle', 'base-theme' ); ?></h2>

		<p class="acard__text">
			<?php esc_html_e( 'Free delivery over €100, 30-day returns and first access to limited releases — yours with every order.', 'base-theme' ); ?>
		</p>

		<a class="acard__btn" href="<?php echo esc_url( myshop_shop_url() ); ?>"><?php esc_html_e( 'Explore the collection', 'base-theme' ); ?></a>
	</article>

</div>

<!-- Curated -->
<?php $curated = myshop_get_products( array( 'limit' => 4, 'type' => 'bestseller' ) ); ?>
<?php if ( $curated ) : ?>
	<section class="account-curated">
		<div class="sec-head">
			<div class="sec-head__text">
				<h2 class="sec-head__title account-curated__title"><?php esc_html_e( 'Curated for you', 'base-theme' ); ?></h2>
				<p class="sec-head__sub"><?php esc_html_e( 'Hand-selected pieces from the current collection.', 'base-theme' ); ?></p>
			</div>
			<a class="link-arrow" href="<?php echo esc_url( myshop_shop_url() ); ?>">
				<?php esc_html_e( 'View all', 'base-theme' ); ?>
				<i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
			</a>
		</div>

		<div class="product-grid">
			<?php foreach ( $curated as $i => $curated_product ) : ?>
				<?php myshop_product_card( $curated_product, $i ); ?>
			<?php endforeach; ?>
		</div>
	</section>
<?php endif; ?>

<?php
do_action( 'woocommerce_account_dashboard' );
