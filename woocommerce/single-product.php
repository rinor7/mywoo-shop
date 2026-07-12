<?php
/**
 * Single product — theme override.
 *
 * Skips the boxed .shop-container wrapper so the PDP hero can run full-bleed;
 * each section manages its own container.
 *
 * @package Base Theme
 * @version 1.6.4
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="primary" class="site-main pdp-main">

	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<?php wc_get_template_part( 'content', 'single-product' ); ?>
	<?php endwhile; ?>

</main>

<?php
get_footer();
