<?php
/**
 * The Template for displaying the 404 Error Page
 *
 * @package Quality_Control
 * @since Quality Control 0.1
 */

get_header( '404' ); ?>

	<h1 class="screen-reader-text"><?php _e( '404 - Page Not Found', 'quality' ); ?></h1>
	
	<div id="message"><?php _e( 'Sorry, the page you are looking for could not be located.', 'quality' ); ?></div>

<?php get_footer( '404' ); ?>
