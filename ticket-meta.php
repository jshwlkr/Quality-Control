<?php do_action( 'quality_ticket_meta_before' ); ?>

<li>
	<small><?php _e( 'Ticket', 'quality' ); ?></small>
	<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'quality' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark">#<?php the_ID(); ?></a>
</li>

<li>
	<small><?php _e( 'Category', 'quality' ); ?></small>
	<?php the_category( ', ' ); ?>
</li>

<li>
	<small><?php _e( 'Milestone', 'quality' ); ?></small>
	<?php echo get_the_term_list( $post->ID, 'ticket_milestone', '', ', ', '' ); ?>	
</li>

<?php do_action( 'quality_ticket_meta_tax_after' ); ?>

<li>
	<small><?php _e( 'Tags', 'quality' ); ?></small>
	<?php if( !get_the_term_list( $post->ID, 'post_tag', '', ', ', '' ) ) : ?>
		&mdash;
	<?php else : ?>
		<?php echo get_the_term_list( $post->ID, 'post_tag', '', ', ', '' ); ?>
	<?php endif; ?>
</li>

<li>
	<small><?php _e( 'Created', 'quality' ); ?></small>
	<?php echo ( is_single() ? get_the_date() : sprintf( __( '%s ago', 'quality' ), human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) ) ); ?>
</li>

<?php do_action( 'quality_ticket_meta_after' ); ?>