<?php
/**
 * @package Base Theme
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />

    <!-- Change this to "index, follow" when you go live -->
    <meta name="robots" content="noindex, nofollow">

    <link rel="profile" href="https://gmpg.org/xfn/11">
    <link rel="apple-touch-icon" href="<?php echo get_template_directory_uri(); ?>/assets/img/apple-touch-icon.png">

    <link rel="preload" href="<?php echo get_template_directory_uri(); ?>/assets/fonts/Montserrat-Regular.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?php echo get_template_directory_uri(); ?>/assets/fonts/Montserrat-SemiBold.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?php echo get_template_directory_uri(); ?>/assets/fonts/Montserrat-Bold.woff2" as="font" type="font/woff2" crossorigin>

    <?php wp_head(); ?>
</head>

<body <?php body_class( wp_is_mobile() ? 'wp-is-mobile' : 'wp-is-desktop' ); ?>>

<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'base-theme' ); ?></a>

<div id="page" class="site">

    <!-- Announcement bar (messages: Global Settings → Announcement Bar) -->
    <?php $messages = function_exists( 'myshop_announce_messages' ) ? myshop_announce_messages() : array(); ?>
    <?php if ( $messages ) : ?>
    <div class="announce js-announce">
        <div class="shop-container announce__inner">
            <button type="button" class="announce__nav js-announce-prev" aria-label="<?php esc_attr_e( 'Previous message', 'base-theme' ); ?>">
                <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
            </button>

            <div class="announce__viewport" aria-live="polite">
                <?php foreach ( $messages as $i => $message ) : ?>
                    <p class="announce__item<?php echo 0 === $i ? ' is-active' : ''; ?>"><?php echo wp_kses_post( $message ); ?></p>
                <?php endforeach; ?>
            </div>

            <button type="button" class="announce__nav js-announce-next" aria-label="<?php esc_attr_e( 'Next message', 'base-theme' ); ?>">
                <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
            </button>

            <button type="button" class="announce__close js-announce-close" aria-label="<?php esc_attr_e( 'Dismiss', 'base-theme' ); ?>">
                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
            </button>
        </div>
    </div>
    <?php endif; ?>

    <!-- Header -->
    <header id="header-site" class="site-header js-header">
        <div class="shop-container header__inner">

            <button type="button" class="header__burger js-menu-open" aria-label="<?php esc_attr_e( 'Open menu', 'base-theme' ); ?>"
                aria-controls="mobile-menu" aria-expanded="false">
                <span class="burger"><span></span><span></span><span></span></span>
            </button>

            <div class="header__logo">
                <?php if ( has_custom_logo() ) : ?>
                    <?php the_custom_logo(); ?>
                <?php else : ?>
                    <a class="logo-text" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
                        <?php bloginfo( 'name' ); ?>
                    </a>
                <?php endif; ?>
            </div>

            <?php if ( function_exists( 'myshop_nav_menu_content' ) && 'search' === myshop_nav_menu_content() ) : ?>
                <form class="header__search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                    <i class="fa-solid fa-magnifying-glass header__search-icon" aria-hidden="true"></i>

                    <label class="screen-reader-text" for="header-search"><?php esc_html_e( 'Search products', 'base-theme' ); ?></label>
                    <input class="header__search-input" type="search" id="header-search" name="s"
                        placeholder="<?php esc_attr_e( 'What are you looking for?', 'base-theme' ); ?>" autocomplete="off">

                    <?php if ( function_exists( 'WC' ) ) : ?>
                        <input type="hidden" name="post_type" value="product">
                    <?php endif; ?>
                </form>
            <?php else : ?>
                <nav class="header__nav" aria-label="<?php esc_attr_e( 'Primary', 'base-theme' ); ?>">
                    <?php
                    wp_nav_menu(
                        array(
                            'theme_location' => 'menu-1',
                            'menu_id'        => 'primary-menu',
                            'menu_class'     => 'nav-list',
                            'container'      => false,
                            'depth'          => 2,
                            'fallback_cb'    => 'myshop_nav_fallback',
                        )
                    );
                    ?>
                </nav>
            <?php endif; ?>

            <div class="header__actions">
                <?php if ( function_exists( 'myshop_language_switcher' ) ) { myshop_language_switcher(); } ?>

                <?php if ( ! function_exists( 'myshop_nav_menu_content' ) || 'search' !== myshop_nav_menu_content() ) : ?>
                    <!-- Redundant when the header already shows a search field in the nav slot. -->
                    <button type="button" class="icon-btn js-search-open" aria-label="<?php esc_attr_e( 'Search', 'base-theme' ); ?>">
                        <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                    </button>
                <?php endif; ?>

                <a class="icon-btn header__account" href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/' ) ); ?>"
                    aria-label="<?php esc_attr_e( 'My account', 'base-theme' ); ?>">
                    <i class="fa-regular fa-user" aria-hidden="true"></i>
                </a>

                <?php $wishlist_url = function_exists( 'myshop_wishlist_url' ) ? myshop_wishlist_url() : ''; ?>
                <?php if ( $wishlist_url ) : ?>
                    <a class="icon-btn header__wish" href="<?php echo esc_url( $wishlist_url ); ?>" aria-label="<?php esc_attr_e( 'Wishlist', 'base-theme' ); ?>">
                        <i class="fa-regular fa-heart" aria-hidden="true"></i>
                        <?php $wl_count = myshop_wishlist_count(); ?>
                        <span class="icon-btn__count js-wish-count<?php echo $wl_count ? '' : ' is-empty'; ?>"><?php echo (int) $wl_count; ?></span>
                    </a>
                <?php else : ?>
                    <button type="button" class="icon-btn header__wish js-wishlist-peek" aria-label="<?php esc_attr_e( 'Wishlist', 'base-theme' ); ?>">
                        <i class="fa-regular fa-heart" aria-hidden="true"></i>
                        <span class="icon-btn__count js-wish-count is-empty">0</span>
                    </button>
                <?php endif; ?>

                <button type="button" class="icon-btn js-cart-open" aria-label="<?php esc_attr_e( 'Open bag', 'base-theme' ); ?>">
                    <i class="fa-solid fa-bag-shopping" aria-hidden="true"></i>
                    <?php myshop_cart_count_html(); ?>
                </button>
            </div>

        </div>
    </header>
