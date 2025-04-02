<?php
/**
 * The sidebar containing the main widget area.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package VT Blogging
 */
?>

<aside id="secondary" class="widget-area sidebar">
	<?php 
		if ( is_active_sidebar( 'sidebar-1' ) ) {
        	dynamic_sidebar( 'sidebar-1' );
   		}
		else {
			//Helper Text
			if ( current_user_can( 'edit_theme_options' ) ) { ?>
				<div class="widget">
	                <h2 class="widget-title"><span><?php esc_html_e( 'Sidebar Widget Area', 'vt-blogging' ); ?></span></h2>

	           		<div class="textwidget">
	                  	<p><?php esc_html_e( 'This is the Primary Sidebar Widget Area', 'vt-blogging' ); ?></p>
	                  	<p><?php printf(
							/* translators: %s: Widgets Panel */
							__( 'By default, it will load Search and Archives widgets as shown below. You can add widget to this area by visiting your <a href="%s">Widgets Panel</a> which will replace default widgets.', 'vt-blogging' ), esc_url( admin_url( 'widgets.php' ) ) ); ?></p>
	                 </div>
	       		</div><!-- #widget-default-text -->
			<?php
			} ?>
			<div class="widget widget_search" id="default-search">
				<h2 class="widget-title"><span><?php esc_html_e( 'Search', 'vt-blogging' ); ?><span></h2>
				<?php get_search_form(); ?>
			</div><!-- #default-search -->
			<div class="widget">
				<h4 class="widget-title"><span><?php esc_html_e( 'Archives', 'vt-blogging' ); ?><span></h2>
				<ul>
					<?php wp_get_archives( array( 'type' => 'monthly' ) ); ?>
				</ul>
			</div><!-- #default-archives -->
		<?php
	} ?>
</aside><!-- #secondary -->