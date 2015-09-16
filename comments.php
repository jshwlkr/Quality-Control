<?php
/**
 * The template for displaying Comments.
 *
 * @package Quality_Control
 * @since Quality Control 0.1
 */
 
global $quality_options, $current_user;

	if ( post_password_required() ) : 
?>
		<p class="nopassword"><?php _e( 'This post is password protected. Enter the password to view any comments.', 'quality' ); ?></p>
<?php
		return;
	endif;

	if ( have_comments() ) :
		wp_list_comments( array( 
			'callback' => 'quality_comment' 
		) );
	endif;
?>

<?php 
	if( 2 == $quality_options[ 'assigned_perms' ] ||
		current_user_can( 'manage_options' ) ||
		$current_user->ID == get_the_author_meta( 'ID' ) ||
		( count( $owners ) == 0 && $qualiy_options[ 'assigned_perms' ] > 0 )
	) : 
?>

	<div id="respond">
		
		<?php if ( get_option( 'comment_registration' ) && !is_user_logged_in() ) : ?>
			<h3><?php _e( 'Sorry, you must be logged in to update this ticket.', 'quality' ); ?></h3>
			<?php do_action( 'comment_form_must_log_in_after' ); ?>
		<?php else : ?>

			<?php do_action( 'quality_ticket_form', array( 'location' => 'ticket' ) ); ?>
		
		<?php endif; ?>
		
	</div>

<?php endif; ?>