<?php
/**
 * The Template for displaying all pages.
 *
 * @package Quality_Control
 * @since Quality Control 0.1
 */

get_header( 'page' ); ?>

			<div id="main" role="main">
		
				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

					<div id="ticket-manager-<?php the_ID(); ?>" <?php post_class( 'tabber' ); ?>>
					
						<?php get_template_part( 'inc/templates/navigation', 'page' ); ?>
						
						<div class="panel">
						
							<div class="entry inner">
							
								<h1><?php the_title(); ?></h1>
								
								<?php the_content(); ?>
							
							</div>
						
						</div>
						
					</div><!-- #post -->

				<?php endwhile; endif; ?>
				
			</div><!-- End #main -->
			
			<?php get_sidebar( 'page' ); ?>

<?php get_footer( 'page' ); ?>
