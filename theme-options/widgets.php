<?php

function standard_widgets_init() {
	register_sidebar(
		array('name'          => esc_html__( 'Footer Col 1', 'base-theme' ),
			'id'            => 'footer-1',
			'description'   => esc_html__( 'Text/HTML under the footer logo: short about text and social icons.', 'base-theme' ),
			'before_widget' => '<div class="widget-wrapper">',
			'after_widget'  => '</div>',
			'before_title'  => '<span class="widget-title">',
			'after_title'   => '</span>',)
	);
	register_sidebar(
		array('name'          => esc_html__( 'Footer Col 2', 'base-theme' ),
			'id'            => 'footer-2',
			'description'   => esc_html__( 'Add widgets here to appear in your site footer.', 'base-theme' ),
			'before_widget' => '<div class="widget-wrapper">',
			'after_widget'  => '</div>',
			'before_title'  => '<span class="widget-title">',
			'after_title'   => '</span>',)
	);
	register_sidebar(
		array('name'          => esc_html__( 'Footer Col 3', 'base-theme' ),
			'id'            => 'footer-3',
			'description'   => esc_html__( 'Add widgets here to appear in your site footer.', 'base-theme' ),
			'before_widget' => '<div class="widget-wrapper">',
			'after_widget'  => '</div>',
			'before_title'  => '<span class="widget-title">',
			'after_title'   => '</span>',)
	);
	register_sidebar(
		array('name'          => esc_html__( 'Footer Col 4', 'base-theme' ),
			'id'            => 'footer-4',
			'description'   => esc_html__( 'Add widgets here to appear in your site footer.', 'base-theme' ),
			'before_widget' => '<div class="widget-wrapper">',
			'after_widget'  => '</div>',
			'before_title'  => '<span class="widget-title">',
			'after_title'   => '</span>',)
	);
	register_sidebar(
		array('name'          => esc_html__( 'Footer Col 5', 'base-theme' ),
			'id'            => 'footer-5',
			'description'   => esc_html__( 'Add widgets here to appear in your site footer.', 'base-theme' ),
			'before_widget' => '<div class="widget-wrapper">',
			'after_widget'  => '</div>',
			'before_title'  => '<span class="widget-title">',
			'after_title'   => '</span>',)
	);
}
add_action( 'widgets_init', 'standard_widgets_init' );

/**
 * Social Icons widget — paste profile URLs in Appearance → Widgets, empty
 * fields are skipped. Renders the same ul.footer__social markup the theme
 * styles, so it can replace the hardcoded fallback in any footer column.
 */
class MyShop_Social_Widget extends WP_Widget {

	/** Networks offered in the widget form: slug => [label, Font Awesome class, placeholder]. */
	private const NETWORKS = array(
		'instagram' => array( 'Instagram', 'fa-instagram', '@username or full URL' ),
		'facebook'  => array( 'Facebook', 'fa-facebook-f', 'username or full URL' ),
		'whatsapp'  => array( 'WhatsApp', 'fa-whatsapp', '+383 44 123 456 or wa.me link' ),
		'x'         => array( 'X (Twitter)', 'fa-x-twitter', '@username or full URL' ),
		'linkedin'  => array( 'LinkedIn', 'fa-linkedin-in', 'username or full URL' ),
	);

	/**
	 * Accepts whatever the admin typed — @handle, phone number, bare domain or
	 * full URL — and returns a proper https:// link for the network.
	 */
	private function normalize_link( $slug, $value ) {
		$value = trim( (string) $value );

		if ( '' === $value ) {
			return '';
		}

		// Full URL pasted: keep it (bump plain http to https).
		if ( preg_match( '#^https?://#i', $value ) ) {
			return esc_url_raw( preg_replace( '#^http://#i', 'https://', $value ) );
		}

		if ( 'whatsapp' === $slug ) {
			// A phone number in any format, or a pasted wa.me/… link —
			// either way only the digits matter.
			$digits = preg_replace( '/\D+/', '', $value );
			return $digits ? 'https://wa.me/' . $digits : '';
		}

		$value = ltrim( $value, '@/' );

		// "instagram.com/user" style input: strip the domain, keep the handle/path.
		$domains = array(
			'instagram' => 'instagram.com',
			'facebook'  => 'facebook.com',
			'x'         => 'x.com',
			'linkedin'  => 'linkedin.com',
		);
		$value   = preg_replace( '#^(www\.)?' . preg_quote( $domains[ $slug ], '#' ) . '/#i', '', $value );

		if ( 'linkedin' === $slug && ! preg_match( '#^(in|company|school)/#i', $value ) ) {
			$value = 'in/' . $value; // bare username → personal profile path
		}

		return esc_url_raw( 'https://' . $domains[ $slug ] . '/' . $value );
	}

	public function __construct() {
		parent::__construct(
			'myshop_social',
			esc_html__( 'MyShop: Social Icons', 'base-theme' ),
			array( 'description' => esc_html__( 'Row of social profile icons. Empty URLs are hidden.', 'base-theme' ) )
		);
	}

	public function widget( $args, $instance ) {
		$links = array();

		foreach ( self::NETWORKS as $slug => $net ) {
			if ( ! empty( $instance[ $slug ] ) ) {
				$links[ $slug ] = $instance[ $slug ];
			}
		}

		if ( ! $links ) {
			return;
		}

		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . esc_html( $instance['title'] ) . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput
		}
		?>
		<ul class="footer__social">
			<?php foreach ( $links as $slug => $url ) : ?>
				<li>
					<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener"
						aria-label="<?php echo esc_attr( self::NETWORKS[ $slug ][0] ); ?>">
						<i class="fa-brands <?php echo esc_attr( self::NETWORKS[ $slug ][1] ); ?>" aria-hidden="true"></i>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php
		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput
	}

	public function form( $instance ) {
		$title = $instance['title'] ?? '';
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title (optional):', 'base-theme' ); ?></label>
			<input class="widefat" type="text"
				id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
				value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php foreach ( self::NETWORKS as $slug => $net ) : ?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( $slug ) ); ?>"><?php echo esc_html( $net[0] ); ?>:</label>
				<input class="widefat" type="text" placeholder="<?php echo esc_attr( $net[2] ); ?>"
					id="<?php echo esc_attr( $this->get_field_id( $slug ) ); ?>"
					name="<?php echo esc_attr( $this->get_field_name( $slug ) ); ?>"
					value="<?php echo esc_attr( $instance[ $slug ] ?? '' ); ?>">
			</p>
		<?php endforeach; ?>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance          = array();
		$instance['title'] = sanitize_text_field( $new_instance['title'] ?? '' );

		foreach ( array_keys( self::NETWORKS ) as $slug ) {
			$instance[ $slug ] = $this->normalize_link( $slug, $new_instance[ $slug ] ?? '' );
		}

		return $instance;
	}
}

add_action(
	'widgets_init',
	function () {
		register_widget( 'MyShop_Social_Widget' );
	}
);