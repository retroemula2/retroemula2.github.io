<?php
/**
 * Singleton class for handling the theme's customizer integration.
 *
 * @since  1.0.0
 * @access public
 */
final class VT_Blogging_Upsell_Customize {

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new self;
			$instance->setup_actions();
		}

		return $instance;
	}

	/**
	 * Constructor method.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function __construct() {}

	/**
	 * Sets up initial actions.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function setup_actions() {

		// Register panels, sections, settings, controls, and partials.
		add_action( 'customize_register', array( $this, 'sections' ) );

		// Register scripts and styles for the controls.
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_control_scripts' ), 0 );
	}

	/**
	 * Sets up the customizer sections.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  object  $manager
	 * @return void
	 */
	public function sections( $manager ) {

		// Load custom sections.
		get_template_part( 'customizer/customizer-library/extensions/section-pro' );

		// Register custom section types.
		$manager->register_section_type( 'VT_Blogging_Upsell_Customize_Section_Pro' );

		// Register sections.
		$manager->add_section(
			new VT_Blogging_Upsell_Customize_Section_Pro(
				$manager,
				'vt-blogging-upsell',
				array(
					'title'    => esc_html__( 'VT Blogging Pro', 'vt-blogging' ),
					'pro_text' => esc_html__( 'Buy Pro', 'vt-blogging' ),
					'pro_url'  => 'https://volthemes.com/themes/vt-blogging-pro/?utm_source=customizer&utm_medium=theme-info-link',
					'priority' => 1
				)
			)
		);
	}

	/**
	 * Loads theme customizer CSS.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue_control_scripts() {

		wp_enqueue_script( 'vt-blogging-upsell-controls', trailingslashit( get_template_directory_uri() ) . 'customizer/customizer-library/js/customize-controls.js', array( 'customize-controls' ) );
	}
}

// Doing this customizer thang!
VT_Blogging_Upsell_Customize::get_instance();