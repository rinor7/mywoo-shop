<?php

add_action('customize_register', 'theme_customizer_register');
function theme_customizer_register($wp_customize) {
    // Create a new section for container max-width options
    $wp_customize->add_section('container_max_width_section', array(
        'title' => __('Container Max Width', 'base-theme'),
        'priority' => 30,
    ));

    // Register setting for each media query
    $wp_customize->add_setting('container_max_width_large_setting', array(
        'default' => 1140,
    ));
    $wp_customize->add_setting('container_max_width_medium_setting', array(
        'default' => 960,
    ));
    $wp_customize->add_setting('container_max_width_small_setting', array(
        'default' => 720,
    ));
    $wp_customize->add_setting('container_max_width_extra_small_setting', array(
        'default' => 540,
    ));

    // Step 3: Add Control for each media query
    $wp_customize->add_control('container_max_width_large_control', array(
        'label' => __('Large Screens (>= 1200px)', 'base-theme'),
        'section' => 'container_max_width_section',
        'settings' => 'container_max_width_large_setting',
        'type' => 'text',
        'input_attrs' => array(
            'min' => 800,
            'max' => 2000,
            'step' => 10,
        ),
    ));
    $wp_customize->add_control('container_max_width_medium_control', array(
        'label' => __('Medium Screens (>= 992px)', 'base-theme'),
        'section' => 'container_max_width_section',
        'settings' => 'container_max_width_medium_setting',
        'type' => 'text',
        'input_attrs' => array(
            'min' => 600,
            'max' => 1500,
            'step' => 10,
        ),
    ));
    $wp_customize->add_control('container_max_width_small_control', array(
        'label' => __('Small Screens (>= 768px)', 'base-theme'),
        'section' => 'container_max_width_section',
        'settings' => 'container_max_width_small_setting',
        'type' => 'text',
        'input_attrs' => array(
            'min' => 400,
            'max' => 1000,
            'step' => 10,
        ),
    ));
    $wp_customize->add_control('container_max_width_extra_small_control', array(
        'label' => __('Extra Small Screens (>= 576px)', 'base-theme'),
        'section' => 'container_max_width_section',
        'settings' => 'container_max_width_extra_small_setting',
        'type' => 'text',
        'input_attrs' => array(
            'min' => 300,
            'max' => 800,
            'step' => 10,
        ),
    ));
}

// Step 4: Output Custom CSS
add_action('wp_head', 'theme_custom_css');
function theme_custom_css() {
    ?>
    <style>
       @media (min-width: 576px) {
            .container {
                max-width: <?php echo get_theme_mod('container_max_width_extra_small_setting', 540); ?>px;
            }
        }
		@media (min-width: 768px) {
            .container {
                max-width: <?php echo get_theme_mod('container_max_width_small_setting', 720); ?>px;
            }
        }
		@media (min-width: 992px) {
            .container {
                max-width: <?php echo get_theme_mod('container_max_width_medium_setting', 960); ?>px;
            }
        }
        @media (min-width: 1200px) {
            .container {
                max-width: <?php echo get_theme_mod('container_max_width_large_setting', 1140); ?>px;
            }
        }
    </style>
    <?php
}
