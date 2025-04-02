<?php
/**
 * VT Blogging functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package VT Blogging
 */
$theme = wp_get_theme();
define('VT_BLOGGING_VERSION', $theme -> get('Version'));
define('VT_BLOGGING_AUTHOR_URI', $theme -> get('AuthorURI'));

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function vt_blogging_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'vt_blogging_content_width', 660 );
}
add_action( 'after_setup_theme', 'vt_blogging_content_width', 0 );

if ( ! function_exists( 'vt_blogging_setup' ) ) :

function vt_blogging_setup() {

	// Translations can be filed in the /languages/ directory.
	load_theme_textdomain( 'vt-blogging', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	// Let WordPress manage the document title.
	add_theme_support( 'title-tag' );

	// Enable support for Post Thumbnails on posts and pages
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'post-thumbnails', 300, 300, true); // default Post Thumbnail dimensions (cropped)
	add_image_size( 'vt_blogging_thumb', 200, 150, true);
	add_image_size( 'vt_blogging_related_post',  80, 76, true);
	add_image_size( 'vt_blogging_widget_thumb', 80 ); // 80 pixels wide (and unlimited height)

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary Menu', 'vt-blogging' )
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	/* Add callback for custom TinyMCE editor stylesheets. (editor-style.css) */
	add_editor_style('editor-style.css');
	
	// Enable support for Post Formats.
	add_theme_support( 'post-formats', array( 'image', 'gallery', 'video', 'audio'));
	
	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'vt_blogging_custom_background_args', array(
		'default-color' => 'f0f0f0',
		'default-image' => '',
	) ) );
	
	// Custom logo
	add_theme_support( 'custom-logo', array(
	   'height'      => 67,
	   'width'       => 200,
	   'flex-height' => true,
	   'flex-width' => true,
	   'header-text' => array( 'site-title', 'site-description' ),
	) );
	
}
endif;
add_action( 'after_setup_theme', 'vt_blogging_setup' );

/**
 * Enqueues scripts and styles.
 */
add_action('wp_enqueue_scripts', 'vt_blogging_scripts');
function vt_blogging_scripts() {

    // CSS
	wp_enqueue_style( 'vt-blogging-style', get_stylesheet_uri(), '', VT_BLOGGING_VERSION ); 
    wp_enqueue_style( 'genericons-style', get_template_directory_uri() . '/assets/fonts/genericons/genericons.css' );
	wp_enqueue_style( 'vt-blogging-fonts', vt_blogging_fonts_url(), array(), null );
	
    if ( get_theme_mod( 'site-layout', 'choice-1' ) == 'choice-1' ) {
    	wp_enqueue_style( 'responsive-style',   get_template_directory_uri() . '/responsive.css', array(), VT_BLOGGING_VERSION ); 
	}
	
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }  
	
    // JS
	wp_enqueue_script( 'jquery' ); // default Scripts Included and Registered by WordPress
	wp_enqueue_script( 'superfish', get_template_directory_uri() . '/assets/js/superfish.min.js', array(), '', true );
	wp_enqueue_script( 'slicknav', get_template_directory_uri() . '/assets/js/jquery.slicknav.min.js', array(), '', true );
	wp_enqueue_script( 'modernizr', get_template_directory_uri() . '/assets/js/modernizr.min.js',array(), '', true ); 
	wp_enqueue_script( 'html5shiv', get_template_directory_uri() . '/assets/js/html5shiv.min.js', array(), '', true ); 
	wp_enqueue_script( 'vt-blogging-scripts', get_template_directory_uri() . '/assets/js/jquery.custom.js', array(), '', true );
	
}
add_action( 'wp_enqueue_scripts', 'vt_blogging_scripts' );

/**
 *	Register Google fonts.
 *-----------------------------------------------------------------*/
function vt_blogging_fonts_url() {
	$fonts_url     = '';
	$_defaults     = array( 'Open Sans:400,600,700' );
	$font_families = apply_filters( 'vt_blogging_font_families', $_defaults );
	$subsets       = apply_filters( 'vt_blogging_font_subsets', 'latin,latin-ext' );

	if ( $font_families ) {
		$font_families = array_unique( $font_families );
		$query_args    = array(
			  'family' => urlencode( implode( '|', $font_families ) )
			, 'subset' => urlencode( $subsets )
		);
		$fonts_url = esc_url( add_query_arg( $query_args, '//fonts.googleapis.com/css' ) );
	}

	return $fonts_url;
}

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function vt_blogging_sidebar_init() {

	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'vt-blogging' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'vt-blogging' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title"><span>',
		'after_title'   => '</span></h3>',
	) );	
	register_sidebar( array(
		'name'          => esc_html__( 'Footer Column 1', 'vt-blogging' ),
		'id'            => 'footer-1',
		'description'   => esc_html__( 'Add widgets here.', 'vt-blogging' ),
		'before_widget' => '<div id="%1$s" class="widget footer-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title"><span>',
		'after_title'   => '</span></h3>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Footer Column 2', 'vt-blogging' ),
		'id'            => 'footer-2',
		'description'   => esc_html__( 'Add widgets here.', 'vt-blogging' ),
		'before_widget' => '<div id="%1$s" class="widget footer-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title"><span>',
		'after_title'   => '</span></h3>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Footer Column 3', 'vt-blogging' ),
		'id'            => 'footer-3',
		'description'   => esc_html__( 'Add widgets here.', 'vt-blogging' ),
		'before_widget' => '<div id="%1$s" class="widget footer-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title"><span>',
		'after_title'   => '</span></h3>',
	) );				
}
add_action( 'widgets_init', 'vt_blogging_sidebar_init' );

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Load Customizer Library scripts
 */
require get_template_directory() . '/customizer/customizer-library/customizer-library.php';
require get_template_directory() . '/customizer/customizer-options.php';
require get_template_directory() . '/customizer/styles.php';
require get_template_directory() . '/inc/customizer.php';
require_once( trailingslashit( get_template_directory() ) . 'customizer/customizer-library/extensions/class-upsell-customize.php' );

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

/**
 * Registers custom widgets.
 */
function vt_blogging_widgets_init() {	

	require trailingslashit( get_template_directory() ) . 'inc/widgets/widget-popular.php';
	register_widget( 'vt_blogging_Popular_Widget' );
	
	require trailingslashit( get_template_directory() ) . 'inc/widgets/widget-about.php';
	register_widget( 'vt_blogging_About_Widget' );											

}
add_action( 'widgets_init', 'vt_blogging_widgets_init' );

/**
 * Assign the VT Blogging version to a variable.
 */
$theme = wp_get_theme( 'vt-blogging' );
$vt_blogging_version = $theme['Version'];

/* Calling in the admin area for the Welcome Page */
if ( is_admin() ) {
	require get_template_directory() . '/inc/class-vt-blogging-admin.php';
}