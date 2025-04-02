<?php
/**
 * Template for displaying search forms
 *
 * @since VT Blogging 1.0
 */
?>

<form id="searchform" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<span class="screen-reader-text"><?php echo esc_attr_x( 'Search for:', 'label', 'vt-blogging' ); ?></span>
	<input type="search" class="search-input" placeholder="<?php echo esc_attr_x( 'Search...', 'placeholder', 'vt-blogging' ); ?>" value="<?php echo get_search_query(); ?>" name="s" title="<?php echo esc_attr_x( 'Search for:', 'label', 'vt-blogging' ); ?>" />
	<button type="submit" role="button" class="search-submit"><?php echo esc_attr_x( 'Search', 'submit button', 'vt-blogging' ); ?></button>
</form>