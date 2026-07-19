<?php
/**
 * Policy Page — hero fields
 *
 * Adds a "Policy Hero" box to every page using the Policy Page template
 * (Privacy, Terms, Cookies, Refunds...). The template renders only what
 * is saved here — empty field means the element is not shown at all.
 *
 * @package Base Theme
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'acf/init',
	function () {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		acf_add_local_field_group(
			array(
				'key'      => 'group_ms_policy_hero',
				'title'    => __( 'Policy Hero', 'base-theme' ),
				'fields'   => array(
					array(
						'key'          => 'field_ms_policy_eyebrow',
						'name'         => 'policy_eyebrow',
						'label'        => __( 'Eyebrow', 'base-theme' ),
						'type'         => 'text',
						'instructions' => __( 'Small label above the page title (e.g. "Legal"). Leave empty to hide it.', 'base-theme' ),
					),
					array(
						'key'          => 'field_ms_policy_updated_label',
						'name'         => 'policy_updated_label',
						'label'        => __( '“Last updated” label', 'base-theme' ),
						'type'         => 'text',
						'instructions' => __( 'Shown under the title, followed automatically by the date this page was last edited. Leave empty to hide the whole line.', 'base-theme' ),
					),
				),
				'location' => array(
					array(
						array(
							'param'    => 'page_template',
							'operator' => '==',
							'value'    => 'page-policy.php',
						),
					),
				),
				'position' => 'side',
			)
		);
	}
);
