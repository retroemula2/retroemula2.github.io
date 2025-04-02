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
				<?php
					the_archive_title( '<h1 class="page-title">', '</h1>' );
					the_archive_description( '<div class="taxonomy-description">', '</div>' );
				?>
				</div><!-- .section-header -->

				<?php if ( get_theme_mod( 'archive-page-layout' ) == 'layout-1' ) :

						get_template_part( 'layouts/blog', 'layout-1' ); 

					elseif ( get_theme_mod( 'archive-page-layout' ) == 'layout-2' ): 

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