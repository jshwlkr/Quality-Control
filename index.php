<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query. 
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Quality_Control
 * @since Quality Control 0.1
 */

get_header( 'dashboard' ); ?>

			<div id="main" role="main">
			
				<div id="ticket-manager" class="tabber"> 
				
					<?php get_template_part( 'inc/templates/navigation', 'dashboard' ); ?>
					
					<div id="recent-tickets" class="panel"> 
					
						<?php get_template_part( 'inc/templates/loop', 'dashboard' ); ?>
						
					</div>
				
				</div><!-- End #ticket-manager --> 
			
			</div><!-- End #main -->
			
			<?php get_sidebar( 'dashboard' ); ?>
		
<?php get_footer( 'dashboard' ); ?>
