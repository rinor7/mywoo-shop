<?php
/* Template Name: Home */
get_header();
?>

<main id="primary" class="site-frontpage">

	<?php
	// Order and on/off state both come from myshop_home_sections();
	// the toggles live in the "Frontpage Sections" box on this page.
	foreach ( myshop_home_sections() as $key => $section ) {

		if ( ! myshop_section_on( $key ) ) {
			continue;
		}

		get_template_part( 'template-parts/home/' . $section[1] );
	}
	?>

</main>

<?php get_footer(); ?>
