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
				<h1><?php printf( 
				/* translators: %s: for the search keyword */
				esc_html__( 'Search results for: %s', 'vt-blogging' ), '' . esc_attr( get_search_query() ) . '</h1>' ); ?>
			</div><!-- .section-header -->
			<?php

			if ( have_posts() ) :	
						
			/* Start the Loop */
			while ( have_posts() ) : the_post();

				/*
				 * Include the Post-Format-specific template for the content.
				 * If you want to override this in a child theme, then include a file
				 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
				 */

				get_template_part( 'template-parts/content', 'search' );

				endwhile;

					the_posts_pagination();
				
				else :

					get_template_part( 'template-parts/content', 'none' );

				?>

			<?php endif; ?>

		</div>

		</main><!-- .site-main -->

		<?php the_posts_pagination(); ?>

	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>