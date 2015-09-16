<?php
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Quality_Control
 * @since Quality Control 0.1
 */

get_header(); ?>

			<div id="main" role="main">
		
				<h2 class="screen-reader-text"><?php _e( 'Ticket Archives', 'quality' ); ?></h2> 
			
				<div id="ticket-manager" class="tabber"> 
					
					<?php get_template_part( 'inc/templates/navigation', 'archive' ); ?>
					
					<div id="recent-tickets" class="panel"> 
					
						<ol class="ticket-list">
						
							<?php get_template_part( 'inc/templates/loop', 'archive' ); ?>
							
						</ol> 
						
					</div>
				
				</div><!-- End #ticket-manager --> 
			
			</div><!-- End #main -->
			
			<?php get_sidebar( 'archive' ); ?>
		
<?php get_footer(); ?>