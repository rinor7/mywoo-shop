<?php
// Load all hero banner settings at once (from options page group)
$hero_banners = function_exists('get_field') ? get_field('hero_banners', 'option') : [];

$hero_class_attr = '';
$background_image = '';

if (is_tax() || is_category() || is_tag()) {
    $term = get_queried_object();
    $page_title = $term->name;

    // Hero from term
    $hero = function_exists('get_field') ? get_field('hero', $term) : '';
    $hero_content_type = function_exists('get_field') ? get_field('hero_content_type', $term) : '';

    // Fallback to hero_banners group
    if (!$hero) {
        $taxonomy = get_taxonomy($term->taxonomy);
        if (!empty($taxonomy->object_type[0])) {
            $post_type = $taxonomy->object_type[0];
            $taxonomy_field = 'taxonomy_hero_' . $post_type;

            if (!empty($hero_banners[$taxonomy_field])) {
                $hero = $hero_banners[$taxonomy_field];
                $hero_content_type = $hero_banners[$taxonomy_field . '_content_type'] ?? '';
            }
        }
    }

    // Fallback to default taxonomy hero
    if (!$hero && !empty($hero_banners['default_taxonomy_hero'])) {
        $hero = $hero_banners['default_taxonomy_hero'];
        $hero_content_type = '';
    }

    $thumbnail = '';

} elseif (is_home()) {
    $page_title = get_the_title(get_option('page_for_posts'));
    $post_type = 'post';
    $blog_page_id = get_option('page_for_posts');

    // First try to get hero from the Blog page itself
    $hero = function_exists('get_field') ? get_field('hero', $blog_page_id) : '';
    $hero_content_type = function_exists('get_field') ? get_field('hero_content_type', $blog_page_id) : '';

    // Fallback to Global Settings if no page-specific hero
    if (!$hero) {
        $hero = $hero_banners['archive_hero_post'] ?? '';
        $hero_content_type = $hero_banners['archive_hero_post_content_type'] ?? '';
    }

    // Final fallback to default archive hero
    if (!$hero && !empty($hero_banners['default_archive_hero'])) {
        $hero = $hero_banners['default_archive_hero'];
        $hero_content_type = '';
    }

    $thumbnail = '';

} elseif (is_post_type_archive()) {
    $post_type = get_post_type();
    $post_type_object = get_post_type_object($post_type);
    $page_title = $post_type_object ? $post_type_object->labels->singular_name : '';

    $archive_field = 'archive_hero_' . $post_type;
    $hero = $hero_banners[$archive_field] ?? '';
    $hero_content_type = $hero_banners[$archive_field . '_content_type'] ?? '';

    if (!$hero && !empty($hero_banners['default_archive_hero'])) {
        $hero = $hero_banners['default_archive_hero'];
        $hero_content_type = '';
    }

    $thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'full');

} elseif (is_singular('page')) {
    $page_title = get_the_title();
    $hero = function_exists('get_field') ? get_field('hero', get_the_ID()) : '';
    $hero_content_type = function_exists('get_field') ? get_field('hero_content_type', get_the_ID()) : '';
    $thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'full');
} elseif (is_singular()) {
    $post_type = get_post_type();
    $page_title = get_the_title();

    $post_type_field = 'hero_' . $post_type;
    $hero = $hero_banners[$post_type_field] ?? '';
    $hero_content_type = $hero_banners[$post_type_field . '_content_type'] ?? '';

    if (!$hero && !empty($hero_banners['default_single_hero'])) {
        $hero = $hero_banners['default_single_hero'];
        $hero_content_type = '';
    }

    $thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'full');
}

// Fallback order: Post Type Hero → Global Hero → Thumbnail → Default image
$default_image = get_template_directory_uri() . '/assets/img/bg.webp';
$background_image = $hero ?: ($thumbnail ?: $default_image);

// Only add class if hero exists and content type selected
$hero_class_attr = ($hero && $hero_content_type) ? $hero_content_type : '';

if ($background_image): ?>
<section class="block-hero <?php echo esc_attr($hero_class_attr); ?>" style="background-image: url('<?php echo esc_url($background_image); ?>');">
    <div class="container">
        <div class="block-hero-content">
            <div class="content">
                <h1 class="hero-title"><?php echo esc_html($page_title); ?></h1>
                <div class="breadcrumbs">
                    <?php if (function_exists('yoast_breadcrumb')) {
                        yoast_breadcrumb('<p id="breadcrumbs">','</p>');
                    } ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>
