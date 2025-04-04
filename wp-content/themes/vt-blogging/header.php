<?php
/**
 * The header for our theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package VT Blogging
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta content="Descarga,gratis,consolas,nes,snes,psx,ps2,N64,dreamcast,gameboy,gamecube,megadrive,genesis,pcengine,turbografx,32xemuladores,español,ingles,romhacks' name='keywords"/>
<meta content="Descarga juegos retro gratis para emuladores de PC o Android, de consolas Nintendo Entertainment System (NES) (SNES), Game Boy (GB) (GBC), Nintendo 64 (N64), GameCube (NGC), Playstation (PS1) (PS2) , traducidos al español e ingles, incluidos Mods, Romhacks.' name='description"/>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="HandheldFriendly" content="true">
<link rel="profile" href="http://gmpg.org/xfn/11">
<meta name="google-site-verification" content="WBeyYMBeso1JjmSRafghyA67BpAklT03tQcN5hi5_x0" />
<meta name="msvalidate.01" content="F4C07540638613A75887810560843347" />
<meta name="yandex-verification" content="c01a0aedba530144" />
<?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>
<div id="page" class="site">

	<header id="masthead" class="site-header clear">

		<div class="container">

			<div class="site-branding">
			
				<?php if ( has_custom_logo() ) { ?>
					<div id="logo">
						<span class="helper"></span>
						<?php the_custom_logo(); ?>
					</div><!-- #logo -->
				<?php } else { ?>
					<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
					<h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>
				<?php } ?>

			</div><!-- .site-branding -->

			<nav id="primary-nav" class="main-navigation">

				<?php 
					if ( has_nav_menu( 'primary' ) ) {
						wp_nav_menu( array( 'theme_location' => 'primary', 'menu_id' => 'primary-menu', 'menu_class' => 'sf-menu' ) );
					} else {
				?>

				<ul class="sf-menu">
				  <?php if ( current_user_can( 'edit_theme_options' ) ) { ?>
					<li><?php printf( __( '<a href="%s">Add menu for theme location: Primary Menu.</a>', 'vt-blogging' ), esc_url( admin_url( 'nav-menus.php' ) ) ); ?></li>
				<?php } ?>
				</ul><!-- .sf-menu -->

				<?php } ?>

			</nav><!-- #primary-nav -->

			<?php if ( get_theme_mod('header-search-on', true) ) : ?>
				<span class="search-icon">
					<span class="genericon genericon-search"></span>
					<span class="genericon genericon-close"></span>			
				</span>
			<?php endif; ?>	

			<div id="slick-mobile-menu"></div>
		
		</div><!-- .container -->

	</header><!-- #masthead -->

	<?php if ( get_theme_mod('header-search-on', true) ) : ?>
		<div class="header-search">
			<div class="container">
				<?php get_search_form(); ?>
			</div>
		</div><!-- .header-search -->
	<?php endif; ?>

	<div id="content" class="site-content container clear">