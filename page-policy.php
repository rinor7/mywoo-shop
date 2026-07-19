<?php
/**
 * Template Name: Policy Page
 *
 * Shared template for legal pages (Privacy, Terms, Cookies, Refunds...):
 * a quiet banner with the H1, then the content in a readable column.
 *
 * @package Base Theme
 */

get_header();
?>

<main id="primary" class="site-default policy">

	<?php
	$policy_eyebrow       = function_exists( 'get_field' ) ? trim( (string) get_field( 'policy_eyebrow' ) ) : '';
	$policy_updated_label = function_exists( 'get_field' ) ? trim( (string) get_field( 'policy_updated_label' ) ) : '';
	?>

	<section class="policy-hero">
		<div class="shop-container policy-hero__inner">
			<?php if ( '' !== $policy_eyebrow ) : ?>
				<span class="eyebrow"><?php echo esc_html( $policy_eyebrow ); ?></span>
			<?php endif; ?>

			<h1 class="policy-hero__title"><?php the_title(); ?></h1>

			<?php if ( '' !== $policy_updated_label ) : ?>
				<p class="policy-hero__updated">
					<?php echo esc_html( $policy_updated_label . ' ' . get_the_modified_date() ); ?>
				</p>
			<?php endif; ?>
		</div>
	</section>

	<div class="shop-container">
		<article class="policy-body">
			<?php
			while ( have_posts() ) :
				the_post();
				the_content();
			endwhile;
			?>
		</article>
	</div>

</main>

<?php get_footer(); ?>
