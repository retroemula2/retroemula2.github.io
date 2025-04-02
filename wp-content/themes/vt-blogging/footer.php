<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package VT Blogging
 */

?>

	</div><!-- #content .site-content -->
	
	<footer id="colophon" class="site-footer">

		<?php if ( ( is_active_sidebar( 'footer-1' ) || is_active_sidebar( 'footer-2' ) || is_active_sidebar( 'footer-3' ) || is_active_sidebar( 'footer-4' ) ) ) { ?>

			<div class="footer-columns clear">

				<div class="container clear">

					<div class="footer-column footer-column-1">
						<?php dynamic_sidebar( 'footer-1' ); ?>
					</div>

					<div class="footer-column footer-column-2">
						<?php dynamic_sidebar( 'footer-2' ); ?>
					</div>

					<div class="footer-column footer-column-3">
						<?php dynamic_sidebar( 'footer-3' ); ?>
					</div>											

				</div><!-- .container -->

			</div><!-- .footer-columns -->

		<?php } ?>
		
		<div id="site-bottom" class="container clear">

			<?php do_action( 'vt_blogging_footer' ); ?>			

		</div><!-- #site-bottom -->
							
	</footer><!-- #colophon -->
	
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>