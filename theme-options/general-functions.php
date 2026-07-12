<?php
// Send no-cache headers on the frontend. Without them browsers heuristically
// reuse cached HTML, so edits "don't show" until a hard refresh — and a shop
// page (cart count, stock) must not be stale anyway. A future full-page cache
// plugin will manage its own headers on top of this.
function myshop_nocache_headers() {
	if ( ! is_admin() ) {
		nocache_headers();
	}
}
add_action( 'send_headers', 'myshop_nocache_headers' );

//Remove Comments Option from Admin Menu
function df_disable_comments_admin_menu() {
    remove_menu_page('edit-comments.php');
}
add_action('admin_menu', 'df_disable_comments_admin_menu');
// Remove comments from the admin bar
function df_remove_comments_admin_bar() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('comments');
}
add_action('wp_before_admin_bar_render', 'df_remove_comments_admin_bar');
// Remove comments and trackbacks support from post types
function df_remove_comment_support() {
    foreach (get_post_types() as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
}
add_action('init', 'df_remove_comment_support', 100);
// Redirect any user trying to access comments page
function df_redirect_comments_page() {
    global $pagenow;
    if ($pagenow === 'edit-comments.php') {
        wp_redirect(admin_url());
        exit;
    }
}
add_action('admin_init', 'df_redirect_comments_page');
// Close comments on the front-end
function df_disable_comments_status() {
    return false;
}
add_filter('comments_open', 'df_disable_comments_status', 20, 2);
add_filter('pings_open', 'df_disable_comments_status', 20, 2);
// Hide existing comments
function df_hide_existing_comments($comments) {
    $comments = array();
    return $comments;
}
add_filter('comments_array', 'df_hide_existing_comments', 10, 2);
//Function for rendering section headers
function strip_outer_p_tags($content) {
    // Remove outer <p> tags if they exist, but keep inner tags
    if (preg_match('#^<p>(.*)</p>$#is', trim($content), $matches)) {
        return $matches[1];
    }
    return $content;
}
function render_section_header($input, $post_id = null) {

    // If string → fetch fields
    if (is_string($input)) {
        $fields = get_field($input, $post_id ?: get_the_ID());
    } else {
        $fields = $input;
    }

    if (!is_array($fields) || empty($fields)) return;

    $title    = $fields['title_section'] ?? '';
    $subtitle = $fields['subtitle_section'] ?? '';

    if (!$title && !$subtitle) return;

    $margin_desktop = !empty($fields['margin_bottom_desktop']) 
        ? (int)$fields['margin_bottom_desktop'] . 'px' 
        : '6px';

    $margin_mobile = !empty($fields['margin_bottom_mobile']) 
        ? (int)$fields['margin_bottom_mobile'] . 'px' 
        : $margin_desktop;

    echo '<div class="section-header" style="--mb-desktop:' . esc_attr($margin_desktop) . ';--mb-mobile:' . esc_attr($margin_mobile) . ';">';

        if ($title) {
            echo '<div class="section-header-title">' . wp_kses_post(strip_outer_p_tags($title)) . '</div>';
        }

        if ($subtitle) {
            echo '<div class="section-header-subtitle">' . wp_kses_post(strip_outer_p_tags($subtitle)) . '</div>';
        }

    echo '</div>';
}

//Theme Settings Menu 
if (function_exists('acf_add_options_page')) {
    acf_add_options_page(array(
        'page_title'    => 'Global Settings',
        'menu_title'    => 'Global Settings',
        'menu_slug'     => 'global-settings',
        'capability'    => 'edit_posts',
        'redirect'      => false
    ));
}

// Enable pagination for post type archives
function enable_post_type_archive_pagination() {
    add_rewrite_rule(
        '^([^/]+)/page/([0-9]+)/?$',
        'index.php?post_type=$matches[1]&paged=$matches[2]',
        'top'
    );
}
add_action('init', 'enable_post_type_archive_pagination');

// Modify main query for post type archives to limit posts
function modify_post_type_archive_query($query) {
    if (!is_admin() && $query->is_main_query()) {
        if (is_post_type_archive()) {
            $query->set('posts_per_page', 3);
        }
    }
}
add_action('pre_get_posts', 'modify_post_type_archive_query');