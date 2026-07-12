<form action="/" method="get">
    <label for="search">Search in <?php echo home_url( '/' ); ?></label>
    <input type="text" name="s" id="search" value="<?php the_search_query(); ?>" />
    <input 
        type="image" 
        alt="Search" 
        src="<?php bloginfo( 'template_url' ); ?>/assets/img/search.png" 
        width="26" 
        height="26" 
    />
	<!-- <input type="hidden" value="post" name="post_type" id="post_type" /> -->
</form>
