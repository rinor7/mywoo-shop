<?php 
// ==============================
// PHONE HELPERS
// ==============================

// Get raw phone (used for tel:)
function get_global_phone() {
    return get_field('phone_number', 'option');
}

// Get display phone (fallback to main if empty)
function get_global_phone_display() {
    $display = get_field('phone_display', 'option');
    $phone   = get_global_phone();

    return $display ? $display : $phone;
}

// Clean phone for tel:
function get_global_phone_clean() {
    $phone = get_global_phone();
    if (!$phone) return '';
    return preg_replace('/[^0-9+]/', '', $phone);
}

// Return clickable phone
function get_phone_link() {
    $display = get_global_phone_display();
    $clean   = get_global_phone_clean();

    if (!$display || !$clean) return '';

    return '<a href="tel:' . esc_attr($clean) . '" class="phone-link" class="phone-link">' . esc_html($display) . '</a>';
}

// ==============================
// SHORTCODE
// ==============================

function phone_shortcode($atts) {
    $atts = shortcode_atts([
        'format' => 'link', // link | text | raw
    ], $atts);

    $display = get_global_phone_display();
    $clean   = get_global_phone_clean();

    if (!$display) return '';

    if ($atts['format'] === 'text') {
        return esc_html($display);
    }

    if ($atts['format'] === 'raw') {
        return esc_html($clean);
    }

    return get_phone_link();
}
add_shortcode('phone', 'phone_shortcode');

// ==============================
// EMAIL HELPERS (SIMPLIFIED)
// ==============================

function get_global_email() {
    return get_field('email_address', 'option');
}

function get_email_link() {
    $email = get_global_email();

    if (!$email) return '';

    return '<a href="mailto:' . esc_attr($email) . '" class="email-link">' . esc_html($email) . '</a>';
}

// ==============================
// SHORTCODE
// ==============================

function email_shortcode($atts) {
    $atts = shortcode_atts([
        'format' => 'link', // link | text | raw
    ], $atts);

    $email = get_global_email();

    if (!$email) return '';

    if ($atts['format'] === 'text') {
        return esc_html($email);
    }

    if ($atts['format'] === 'raw') {
        return esc_html($email);
    }

    return get_email_link();
}
add_shortcode('email', 'email_shortcode');