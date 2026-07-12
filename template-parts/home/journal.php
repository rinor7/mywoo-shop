<?php
/**
 * Home — journal / latest posts.
 *
 * Uses real posts when the blog has any, demo entries otherwise.
 *
 * @package Base Theme
 */

defined( 'ABSPATH' ) || exit;

$query = new WP_Query(
	array(
		'post_type'           => 'post',
		'posts_per_page'      => 3,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);

$posts = array();

if ( $query->have_posts() ) {
	while ( $query->have_posts() ) {
		$query->the_post();

		$terms = get_the_category();

		$posts[] = array(
			'title' => get_the_title(),
			'link'  => get_permalink(),
			'date'  => get_the_date(),
			'cat'   => ! empty( $terms ) ? $terms[0]->name : __( 'Journal', 'base-theme' ),
			'text'  => wp_trim_words( get_the_excerpt(), 18 ),
			'image' => has_post_thumbnail() ? get_the_post_thumbnail_url( get_the_ID(), 'large' ) : myshop_placeholder( 'texture-2' ),
		);
	}
	wp_reset_postdata();
} else {
	$posts = array(
		array(
			'title' => __( 'How to care for full-grain leather', 'base-theme' ),
			'link'  => myshop_shop_url(),
			'date'  => date_i18n( get_option( 'date_format' ), strtotime( '-4 days' ) ),
			'cat'   => __( 'Guides', 'base-theme' ),
			'text'  => __( 'A tote should look better in year five than in week one. Four habits that get it there.', 'base-theme' ),
			'image' => myshop_placeholder( 'texture-1' ),
		),
		array(
			'title' => __( 'Inside the workshop: making the Meridian', 'base-theme' ),
			'link'  => myshop_shop_url(),
			'date'  => date_i18n( get_option( 'date_format' ), strtotime( '-12 days' ) ),
			'cat'   => __( 'Behind the scenes', 'base-theme' ),
			'text'  => __( 'Two hundred movements, assembled by six people. We spent a week watching them work.', 'base-theme' ),
			'image' => myshop_placeholder( 'texture-2' ),
		),
		array(
			'title' => __( 'Five pieces that outlast the trend cycle', 'base-theme' ),
			'link'  => myshop_shop_url(),
			'date'  => date_i18n( get_option( 'date_format' ), strtotime( '-21 days' ) ),
			'cat'   => __( 'The Edit', 'base-theme' ),
			'text'  => __( 'Buy once, keep for a decade. The short list we would start a wardrobe with.', 'base-theme' ),
			'image' => myshop_placeholder( 'texture-4' ),
		),
	);
}

if ( empty( $posts ) ) {
	return;
}
?>

<section class="section journal">
	<div class="shop-container">

		<?php
		myshop_section_head(
			array(
				'eyebrow'   => myshop_c( 'j_eyebrow', __( 'Journal', 'base-theme' ) ),
				'title'     => myshop_c( 'j_title', __( 'Stories from the workshop', 'base-theme' ) ),
				'link_url'  => get_permalink( get_option( 'page_for_posts' ) ) ? get_permalink( get_option( 'page_for_posts' ) ) : home_url( '/' ),
				'link_text' => __( 'Read the journal', 'base-theme' ),
			)
		);
		?>

		<div class="journal__grid">
			<?php foreach ( $posts as $i => $post_item ) : ?>
				<article class="jcard reveal" style="--reveal-delay:<?php echo (int) ( $i * 90 ); ?>ms">
					<a class="jcard__media" href="<?php echo esc_url( $post_item['link'] ); ?>" tabindex="-1" aria-hidden="true">
						<img src="<?php echo esc_url( $post_item['image'] ); ?>" alt="" loading="lazy" decoding="async">
					</a>

					<div class="jcard__body">
						<div class="jcard__meta">
							<span class="jcard__cat"><?php echo esc_html( $post_item['cat'] ); ?></span>
							<span class="jcard__dot" aria-hidden="true"></span>
							<time class="jcard__date"><?php echo esc_html( $post_item['date'] ); ?></time>
						</div>

						<h3 class="jcard__title">
							<a href="<?php echo esc_url( $post_item['link'] ); ?>"><?php echo esc_html( $post_item['title'] ); ?></a>
						</h3>

						<?php if ( $post_item['text'] ) : ?>
							<p class="jcard__text"><?php echo esc_html( $post_item['text'] ); ?></p>
						<?php endif; ?>

						<span class="link-arrow">
							<?php esc_html_e( 'Read more', 'base-theme' ); ?>
							<i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
						</span>
					</div>
				</article>
			<?php endforeach; ?>
		</div>

	</div>
</section>
