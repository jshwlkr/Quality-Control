<?php global $quality_options; ?>	
				
					<div class="tabber-navigation"> 
					
						<ul>
							<?php do_action( 'quality_navigation_before' ); ?>
							
							<?php $post = $posts[0]; ?>
							<li <?php if( is_home() ) : ?>class="current-tab"<?php endif; ?>>
								<a href="<?php echo home_url( '/' ); ?>"><?php _e( 'Recent Tickets', 'quality' ); ?></a>
							</li> 
							
							<li <?php if( is_tax( 'ticket_status' ) ) : ?>class="current-tab"<?php endif; ?>>
								<a href="#"><?php _e( 'Status', 'quality' ); ?></a>
								<ul class="children">
									<?php
										$statuses = get_categories( array( 
											'taxonomy' => 'ticket_status',
											'hide_empty' => 0,
											'orderby' => 'name'
										) );
										foreach( $statuses as $status )
											echo '<li><a href="' . get_term_link( $status, 'ticket_status' ) . '" title="' . sprintf( __( 'View all tickets marked %s', 'quality' ), $status->name ) . '" ' . '><span>'. $status->count . '</span>' . $status->name. '</a> </li> ';
									?>
								</ul>
							</li>
							
							<li <?php if( is_tax( 'ticket_milestone' ) ) : ?>class="current-tab"<?php endif; ?>>
								<a href="#"><?php _e( 'Milestone', 'quality' ); ?></a>
								<ul class="children">
									<?php wp_list_categories( 'title_li=&hide_empty=0&taxonomy=ticket_milestone&orderby=name' ); ?>
								</ul>
							</li>
							
							<?php do_action( 'quality_navigation_tax_after' ); ?>
							
							<li <?php if( is_category() ) : ?>class="current-tab"<?php endif; ?>>
								<a href="#"><?php _e( 'Category', 'quality' ); ?></a>
								<ul class="children">
									<?php wp_list_categories( 'title_li=&hide_empty=0&orderby=name' ); ?>
								</ul>
							</li>
							
							<?php if( is_user_logged_in() ) : global $current_user; get_currentuserinfo(); ?>
								
								<li <?php if( is_author( $current_user->user_id ) ) : ?>class="current-tab"<?php endif; ?>>
									<a href="<?php echo get_author_posts_url( $current_user->ID, $current_user->user_nicename ); ?>"><?php _e( 'My Tickets', 'quality' ); ?></a>
								</li>
								
							<?php endif; ?>
							
							<?php do_action( 'quality_navigation_after' ); ?>
							
							<?php if( $quality_options[ 'ticket_page' ] ) : ?> 
							
								<li class="alignright<?php if( is_page( $quality_options[ 'ticket_page' ] ) ) : ?> current-tab<?php endif; ?>">
									<a href="<?php echo get_permalink( $quality_options[ 'ticket_page' ] ); ?>"><?php echo get_the_title( $quality_options[ 'ticket_page' ] ); ?></a>
								</li>
								
							<?php endif; ?>
							
						</ul>
						
					</div> 