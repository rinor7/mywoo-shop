<img src="<?php echo get_bloginfo('template_directory')?>/src/img/res_icon.png" alt="image" loading=“lazy”>

<section class="" style="background-image: url(<?php echo get_bloginfo('template_directory')?>/src/img/bg.png);">

    <img src="<?php echo ( get_field('group')['image'] );?>" alt="image" loading=“lazy”>

    <img src="<?php the_post_thumbnail_url();?>" alt="image" loading=“lazy”>

    <img src="<?php the_field('image'); ?>" alt="image" loading=“lazy”>

    <?php dynamic_sidebar('widget');?>

    <?php the_field('field'); ?>

    <?php the_field('field', 11171); ?>

    <!-- The esc_html() function is used to escape HTML entities -->
    <?php echo esc_html(get_field('group')['title']); ?>

    <!-- This method outputs the content directly without any escaping -->
    <?php echo ( get_field('group')['title'] );?>

    <?php echo ( get_field('group', 11171)['title'] );?>

    <?php the_time('j F Y'); ?>

    <?php echo get_the_author(); ?>

    <?php the_permalink(); ?>

    <?php the_title(); ?>

    <?php the_content(); ?>

    <?php if(is_active_sidebar('widget') ) { ?>
    <ul>
        <?php dynamic_sidebar('widget');?>
    </ul>
    <?php } ?>

    <?php if (!empty(get_field('field'))) { ?>
    <?php the_field('field'); ?>
    <?php } ?>

    <a href="tel:0038349000000" aria-label="link">049 000 000</a>

    <a href="mailto:info@info.com" aria-label="link">info@info.com</a>

    <ul>
        <li><a href="/" aria-label="fb"><span class="fab fa-facebook-f"></span></a></li>
        <li><a href="/" aria-label="twitter"><span class="fab fa-twitter"></span></a></li>
        <li><a href="/" aria-label="linkedin"><span class="fab fa-linkedin-in"></span></a></li>
    </ul>

    <?php echo do_shortcode('[contact-form-7 id="39" title="Contact Form 1"]'); ?>

    <!-- Post Type -->
    <?php
    $args = array(
    'post_type' => 'posttype',
    'posts_per_page' => '-1',
    'order' => 'ASC',
    'category_name'  => 'your_category_slug',
    'tag' => 'your_tag_slug',
    'tag' => 'tag1,tag2,tag3',
    'terms'    => 'your_term',
    );
    $loop = new WP_Query($args);
    while($loop->have_posts()):
    $loop->the_post();
    ?>

    <?php
    endwhile;
    wp_reset_postdata();
?>

    <!-- POSTS default post type of WP -->
    <?php 
    $args = array(
    'posts_per_page' => 4,
    'order' => 'ASC',
    'category_name'  => 'your_category_slug',
    'tag' => 'your_tag_slug',
    'tag' => 'tag1,tag2,tag3',
    'terms'    => 'your_term',
    ); 
    $the_query = new WP_Query( $args );
    if($the_query->have_posts() ) : while ( $the_query->have_posts() ) : $the_query->the_post(); 
?>

    <?php
    endwhile;
    endif;
    wp_reset_postdata();
?>


    <!-- Repeater -->
    <?php
    if( have_rows('group') ):
        while ( have_rows('group') ) : the_row();
            $i = 1;
            if( have_rows('repeater') ):
                ?>
    <ul>
        <?php
                    while( have_rows('repeater') ): the_row();
                    ?>
        <li>
            <?php the_sub_field('field'); ?>
            <?php 
                    $url = get_sub_field('image');
                    $imageID = attachment_url_to_postid($url);
                    ?>
            <img src="<?php the_sub_field('image'); ?>"
                alt="<?= get_post_meta( $imageID, '_wp_attachment_image_alt', true);?>">
        </li>
        <?php
                    $i++;
                    endwhile;
                    ?>
    </ul>
    <?php
            endif;
        endwhile;
    endif;
?>

    <?php include("includes/headers/standard.php"); ?>

    <?php get_template_part('includes/headers/standard', null, array(
    'link' => $link_url,
    )); ?>


    <?php if (is_front_page() || is_404() || is_single() ): ?>
    <?php endif; ?>


    <?php if(wp_is_mobile()) { ?>
                  
    <?php } else { ?>
    
    <?php } ?>

    <?php echo __('Year', 'base-theme'); ?>

     <?php get_search_form(); ?>


    <!-- For posts (single.php) - Basic pagination inside the content-->
    <?php while (have_posts()) : the_post(); ?>
        <?php the_content(); ?>
        <?php wp_link_pages(); ?>
    <?php endwhile; ?>
    <!-- For posts (single.php) - Custom styled pagination -->
    <?php wp_link_pages(array(
        'before' => '<div class="page-links">Pages: ',
        'after' => '</div>',
        'separator' => ' | '
    )); ?>