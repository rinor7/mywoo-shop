<?php
function site_identity_setup() {

    // Let WordPress handle the <title> tag
    add_theme_support('title-tag');

    // Enable featured images
    add_theme_support('post-thumbnails');

    // RSS feed links in <head>
    add_theme_support('automatic-feed-links');

    // Custom logo support
    add_theme_support('custom-logo');

    // Modern HTML5 markup
    add_theme_support('html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ]);

    // Wide & full alignment support (Gutenberg)
    add_theme_support('align-wide');

    // Responsive embeds (videos, iframes)
    add_theme_support('responsive-embeds');

    // Editor styles (if you style Gutenberg)
    add_theme_support('editor-styles');
	
}
add_action('after_setup_theme', 'site_identity_setup');

// Add Footer Logo Option to Customizer
add_action('customize_register', function($wp_customize) {
    $wp_customize->add_setting('footer_logo');
    $wp_customize->add_control(new WP_Customize_Image_Control(
        $wp_customize,
        'footer_logo',
        [
            'label' => 'Footer Logo (fill only if its needed different with Header version)',
            'section' => 'title_tagline', // same place as main logo
        ]
    ));
});