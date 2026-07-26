<form class="widget-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get">
    <label class="screen-reader-text" for="widget-search"><?php esc_html_e( 'Search', 'base-theme' ); ?></label>
    <input class="widget-search-form__input" type="text" name="s" id="widget-search"
        value="<?php echo esc_attr( get_search_query() ); ?>"
        placeholder="<?php esc_attr_e( 'Search…', 'base-theme' ); ?>">
    <button class="widget-search-form__submit" type="submit" aria-label="<?php esc_attr_e( 'Search', 'base-theme' ); ?>">
        <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
    </button>
</form>
