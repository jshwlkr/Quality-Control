<?php
/**
 * The template for displaying Search Results pages.
 *
 * @package Quality_Control
 * @since Quality Control 0.1
 */

get_header( 'search' ); ?>

			<div id="main" role="main">
		
				<h2 class="screen-reader-text"><?php _e( 'Ticket Dashboard', 'quality' ); ?></h2> 
			
				<div id="ticket-manager" class="tabber"> 
				
					<?php get_template_part( 'inc/templates/navigation', 'search' ); ?>
					
					<div id="recent-tickets" class="panel"> 
					
						<ol class="ticket-list">
						
							<?php get_template_part( 'inc/templates/loop', 'search' ); ?>
							
						</ol> 
						
					</div>
				
				</div><!-- End #ticket-manager --> 
			
			</div><!-- End #main -->
			
			<?php get_sidebar( 'search' ); ?>
		
<?php get_footer( 'search' ); ?>