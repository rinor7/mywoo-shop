<?php get_header(); ?>

	<main id="primary" class="site-single single-page">

		<?php $hero_bg = function_exists( 'myshop_post_hero_background' ) ? myshop_post_hero_background( get_the_ID() ) : ''; ?>
		<header class="single-hero"<?php echo $hero_bg ? ' style="--bg-override:' . esc_attr( $hero_bg ) . '"' : ''; ?>>
			<div class="container">
				<h1 class="single-hero__title"><?php the_title(); ?></h1>
			</div>
		</header>

		<div class="container">
			<div class="content">
				<?php the_content(); ?>
			</div>
		</div>

	</main><!-- #main -->

<?php get_footer(); ?>
