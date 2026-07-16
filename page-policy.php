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

	<section class="policy-hero">
		<div class="shop-container policy-hero__inner">
			<span class="eyebrow"><?php esc_html_e( 'Legal', 'base-theme' ); ?></span>
			<h1 class="policy-hero__title"><?php the_title(); ?></h1>
			<p class="policy-hero__updated">
				<?php
				printf(
					/* translators: %s: last modified date */
					esc_html__( 'Last updated %s', 'base-theme' ),
					esc_html( get_the_modified_date() )
				);
				?>
			</p>
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
