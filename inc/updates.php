<?php
if ( !function_exists( 'quality_comment' ) ) :
/**
 * The comments, or "updates" for each ticket.
 *
 * @since Quality Control 0.1
 */
function quality_comment( $comment, $args, $depth ) 
{	
	$GLOBALS[ 'comment' ] = $comment;
	$updates = get_comment_meta( get_comment_ID(), 'ticket_updates', true );
	$i = 0;
?>
	<li <?php comment_class( 'ticket' . ( $i % 2 ? '' : ' alt' ) ); ?> id="comment-<?php comment_ID(); ?>"> 
		
		<div class="ticket-gravatar"> 
			<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><?php echo get_avatar( $comment, 29 ); ?></a> 
		</div> 
		
		<div class="ticket-info"> 
		
			<p class="ticket-author">
				<strong><?php comment_author_link(); ?></strong><br />
				<small><?php printf( __( 'about <em title="%s">%s</em> ago', 'quality' ), esc_attr( get_the_date() . __( ' at ', 'quality' ) . get_the_time() ), esc_attr( human_time_diff( get_comment_time( 'U' ), current_time( 'timestamp' ) ) ) ); ?></small>
			</p>
			
			<div class="reply">
			
				<?php if( get_comment_text() != '&nbsp;' ) : comment_text(); endif; ?>
				
				<?php if( $updates ) : ?>
					
					<ol class="update-list<?php if( get_comment_text() == '&nbsp;' ) :?> single<?php endif; ?>">
						<li><strong class="title"><?php _e( 'Updates Made:', 'quality' ); ?></strong>
							<ul>
								<?php foreach( $updates as $update ) : ?>
									<li><?php echo $update; ?></li>
								<?php endforeach; ?>
							</ul>
						</li>
					</ol>
					
				<?php endif; ?>
				
			</div>
			
		</div>
<?php
	$i++;
}
endif;

/** Tell WordPress to run quality_save_comment() when the 'comment_post' hook is run. */ 
add_action( 'comment_post', 'quality_save_comment', 1 );

/**
 * Create the update history in the form of comment meta.
 *
 * How this works: This function compares the previous ticket
 * attributes, and see if the updates made via the comment form
 * are different. If they are, then it adds a bit of text as
 * comment meta. Tags and Categories are done directly in this function,
 * while status, milestones, and attachments are done through a hook, so they
 * can be removed.
 *
 * @since Quality Control 0.1
 * @uses wp_set_object_terms
 * @uses get_term
 * @uses get_the_category
 * @uses add_comment_meta
 */
function quality_save_comment( $comment_id ) 
{
	global $post, $updates;
	
	//$comment_meta = $_POST[ "quality_comment_meta" ];
	
	$category = get_the_category( $post->ID );
	$ticket_category = get_term( esc_attr( $_POST[ 'ticket_category' ] ), 'category' );
	$tags = get_the_tags( $post->ID );

	if( $tags )
	{
		$ticket_tags = "";
			
		foreach( $tags as $tag )
			$ticket_tags .= $tag->name . ', ';
			
		$ticket_tags = substr( $ticket_tags, 0, -2 );
	}
	
	$updates = array();
	
	do_action( 'quality_update_ticket_comment', $post, $_POST );
		
	/** Has the category changed? */
	if( $ticket_category->term_id != $category[0]->cat_ID )
	{
		$updates[] = apply_filters( 'quality_comment_category_updated', sprintf( __( '<strong>category</strong> changed from <em>%s</em> to <em>%s</em>', 'quality' ), $category[0]->cat_name, $ticket_category->name ) );
		
		$status = wp_set_object_terms( $post->ID, intval( $ticket_category->term_id ), 'category', false );
	}
	
	/** Have the tags changed? */
	if( esc_attr( $_POST[ 'ticket_tags' ] ) != $ticket_tags )
	{
		$updates[] = apply_filters( 'quality_comment_tags_updated', sprintf( __( '<strong>tags</strong> updated from <em>%s</em> to <em>%s</em>', 'quality' ), $ticket_tags, esc_attr( $_POST[ 'ticket_tags' ] ) ) );
		
		$tags = wp_set_post_tags( $post->ID, explode( ', ', esc_attr( $_POST[ 'ticket_tags' ] ) ), false );
	}
	
	$updates = apply_filters( 'quality_comment_update_meta', $updates );
	
	/** Add the array of updates as comment meta. */
	if( !empty( $updates ) )
		add_comment_meta( $comment_id, 'ticket_updates', $updates, true );
		
	do_action( 'quality_comment_update_made', $comment_id );
}

/** Tell WordPress to run quality_update_ticket_status() when the 'quality_update_ticket_comment' hook is run. */ 
add_action( 'quality_update_ticket_comment', 'quality_update_ticket_status', 10, 2 );

/**
 * When a comment has been created, check to see if they are updating the status.
 * If they are, actually update the status, but then also provide a string which
 * says what is updated.
 *
 * @since Quality Control 0.1.2
 * @uses wp_set_object_terms
 * @uses get_term
 */
function quality_update_ticket_status( $post, $comment_meta )
{
	global $updates;
	
	$ticket_status = get_term( esc_attr( $comment_meta[ 'ticket_status' ] ), 'ticket_status' );
	
	if( $ticket_status->term_id != quality_ticket_status( $post->ID, 'term_id', 'ticket_status' ) )
	{
		$updates[] = apply_filters( 'quality_comment_state_updated', sprintf( __( '<strong>state</strong> changed from <em>%s</em> to <em>%s</em>', 'quality' ), quality_ticket_status( $post->ID, 'name', 'ticket_status' ), $ticket_status->name ) );
		
		$status = wp_set_object_terms( $post->ID, intval( $ticket_status->term_id ), 'ticket_status', false );
	
		return $updates;
	}
}

/** Tell WordPress to run quality_update_ticket_milestone() when the 'quality_update_ticket_comment' hook is run. */ 
add_action( 'quality_update_ticket_comment', 'quality_update_ticket_milestone', 10, 2 );

/**
 * When a comment has been created, check to see if they are updating the milestone.
 * If they are, actually update the milestone, but then also provide a string which
 * says what is updated.
 *
 * @since Quality Control 0.1.2
 * @uses wp_set_object_terms
 * @uses get_term
 */
function quality_update_ticket_milestone( $post, $comment_meta )
{
	global $updates;
	
	$ticket_milestone = get_term( esc_attr( $comment_meta[ 'ticket_milestone' ] ), 'ticket_milestone' );
	
	if( $ticket_milestone->term_id != quality_ticket_status( $post->ID, 'term_id', 'ticket_milestone' ) )
	{
		$updates[] = apply_filters( 'quality_comment_milestone_updated', sprintf( __( '<strong>milestone</strong> changed from <em>%s</em> to <em>%s</em>', 'quality' ), quality_ticket_status( $post->ID, 'name', 'ticket_milestone' ), $ticket_milestone->name ) );
	
		$milestone = wp_set_object_terms( $post->ID, intval( $ticket_milestone->term_id ), 'ticket_milestone', false );
		
		return $updates;
	}
}

/** Tell WordPress to run quality_update_ticket_owners() when the 'quality_update_ticket_comment' hook is run. */ 
add_action( 'quality_update_ticket_comment', 'quality_update_ticket_owners', 10, 2 );

/**
 * When a comment has been created, check to see if the assigned
 * users have been changed. If so, see if you can find the difference.
 *
 * @since Quality Control 0.1.2
 * @uses wp_set_object_terms
 * @uses get_term
 */
function quality_update_ticket_owners( $post, $comment_meta )
{
	global $updates;
	
	$ticket_owners = $comment_meta[ 'ticket_assign' ];
	
	if( $ticket_owners != quality_get_ticket_assigned_to() )
	{
		$updates[] = apply_filters( 'quality_comment_owners_updated', sprintf( __( '<strong>owner(s)</strong> changed from <em>%s</em> to <em>%s</em>', 'quality' ), quality_get_ticket_assigned_to(), $ticket_owners ) );
		
		$ticket_owners = explode( ",", $ticket_owners );
		$owners = array();
		
		delete_post_meta( $post->ID, '_assigned_to' );
		
		foreach( $ticket_owners as $owner )
		{
			$owner = get_userdatabylogin( $owner );
			//$owners[] = $owner->ID;
			add_post_meta( $post->ID, '_assigned_to', $owner->ID );
		}
		
		//$owners = update_post_meta( $post->ID, '_assigned_to', $owners );
	
		return $updates;
	}
}

/** Tell WordPress to run quality_update_ticket_attachment() when the 'quality_update_ticket_comment' hook is run. */ 
add_action( 'quality_update_ticket_comment', 'quality_update_ticket_attachment', 10, 2 );

/**
 * When a comment has been created, check to see if they are trying
 * to attach a file. Adding the ID of the attachment to comment meta,
 * just as something to have in the future.
 *
 * @since Quality Control 0.1.2
 * @uses media_handle_upload
 * @uses add_comment_meta
 */
function quality_update_ticket_attachment( $post, $comment_meta )
{
	global $updates;
	
	if( !empty( $_FILES[ 'ticket_attachment' ][ 'size' ] ) )
	{
		require_once( ABSPATH . "wp-admin" . '/includes/admin.php' );

		$attachment = media_handle_upload( 'ticket_attachment', $post->ID );
		
		$updates[] = apply_filters( 'quality_comment_attachment_added', sprintf( __( 'File Attached: <a href="%s">%s</a>', 'quality' ), get_permalink( $attachment ), get_the_title( $attachment ) ) );
		
		add_comment_meta( $comment_id, 'file_attachment', $attachment, true );
	}
}

/** Tell WordPress to run quality_restrict_comment_editing() when the 'map_meta_cap' filter is called. */
add_filter( 'map_meta_cap', 'quality_restrict_comment_editing', 10, 3 );

/**
 * Restrict post author to only editing their comments.
 *
 * via http://scribu.net/wordpress/prevent-blog-authors-from-editing-comments.html
 *
 * @since Quality Control 0.1
 * @uses add_options_page
 */
function quality_restrict_comment_editing( $caps, $cap, $user_id ) 
{
	global $pagenow, $comment;
 
	if( 'edit_post' == $cap && is_admin() && 'comment.php' == $pagenow ) 
	{
		if ( $comment->user_id != $user_id )
			$caps[] = 'moderate_comments';
	}
 
	return $caps;
}

/** Tell WordPress to run quality_blank_comment() when the 'pre_comment_on_post' action is called. */
add_action( 'pre_comment_on_post', 'quality_blank_comments' );

/**
 * If the comment field is blank, insert an invisible &nbsp;
 * This only works once. If you do it twice in the same ticket,
 * it will trigger the duplicate comment error. still looking for a work-around.
 *
 * via http://www.johnpbloch.com/
 *
 * @since Quality Control 0.1.3
 */
function quality_blank_comments()
{
	$comment_content = ( isset( $_POST[ 'comment' ] ) ) ? trim( $_POST['comment'] ) : null;

	if( empty( $comment_content ) )
		$_POST[ 'comment' ] = '&nbsp;';
}