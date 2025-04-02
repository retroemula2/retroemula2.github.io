<?php get_header(); ?>	

	<div id="primary" class="content-area layout-1c clear">

		<main id="main" class="site-main clear">

			<div id="recent-content" class="content-loop">

				<?php if ( get_theme_mod( 'blog-page-layout' ) == 'layout-1' ) :

						get_template_part( 'layouts/blog', 'layout-1' ); 

					elseif ( get_theme_mod( 'blog-page-layout' ) == 'layout-2' ): 

						get_template_part( 'layouts/blog', 'layout-2' );
						
					else :

						get_template_part( 'layouts/blog', 'layout-1' );

					endif;
				 
				?>

			</div><!-- #recent-content -->		

		</main><!-- .site-main -->

		<?php the_posts_pagination(); ?>
		
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>