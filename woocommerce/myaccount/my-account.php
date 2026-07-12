<?php
/**
 * My Account layout — sidebar + content.
 *
 * @package Base Theme
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;

$account_user = wp_get_current_user();
?>

<div class="account">
	<aside class="account__side">
		<div class="account__card">
			<span class="account__avatar"><?php echo get_avatar( $account_user->ID, 52 ); ?></span>
			<strong class="account__name"><?php echo esc_html( $account_user->display_name ); ?></strong>
			<span class="account__since"><?php echo esc_html( myshop_member_since() ); ?></span>
		</div>

		<?php do_action( 'woocommerce_account_navigation' ); ?>
	</aside>

	<div class="account__content woocommerce-MyAccount-content">
		<?php do_action( 'woocommerce_account_content' ); ?>
	</div>
</div>
