<?php
/**
 * VT Blogging Theme Customizer.
 *
 * @package VT Blogging
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function vt_blogging_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
	$wp_customize->remove_section('header_image');

}
add_action( 'customize_register', 'vt_blogging_customize_register' );

/**
 * Enqueue the customizer stylesheet.
 */
function vt_blogging_enqueue_customizer_stylesheets() {

    wp_register_style( 'vt-blogging-customizer-css', get_template_directory_uri() . '/customizer/customizer-library/css/customizer.css', NULL, NULL, 'all' );
    wp_enqueue_style( 'vt-blogging-customizer-css' );
}
add_action( 'customize_controls_print_styles', 'vt_blogging_enqueue_customizer_stylesheets' );

function reset_mytheme_options() { 
    remove_theme_mods();
}
add_action( 'after_switch_theme', 'reset_mytheme_options' );