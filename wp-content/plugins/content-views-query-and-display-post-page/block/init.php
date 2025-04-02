<?php

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CVB_API_URL', 'https://contentviewspro.com/demo/wp-json/api/v1/' );

include_once dirname( __FILE__ ) . '/common.php';
include_once dirname( __FILE__ ) . '/template_pattern.php';
include_once dirname( __FILE__ ) . '/old/main.php';

if ( !class_exists( 'ContentViews_Block' ) ) {

	class ContentViews_Block {

		protected static $instance = null;

		protected $block_name = '';
		public $attributes			 = null;
		protected $custom_attributes = [];
		protected $title		 = '';
		protected $desc			 = '';
		protected $keywords		 = '';
		protected $textdomain	 = '';
		protected $supports		 = '';
		protected $example		 = '';

		function __construct() {
			add_action( 'init', array( $this, 'init_block' ), 20 );
		}

		static function get_instance() {
			if ( null == self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}

		function init_block() {
			if ( !function_exists( 'register_block_type' ) ) {
				return;
			}

			$this->attributes = array_replace_recursive( self::get_attributes(), $this->custom_attributes );

			$this->attributes[ 'blockName' ] = [
				'type'		 => 'string',
				'__key'		 => '__SAME__',
				'default'	 => $this->block_name
			];

			$this->attributes[ 'headingText' ][ 'default' ] = !empty( $this->title ) ? $this->title : ucfirst( $this->block_name );

			$this->attributes = apply_filters( PT_CV_PREFIX_ . 'block_attrs', $this->attributes );

			register_block_type( 'contentviews/' . $this->block_name, array(
				'title'				 => $this->title,
				'attributes'		 => $this->attributes,
				'editor_style'		 => 'contentviews-block-style',
				'editor_script'		 => 'contentviews-block-script',
				'render_callback'	 => array( $this, 'block_output' ),
			) );
		}

		// Render block output
		function block_output( $block_attributes, $content ) {

			$output = $style = '';

			$is_gb_editor = defined( 'REST_REQUEST' ) && REST_REQUEST && !empty( $_REQUEST[ 'context' ] ) && $_REQUEST[ 'context' ] === 'edit';

			// For Editor only (to reduce re-render): force to show, then hide/show by editor CSS when toggle
			if ( $is_gb_editor ) {
				foreach ( array_keys( self::field_toggles() ) as $opt ) {
					$block_attributes[ $opt ] = 1;
				}
			}

			$block_data			 = $this->get_attributes_and_settings( $block_attributes );
			$block_attributes	 = $block_data[ 0 ];

			if ( $is_gb_editor ) {
				// disable Pro lazyload, as it might not work in backend block editor
				add_filter( PT_CV_PREFIX_ . 'do_lazy_image', '__return_false' );
			}

			// disable things relate to classic views
			add_filter( PT_CV_PREFIX_ . 'hide_editview', '__return_true' );

			if ( $is_gb_editor || !is_admin() ) {
				ob_start();
				$GLOBALS[ 'cv_current_post' ] = apply_filters( PT_CV_PREFIX_ . 'current_postid', get_queried_object_id() );

				$settings	 = $block_data[ 1 ];
				$view_id	 = !empty( $block_attributes[ 'blockId' ] ) ? cv_sanitize_vid( $block_attributes[ 'blockId' ] ) : PT_CV_Functions::string_random();

				echo PT_CV_Functions::view_process_settings( $view_id, $settings );

				// maybe need view_final_output();

				$output .= ob_get_clean();
			}

			if ( $is_gb_editor ) {
				// prevent click link
				$output = str_replace( '<a href=', '<a onclick="event.preventDefault()" href=', $output );

				// modify output for editor only
				$output = apply_filters( PT_CV_PREFIX_ . 'block_editor_output', $output, $block_attributes );
			}

			$class = apply_filters( PT_CV_PREFIX_ . 'wrapper_class', PT_CV_PREFIX . 'wrapper' );

			$style		 = $field_css	 = '';
			$view_css	 = ContentViews_Block_Common::view_styles( $block_attributes );
			if ( !$is_gb_editor ) {
				$field_css = ContentViews_Block_Common::generate_styles( $block_attributes );
			}
			if ( $view_css || $field_css ) {
				$style = "<style>$view_css\n$field_css</style>";
			}

			$style .= ContentViews_Block_Common::load_googlefont( $block_attributes );

			// heading
			$output = self::heading_output( $block_attributes ) . $output;

			return "<div class='$class'> $output </div> $style";
		}

		function get_attributes_and_settings( $block_attributes ) {
			$block_attributes = apply_filters( PT_CV_PREFIX_ . 'block_settings', $block_attributes );

			$settings = array();
			$this->mapping( $block_attributes, $settings );

			return [ $block_attributes, $settings ];
		}

		// Generate view settings from block data
		function mapping( $data, &$settings ) {

			$atts = $this->attributes;
			foreach ( $atts as $block_key => $info ) {
				if ( isset( $info[ '__key' ] ) ) {
					$key							 = ($info[ '__key' ] !== '__SAME__') ? $info[ '__key' ] : $block_key;
					$value							 = isset( $data[ $block_key ] ) ? $data[ $block_key ] : '';
					$settings[ PT_CV_PREFIX . $key ] = apply_filters( PT_CV_PREFIX_ . 'mapping_value', $value, $info, $data, $settings );
				}
			}

			// required options
			$settings[ PT_CV_PREFIX . 'advanced-settings' ]	 = array_keys( PT_CV_Values::advanced_settings() );
			
			// handle some complex options
			if ( empty( $settings[ PT_CV_PREFIX . 'limit' ] ) ) {
				$settings[ PT_CV_PREFIX . 'limit' ] = 1000000;
			}

			$settings[ PT_CV_PREFIX . 'field-thumbnail-nowprpi' ] = !$settings[ PT_CV_PREFIX . 'responsiveImg' ];
			$settings[ PT_CV_PREFIX . 'field-thumbnail-nodefault' ]	 = !$settings[ PT_CV_PREFIX . 'defaultImg' ];

			$settings[ PT_CV_PREFIX . 'multi-post-types' ]	 = self::values_from_block( $data, 'multipostType', 'any' );

			$settings[ PT_CV_PREFIX . 'taxonomy' ] = self::values_from_block( $data, 'taxonomy', '' );

			if ( is_array( $settings[ PT_CV_PREFIX . 'taxonomy' ] ) ) {
				foreach ( array_keys( PT_CV_Values::taxonomy_list( true ) ) as $taxonomy ) {
					$settings[ PT_CV_PREFIX . "$taxonomy-terms" ] = self::values_from_block( $data, "$taxonomy-terms", [] );
				}
			}

			// show ctf
			$settings[ PT_CV_PREFIX . 'custom-fields-list' ] = self::values_from_block( $data, 'CTFlist', '' );
			if ( isset( $data[ 'CTFname' ] ) && $data[ 'CTFname' ] ) {
				$settings[ PT_CV_PREFIX . 'custom-fields-enable-custom-name' ] = 'yes';
			}

			$postparent = isset( $data[ 'parentPage' ][ 'value' ] ) ? intval( $data[ 'parentPage' ][ 'value' ] ) : null;
			if ( $postparent ) {
				$settings[ PT_CV_PREFIX . 'post_parent' ] = $postparent;
			}

			$settings[ PT_CV_PREFIX . 'post__in' ]		 = self::values_from_block( $data, 'includeId', '' );
			$settings[ PT_CV_PREFIX . 'post__not_in' ]	 = self::values_from_block( $data, 'excludeId', '' );

			$settings[ PT_CV_PREFIX . 'author__in' ] = self::values_from_block( $data, 'author', '' );
			$settings[ PT_CV_PREFIX . 'author__not_in' ] = self::values_from_block( $data, 'authorNot', '' );
			$columns = (array) $data[ 'columns' ];
			$settings[ PT_CV_PREFIX . $data[ 'viewType' ] . '-number-columns' ]	 = $columns[ 'md' ];
			$settings[ PT_CV_PREFIX . 'resp-tablet-number-columns' ]			 = isset( $columns[ 'sm' ] ) ? $columns[ 'sm' ] : $columns[ 'md' ];
			$settings[ PT_CV_PREFIX . 'resp-number-columns' ]					 = $columns[ 'xs' ];

			$meta = self::values_from_block( $data, 'metaWhich', array() );
			foreach ( $meta as $field ) {
				$settings[ PT_CV_PREFIX . "meta-fields-$field" ] = 'yes';
			}

			// Switch fields position
			if ( $settings[ PT_CV_PREFIX . 'show-field-title' ] && $settings[ PT_CV_PREFIX . 'show-field-taxoterm' ] && $settings[ PT_CV_PREFIX . 'taxo-position' ] === 'below_title' ) {
				unset( $settings[ PT_CV_PREFIX . 'show-field-taxoterm' ] );
				$settings = self::array_insert_after( $settings, PT_CV_PREFIX . 'show-field-title', array( PT_CV_PREFIX . 'show-field-taxoterm' => 1 ) );
			}

			$settings = apply_filters( PT_CV_PREFIX_ . 'mapping_settings', $settings, $data );

			//echo "<pre>"; print_r($settings); echo "</pre>";
		}

		// Get 'value' from [[label: x1, value: y1],[label: x2, value: y2]] of block attribute
		static function values_from_block( $arr, $key, $default ) {
			return isset( $arr[ $key ] ) && is_array( $arr[ $key ] ) ? array_column( $arr[ $key ], 'value' ) : $default;
		}

		// Insert after specific key
		static function array_insert_after( array $array, $key, array $new ) {
			$keys	 = array_keys( $array );
			$index	 = array_search( $key, $keys );
			$pos	 = false === $index ? count( $array ) : $index + 1;

			return array_slice( $array, 0, $pos ) + $new + array_slice( $array, $pos );
		}

		static function default_img_size(){
			$sizes = get_intermediate_image_sizes();
			$size = in_array( 'medium_large', $sizes ) ? 'medium_large': 'large';
			return apply_filters( PT_CV_PREFIX_ . 'block_img_size', $size );
		}

		// Define block attributes
		static function get_attributes() {
			$woo_default = get_option( 'pt_cv_version_pro' ) ? true: false;
			$atts = [
				'blockId'		 => [					
					'type' => 'string',
				],
				'postType'		 => [
					'__key'		 => 'content-type',
					'type'		 => 'string',
					'default'	 => 'post',
				],
				'multipostType'			 => [
					'__key'	 => 'multi-post-types',
					'type'	 => 'array',
				],
				'viewType'				 => [
					'__key'		 => 'view-type',
					'type'		 => 'string',
					'default'	 => 'blockgrid',
				],
				'whichLayout'	 => [
					'__key'		 => '__SAME__',
					'type'		 => 'string',
					'default'	 => 'layout1',
				],
				'layoutFormat'	 => [
					'__key'		 => 'layout-format',
					'type'		 => 'string',
					'default'	 => '1-col',
				],
				'columns'		 => [
					'type'		 => 'object',
					'default'	 => (object) [ 'md' => 3, 'xs' => 1 ],
				],
				'gridGap'		 => [
					'type'		 => 'object',
					'default'	 => (object) [ 'md' => 20 ],
				],
				'alignment'		 => [
					'__key'	 => 'style-text-align',
					'type'	 => 'string',
					'default'	 => 'left',
				],
				'limit'			 => [
					'__key'		 => 'limit',
					'type'		 => 'string',
					'default'	 => '',
				],				
				'taxonomy'		 => [
					'type'	 => 'array',
				],
				'author'		 => [
					'type'	 => 'array',
				],
				'authorNot'		 => [
					'type'	 => 'array',
				],
				'keyword'		 => [
					'__key'	 => 's',
					'type'	 => 'string',
				],
				'offset'		 => [
					'__key'	 => 'offset',
					'type'	 => 'string',
				],
				'includeId'		 => [
					'__key'	 => 'post__in',
					'type'	 => 'array',
				],
				'excludeId'		 => [
					'__key'	 => 'post__not_in',
					'type'	 => 'array',
				],
				'parentPage'	 => [
					'type'	 => 'object',
				],
				'orderby'		 => [
					'__key'	 => 'orderby',
					'type'	 => 'string',
				],
				'order'			 => [
					'__key'		 => 'order',
					'type'		 => 'string',
					'default'	 => 'desc',
				],
				'sortCtf'				 => [
					'type' => 'array',
				],
				'taxonomyRelation'		 => [
					'__key'		 => 'taxonomy-relation',
					'type'		 => 'string',
					'default'	 => 'AND',
				],
				'filterCtf'				 => [
					'type'		 => 'array',
				],
				'filterCtfRel'	 => [
					'__key'		 => 'ctf-filter-relation',
					'type'		 => 'string',
				],
				'triggerRender'			 => [
					'type' => 'string',
				],
				'hasLF'			 => [
					'type'		 => 'object',
				],
				'noLFSub'	 => [
					'type'		 => 'boolean',
				],
				'noLFRes'	 => [
					'type'		 => 'boolean',
				],
				'filterDate'	     => [
					'__key'		 => 'post_date_value',
					'type'		 => 'string',
				],
				'wooPick'	     => [
					'__key'		 => 'products-list',
					'type'		 => 'string',
					'default'	 => '',
				],
				'fieldsPosition'	 => [
					'__key'		 => '__SAME__',
					'type'		 => 'array',
				],
				'showHeading'	 => [
					'type'		 => 'boolean',
					'default'	 => true,
				],
				'showThumbnail'	 => [
					'__key'		 => 'show-field-thumbnail',
					'type'		 => 'boolean',
					'default'	 => true,
				],
				'showTaxonomy'	 => [
					'__key'		 => 'show-field-taxoterm',
					'type'		 => 'boolean',
					'default'	 => true,
				],
				'topmetaWhich'		 => [
					'__key'		 => 'topmeta-which',
					'type'		 => 'string',
					'default'	 => 'mtt_taxonomy',
				],
				'taxoWhich'		 => [
					'__key'		 => 'taxo-which',
					'type'		 => 'string',
					'default'	 => 'category',
				],
				'taxoPosition'	 => [
					'__key'		 => 'taxo-position',
					'type'		 => 'string',
					'default'	 => 'above_title',
				],
				'showCustomField'		 => [
					'__key'		 => 'show-field-custom-fields',
					'type'		 => 'boolean',
				],
				'CTFlist'		 => [
					'type'		 => 'array',
				],
				'CTFname'		 => [
					'__key'		 => 'custom-fields-show-name',
					'type'		 => 'boolean',
				],
				'CTFcolumn'		 => [
					'__key'		 => 'custom-fields-number-columns',
					'type'		 => 'string',
				],
				'showTitle'		 => [
					'__key'		 => 'show-field-title',
					'type'		 => 'boolean',
					'default'	 => true,
				],
				'showWooPrice'	 => [
					'__key'		 => 'show-field-wooprice',
					'type'		 => 'boolean',
					'default'	 => $woo_default,
				],
				'showWooATC'	 => [
					'__key'		 => 'show-field-wooatc',
					'type'		 => 'boolean',
					'default'	 => $woo_default,
				],
				'showContent'	 => [
					'__key'		 => 'show-field-content',
					'type'		 => 'boolean',
					'default'	 => true,
				],
				'showReadmore'		 => [
					'__key'		 => 'show-field-readmore',
					'type'		 => 'boolean',
					'default'	 => true,
				],
				'showMeta'		 => [
					'__key'      => 'show-field-meta-fields',
					'type'		 => 'boolean',
					'default'	 => false,
				],
				'metaWhich'		 => [
					'type'		 => 'array',
					'default'	 => array_slice( ContentViews_Block_Common::meta_list(), 0, 2 ),
				],
				'metaSeparator'	 => [
					'__key'		 => '__SAME__',
					'type'		 => 'string',
					'default'	 => '/',
				],
				'metaIcon'		 => [
					'__key'	 => '__SAME__',
					'type'		 => 'boolean',
					'default'	 => false,
				],
				'authorAvatar'	 => [
					'__key'	 => '__SAME__',
					'type'		 => 'boolean',
					'default'	 => false,
				],
				'authorPrefix'	 => [
					'__key'	 => '__SAME__',
					'type'	 => 'string',
				],
				'dateFormat'	 => [
					'__key'	 => '__SAME__',
					'type'	 => 'string',
					'default'	 => '',
				],
				'dateFormatCustom'	 => [
					'__key'	 => '__SAME__',
					'type'	 => 'string',
				],
				'showPagination' => [
					'__key'		 => 'enable-pagination',
					'type'		 => 'boolean',
					'default'	 => false,
				],
				'showAds' => [
					'__key'		 => 'ads-enable',
					'type'		 => 'boolean',
				],
				'titleTag'		 => [
					'__key'		 => 'field-title-tag',
					'type'		 => 'string',
					'default'	 => 'h4',
				],
				'imgSize'		 => [
					'__key'		 => 'field-thumbnail-size',
					'type'		 => 'string',
					'default'	 => self::default_img_size(),
				],
				'thumbnailMaxWidth'		 => [
					'__key'	 => '__SAME__',
					'type'	 => 'object',
				],
				'thumbnailMaxWidthUnits'	 => [
					'__key'	 => '__SAME__',
					'type'	 => 'object',
				],
				'thumbnailHeight'		 => [
					'__key'	 => '__SAME__',
					'type'	 => 'object',
				],
				'thumbnailHeightUnits'	 => [
					'__key'	 => '__SAME__',
					'type'	 => 'object',
				],
				'thumbnailEffect'		 => [
					'__key'	 => '__SAME__',
					'type'	 => 'string',
				],
				'subImg'		 => [
					'__key'		 => 'field-thumbnail-auto',
					'type'		 => 'string',
					'default'	 => get_option( 'pt_cv_version_pro' ) ? 'image' : 'none',
				],
				'defaultImg' => [
					'__key'	     => '__SAME__',
					'type'		 => 'boolean',
					'default'	 => true,
				],
				'responsiveImg' => [
					'__key'	     => '__SAME__',
					'type'		 => 'boolean',
					'default'	 => false,
				],
				'lazyImg'				 => [
					'__key'		 => 'field-thumbnail-lazyload',
					'type'		 => 'boolean',
					'default'	 => false,
				],
				'contentShow'			 => [
					'__key'		 => 'field-content-show',
					'type'		 => 'string',
					'default'	 => 'excerpt',
				],
				'excerptLength'	 => [
					'__key'		 => 'field-excerpt-length',
					'type'		 => 'string',
					'default'	 => '20',
				],
				'excerptManual'	 => [
					'__key'		 => 'field-excerpt-manual',
					'type'		 => 'boolean',
					'default'	 => true,
				],
				'readmoreText'	 => [
					'__key'		 => 'field-excerpt-readmore-text',
					'type'		 => 'string',
					'default'	 => ucwords( rtrim( __( 'Read more...' ), '.' ) ),
				],
				'postsPerPage'			 => [
					'__key'		 => 'pagination-items-per-page',
					'type'		 => 'string',
					'default'	 => '6',
				],
				'pagingType'	 => [
					'__key'		 => 'pagination-type',
					'type'		 => 'string',
					'default'	 => 'ajax',
				],
				'pagingStyle'	 => [
					'__key'		 => 'pagination-style',
					'type'		 => 'string',
					'default'	 => 'regular',
				],
				'pagingNoScroll' => [
					'__key'	     => '__SAME__',
					'type'		 => 'boolean',
					'default'	 => true,
				],
				'headingText'	 => [
					'type' => 'string',
				],
				'headingStyle'			 => [
					'__key'		 => '__SAME__',
					'type'		 => 'string',
					'default'	 => 'heading1',
				],
				'headingTag'			 => [
					'__key'		 => '__SAME__',
					'type'		 => 'string',
					'default'	 => 'h3',
				],
				'sameAs'				 => [
					'type'     => 'string',
					'default'  => '',
				],
				'linkTarget'				 => [
					'__key'		 => 'other-open-in',
					'type'		 => 'string',
					'default'	 => '_self',
				],
				'windowWidth'				 => [
					'__key'		 => 'other-window-size-width',
					'type'		 => 'string',
					'default'	 => '800',
				],
				'windowHeight'				 => [
					'__key'		 => 'other-window-size-height',
					'type'		 => 'string',
					'default'	 => '600',
				],
				'lbWidth'				 => [
					'__key'		 => 'other-lightbox-size-width',
					'type'		 => 'string',
					'default'	 => '80%',
				],
				'lbHeight'				 => [
					'__key'		 => 'other-lightbox-size-height',
					'type'		 => 'string',
					'default'	 => '80%',
				],
				'lbSelector'				 => [
					'__key'		 => 'other-lightbox-content-selector',
					'type'		 => 'string',
					'default'	 => '#content',
				],
				'lbNavi'				 => [
					'__key'		 => 'other-lightbox-enable-navigation',
					'type'		 => 'boolean',
					'default'	 => true,
				],
			];

			$taxos = PT_CV_Values::taxonomy_list( true );
			foreach ( (array) array_keys( $taxos ) as $taxonomy ) {
				$atts[ "$taxonomy-terms" ] = [ 'type' => 'array', ];
				$atts[ "$taxonomy-operator" ] = [ 'type' => 'string', '__key' => '__SAME__', ];

				$atts[ "$taxonomy-LfEnable" ] = [ 'type' => 'boolean', '__key' => "$taxonomy-live-filter-enable", ];
				$atts[ "$taxonomy-LfType" ] = [ 'type' => 'string', '__key' => "$taxonomy-live-filter-type", ];
				$atts[ "$taxonomy-LfBehavior" ] = [ 'type' => 'string', '__key' => "$taxonomy-live-filter-operator", ];
				$atts[ "$taxonomy-LfLabel" ] = [ 'type' => 'string', '__key' => "$taxonomy-live-filter-heading", ];
				$atts[ "$taxonomy-LfDefault" ] = [ 'type' => 'string', '__key' => "$taxonomy-live-filter-default-text", ];
				$atts[ "$taxonomy-LfOrder" ] = [ 'type' => 'string', '__key' => "$taxonomy-live-filter-order-options", ];
				$atts[ "$taxonomy-LfOrderFlag" ] = [ 'type' => 'string', '__key' => "$taxonomy-live-filter-order-flag", ];
				$atts[ "$taxonomy-LfCount" ] = [ 'type' => 'boolean', '__key' => "$taxonomy-live-filter-show-count", ];
				$atts[ "$taxonomy-LfNoEmpty" ] = [ 'type' => 'boolean', '__key' => "$taxonomy-live-filter-hide-empty", ];
				$atts[ "$taxonomy-LfRequire" ] = [ 'type' => 'boolean', '__key' => "$taxonomy-live-filter-require-exist", ];
			}

			$defaults = self::default_values();
			foreach ( self::get_fields() as $element ) {
				foreach ( self::style_options() as $type => $options ) {
					foreach ( $options as $option ) {
						$arr = [ 'type' => $type ];

						if ( $option === 'Deco' && $element === 'title' ) {
							$arr[ 'default' ] = 'none';
						}

						if ( isset( $defaults[ $element ][ $option ] ) ) {
							$val = $defaults[ $element ][ $option ];
							if ( $option === 'fSize' ) {
								$val = (object) [ 'md' => $val ];
							}
							$arr[ 'default' ] = $val;
						}

						$atts[ "{$element}$option" ] = $arr;
					}
				}
			}

			return apply_filters( PT_CV_PREFIX_ . 'block_attributes', $atts );
		}

		static function style_options() {
			return [
				'string' => [ 'Align', 'Color', 'HoverColor', 'BgColor', 'HoverBgColor', 'Weight', 'fStyle', 'Tran', 'Deco', 'BorderColor', 'BorderStyle', 'BoxShadowColor' ],
				'object' => [ 'Family', 'fSize', 'Line', 'Margin', 'Padding', 'BorderWidth', 'BorderRadius', 'BoxShadow', 'fSizeUnits', 'LineUnits', 'MarginUnits', 'PaddingUnits', 'BorderWidthUnits', 'BorderRadiusUnits' ],
			];
		}

		// List of field toggle attributes
		static function field_toggles() {
			$base = [ 'showHeading' => '', 'showTitle' => 'title', 'showThumbnail' => 'thumb-wrapper', ];
			$main = [ 'showTaxonomy' => 'taxoterm', 'showContent' => 'content', 'showReadmore' => 'rmwrap', 'showMeta' => 'meta-fields' ];
			$sub = [];
			foreach ( $main as $key => $selector ) {
				$sub[ $key . 'Others' ] = $selector;
			}
			return array_merge( $base, $main, $sub );
		}

		static function get_fields() {
			$fields = [ 'view', 'remain-wrapper', 'overlay-wrapper', 'content-item', 'meta-fields', 'heading', 'thumbnail', 'thumbnailsm', 'thumbnailAll', 'title', 'titlesm', 'content', 'readmore', 'taxoterm', 'pagination', 'paginationActive', 'wooprice', 'wooatc', 'custom-fields' ];
			return apply_filters( PT_CV_PREFIX_ . 'block_fields', $fields );
		}

		static function default_values() {
			return [
				'view'		 => [ 'Margin' => (object) [ 'md' => [ 'bottom' => 30 ] ] ],
				'title'		 => [ 'fSize' => '20', 'Weight' => '600' ],
				'titlesm'	 => [ 'fSize' => '18', 'Weight' => '600' ],
				'content'	 => [ 'fSize' => '15' ],
				'readmore'		 => [ 'fSize' => '14', 'Color' => '#fff', 'BgColor' => '#0075ff' ],
				'taxoterm'		 => [ 'fSize' => '14', 'Color' => '#222', 'BgColor' => '#fff6f6', 'Padding' => (object) [ 'md' => [ 'top' => 1, 'right' => 6, 'bottom' => 1, 'left' => 6 ] ], 'Margin' => (object) [ 'md' => [ 'bottom' => 10 ] ] ],
				'pagination'	 => [ 'fSize' => '14' ],
				'meta-fields'	 => [ 'fSize' => '13', 'Margin' => (object) [ 'md' => [ 'top' => 10, ] ] ],
			];
		}

		static function is_block() {
			return PT_CV_Functions::setting_value( PT_CV_PREFIX . 'blockName' );
		}

		static function heading_output( $atts ) {
			if ( !$atts[ 'showHeading' ] ) {
				return '';
			}
			$text	 = isset( $atts[ 'headingText' ] ) ? $atts[ 'headingText' ] : $atts[ 'blockName' ];
			$tag	 = isset( $atts[ 'headingTag' ] ) ? $atts[ 'headingTag' ] : 'h3';
			$style	 = isset( $atts[ 'headingStyle' ] ) ? $atts[ 'headingStyle' ] : 'heading1';
			$prefix	 = PT_CV_PREFIX;
			$heading = "<{$tag} class='{$prefix}heading-container $style' data-blockid='{$atts[ 'blockId' ]}'><span class='{$prefix}heading'> $text </span></{$tag}>";
			return $heading;
		}

	}

}


$GLOBALS[ 'contentviews_blocks' ] = [];
foreach ( glob( dirname( __FILE__ ) . '/blocks/*.php' ) as $file ) {
	include_once $file;

	$filename							 = basename( $file, '.php' );
	$classname	 = 'ContentViews_Block_' . ucfirst( $filename );
	if ( class_exists( $classname, false ) ) {
		$GLOBALS[ 'contentviews_blocks' ][ $filename ] = new $classname();
	}
}