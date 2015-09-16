<?php do_action( 'quality_ticket_meta_before' ); ?>

<li>
	<small><?php _e( 'Ticket', 'quality' ); ?></small>
	<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'quality' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark">#<?php the_ID(); ?></a>
</li>

<li>
	<small><?php _e( 'Category', 'quality' ); ?></small>
	<?php the_category( ', ' ); ?>
</li>

<?php if( taxonomy_exists( 'ticket_milestone' ) ) : ?>

<li>
	<small><?php _e( 'Milestone', 'quality' ); ?></small>
	<?php echo get_the_term_list( $post->ID, 'ticket_milestone', '', ', ', '' ); ?>	
</li>

<?php endif; ?>

<?php do_action( 'quality_ticket_meta_tax_after' ); ?>

<?php if( get_the_term_list( $post->ID, 'post_tag', '', ', ', '' ) ) : ?>
<li>
	<small><?php _e( 'Tags', 'quality' ); ?></small>
	<?php echo get_the_term_list( $post->ID, 'post_tag', '', ', ', '' ); ?>
</li>
<?php endif; ?>

<li>
	<small><?php _e( 'Created by', 'quality' ); ?></small>
	<?php the_author_posts_link(); ?>
</li>

<?php if( quality_assigned_to() ) : ?>
<li>
	<small><?php _e( 'Assigned to', 'quality' ); ?></small>
	<?php quality_assigned_to_list(); ?>
</li>
<?php endif; ?>

<?php do_action( 'quality_ticket_meta_after' ); ?>