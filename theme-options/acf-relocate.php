<?php
// Automatically create the acf-json directory ( custom fields inside custom theme )
add_action('after_setup_theme', 'create_acf_json_dir');
function create_acf_json_dir() {
    $dir = get_template_directory() . '/acf-json';
    if (!is_dir($dir)) {
        mkdir($dir);
    }
}

// Export ACF settings to a JSON file
add_filter('acf/settings/save_json', 'my_acf_json_save_point');
function my_acf_json_save_point( $path ) {
    // update path
    $path = get_template_directory() . '/acf-json';
    
    // return
    return $path;
}

// Load ACF settings from a JSON file
add_filter('acf/settings/load_json', 'my_acf_json_load_point');
function my_acf_json_load_point( $paths ) {
    // remove original path (optional)
    unset($paths[0]);
    // append path
    $paths[] = get_template_directory() . '/acf-json';
    // return
    return $paths;
}