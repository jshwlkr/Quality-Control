<?php
/** Tell WordPress to run quality_custom_post_types() when the 'init' hook is run. */ 
add_action( 'init', 'quality_custom_post_types' );

/**
 * Register the Tickets post type, and remove the default "Post" type.
 *
 * @since Quality Control 0.1
 * @uses register_post_type
 */
function quality_custom_post_types()
{
	global $wp_post_types, $menu;
	
	if( isset( $wp_post_types[ 'post' ] ) )
		unset( $wp_post_types[ 'post'] );
	
	// Create the Post Type
	register_post_type( 'ticket', array(
		'label' => __( 'Tickets', 'quality' ),
		'labels' => array(
			'name' => __( 'Tickets', 'quality' ),
			'singular_name' => __( 'Ticket', 'quality' ),
			'add_new' => __( 'Add New', 'quality' ),
			'add_new_item' => __( 'Add New Ticket', 'quality' ),
			'edit_item' => __( 'Edit Ticket', 'quality' ),
			'view_item' => __( 'View Ticket', 'quality' ),
			'search_items' => __( 'Search Tickets', 'quality' ),
			'not_found' => __( 'No Tickets Found', 'quality' ),
			'not_found_in_trash' => __( 'No Tickets found in trash', 'quality' )
		),
		'description' => __( 'Tickets, usually used to track bugs.', 'quality' ),
		'public' => true,
		'rewrite' => array(
			'slug' => 'ticket'
		),
		'capability_type' => 'post',
		'supports' => array(
			'title', 'editor', 'author', 'comments'
		),
		'taxonomies' => array(
			'category', 'post_tag'
		),
		'menu_position' => 5
	) );
	
}

/** Tell WordPress to run quality_custom_tax() when the 'init' hook is run. */ 
add_action( 'init', 'quality_custom_tax' );

/**
 * Register the default custom taxonomies: ticket_milestone, and ticket_status
 *
 * @since Quality Control 0.1
 * @uses register_taxonomy
 */
function quality_custom_tax()
{
	// Create Milestones Taxonomy.
	register_taxonomy( 'ticket_milestone', array( 'ticket' ), array(
		'labels' => array(
			'name' => __( 'Milestones', 'quality' ),
			'singular_name' => __( 'Milestone', 'quality' ),
			'search_items' => __( 'Search Milestones', 'quality' ),
			'popular_items' => __( 'Popular Milestones', 'quality' ),
			'all_items' => __( 'All Milestones', 'quality' ),
			'update_item' => __( 'Update Milestone', 'quality' ),
			'add_new_item' => __( 'Add New Milestone', 'quality' ),
			'new_item_name' => __( 'New Milestone Name', 'quality' ),
			'edit_item' => __( 'Edit Milestone', 'quality' )
		),
		'show_tagcloud' => false,
		'show_ui' => true,
		'rewrite' => array(
			'slug' => 'milestone'
		)
	) );
	
	// Create Status Taxonomy
	register_taxonomy( 'ticket_status', array( 'ticket' ), array(
		'labels' => array(
			'name' => __( 'States', 'quality' ),
			'singular_name' => __( 'Status', 'quality' ),
			'search_items' => __( 'Search States', 'quality' ),
			'popular_items' => __( 'Popular States', 'quality' ),
			'all_items' => __( 'All States', 'quality' ),
			'update_item' => __( 'Update Status', 'quality' ),
			'add_new_item' => __( 'Add New Status', 'quality' ),
			'new_item_name' => __( 'New Status Name', 'quality' ),
			'edit_item' => __( 'Edit Status', 'quality' )
		),
		'show_tagcloud' => false,
		'show_ui' => true,
		'rewrite' => array(
			'slug' => 'status'
		)
	) );
	
	// Change the Name of Post Tags to Ticket Tags
	register_taxonomy( 'post_tag', array( 'ticket' ), array(
		'labels' => array(
			'name' => __( 'Tags', 'quality' )
		)
	) );
}

/** Tell WordPress to run quality_manage_column_titles() when the 'manage_edit-ticket_column' filter is run. */ 
add_filter( 'manage_edit-ticket_columns', 'quality_manage_column_titles' );

/**
 * Add Extra columns to the ticket overview.
 *
 * @since Quality Control 0.1
 */
function quality_manage_column_titles( $post_columns )
{
	$columns = array(
		'cb' => '<input type="checkbox" />',
		'title' => __( 'Ticket', 'quality' ),
		'status' => __( 'Status', 'quality' ),
		'milestone' => __( 'Milestone', 'quality' ),
		'tags' => __( 'Tags', 'quality' ),
		'categories' => __( 'Categories', 'quality' ),
		'assigned' => __( 'Assigned To', 'quality' ),
		'date' => __( 'Created', 'quality' )
	);
	
	return $columns;
}

/** Tell WordPress to run quality_manage_columns() when the 'manage_posts_custom_column' hook is run. */ 
add_action( 'manage_posts_custom_column', 'quality_manage_columns' );

/**
 * Create callbacks for custom columns.
 *
 * @since Quality Control 0.1
 * @uses get_the_term_list
 */
function quality_manage_columns( $column )
{
	global $post;
	
	switch( $column )
	{
		case 'milestone' :
		
			$milestones = get_the_terms( $post->ID, 'ticket_milestone' );
			
			if( !empty( $milestones ) ) 
			{
				$out = array();
				foreach ( $milestones as $c )
					$out[] = "<a href='edit.php?post_type={$post->post_type}&amp;ticket_milestone={$c->slug}'> " . esc_html( sanitize_term_field( 'name', $c->name, $c->term_id, 'ticket_milestone', 'display' ) ) . "</a>";
				echo join( ', ', $out );
			} 
			else 
				_e( 'No Milestone', 'quality' );
				
			break;
			
		case 'status' :
		
			$states = get_the_terms( $post->ID, 'ticket_status' );
			
			if( !empty( $states ) ) 
			{
				$out = array();
				foreach ( $states as $c )
					$out[] = "<a href='edit.php?post_type={$post->post_type}&amp;ticket_status={$c->slug}'> " . esc_html( sanitize_term_field( 'name', $c->name, $c->term_id, 'ticket_milestone', 'display' ) ) . "</a>";
				echo join( ', ', $out );
			} 
			else 
				_e( 'No Status', 'quality' );
				
			break;
			
		case 'assigned' :
			
			echo quality_assigned_to_list();
			
			break;
	}
}

/** Tell WordPress to run quality_custom_types_menu() when the 'admin_menu' hook is run. */ 
add_action( 'admin_menu', 'quality_custom_types_menu' );

/**
 * Remove the "Post" menu item. Because the "Post" post type is 
 * hard coded into the menu, we need to manually remove it.
 *
 * @since Quality Control 0.1
 */
function quality_custom_types_menu( $menu )
{
	global $menu;

	if( isset( $menu[5] ) )
		unset( $menu[5] );
		
	return $menu;
}

/** Tell WordPress to run quality_custom_favorites() when the 'favorite_actions' filter is run. */
add_filter( 'favorite_actions', 'quality_custom_favorites' );

/**
 * Remove the default "New Post" in favorite actions. Since we removed
 * that post type.
 *
 * @since Quality Control 0.1.2
 */	
function quality_custom_favorites( $actions ) 
{
	unset( $actions[ 'post-new.php' ] );
	
	return $actions;
}

/** Tell WordPress to run quality_create_meta_box() when the 'admin_menu' hook is run. */
add_action( 'admin_menu', 'quality_create_meta_box' );

/**
 * Register Meta Boxes
 *
 * @since Quality Control 0.1
 * @uses add_meta_box
 */	 
function quality_create_meta_box() 
{
	remove_meta_box( 'tagsdiv-ticket_milestone', 'ticket', 'side' );
	remove_meta_box( 'tagsdiv-ticket_status', 'ticket', 'side' );

	add_meta_box( 'ticket-status', __( 'Status', 'quality' ), 'quality_meta_box_ticket_status', 'ticket', 'side', 'low' );
	
	add_meta_box( 'ticket-milestone', __( 'Milestone', 'quality' ), 'quality_meta_box_ticket_milestone', 'ticket', 'side', 'low' );
}

if ( !function_exists( 'quality_meta_box_ticket_status' ) ) :
/**
 * Create the input box for the status meta box.
 * 
 * Instead of a comma separated list, a select box
 * is created instead.
 *
 * @since Quality Control 0.1
 * @uses get_the_terms
 * @uses wp_dropdown_categories
 */
function quality_meta_box_ticket_status()
{
	global $post, $quality_options;
	
	echo'<div class="input-text-wrap" style="margin:5px 0 0">';
	
	wp_dropdown_categories( array(
		'taxonomy' => 'ticket_status',
		'hide_empty' => 0,
		'name' => 'quality[ticket_status]',
		'selected' => ( quality_ticket_status( $post->ID ) ? quality_ticket_status( $post->ID ) : $quality_options[ 'default_status' ] )
	) );
		
	echo'</div>';
}
endif;

if ( !function_exists( 'quality_meta_box_ticket_milestone' ) ) :
/**
 * Create the input box for the status meta box.
 * 
 * Instead of a comma separated list, a select box
 * is created instead.
 *
 * @since Quality Control 0.1
 * @uses get_the_terms
 * @uses wp_dropdown_categories
 */
function quality_meta_box_ticket_milestone()
{
	global $post;
	
	echo'<div class="input-text-wrap" style="margin:5px 0 0">';
	
	wp_dropdown_categories( array(
		'taxonomy' => 'ticket_milestone',
		'hide_empty' => 0,
		'name' => 'quality[ticket_milestone]',
		'selected' => quality_ticket_status( $post->ID, 'term_id', 'ticket_milestone' )
	) );
		
	echo'</div>';
}
endif;

/** Tell WordPress to run quality_save_status() when the 'save_post' hook is run. */
add_action( 'save_post', 'quality_save_status', 1, 2 );

/**
 * Save the status of a post when a post is saved.
 * 
 * This is needed for creating/updating/saving tickets
 * in the backend, as opposed to creating a new ticket via
 * the frontend page.
 *
 * @since Quality Control 0.1
 * @uses get_post
 * @uses current_user_can
 * @uses wp_set_object_terms
 */
function quality_save_status( $post_id, $post )
{
	global $post, $type;

	$post = get_post( $post_id );

	if( !isset( $_POST[ "quality" ] ) )
		return;

	if( $post->post_type == 'revision' )
		return;

	if( !current_user_can( 'edit_post', $post->ID ))
		return; 
		
    $status = wp_set_object_terms( $post->ID, intval( $_POST[ 'quality' ][ 'ticket_status' ] ), 'ticket_status', false );
	$milestone = wp_set_object_terms( $post->ID, intval( $_POST[ 'quality' ][ 'ticket_milestone' ] ), 'ticket_milestone', false );
	
	do_action( 'quality_set_object_terms', $post->ID, $_POST[ 'quality' ] );
}

if ( !function_exists( 'quality_ticket_status' ) ) :
/**
 * Get the status of a ticket.
 *
 * @since Quality Control 0.1
 * @uses get_the_terms
 */
function quality_ticket_status( $post_id, $format = 'term_id', $term = 'ticket_status' )
{
	$terms = get_the_terms( $post_id, $term );
	
	if( empty( $terms ) )
		return false;
	
	foreach( $terms as $term )	
		return $term->$format;
}
endif;

if ( !function_exists( 'quality_ticket_milestone' ) ) :
/**
 * Get the milestone of a ticket.
 *
 * @since Quality Control 0.1.5
 * @uses get_the_terms
 */
function quality_ticket_milestone( $post_id, $format = 'term_id', $term = 'ticket_milestone' )
{
	return quality_ticket_status( $post_id, $format, $term );
}
endif;

if ( !function_exists( 'quality_add_ticket' ) ) :
/**
 * Create a fresh ticket via the front-end form submission.
 * Checks for valid permissions, then gathers
 * the information, and creates a new post.
 *
 * @since Quality Control 0.1
 * @returns string $message An error or success message.
 */
function quality_add_ticket()
{
	global $quality_options, $current_user;
	
	if( !isset( $_POST[ 'create_ticket'] ) )
		return false;
		
	if( !quality_ticket_creation_cap() )
		return apply_filters( 'quality_ticket_cap_notice', __( 'Sorry, you do not have permission to create a ticket', 'quality' ) );
			
	$ticket = array();
	
	foreach( $_POST as $key => $value )
		$ticket[ $key ] = isset( $value ) ? $value : "";
	
	get_currentuserinfo();
	$ticket[ 'ticket_author' ] = $current_user->ID;
			
	if( !empty( $ticket[ 'ticket_title' ] ) && !empty( $ticket[ 'comment' ] ) )
	{	
		$args = array(
			'post_type' => 'ticket',
			'post_status' => 'publish',
			'comment_status' => 'open',
			'post_category' => array( $ticket[ 'ticket_category' ] ),
			'tags_input' => $ticket[ 'ticket_tags' ],
			'post_content' => $ticket[ 'comment' ],
			'post_title' => $ticket[ 'ticket_title' ],
			'post_author' => $ticket[ 'ticket_author' ]
		);
		
		$args = apply_filters( 'quality_ticket_args', $args );
		
		$ticket_id = wp_insert_post( $args );
		
		do_action( 'quality_insert_ticket', $ticket_id, $ticket );
		
		$message = apply_filters( 'quality_ticket_created', sprintf( __( 'Cool, your ticket was added. <a href="%s">View Ticket</a> or <a href="%s">Add Another</a>', 'quality' ), get_permalink( $ticket->ID ), get_permalink( $quality_options[ 'ticket_page' ] ) ) );
	}
	else
	{
		$message = apply_filters( 'quality_create_ticket_fields', __( 'Please fill out all required information.', 'quality' ) );
	}
	
	return $message;
}
endif;

if ( !function_exists( 'quality_adding_ticket' ) ) :
/**
 * Has the form for creating at ticket been submitted?
 *
 * @since Quality Control 0.1
 */
function quality_adding_ticket()
{
	if( isset( $_POST[ 'create_ticket'] ) )
		return true;
		
	return false;
}
endif;

/** Tell WordPress to run quality_save_tax() when the 'quality_insert_ticket' hook is run. */
add_action( 'quality_insert_ticket', 'quality_save_tax', 10, 2 );

/**
 * Assign the taxonomy terms (status, milestone) to the object (ticket)
 * when a ticket is created.
 *
 * @since Quality Control 0.1.2
 * @uses wp_set_object_terms
 */
function quality_save_tax( $ticket_id, $ticket )
{	
	wp_set_object_terms( $ticket_id, array( intval( $ticket[ 'ticket_status' ] ) ), 'ticket_status', false );
	wp_set_object_terms( $ticket_id, array( intval( $ticket[ 'ticket_milestone' ] ) ), 'ticket_milestone', false );
}

/** Tell WordPress to run quality_save_attachment() when the 'quality_insert_ticket' hook is run. */
add_action( 'quality_insert_ticket', 'quality_save_attachment', 10, 2 );

/**
 * Attach a file to the ticket.
 *
 * @since Quality Control 0.1.2
 * @uses media_handle_upload
 * @todo Only upload if something is there. Check works fine for comments,
 * 		 but not here for some reason.
 */
function quality_save_attachment( $ticket_id, $ticket )
{
	require_once( ABSPATH . "wp-admin" . '/includes/admin.php' );

	$attachment = media_handle_upload( 'ticket_attachment', $ticket_id );
	
	return $attachment;
}

/** Tell WordPress to run quality_notify_user() when the 'quality_insert_ticket' hook is run. */
add_action( 'quality_insert_ticket', 'quality_assign_user', 10, 2 );

/**
 * Assign users to a ticket when one is created. 
 *
 * @since Quality Control 0.1.5
 * @uses add_post_meta
 * @todo Send email alerts to people who have been assigned to it.
 */
function quality_assign_user( $ticket_id, $ticket )
{
	$ticket_assign = esc_attr( $ticket[ 'ticket_assign' ] );
		
	if( !empty( $ticket_assign ) )
	{
		$ticket_assign = explode( ',', $ticket_assign );
		
		foreach( $ticket_assign as $user )
		{
			$user = get_userdatabylogin( $user );
			add_post_meta( $ticket_id, '_assigned_to', $user->ID, false );
		}
	}
}

if ( !function_exists( 'quality_assigned_to' ) ) :
/**
 * Check to see if this ticket has been assigned to someone.
 * If yes, it will return an array of owners.
 *
 * @since Quality Control 0.1.5
 * @uses get_post_meta
 */
function quality_assigned_to( $post_id = null )
{
	global $post;
	
	if( null == $post_id )
		$post_id = $post->ID;
	
	$owners = get_post_meta( $post_id, '_assigned_to', false );
		
	if( $owners )
		return $owners;
		
	return false;
}
endif;

if ( !function_exists( 'quality_get_ticket_assigned_to' ) ) :
/**
 * Create a comma separated flat list of the current
 * owners. The owners are not linked (see quality_assigned_to_list)
 *
 * @since Quality Control 0.1.5
 * @uses get_post_meta
 * @uses get_userdata
 */
function quality_get_ticket_assigned_to( $post_id = null, $separator = ', ' )
{
	global $post;
	
	if( null == $post_id )
		$post_id = $post->ID;
		
	$assigned_to = get_post_meta( $post_id, '_assigned_to', false );
	$userlist = "";
	
	if( empty( $assigned_to ) )
		return false;
	
	$i = 0;
	foreach( $assigned_to as $user )
	{
		if( $i > 0 )
			$userlist .= $separator;
			
		$user = get_userdata( $user );
		$userlist .= $user->user_login;
				
		$i++;
	}
	
	return $userlist;
}
endif;

if ( !function_exists( 'quality_assigned_to_list' ) ) :
/**
 * Create a list of linked owners. Links to a page
 * showing all tickets assigned to that user.
 *
 * @since Quality Control 0.1.5
 * @uses get_post_meta
 * @uses get_userdata
 */
function quality_assigned_to_list( $post_id = null, $separator = ', ' )
{
	global $post;
	
	if( null == $post_id )
		$post_id = $post->ID;
		
	$assigned_to = get_post_meta( $post_id, '_assigned_to', false );
	$userlist = "";
	
	$i = 0;
	foreach( $assigned_to as $user )
	{
		if( $i > 0 )
			$userlist .= $separator;
			
		$user = get_userdata( $user );
		$userlist .= sprintf( 
			'<a href="%1$s" title="%2$s">%3$s</a>', 
			home_url( '?assigned=' . $user->user_login ),
			esc_attr( sprintf( __( 'Tickets by %s', 'quality' ), $user->display_name ) ),
			$user->display_name
		);
				
		$i++;
	}

	echo $userlist;
}
endif;

if ( !function_exists( 'quality_ticket_creation_cap' ) ) :
/**
 * Limit who can create a ticket.
 * Defaults to people who can publish posts. Create a function
 * with this name inside a child theme to override.
 *
 * @since Quality Control 0.1.2
 * @uses current_user_can
 */
function quality_ticket_creation_cap()
{	
	if( current_user_can( 'publish_posts' ) )
		return true;
		
	return false;
}
endif;

/** Tell WordPress to run quality_the_author() when the 'the_author' and 'get_the_author_display_name' filter is run. */
add_filter( 'the_author', 'quality_the_author' );
add_filter( 'get_the_author_display_name', 'quality_the_author' );

/**
 * For people who allow guests to submit tickets,
 * the author is blank. Create a label so something shows.
 *
 * @since Quality Control 0.1.2
 */
function quality_the_author( $display_name )
{
	if( empty( $display_name ) )
		return apply_filters( 'quality_anon_author', __( 'Anonymous', 'quality' ) );
	else
		return $display_name;
}

/** Tell WordPress to run quality_show_assigned_to() when the 'pre_get_posts' action is run. */
add_action( 'pre_get_posts', 'quality_show_assigned_to' );

/**
 * Update the query to show only tickets
 * assigned to a certain user. This can be accessed on any
 * page by simply adding ?assigned=username to the URL.
 * this allows you to sort any page.
 *
 * @since Quality Control 0.1.5
 */
function quality_show_assigned_to()
{
	global $wp_query;
	
	$assigned = get_query_var( 'assigned' );
	
	if( $assigned )
	{
		$user = get_userdatabylogin( $assigned );
		
		if( empty( $user ) )
			return false;
			
		$wp_query->query_vars[ 'meta_key' ] = '_assigned_to';
		$wp_query->query_vars[ 'meta_value' ] = $user->ID;
	}
}

if ( !function_exists( 'quality_get_ticket_tags' ) ) :
/**
 * Create a comma separated flat list of the current
 * tags.
 *
 * @since Quality Control 0.1.5
 * @uses get_the_tags
 */
function quality_get_ticket_tags( $post_id = null, $separator = ', ' )
{
	global $post;
	
	if( null == $post_id )
		$post_id = $post->ID;
		
	$tags = get_the_tags( $post_id );
	$taglist = "";
	
	if( empty( $tags ) )
		return false;
	
	$i = 0;
	foreach( $tags as $tag )
	{
		if( $i > 0 )
			$taglist .= $separator;
			
		$taglist .= $tag->name;
				
		$i++;
	}
	
	return $taglist;
}
endif;

add_action( 'quality_ticket_form', 'quality_ticket_form' );

/**
 * Outputs a complete ticket form for use within a template.
 * Most strings and form fields may be controlled through the $args array passed
 * into the function, while you may also choose to use the ticket_form_basic_fields,
 * ticket_form_advanced_fields, or ticket_form_meta_fields
 * filter to modify the array of default fields if you'd just like to add a new
 * one or remove a single field. All fields are also individually passed through
 * a filter of the form ticket_form_field_$name where $name is the key used
 * in the array of fields.
 *
 * @since Quality Control 0.1.5
 * @param array $args Options for strings, fields, etc in the form.
 * @param mixed $post_id Post ID to generate the form for, uses current post if null.
 * @return void
 */
function quality_ticket_form( $args = array(), $post_id = null )
{
	global $post, $quality_options;
	
	$category = get_the_category( $post->ID );
	$taglist = quality_get_ticket_tags( $post->ID );
	
	if( $args[ 'location' ] == 'page' )
		$taglist = quality_form_data( 'ticket_tags' );
		
	$basic_fields = array(
		'id' => 'ticket',
		'label' => __( 'Basic Info', 'quality' ),
		'fields' => array(
			'title' 	=> '<p id="ticket-title">
								<label for="ticket_title">' . __( 'Title:', 'quality' ) . '</label>
								<input type="text" name="ticket_title" value="' . quality_form_data( 'ticket_title' ) . '" />
							</p>',
			'tags' 		=> '<p id="ticket-tags">
								<label for="ticket_tags">' . __( 'Tags: <em>(Optional)</em>', 'quality' ) . '</label>
								<input type="text" name="ticket_tags" value="' . $taglist . '" />
							</p>',
			'comment' 	=> '<p>
								<label for="comment">' . __( 'Description:', 'quality' ) . '</label>
								<textarea name="comment" id="comment"></textarea>
							</p>'
		)
	);
	
	$advanced_fields = array(
		'id' => 'ticket-properties',
		'label' => __( 'Ticket Properties', 'quality' ),
		'fields' => array(
			'ticket-status' 	=> '<p id="ticket-status" class="inline-input">
										<label for="ticket_status">' . __( 'Status:', 'quality' ) . '</label>' .
										wp_dropdown_categories( array( 
												'name' => 'ticket_status',
												'hide_empty' => 0,
												'taxonomy' => 'ticket_status',
												'hierarchical' => 1,
												'selected' => ( $args[ 'location' ] == 'page' ? 
													( quality_form_data( 'ticket_status' ) ? 
														quality_form_data( 'ticket_status' ) : 
														$quality_options[ 'default_status' ] 
													) : 
													quality_ticket_status( $post->ID ) 
												),
												'echo' => 0
											) ) . '
									</p>',
			'ticket-milestone' 	=> '<p id="ticket-milestone" class="inline-input">
										<label for="ticket_milestone">' . __( 'Milestone:', 'quality' ) . '</label>' .
										wp_dropdown_categories( array( 
												'name' => 'ticket_milestone',
												'hide_empty' => 0,
												'taxonomy' => 'ticket_milestone',
												'hierarchical' => 1,
												'selected' => ( $args[ 'location' ] == 'page' ? 
													quality_form_data( 'ticket_milestone' ) :
													quality_ticket_status( $post->ID, 'term_id', 'ticket_milestone' )
												),
												'echo' => 0
											) ) . '
									</p>',
			'ticket-category' 	=> '<p id="ticket-category" class="inline-input">
										<label for="ticket_category">' . __( 'Category:', 'quality' ) . '</label>' .
										wp_dropdown_categories( array( 
												'name' => 'ticket_category',
												'hide_empty' => 0,
												'hierarchical' => 1,
												'selected' => ( $args[ 'location' ] == 'page' ? 
													( quality_form_data( 'ticket_category' ) ? 
														quality_form_data( 'ticket_category' ) : 
														get_option( 'default_category' ) 
													) :
													$category[0]->cat_ID
												),
												'echo' => 0
											) ) . '
									</p>',
			'ticket-assign'		=> '<p id="ticket-assign" class="inline-input">
										<label for="ticket_assign">' . __( 'Assigned To: <em>(Optional) Separated multiple usernames by comma. You are automatically assigned.</em>', 'quality' ) . '</label>
										<input type="text" name="ticket_assign" value="' . ( $args[ 'location' ] == 'ticket' ? quality_get_ticket_assigned_to() : quality_form_data( 'ticket_assign' ) ) . '" />
									</p>'
		)
	);
	
	$meta_fields = array(
		'id' => 'ticket-meta',
		'label' => __( 'Other Options', 'quality' ),
		'fields' => array(
			'ticket-attachment' => '<p id="ticket-attachment">
										<label for="ticket_attachment">' . __( 'Attach File', 'quality' ) . '</label>
										<input type="file" name="ticket_attachment" id="ticket_attachment"/>
									</p>'
		)
	);
	
	$defaults = array(
		'location' => 'page',
		'basic_fields' => apply_filters( 'ticket_form_basic_fields', $basic_fields ),
		'advanced_fields' => apply_filters( 'ticket_form_advanced_fields', $advanced_fields ),
		'meta_fields' => apply_filters( 'ticket_form_meta_fields', $meta_fields ),
		'ticket_form_before' => '',
		'ticket_form_after' => '',
		'id_form' => 'ticket_form',
		'id_submit' => 'submit',
		'label_submit' => ( $args[ 'location' ] == 'page' ? __( 'Create Ticket', 'quality' ) : __( 'Update Ticket', 'quality' ) )
	);
	
	$args = wp_parse_args( $args, apply_filters( 'ticket_form_defaults', $defaults ) );
	
	if( $args[ 'location' ] == 'ticket' )
	{
		unset( $args[ 'basic_fields' ][ 'fields' ][ 'title' ] );
		unset( $args[ 'advanced_fields' ][ 'fields' ][ 'ticket-cc' ] );
	}
?>

	<?php do_action( 'ticket_form_before' ); ?>
	
	<form action="<?php echo ( $args[ 'location' ] == 'page' ? '#respond' : site_url( 'wp-comments-post.php' ) ); ?>" method="post" name="add-ticket" enctype="multipart/form-data" id="<?php echo esc_attr( $args[ 'id_form' ] ); ?>">
	
		<?php do_action( 'ticket_form_top' ); ?>
		
		<?php do_action( 'ticket_form_before_fields' ); ?>
	
		<fieldset id="<?php echo esc_attr( $args[ 'basic_fields' ][ 'id' ] ); ?>">
		
			<legend><?php echo esc_attr( $args[ 'basic_fields' ][ 'label' ] ); ?></legend>
		
			<?php 
				do_action( 'ticket_form_before_basic_fields' );
				foreach( $args[ 'basic_fields' ][ 'fields' ] as $name => $field )
				{
					echo apply_filters( "ticket_form_field_{$name}", $field ) . "\n";
				}
				do_action( 'ticket_form_after_basic_fields' );
			?>
			
		</fieldset>
		
		<fieldset id="<?php echo esc_attr( $args[ 'advanced_fields' ][ 'id' ] ); ?>">
			
			<legend><?php echo esc_attr( $args[ 'advanced_fields' ][ 'label' ] ); ?></legend>
		
			<?php 
				do_action( 'ticket_form_before_advanced_fields' );
				foreach( $args[ 'advanced_fields' ][ 'fields' ] as $name => $field )
				{
					echo apply_filters( "ticket_form_field_{$name}", $field ) . "\n";
				}
				do_action( 'ticket_form_after_advanced_fields' );
			?>
		
		</fieldset>
		
		<fieldset id="<?php echo esc_attr( $args[ 'meta_fields' ][ 'id' ] ); ?>">
			
			<legend><?php echo esc_attr( $args[ 'meta_fields' ][ 'label' ] ); ?></legend>
		
			<?php 
				do_action( 'ticket_form_before_meta_fields' );
				foreach( $args[ 'meta_fields' ][ 'fields' ] as $name => $field )
				{
					echo apply_filters( "ticket_form_field_{$name}", $field ) . "\n";
				}
				do_action( 'ticket_form_after_meta_fields' );
			?>
		
		</fieldset>
		
		<?php do_action( 'ticket_form_after_fields' ); ?>
		
		<p id="ticket-create">
		
			<?php if( $args[ 'location' ] == 'page' ) : ?>
				<input type="hidden" name="create_ticket" value="proccess" />
			<?php else : ?>
				<?php do_action( 'comment_form', $post->ID ); ?>
				<?php comment_id_fields(); ?>
			<?php endif; ?>
			
			<input type="submit" name="submit" id="<?php echo esc_attr( $args[ 'id_submit' ] ); ?>" value="<?php echo esc_attr( $args[ 'label_submit' ] ); ?>" />
		</p>
		
		<?php do_action( 'ticket_form_bottom' ); ?>
		
	</form>
	
	<?php do_action( 'ticket_form_after' ); ?>

<?php
}