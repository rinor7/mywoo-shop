<?php
/**
 * Home — newsletter.
 *
 * Posts to Contact Form 7 if a form is wired up later; for now it is a
 * front-end-only capture that confirms with a toast.
 *
 * @package Base Theme
 */

defined( 'ABSPATH' ) || exit;
?>

<section class="section newsletter grain">
	<div class="shop-container newsletter__inner">

		<div class="newsletter__copy reveal">
			<span class="eyebrow eyebrow--light"><?php echo esc_html( myshop_c( 'nl_eyebrow', __( 'Stay close', 'base-theme' ) ) ); ?></span>
			<h2 class="newsletter__title"><?php echo esc_html( myshop_c( 'nl_title', __( 'Ten percent off your first order', 'base-theme' ) ) ); ?></h2>
			<p class="newsletter__text">
				<?php echo esc_html( myshop_c( 'nl_text', __( 'Restocks, new releases and the occasional essay. One email a week, never more.', 'base-theme' ) ) ); ?>
			</p>
		</div>

		<form class="newsletter__form reveal js-newsletter" style="--reveal-delay:120ms" novalidate>
			<div class="newsletter__row">
				<label class="screen-reader-text" for="nl-email"><?php esc_html_e( 'Email address', 'base-theme' ); ?></label>
				<input class="field newsletter__input" type="email" id="nl-email" name="email" required
					placeholder="<?php esc_attr_e( 'you@example.com', 'base-theme' ); ?>">

				<button type="submit" class="btn btn--accent newsletter__submit">
					<?php esc_html_e( 'Subscribe', 'base-theme' ); ?>
				</button>
			</div>

			<p class="newsletter__note">
				<i class="fa-solid fa-lock" aria-hidden="true"></i>
				<?php echo esc_html( myshop_c( 'nl_note', __( 'No spam. Unsubscribe in one click.', 'base-theme' ) ) ); ?>
			</p>
		</form>

	</div>
</section>
