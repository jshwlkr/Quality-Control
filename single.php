<?php
/**
 * The Template for displaying all single posts.
 *
 * @package Quality_Control
 * @since Quality Control 0.1
 */

global $authordata, $quality_options, $current_user;
  
get_header( 'single' ); ?>

			<div id="main" role="main">
		
				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

					<div id="ticket-manager-<?php the_ID(); ?>" <?php post_class( 'tabber' ); ?>>
					
						<?php get_template_part( 'inc/templates/navigation', 'single' ); ?>
						
						<div class="panel">
						
							<ol class="ticket-list">
							
								<?php
									/** 
									 * A very clunky way of doing this. Should be converted
									 * into a function.
									 */
									$owners = quality_assigned_to();
									
									if( empty( $owners ) )
										$owners = array();
																		
									if( $quality_options[ 'assigned_perms' ] > 0 || 
										in_array( $current_user->ID, $owners ) || 
										current_user_can( 'manage_options' ) ||
										$current_user->ID == get_the_author_meta( 'ID' ) ||
										count( $owners ) == 0
									) :
								?>
							
								<li id="single-ticket" class="ticket">
								
									<p class="ticket-author">
										
										<?php echo get_the_date(); ?>
										
										<?php if( current_user_can( 'delete_post', $post->ID ) ) : ?>
											&mdash;
											<a href="<?php echo get_delete_post_link( $post->ID ); ?>"><?php _e( 'Delete Ticket', 'quality' ); ?></a>
										<?php endif; ?>
									</p>
		
									<a href="<?php echo get_term_link( quality_ticket_status( $post->ID, 'slug' ), 'ticket_status' ); ?>" class="ticket-status <?php echo quality_ticket_status( $post->ID, 'slug' ); ?>"><?php echo quality_ticket_status( $post->ID, 'name' ); ?></a>
			
									<h1 class="ticket-title">
										<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'quality' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
									</h1>
									
									<ul class="ticket-meta single">
										<?php get_template_part( 'inc/templates/ticket-meta', 'single' ); ?>
									</ul>
																				
									<div class="entry single-ticket">
										<?php the_content(); ?>
										<?php wp_link_pages(); ?>
									</div>
																		
									<ol class="update-list">
									
									<?php
										foreach( get_comments( 'order=ASC&post_id=' . $post->ID ) as $comment ) :
											if( get_comment_meta( get_comment_ID(), 'ticket_updates', true ) ) :
									?>
										<li><strong class="title"><?php printf( __( 'Changed %s ago by %s', 'quality' ), human_time_diff( get_comment_time( 'U' ), current_time( 'timestamp' ) ), get_comment_author() ); ?></strong>
											<ul>
											
												<?php foreach( get_comment_meta( get_comment_ID(), 'ticket_updates', true ) as $item ) : ?>
											
													<li><?php echo $item; ?></li>
											
												<?php endforeach; ?>
												
											</ul>
										</li>
										
									<?php endif; endforeach; ?>
																		
									<?php
										$attachments = get_posts( array(
											'post_type' => 'attachment',
											'numberposts' => -1,
											'post_status' => null,
											'post_parent' => $post->ID
										) );
										
										if( $attachments ) :
									?>
									
										<li><strong class="title"><?php _e( 'Attachments', 'quality' ); ?></strong>
											<ul>
											
												<?php foreach( $attachments as $post ) : setup_postdata( $post ); ?>
												
													<li id="attachment-<?php echo $post->ID; ?>"><a href="<?php echo wp_get_attachment_url( $post->ID ); ?>"><?php the_title(); ?></a> by <?php the_author(); ?> on <?php echo get_the_date(); ?></li>
												
												<?php endforeach; ?>
											
											</ul>
										
										</li>
										
									<?php endif; wp_reset_query(); ?>
									
									</ol>
									
								</li>
								
								<?php comments_template(); ?>
								
								<?php else : ?>

								<li class="ticket no-results">
									<?php printf( __( 'Sorry, you are not assigned to this ticket, and therefore cannot view it. It is currently assigned to %s', 'quality' ), quality_get_ticket_assigned_to() ); ?>
								</li>
								
								<?php endif; ?>
							
							</ol>
						
						</div>
						
					</div><!-- #post -->

				<?php endwhile; endif; ?>
				
			</div><!-- End #main -->
			
			<?php get_sidebar( 'single' ); ?>

<?php get_footer( 'single' ); ?>