<?php
/**
 * The template for displaying archive pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package VT Blogging
 */

get_header(); ?>

	<div id="primary" class="content-area clear">

		<main id="main" class="site-main clear">

		<div id="recent-content" class="content-loop">

			<div class="section-header clear">
				<h1>
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'vt-blogging' ); ?></a> &raquo; <?php single_cat_title(''); ?>
				</h1>	
			</div><!-- .section-header -->

			<?php

			if ( have_posts() ) :	
						
			/* Start the Loop */
			while ( have_posts() ) : the_post();

				get_template_part('template-parts/content', 'loop');

			endwhile;

			else :

				get_template_part( 'template-parts/content', 'none' );

			endif; 

			?>

		</div><!-- #recent-content -->

		</main><!-- .site-main -->

		<?php the_posts_pagination(); ?>

	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>