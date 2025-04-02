<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package VT Blogging
 */

/* Theme Dashboard */
if ( is_admin() && isset($_GET['activated'] ) && $pagenow == "themes.php" )
	wp_redirect( 'themes.php?page=vt-blogging-welcome');

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function vt_blogging_body_classes( $classes ) {
	// Adds a class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	return $classes;
}
add_filter( 'body_class', 'vt_blogging_body_classes' );

/**
 * Add a pingback url auto-discovery header for singularly identifiable articles.
 */
function vt_blogging_pingback_header() {
	if ( is_singular() && pings_open() ) {
		echo '<link rel="pingback" href="', bloginfo( 'pingback_url' ), '">';
	}
}
add_action( 'wp_head', 'vt_blogging_pingback_header' );

/**
 * Filter the except length to 20 characters.
 *
 * @param int $length Excerpt length.
 * @return int (Maybe) modified excerpt length.
 */
if ( ! function_exists( 'vt_blogging_custom_excerpt_length' ) ) :

function vt_blogging_custom_excerpt_length( $length ) {
    return get_theme_mod('entry-excerpt-length', '38');
}
add_filter( 'excerpt_length', 'vt_blogging_custom_excerpt_length', 999 );

endif;

/**
 * Customize excerpt more.
 */
if ( ! function_exists( 'vt_blogging_excerpt_more' ) ) :

function vt_blogging_excerpt_more( $more ) {
   return '... ';
}
add_filter( 'excerpt_more', 'vt_blogging_excerpt_more' );

endif;

/**
 * Display the first (single) category of post.
 */
if ( ! function_exists( 'vt_blogging_first_category' ) ) :
function vt_blogging_first_category() {
    $category = get_the_category();
    if ($category) {
      echo '<a href="' . get_category_link( $category[0]->term_id ) . '" title="' . sprintf( __( "View all posts in %s", 'vt-blogging' ), $category[0]->name ) . '" ' . '>' . $category[0]->name.'</a> ';
    }    
}
endif;

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
if ( ! function_exists( 'vt_blogging_categorized_blog' ) ) :

function vt_blogging_categorized_blog() {
    if ( false === ( $all_the_cool_cats = get_transient( 'vt_blogging_categories' ) ) ) {
        // Create an array of all the categories that are attached to posts.
        $all_the_cool_cats = get_categories( array(
            'fields'     => 'ids',
            'hide_empty' => 1,
            // We only need to know if there is more than one category.
            'number'     => 2,
        ) );

        // Count the number of categories that are attached to the posts.
        $all_the_cool_cats = count( $all_the_cool_cats );

        set_transient( 'vt_blogging_categories', $all_the_cool_cats );
    }

    if ( $all_the_cool_cats > 1 ) {
        // This blog has more than 1 category so vt_blogging_categorized_blog should return true.
        return true;
    } else {
        // This blog has only 1 category so vt_blogging_categorized_blog should return false.
        return false;
    }
}

endif;

/**
 * Footer info, copyright information
 */
if ( ! function_exists( 'vt_blogging_footer' ) ) :
function vt_blogging_footer() {
   $site_link = '<a href="' . esc_url( home_url( '/' ) ) . '" title="' . esc_attr( get_bloginfo( 'name', 'display' ) ) . '" ><span>' . get_bloginfo( 'name', 'display' ) . '</span></a>';

   $wp_link = '<a href="'.esc_url("https://wordpress.org").'" target="_blank" title="' . esc_attr__( 'WordPress', 'vt-blogging' ) . '"><span>' . __( 'WordPress', 'vt-blogging' ) . '</span></a>';

   $tg_link =  '<a href="'.esc_url("https://volthemes.com/theme/vt-blogging-pro/").'" target="_blank" title="'.esc_attr__( 'VolThemes', 'vt-blogging' ).'"><span>'.__( 'VolThemes', 'vt-blogging') .'</span></a>';

   $default_footer_value = sprintf( __( 'Copyright &copy; %1$s %2$s. All rights reserved.', 'vt-blogging' ), date_i18n( 'Y' ), $site_link ).'<br>'.sprintf( __( 'Theme: %1$s by %2$s.', 'vt-blogging' ), 'VT Blogging', $tg_link ).' '.sprintf( __( 'Powered by %s.', 'vt-blogging' ), $wp_link );

   $vt_blogging_footer = '<div class="site-info">'.$default_footer_value.'</div>';
   echo wp_kses_post($vt_blogging_footer);
}
endif;
add_action( 'vt_blogging_footer', 'vt_blogging_footer', 10 );

// Scroll to top
function vt_blogging_scroll_to_top() {
	if (get_theme_mod('back-top-on', 1) == 1){
?>
	<div id="back-top">
		<a href="#top" title="<?php echo esc_attr('Back to top', 'vt-blogging'); ?>"><span class="genericon genericon-collapse"></span></a>
	</div>
<?php
}}
add_action('wp_footer', 'vt_blogging_scroll_to_top');

/**
 * Flush out the transients used in vt_blogging_categorized_blog.
 */
if ( ! function_exists( 'vt_blogging_category_transient_flusher' ) ) :

function vt_blogging_category_transient_flusher() {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    // Like, beat it. Dig?
    delete_transient( 'vt_blogging_categories' );
}
add_action( 'edit_category', 'vt_blogging_category_transient_flusher' );
add_action( 'save_post',     'vt_blogging_category_transient_flusher' );

endif;