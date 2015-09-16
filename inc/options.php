<?php
/** Tell WordPress to run quality_settings_page() when the 'admin_menu' hook is run. */
add_action('admin_menu', 'quality_settings_page');

/**
 * Create the settings page.
 *
 * @since Quality Control 0.1
 * @uses add_options_page
 */
function quality_settings_page() 
{	
	add_options_page( __( 'Quality Control Options' ), 'Quality Control', 'manage_options', 'quality_control', 'quality_options_page' );
}

if ( !function_exists( 'quality_options_page' ) ) :
/**
 * Callback for quality_settings_page. Create the page and the form.
 *
 * @since Quality Control 0.1
 * @uses settings_fields
 * @uses do_settings_section
 */
function quality_options_page()
{
	global $quality_options;
?>
	<div class="wrap">
		<div id="icon-options-general" class="icon32"><br /></div>
		<h2><?php _e( 'Quality Control Settings', 'quality' ); ?></h2>
		
		<form method="post" action="options.php">
			<?php settings_fields( 'quality_options' ); ?>
			<?php do_settings_sections( 'quality_control' ); ?>

			<p class="submit">
				<input type="submit" name="Submit" class="button-primary" value="Save Changes">
			</p>
		</form>
	</div>
<?php
}
endif;

/** Tell WordPress to run quality_admin_init() when the 'admin_init' hook is run. */
add_action( 'admin_init', 'quality_admin_init' );

/**
 * Register Settings, Sections, and Fields.
 *
 * @since Quality Control 0.1
 * @uses register_setting
 * @uses add_settings_section
 * @uses add_settings_field
 */
function quality_admin_init()
{
	register_setting( 'quality_options', 'quality_options', 'quality_validate_options' );
	
	// Section
	add_settings_section( 'qc_main', __( 'General Settings', 'quality' ), 'quality_settings_desc', 'quality_control' );
	
	add_settings_field( 'qc_ticket_page', __( 'Create Ticket Page', 'quality' ), 'quality_option_ticket_page', 'quality_control', 'qc_main' );
	add_settings_field( 'qc_status_default', __( 'Default Status', 'quality' ), 'quality_option_default_status', 'quality_control', 'qc_main' );
	add_settings_field( 'qc_ticket_resolved', __( 'Completed/Resolved Ticket', 'quality' ), 'quality_option_resolved', 'quality_control', 'qc_main' );
	add_settings_field( 'qc_require_assign', __( 'Assignment Permissions', 'quality' ), 'quality_option_assigned', 'quality_control', 'qc_main' );
	add_settings_field( 'qc_status_colors', __( 'Status Colors', 'quality' ), 'quality_status_colors', 'quality_control', 'qc_main' );
	
	do_action( 'quality_create_settings' );
}

if ( !function_exists( 'quality_validate_options' ) ) :
/**
 * Validate all incoming data. Still not sure I'm doing this right. 
 * Seems to work though.
 *
 * @since Quality Control 0.1.2
 * @uses get_option
 */
function quality_validate_options( $input )
{
	$output = get_option( 'quality_options' );
	
	$output[ 'ticket_page' ] = absint( $input[ 'ticket_page' ] );
	$output[ 'default_status' ] = absint( $input[ 'default_status' ] );
	$output[ 'ticket_resolved_state' ] = absint( $input[ 'ticket_resolved_state' ] );
	$output[ 'assigned_perms' ] = absint( $input[ 'assigned_perms' ] );
	
	$quality_status_colors = $input[ 'status_colors' ];
	$quality_status_colors_text = $input[ 'status_colors_text' ];
	
	if( !empty( $quality_status_colors ) )
	{	
		foreach( $quality_status_colors as $key => $value )
		{
			$color = esc_attr( $value );
			$color = ltrim( $color, '#' );
			if( preg_match( '/^[a-f0-9]{3,6}$/i', $color ) )
				$output[ 'status_colors' ][ $key ] = $color;
		}
	}
	
	if( !empty( $quality_status_colors_text ) )
	{
		foreach( $quality_status_colors_text as $key => $value )
		{
			$color = esc_attr( $value );
			$color = ltrim( $color, '#' );
			if( preg_match( '/^[a-f0-9]{3,6}$/i', $color ) )
				$output[ 'status_colors_text' ][ $key ] = $color;
		}
	}
	
	return $output;
}
endif;

if ( !function_exists( 'quality_settings_desc' ) ) :
/**
 * Callback for add_settings_section. Creates the description based on the id.
 *
 * @since Quality Control 0.1
 */
function quality_settings_desc( $args )
{
	switch( $args[ 'id' ] )
	{
		case 'qc_main' :
			_e( 'Basic settings for ticket management.', 'quality' );
			break;
	}
}
endif;

if ( !function_exists( 'quality_option_ticket_page' ) ) :
/**
 * Callback for qc_ticket_page settings field.
 * Allows the user to select which page acts as the ticket submit page.
 *
 * @since Quality Control 0.1
 */
function quality_option_ticket_page()
{
	global $quality_options;
	
	wp_dropdown_pages( array( 
		'name' => 'quality_options[ticket_page]',
		'show_option_none' => __( '&mdash; Select &mdash;' ), 
		'option_none_value' => 0, 
		'selected' => $quality_options[ 'ticket_page' ]
	) );
	
	echo'<br /><span class="description"><a href="post-new.php?post_type=page">Create a Page</a> with a Custom Template of "Create a Ticket"</span>';
}
endif;

if ( !function_exists( 'quality_option_resolved' ) ) :
/**
 * Callback for qc_ticket_resolved settings field.
 * Associate a ticket state with something that has been "closed", "resolved", etc.
 *
 * @since Quality Control 0.1
 */
function quality_option_resolved()
{
	global $quality_options;
	
	wp_dropdown_categories( array(
		'title_li=' => '',
		'show_option_none' => __( '&mdash; Select &mdash;' ), 
		'hide_empty' => 0,
		'taxonomy' => 'ticket_status',
		'name' => 'quality_options[ticket_resolved_state]',
		'selected' => $quality_options[ 'ticket_resolved_state' ]
	) );
	
	echo'<br /><span class="description">Tickets in this state are assumed to be "completed", "closed", "resolved", or no longer need attenion.</span>';
}
endif;

if ( !function_exists( 'quality_option_default_status' ) ) :
/**
 * Callback for qc_status_default settings field.
 * Select a default status.
 *
 * @since Quality Control 0.1
 */
function quality_option_default_status()
{
	global $quality_options;
	
	wp_dropdown_categories( array(
		'title_li=' => '',
		'show_option_none' => __( '&mdash; Select &mdash;' ), 
		'hide_empty' => 0,
		'taxonomy' => 'ticket_status',
		'name' => 'quality_options[default_status]',
		'selected' => $quality_options[ 'default_status' ]
	) );
	
	echo'<br /><span class="description">This state will be selected by default when creating a ticket.</span>';
}
endif;

if ( !function_exists( 'quality_option_assigned' ) ) :
/**
 * Callback for qc_require_assigned settings field.
 * Let the user choose if users must be assigned to
 * a ticket in order to view it.
 *
 * @since Quality Control 0.1.5
 */
function quality_option_assigned()
{
	global $quality_options;
?>
	<span class="description">Tickets not assigned to the logged in user are</span>
	<select name="quality_options[assigned_perms]">
		<option value="0" <?php selected( $quality_options[ 'assigned_perms' ], 0, true ); ?>>Protected</option>
		<option value="1" <?php selected( $quality_options[ 'assigned_perms' ], 1, true ); ?>>Read Only</option>
		<option value="2" <?php selected( $quality_options[ 'assigned_perms' ], 2, true ); ?>>Readable/Editable</option>
	</select>
	
<?php
}
endif;

if ( !function_exists( 'quality_status_colors' ) ) :
/**
 * Callback for qc_status_colors settings field.
 * Create custom labels for each post status.
 *
 * @since Quality Control 0.1
 */
function quality_status_colors()
{
	global $quality_options;
	
	$states = get_terms( 'ticket_status', 'hide_empty=0' );
		
	foreach( $states as $state )
	{
?>
		<p>
			<label for="quality_options[status_colors][<?php echo $state->slug; ?>]"><?php _e( 'Background Color', 'quality' ); ?>: 
				#<input type="text" name="quality_options[status_colors][<?php echo $state->slug; ?>]" value="<?php echo $quality_options[ 'status_colors' ][ $state->slug ]; ?>" class="colorwell" />
			</label>
			
			&nbsp;&nbsp;&nbsp;
			
			<label for="quality_options[status_colors_text][<?php echo $state->slug; ?>]"><?php _e( 'Text Color:', 'quality' ); ?>
				  #<input type="text" name="quality_options[status_colors_text][<?php echo $state->slug; ?>]" value="<?php echo $quality_options[ 'status_colors_text' ][ $state->slug ]; ?>"  class="colorwell" />
				 
				 <span class="status-preview" style="text-decoration:none; padding:5px 10px; text-transform:uppercase; font-weight:bold; border-radius:3px; -webkit-border-radius:3px; -moz-border-radius:3px; background:#<?php echo $quality_options[ 'status_colors' ][ $state->slug ]; ?>; color:#<?php echo $quality_options[ 'status_colors_text' ][ $state->slug ]; ?>; min-width:100px;"><?php echo $state->name; ?></span>
			</label>
		</p>
<?php	
	}
}
endif;
?>