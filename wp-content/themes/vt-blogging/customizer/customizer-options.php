<?php
/**
 * Defines customizer options
 *
 * @package VT Blogging 
 */

function customizer_library_vt_blogging_options() {

	// Theme defaults
	$primary_color = '#eb5424';
	$header_bg_color = '#343e47';
	$header_search_bg_color = '#3c4852';
	$widget_title_boder_color = '#eb5424';
	$pagination_bg_color = '#eb5424 ';

	// Stores all the controls that will be added
	$options = array();

	// Stores all the sections to be added
	$sections = array();

	// Stores all the panels to be added
	$panels = array();

	// Adds the sections to the $options array
	$options['sections'] = $sections;

	// Panels
	$section = 'theme-settings';

	// Arrays 
	$layout_choices = array(
		'choice-1' => __('Responsive Layout', 'vt-blogging'),
		'choice-2' => __('Fixed Layout', 'vt-blogging')
	);

	$sections[] = array(
		'id' => $section,
		'title' => __( 'Theme Settings', 'vt-blogging' ),
		'priority' => '20'
	);

	$options['site-layout'] = array(
		'id' => 'site-layout',
		'label'   => __( 'Site Layout', 'vt-blogging' ),
		'section' => $section,
		'type'    => 'radio',
		'choices' => $layout_choices,
		'default' => 'choice-1'
	);

    $choices = array(
		'layout-1' => __('List Layout', 'vt-blogging'),
		'layout-2' => __('Standard Blog Layout', 'vt-blogging'),
			
    );
    $options['blog-page-layout'] = array(
        'id' => 'blog-page-layout',
        'label'   => __( 'Blog Page Layout', 'vt-blogging' ),
        'section' => $section,
        'type'    => 'select',
        'choices' => $choices,
        'default' => 'layout-1'
    );
	
	$choices = array(
		'layout-1' => __('List Layout', 'vt-blogging'),
		'layout-2' => __('Standard Blog Layout', 'vt-blogging'),
			
    );
    $options['archive-page-layout'] = array(
        'id' => 'archive-page-layout',
        'label'   => __( 'Archive Page Layout', 'vt-blogging' ),
        'section' => $section,
        'type'    => 'select',
        'choices' => $choices,
        'default' => 'layout-1'
    );
	
	$options['entry-excerpt-length'] = array(
		'id' => 'entry-excerpt-length',
		'label'   => __( 'Number of words to show on excerpt', 'vt-blogging' ),
		'section' => $section,
        'type'    => 'number',
        'default' => 38,
        'description' => __( 'Default: 38', 'vt-blogging' )		
	);
	
	$options['header-search-on'] = array(
		'id' => 'header-search-on',
		'label'   => __( 'Display header search form', 'vt-blogging' ),
		'section' => $section,
		'type'    => 'checkbox',
		'default' => 1,
	);
	
	$options['author-box-on'] = array(
		'id' => 'author-box-on',
		'label'   => __( 'Display author box on single posts', 'vt-blogging' ),
		'section' => $section,
		'type'    => 'checkbox',
		'default' => 1,
	);	
	
	$options['related-posts-on'] = array(
		'id' => 'related-posts-on',
		'label'   => __( 'Display related posts on single posts', 'vt-blogging' ),
		'section' => $section,
		'type'    => 'checkbox',
		'default' => 1,
	);

	$options['back-top-on'] = array(
		'id' => 'back-top-on',
		'label'   => __( 'Display "back to top" icon link on the site bottom', 'vt-blogging' ),
		'section' => $section,
		'type'    => 'checkbox',
		'default' => 1,
	);

	/**-----------------
	 * Color Settings
	 -----------------*/
    $panel = 'vt-colors-settings';

    $panels[] = array(
        'id' => $panel,
        'title' => __( 'Color Settings', 'vt-blogging' ),
        'priority' => '50'
    );
    
    $section = 'colors';
	
	$options['vt-blogging-primary-color'] = array(
        'id' => 'vt-blogging-primary-color',
        'label'   => __( 'Primary Color', 'vt-blogging' ),
        'section' => $section,
        'type'    => 'color',
        'default' => $primary_color,
    );
	
    $options['vt-blogging-header-bg-color'] = array(
        'id' => 'vt-blogging-header-bg-color',
        'label'   => __( 'Header Background', 'vt-blogging' ),
        'section' => $section,
        'type'    => 'color',
        'default' => $header_bg_color,
    );
	
	$options['vt-blogging-header-search-bg-color'] = array(
        'id' => 'vt-blogging-header-search-bg-color',
        'label'   => __( 'Header Search Background', 'vt-blogging' ),
        'section' => $section,
        'type'    => 'color',
        'default' => $header_search_bg_color,
    );
	
	$options['vt-blogging-pagination-bg-color'] = array(
        'id' => 'vt-blogging-pagination-bg-color',
        'label'   => __( 'Pagination Background Color', 'vt-blogging' ),
        'section' => $section,
        'type'    => 'color',
        'default' => $pagination_bg_color,
    );
	
	
	// Adds the sections to the $options array
	$options['sections'] = $sections;

	// Adds the panels to the $options array
	$options['panels'] = $panels;

	$customizer_library = Customizer_Library::Instance();
	$customizer_library->add_options( $options );

	// To delete custom mods use: customizer_library_remove_theme_mods();

}
add_action( 'init', 'customizer_library_vt_blogging_options' );