<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 *
 * @package Base-Theme
 */

get_header(); ?>

<main id="primary" class="site-main">
    <div class="container">
        <?php if (have_posts()) : ?>
            
            <div class="page-header">
                <h1 class="page-title"><?php _e('Blog', 'base-theme'); ?></h1>
            </div>

            <div class="posts-container">
                <?php while (have_posts()) : the_post(); ?>
                    
                    <article id="post-<?php the_ID(); ?>" <?php post_class('post-item'); ?>>
                        <div class="entry-header">
                            <h2 class="entry-title">
                                <a href="<?php the_permalink(); ?>" rel="bookmark">
                                    <?php the_title(); ?>
                                </a>
                            </h2>
                            
                            <div class="entry-meta">
                                <span class="posted-on">
                                    <time class="entry-date published" datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                        <?php echo get_the_date(); ?>
                                    </time>
                                </span>
                                
                                <span class="byline">
                                    <?php _e('by', 'base-theme'); ?> 
                                    <span class="author vcard">
                                        <a class="url fn n" href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>">
                                            <?php echo get_the_author(); ?>
                                        </a>
                                    </span>
                                </span>
                            </div>
                        </div>

                        <div class="entry-content">
                            <?php
                            if (is_home() || is_archive()) {
                                the_excerpt();
                            } else {
                                the_content();
                            }
                            ?>
                        </div>

                        <div class="entry-footer">
                            <a href="<?php the_permalink(); ?>" class="read-more">
                                <?php _e('Read More', 'base-theme'); ?>
                            </a>
                        </div>
                    </article>

                <?php endwhile; ?>
            </div>

            <?php
            // Pagination
            the_posts_navigation(array(
                'prev_text' => __('Older posts', 'base-theme'),
                'next_text' => __('Newer posts', 'base-theme'),
            ));
            ?>

        <?php else : ?>

            <section class="no-results not-found">
                <header class="page-header">
                    <h1 class="page-title"><?php _e('Nothing here', 'base-theme'); ?></h1>
                </header>

                <div class="page-content">
                    <p><?php _e('It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'base-theme'); ?></p>
                    <?php get_search_form(); ?>
                </div>
            </section>

        <?php endif; ?>
    </div>
</main>

<?php
get_sidebar();
get_footer();