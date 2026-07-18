<?php
/**
 * Multilingual (Polylang) integration.
 *
 * Everything degrades silently: Polylang deactivated, only one language
 * configured, or the Global Settings toggle switched off — the switcher
 * simply doesn't render and no Polylang function is ever called unguarded.
 *
 * @package Base Theme
 */

defined( 'ABSPATH' ) || exit;

/* ---------- Global Settings → Multilingual tab ---------- */
add_action(
	'acf/init',
	function () {
		if ( ! function_exists( 'acf_add_local_field' ) ) {
			return;
		}

		$parent = 'group_683a451131cc8'; // existing "Global Settings" group (acf-json)

		acf_add_local_field(
			array(
				'key'       => 'field_ms_tab_multilingual',
				'parent'    => $parent,
				'label'     => __( 'Multilingual', 'base-theme' ),
				'type'      => 'tab',
				'placement' => 'top',
			)
		);

		acf_add_local_field(
			array(
				'key'           => 'field_ms_enable_multilingual',
				'parent'        => $parent,
				'name'          => 'enable_multilingual',
				'label'         => __( 'Enable multilingual', 'base-theme' ),
				'type'          => 'true_false',
				'ui'            => 1,
				'default_value' => 1,
				'instructions'  => __( 'Shows the language switcher when Polylang is active and more than one language is configured. Turning this off hides all language UI — nothing breaks either way.', 'base-theme' ),
			)
		);
	}
);

/**
 * Master switch: ACF toggle on (or never saved) AND Polylang active.
 *
 * @return bool
 */
function myshop_multilingual_enabled() {
	if ( ! function_exists( 'pll_the_languages' ) ) {
		return false;
	}

	if ( function_exists( 'get_field' ) ) {
		$flag = get_field( 'enable_multilingual', 'option' );

		// null = field never saved yet → keep the default (enabled).
		if ( null !== $flag && '' !== $flag && ! $flag ) {
			return false;
		}
	}

	return true;
}

/**
 * Configured languages as Polylang raw arrays — empty when multilingual
 * is off, Polylang is missing, or fewer than two languages exist.
 *
 * @return array[]
 */
function myshop_languages() {
	if ( ! myshop_multilingual_enabled() ) {
		return array();
	}

	$langs = pll_the_languages(
		array(
			'raw'                    => 1,
			'hide_if_empty'          => 0,
			'hide_current'           => 0,
			'force_home'             => 0,
		)
	);

	if ( ! is_array( $langs ) || count( $langs ) < 2 ) {
		return array();
	}

	return $langs;
}

/**
 * Compact DE / EN switcher. Prints nothing unless at least two languages
 * are available, so templates can call it unconditionally.
 *
 * @param string $variant '' for header, 'drawer' for the mobile menu.
 */
function myshop_language_switcher( $variant = '' ) {
	$langs = myshop_languages();

	if ( ! $langs ) {
		return;
	}

	$class = 'lang-switch' . ( $variant ? ' lang-switch--' . sanitize_html_class( $variant ) : '' );
	?>
	<nav class="<?php echo esc_attr( $class ); ?>" aria-label="<?php esc_attr_e( 'Language', 'base-theme' ); ?>">
		<?php foreach ( $langs as $lang ) : ?>
			<?php if ( ! empty( $lang['current_lang'] ) ) : ?>
				<span class="lang-switch__item is-active" aria-current="true">
					<?php echo esc_html( strtoupper( $lang['slug'] ) ); ?>
				</span>
			<?php else : ?>
				<a class="lang-switch__item" href="<?php echo esc_url( $lang['url'] ); ?>" lang="<?php echo esc_attr( $lang['locale'] ); ?>" hreflang="<?php echo esc_attr( $lang['locale'] ); ?>">
					<?php echo esc_html( strtoupper( $lang['slug'] ) ); ?>
				</a>
			<?php endif; ?>
		<?php endforeach; ?>
	</nav>
	<?php
}
