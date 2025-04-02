<?php
/**
 * Implements styles set in the theme customizer
 *
 * @package Customizer Library VT Blogging
 */

if ( ! function_exists( 'customizer_library_vt_blogging_build_styles' ) && class_exists( 'Customizer_Library_Styles' ) ) :
/**
 * Process user options to generate CSS needed to implement the choices.
 *
 * @since  1.0.0.
 *
 * @return void
 */
function customizer_library_vt_blogging_build_styles() {

	// Primary Color
	$setting = 'vt-blogging-primary-color';
	$mod = get_theme_mod( $setting, customizer_library_get_default( $setting ) );

	if ( $mod !== customizer_library_get_default( $setting ) ) {

		$color = sanitize_hex_color( $mod );

		Customizer_Library_Styles()->add( array(
			'selectors' => array(
				'.sf-menu li:hover a, .sf-menu li.sfHover a,
				.sf-menu li:hover li a:hover, .sf-menu li.sfHover li a:hover,
				.content-loop .read-more a:link, .content-loop .read-more a:visited,
				.content-loop .entry-title a:hover,
				.entry-tags .tag-links a:hover::before,
				.entry-tags .edit-link a,
				.entry-related h3 span,
				.author-box .author-meta .author-name a,
				.entry-related .hentry .entry-title a:hover,
				.sidebar .widget ul > li a:hover,
				#site-bottom .site-info a'
			),
			'declarations' => array(
				'color' => $color
			)
		) );
	}

	// Header Background Color
	$setting = 'vt-blogging-header-bg-color';
	$mod = get_theme_mod( $setting, customizer_library_get_default( $setting ) );

	if ( $mod !== customizer_library_get_default( $setting ) ) {

		$color = sanitize_hex_color( $mod );

		Customizer_Library_Styles()->add( array(
			'selectors' => array(
				'.site-header'
			),
			'declarations' => array(
				'background-color' => $color
			)
		) );
	}
	
	// Header Search Background Color
	$setting = 'vt-blogging-header-search-bg-color';
	$mod = get_theme_mod( $setting, customizer_library_get_default( $setting ) );

	if ( $mod !== customizer_library_get_default( $setting ) ) {

		$color = sanitize_hex_color( $mod );

		Customizer_Library_Styles()->add( array(
			'selectors' => array(
				'.header-search'
			),
			'declarations' => array(
				'background-color' => $color
			)
		) );
	}
	
	// Pagination Background Color
	$setting = 'vt-blogging-pagination-bg-color';
	$mod = get_theme_mod( $setting, customizer_library_get_default( $setting ) );

	if ( $mod !== customizer_library_get_default( $setting ) ) {

		$color = sanitize_hex_color( $mod );

		Customizer_Library_Styles()->add( array(
			'selectors' => array(
				'.pagination .page-numbers.current,
				.pagination .nav-links > a:hover'
			),
			'declarations' => array(
				'background-color' => $color
			)
		) );
	}
	
	// Border Color
	$setting = 'border';
	$mod = get_theme_mod( $setting, customizer_library_get_default( $setting ) );

	if ( $mod !== customizer_library_get_default( $setting ) ) {

		$color = sanitize_hex_color( $mod );

		Customizer_Library_Styles()->add( array(
			'selectors' => array(
				'.border'
			),
			'declarations' => array(
				'border-color' => $color
			)
		) );
	}

}
endif;

add_action( 'customizer_library_styles', 'customizer_library_vt_blogging_build_styles' );

if ( ! function_exists( 'customizer_library_vt_blogging_styles' ) ) :
/**
 * Generates the style tag and CSS needed for the theme options.
 *
 * By using the "Customizer_Library_Styles" filter, different components can print CSS in the header.
 * It is organized this way to ensure there is only one "style" tag.
 *
 * @since  1.0.0.
 *
 * @return void
 */
function customizer_library_vt_blogging_styles() {

	do_action( 'customizer_library_styles' );

	// Echo the rules
	$css = Customizer_Library_Styles()->build();

	if ( ! empty( $css ) ) {
		echo "\n<!-- Begin Custom CSS -->\n<style type=\"text/css\" id=\"vt-blogging-custom-css\">\n";
		echo $css;
		echo "\n</style>\n<!-- End Custom CSS -->\n";
	}
}
endif;

add_action( 'wp_head', 'customizer_library_vt_blogging_styles', 11 );