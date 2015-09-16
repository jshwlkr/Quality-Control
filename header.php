<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package Quality_Control
 * @since Quality Control 0.1
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<title><?php
	/*
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged;
	
	// Add the blog name.
	bloginfo( 'name' );

	wp_title( '&rarr;', true, 'left' );
	
	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " &rarr; $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		echo ' &rarr; ' . sprintf( __( 'Page %s', 'quality' ), max( $paged, $page ) );

	?></title>
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

	<div id="container">
	
		<div id="top">
	
			<?php 
				wp_nav_menu( array( 
					'container_class' => '',
					'container_id' => 'menu',
					'menu_id' => 'access',
					'menu_class' => '',
					'theme_location' => 'primary',
				) ); 
			?>
			
			<div id="logged-in">
			
				<?php if( is_user_logged_in() ) : global $current_user; ?>
					<?php printf( __( 'Hey, <strong>%s</strong>', 'quality' ), $current_user->display_name ); ?> <a href="<?php echo wp_logout_url( get_bloginfo( 'url' ) ); ?>"><?php _e( '(logout)', 'quality' ); ?></a>
				<?php else : ?>
					<a href="<?php echo wp_login_url( get_bloginfo('url') ); ?>"><?php _e( 'Login', 'quality' ); ?></a>
				<?php endif; ?>
			
			</div>
			
		</div><!-- End #top -->
 
		<div id="branding" role="banner"> 
		
			<?php quality_page_title(); ?>
			<?php $heading_tag = ( is_home() || is_front_page() ) ? 'h1' : 'div'; ?>
			<<?php echo $heading_tag; ?> id="site-title">
				<a href="<?php echo home_url( '/' ); ?>"><?php bloginfo( 'name' ); ?></a> <?php wp_title( '&rarr;', true, 'left' ); ?>
			</<?php echo $heading_tag; ?>> 
			<div class="tagline"><?php bloginfo( 'description' ); ?></div> 
		
		</div><!-- End #header --> 
				
		<div id="content"> 