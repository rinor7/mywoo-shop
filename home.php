<?php get_header(); ?>

<main id="primary" class="site-archive archive-page-main-posts">

    <div class="container">
        <div class="archive-layout">
            <div class="archive-content">
                <?php if (have_posts()) : ?>

                    <div class="page-header">
                        <h1 class="page-title"><?php echo esc_html( get_the_title( (int) get_option( 'page_for_posts' ) ) ?: __( 'Blog', 'base-theme' ) ); ?></h1>
                    </div>

                    <div class="articles">
                    <?php while (have_posts()) : the_post(); ?>

                        <article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>
                            <?php if ( has_post_thumbnail() ) : ?>
                                <a class="post-card__media" href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail( 'medium_large', array( 'class' => 'post-card__img' ) ); ?>
                                </a>
                            <?php endif; ?>

                            <div class="post-card__body">
                                <span class="post-card__date"><?php echo esc_html( get_the_date() ); ?></span>
                                <h2 class="post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                                <p class="post-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p>
                            </div>
                        </article>

                    <?php endwhile; ?>
                    </div>

                    <?php the_posts_pagination(
                        array(
                            'prev_text' => __( 'Previous', 'base-theme' ),
                            'next_text' => __( 'Next', 'base-theme' ),
                        )
                    ); ?>

                <?php else : ?>

                    <p><?php _e('No posts found', 'base-theme'); ?></p>

                <?php endif; ?>
            </div>
            <?php get_sidebar(); ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>
