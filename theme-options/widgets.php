<?php

function standard_widgets_init() {
	// register_sidebar(
	// 	array('name'          => esc_html__( 'Widget 2', 'standard' ),
	// 		'id'            => 'widget-2',
	// 		'before_widget' => '<div class="widget-wrapper">',
	// 		'after_widget'  => '</div>',
	// 		'before_title'  => '<span class="widget-title">',
	// 		'after_title'   => '</span>',)
	// ); 
	// register_sidebar(
	// 	array('name'          => esc_html__( 'Widget 3', 'standard' ),
	// 		'id'            => 'widget-3',
	// 		'before_widget' => '<div class="widget-wrapper">',
	// 		'after_widget'  => '</div>',
	// 		'before_title'  => '<span class="widget-title">',
	// 		'after_title'   => '</span>',)
	// );
	// register_sidebar(
	// 	array('name'          => esc_html__( 'Widget 4', 'standard' ),
	// 		'id'            => 'widget-4',
	// 		'before_widget' => '<div class="widget-wrapper">',
	// 		'after_widget'  => '</div>',
	// 		'before_title'  => '<span class="widget-title">',
	// 		'after_title'   => '</span>',)
	// );
	// register_sidebar(
	// 	array('name'          => esc_html__( 'Widget 5', 'standard' ),
	// 		'id'            => 'widget-5',
	// 		'before_widget' => '<div class="widget-wrapper">',
	// 		'after_widget'  => '</div>',
	// 		'before_title'  => '<span class="widget-title">',
	// 		'after_title'   => '</span>',)
	// );
	register_sidebar(
		array('name'          => esc_html__( 'Widget 6', 'base-theme' ),
			'id'            => 'widget-6',
			'description'   => esc_html__( 'Add widgets here to appear in your site footer.', 'base-theme' ),
			'before_widget' => '<div class="widget-wrapper">',
			'after_widget'  => '</div>',
			'before_title'  => '<span class="widget-title">',
			'after_title'   => '</span>',)
	);
	register_sidebar(
		array('name'          => esc_html__( 'Footer — Brand (under logo)', 'base-theme' ),
			'id'            => 'footer-1',
			'description'   => esc_html__( 'Text/HTML under the footer logo: short about text and social icons.', 'base-theme' ),
			'before_widget' => '<div class="widget-wrapper">',
			'after_widget'  => '</div>',
			'before_title'  => '<span class="widget-title">',
			'after_title'   => '</span>',)
	);
	register_sidebar(
		array('name'          => esc_html__( 'Footer Column 2', 'base-theme' ),
			'id'            => 'footer-2',
			'description'   => esc_html__( 'Add widgets here to appear in your site footer.', 'base-theme' ),
			'before_widget' => '<div class="widget-wrapper">',
			'after_widget'  => '</div>',
			'before_title'  => '<span class="widget-title">',
			'after_title'   => '</span>',)
	);
	register_sidebar(
		array('name'          => esc_html__( 'Footer Column 3', 'base-theme' ),
			'id'            => 'footer-3',
			'description'   => esc_html__( 'Add widgets here to appear in your site footer.', 'base-theme' ),
			'before_widget' => '<div class="widget-wrapper">',
			'after_widget'  => '</div>',
			'before_title'  => '<span class="widget-title">',
			'after_title'   => '</span>',)
	);
	register_sidebar(
		array('name'          => esc_html__( 'Footer Column 4', 'base-theme' ),
			'id'            => 'footer-4',
			'description'   => esc_html__( 'Add widgets here to appear in your site footer.', 'base-theme' ),
			'before_widget' => '<div class="widget-wrapper">',
			'after_widget'  => '</div>',
			'before_title'  => '<span class="widget-title">',
			'after_title'   => '</span>',)
	);
	register_sidebar(
		array('name'          => esc_html__( 'Footer Column 5', 'base-theme' ),
			'id'            => 'footer-5',
			'description'   => esc_html__( 'Add widgets here to appear in your site footer.', 'base-theme' ),
			'before_widget' => '<div class="widget-wrapper">',
			'after_widget'  => '</div>',
			'before_title'  => '<span class="widget-title">',
			'after_title'   => '</span>',)
	);
}
add_action( 'widgets_init', 'standard_widgets_init' );