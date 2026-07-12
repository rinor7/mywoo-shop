<?php
if ( is_active_sidebar( 'widget-6' ) ) : ?>
	<aside id="secondary" class="widget-area" role="complementary">
		<?php dynamic_sidebar( 'widget-6' ); ?>

		<?php dynamic_sidebar( 'footer-1' ); ?>
	</aside><!-- #secondary -->
<?php endif; ?>