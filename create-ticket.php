<?php
/**
 * Create a ticket.
 *
 * A custom page template that should be assigned to a page.
 * The page should then be set in the settings page.
 *
 * Template Name: Create Ticket
 *
 * @package Quality_Control
 * @since Quality Control 0.1
 */
 
global $quality_options; 
 
get_header( 'create-ticket' ); ?>

			<div id="main" role="main">
			
				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
					
				<div id="ticket-manager" class="tabber"> 
				
					<?php get_template_part( 'inc/templates/navigation', 'create-ticket' ); ?>
					
					<div id="create-ticket" class="panel">
									
						<div class="entry inner">
							<h1><?php the_title(); ?></h1>
							
							<?php the_content(); ?>
						</div>
						
						<?php if( quality_ticket_creation_cap() ) : ?>
						
							<div id="respond">
							
								<?php if( quality_adding_ticket() ) : ?>
								
									<div id="message" style="margin:0;"><?php echo quality_add_ticket(); ?></div>
										<br />
									
								<?php endif ?>
														
								<?php do_action( 'quality_ticket_form', array( 'location' => 'page' ) ); ?>
								
							</div>
							
						<?php else : ?>
							
							<?php do_action( 'quality_create_ticket_no_perms' ); ?>
						
						<?php endif; ?>
						
					</div>
				
				</div><!-- End #ticket-manager --> 
				
				<?php endwhile; endif; ?>
			
			</div><!-- End #main -->
			
			<?php get_sidebar( 'create-ticket' ); ?>
		
<?php get_footer( 'create-ticket' ); ?>