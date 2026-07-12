<?php

//create ACF field with type Text and name icon to add icons on nav item, same for image
function my_wp_nav_menu_objects($items, $args) {
    foreach ($items as &$item) {
        $icon = get_field('icon', $item);
		$custom_text_field = get_post_meta($item->ID, 'custom_text_field', true);
        $image_image_url = get_field('image-nav', $item);
        if ($icon) { // Add font-awesome icon
            $item->title .= ' <i class="fab ' . $icon . '"></i>';
        }
		// if ($custom_text_field) { // Add custom text field
        //     $item->title .= ' <span class="custom-text-menu-item">' . esc_html($custom_text_field) . '</span>';
        // }
        if ($image_image_url) {// Add image style - svg is not supported
            $item->title .= ' <div style="background-image: url(' . esc_url($image_image_url) . ');" class="image-on-menu-item"></div>';
        }
    }
    return $items;
}
add_filter('wp_nav_menu_objects', 'my_wp_nav_menu_objects', 10, 2);