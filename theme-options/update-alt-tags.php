<?php
// Add page under Tools
add_action('admin_menu', function () {
    add_management_page(
        'Update Alt Tags',
        'Update Alt Tags',
        'manage_options',
        'update-alt-tags',
        'update_alt_tags_admin_page'
    );
});

// Admin page UI
function update_alt_tags_admin_page() {
    ?>
    <div class="wrap">
        <h1>Update Alt Tags for Attachments</h1>

        <form method="post">
            <?php wp_nonce_field('update_alt_tags_action', 'update_alt_tags_nonce'); ?>

            <p>
                <label>
                    <input type="checkbox" name="overwrite" value="1">
                    Overwrite existing alt tags
                </label>
            </p>

            <?php submit_button('Run Update', 'primary', 'update_alt_tags'); ?>
        </form>
    </div>
    <?php

    // Handle form submit
    if (isset($_POST['update_alt_tags'])) {

        // Security check
        if (
            !isset($_POST['update_alt_tags_nonce']) ||
            !wp_verify_nonce($_POST['update_alt_tags_nonce'], 'update_alt_tags_action')
        ) {
            echo '<div class="notice notice-error"><p>Security check failed.</p></div>';
            return;
        }

        $overwrite = isset($_POST['overwrite']) ? true : false;

        update_attachment_alt_tags($overwrite);
    }
}

// Main function
function update_attachment_alt_tags($overwrite = false) {

    $media_query = new WP_Query([
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'posts_per_page' => -1,
    ]);

    $updated = 0;
    $updated_items = [];

    foreach ($media_query->posts as $post) {
        $file_path = get_attached_file($post->ID);
        if (!$file_path) continue;

        $current_alt = get_post_meta($post->ID, '_wp_attachment_image_alt', true);

        // Skip if alt exists and overwrite is OFF
        if (!$overwrite && !empty($current_alt)) continue;

        $filename = basename($file_path);

        $clean_name = str_replace(['-', '_'], ' ', $filename);
        $clean_name = pathinfo($clean_name, PATHINFO_FILENAME);
        $clean_name = ucfirst(strtolower($clean_name));

        update_post_meta($post->ID, '_wp_attachment_image_alt', $clean_name);

        $updated++;
        $updated_items[] = [
            'id' => $post->ID,
            'name' => $filename,
            'alt' => $clean_name
        ];
    }

    // Output results
    echo '<div class="notice notice-success">';
    echo '<p><strong>' . $updated . ' images updated.</strong></p>';

    if ($updated > 0) {
        echo '<ul style="max-height:300px;overflow:auto;background:#fff;padding:10px;border:1px solid #ddd;">';

        foreach ($updated_items as $item) {
            echo '<li style="margin-bottom:8px;">';

            // Thumbnail preview
            echo wp_get_attachment_image($item['id'], [40, 40], true, ['style' => 'margin-right:10px;vertical-align:middle;']);

            echo '<strong>' . esc_html($item['name']) . '</strong>';
            echo ' → ';
            echo esc_html($item['alt']);
            echo ' (ID: ' . $item['id'] . ')';

            echo '</li>';
        }

        echo '</ul>';
    }

    echo '</div>';
}