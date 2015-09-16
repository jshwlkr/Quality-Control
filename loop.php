<?php
/**
 * The loop that displays posts.
 *
 * The loop displays the posts and the post content.  See
 * http://codex.wordpress.org/The_Loop to understand it and
 * http://codex.wordpress.org/Template_Tags to understand
 * the tags used in it.
 *
 * This can be overridden in child themes with loop.php or
 * loop-template.php, where 'template' is the loop context
 * requested by a template. For example, loop-index.php would
 * be used if it exists and we ask for the loop with:
 * <code>get_template_part( 'loop', 'index' );</code>
 *
 * @package Quality_Control
 * @since Quality Control 0.1
 */

global $query_string, $quality_options, $authordata; 

print_r( $authordata );

query_posts( $query_string . '&orderby=modified&post_type=ticket' ); if( have_posts() ) : ?>
	
	<ol class="ticket-list">
	
	<?php while ( have_posts() ) : the_post(); $i++; ?>
	
		<li <?php post_class( 'ticket ' . ( $i % 2 ? '' : 'alt ' ) . quality_ticket_status( $post->ID, 'slug' ) ); ?>> 
			
			<p class="ticket-author">
				<?php _e( 'created by', 'quality' ); ?> <?php the_author_posts_link(); ?>
			</p>
			
			<a href="<?php bloginfo( 'url' ); ?>/status/<?php echo quality_ticket_status( $post->ID, 'slug' ); ?>/" class="ticket-status <?php echo quality_ticket_status( $post->ID, 'slug' ); ?>"><?php echo quality_ticket_status( $post->ID, 'name' ); ?></a>
			
			<h2 class="ticket-title">
				<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'quality' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
			</h2> 
			
			<ul class="ticket-meta">
				<?php get_template_part( 'ticket-meta' ); ?>
			</ul>
			
		</li>
	
	<?php endwhile; ?>
	
	</ol>
		
	<?php if( quality_show_pagination() ) : ?>
		<div class="tabber-navigation bottom">
			<ul>
				<li><?php next_posts_link( __( 'Older Tickets', 'quality' ) ); ?></li>
				<li class="alignright"><?php previous_posts_link( __( 'Newer Tickets', 'quality' ) ); ?></li>
			</ul>
		</div><!-- #nav-above -->
	<?php endif; ?>
	
<?php else : ?>

	<ol class="ticket-list">

		<li class="ticket no-results">
			<?php _e( 'Sorry, we couldn&#39;t find any results.', 'quality' ); ?>
		</li>
	
	</ol>

<?php endif; ?>