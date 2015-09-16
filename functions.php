<?php
/**
 * Quality Control functions and definitions
 *
 * Sets up the theme and provides some helper functions. Some helper functions
 * are used in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * The first function, quality_setup(), sets up the theme by registering support
 * for various features in WordPress, such as post thumbnails, navigation menus, and the like.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are instead attached
 * to a filter or action hook. The hook can be removed by using remove_action() or
 * remove_filter() and you can attach your own function to the hook.
 *
 * We can remove the parent theme's hook only after it is attached, which means we need to
 * wait until setting up the child theme:
 *
 * <code>
 * add_action( 'after_setup_theme', 'my_child_theme_setup' );
 * function my_child_theme_setup() {
 *     // We are providing our own filter for excerpt_length (or using the unfiltered value)
 *     remove_filter( 'excerpt_length', 'quality_excerpt_length' );
 *     ...
 * }
 * </code>
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 *
 * @package Quality_Control
 * @since Quality Control 0.1
 */
 
define( 'QC_INC_PATH',  get_template_directory() . '/inc' );
define( 'QC_INC_URL', get_bloginfo('template_directory' ) . '/inc' );
define( 'QC_JS_PATH',  get_template_directory() . '/js' );
define( 'QC_JS_URL', get_bloginfo( 'template_directory' ) . '/js' );

/** Tell WordPress to run quality_setup() when the 'after_setup_theme' hook is run. */ 
add_action( 'after_setup_theme', 'quality_setup' );

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * @since Quality Control 0.1
 */
function quality_setup() 
{
	global $quality_options;
	
	$quality_options = get_option( 'quality_options' );
	
	// Add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );
	
	// Add support for custom backgrounds... Why not?
	add_custom_background();  
	
	// Make the editor more "What you get"
	add_editor_style();
	
	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Navigation', 'quality' ),
	) );

	// Make theme available for translation
	load_theme_textdomain( 'quality', TEMPLATEPATH . '/languages' );

	$locale = get_locale();
	$locale_file = TEMPLATEPATH . "/languages/$locale.php";
	if( is_readable( $locale_file ) )
		require_once( $locale_file );

	if( !is_admin() ) 
	{
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'custom', QC_JS_URL . '/jquery.custom.js' );
	}
	
	// Require other files, to keep things organized.
	require_once( QC_INC_PATH . '/options.php' );
	require_once( QC_INC_PATH . '/tickets.php' );
	require_once( QC_INC_PATH . '/updates.php' );
	require_once( QC_INC_PATH . '/widgets/category-taxonomy.php' );
}

/** Tell WordPress to run quality_status_colors_css() when the 'wp_head' hook is run. */ 
add_action( 'wp_head', 'quality_status_colors_css' );

/**
 * Create the CSS to style the .ticket-status links.
 *
 * @since Quality Control 0.1
 * @uses get_terms
 */
function quality_status_colors_css()
{
	global $quality_options;
	$states = get_terms( 'ticket_status', 'hide_empty=0' );
?>
	<style type="text/css">
		<?php foreach( $states as $state ) : ?>
		.ticket-status.<?php echo $state->slug; ?> {
			background:#<?php echo $quality_options[ 'status_colors' ][ $state->slug ]; ?>; 
			color:#<?php echo $quality_options[ 'status_colors_text' ][ $state->slug ]; ?>;
		}
		<?php endforeach; ?>
	</style>
<?php
}

/** Tell WordPress to run quality_widgets_init() when the 'widgets_init' hook is run. */
add_action( 'widgets_init', 'quality_widgets_init' );
		
/**
 * Register widgetized areas, including two sidebars and four widget-ready columns in the footer.
 *
 * @since Quality Control 0.1
 * @uses register_sidebar
 */
function quality_widgets_init() 
{
	register_sidebar( array(
		'name' => __( 'Primary Widget Area', 'quality' ),
		'id' => 'primary-widget-area',
		'description' => __( 'The primary widget area', 'quality' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
}

/** Tell WordPress to run quality_unregister_widgets() when the 'widgets_init' hook is run. */
add_action( 'widgets_init', 'quality_unregister_widgets' );

/**
 * Remove the default Category Widget. 
 *
 * The default widget does not support defining a custom taxonomy. The
 * Widget that is created by the theme does, as well as counts, and RSS links.
 *
 * @since Quality Control 0.1
 * @uses unregister_widget
 */
function quality_unregister_widgets() {
	unregister_widget( 'WP_Widget_Categories' );
}

if ( !function_exists( 'quality_page_title' ) ) :
/**
 * When this function is called, add_filter is called
 * to filter wp_title. This needs to be run after the
 * initial wp_title is called for the <title>
 *
 * @since Quality Control 0.1
 * @uses add_filter
 */
function quality_page_title()
{
	add_filter( 'wp_title', 'quality_filter_page_title', 10, 2 );
}
endif;

/**
 * This filter is run on wp_title. It wraps the separator in a
 * <span> tag so it can be styled. Also allows for more precise
 * control over certain page titles.
 *
 * @since Quality Control 0.1
 * @uses unregister_widget
 */
function quality_filter_page_title( $title, $separator ) 
{
	global $paged, $page, $post, $wp_query;
	
	$title = str_replace( $separator, '<span>' . $separator . '</span>', $title );
	
	if( isset( $_REQUEST[ 'wp-subscription-manager' ] ) )
		$title = '<span>' . $separator . '</span> ' . __( 'Ticket Manager', 'quality' );
	elseif( is_search() )
		$title = '<span>' . $separator . '</span> ' . sprintf( __( '"%s"', 'quality' ), get_search_query() );
	elseif( is_single() )
		$title = '<span>' . $separator . '</span> ' . sprintf( __( 'Ticket #%d', 'quality' ), $post->ID );
	elseif( is_home() )
		$title = '<span>' . $separator . '</span> ' . __( 'Dashboard', 'quality' );
	
	return $title;
}

if ( !function_exists( 'quality_form_data' ) ) :
/**
 * Used to echo form data if a form errors.
 *
 * @since Quality Control 0.1
 */
function quality_form_data( $field )
{
	if( isset( $_POST[ $field ] ) )
		return esc_attr( $_POST[ $field ] );
}
endif;

if ( !function_exists( 'quality_show_pagination' ) ) :
/**
 * Does the query produce more than 1 page?
 *
 * @since Quality Control 0.1
 */
function quality_show_pagination()
{
	global $wp_query;
		
	if( $wp_query->max_num_pages > 1 )
		return true;
		
	return false;
}
endif;

/** Tell WordPress to run quality_query_vars() when the 'query_vars' filter is called. */
add_filter( 'query_vars', 'quality_query_vars' );

/**
 * Register some query variables so they can be
 * properly called.
 *
 * @since Quality Control 0.1.5
 */
function quality_query_vars( $qvars )
{
	$qvars[] = 'assigned';
	$qvars[] = 'ticket_trashed';
	
	return $qvars;
}

/** Tell WordPress to run quality_redirect_ticket() when the 'template_redirect' hook is run. */
add_action( 'template_redirect', 'quality_trashed_ticket' );

/**
 * When a ticket is deleted, redirect them back to the homepage.
 * Adds a variable to the URL so you can add a message if you want.
 *
 * A ticket can be undeleted by going into the WordPress Control Panel,
 * viewing your posts, and restoring it from the trash.
 *
 * @since Quality Control 0.1
 */
function quality_trashed_ticket()
{
	if( !isset( $_GET[ "ids" ] ) )
		return false;
		
	$ticket_id = esc_attr( $_GET[ "ids" ] );
	
	if( isset( $_REQUEST[ "trashed" ] ) )
	{
		do_action( 'quality_delete_ticket', $ticket_id );
		
		wp_redirect( home_url() . '?ticket_trashed=' . $ticket_id, 302 );
	}
}

/** Tell WordPress to run quality_right_now() when the 'right_now_content_table_end' action is run. */
add_action( 'right_now_content_table_end', 'quality_right_now' );

/**
 * Add the extra taxonomies to the "Right Now" dashboard widget.
 *
 * @since Quality Control 0.1.2
 */
function quality_right_now()
{
	// States
	$num_states = wp_count_terms( 'ticket_status' );
	$num = number_format_i18n( $num_states );
	
	echo "<tr>";
	$text = _n( 'Status', 'States', $num_states );
	if ( current_user_can( 'manage_categories' ) ) {
		$num = "<a href='edit-tags.php?taxonomy=ticket_status&post_type=ticket'>$num</a>";
		$text = "<a href='edit-tags.php?taxonomy=ticket_status&post_type=ticket'>$text</a>";
	}
	echo '<td class="first b b-tags">' . $num . '</td>';
	echo '<td class="t tags">' . $text . '</td>';
	echo "</tr>";
	
	// Milestones
	$num_milestones = wp_count_terms( 'ticket_milestone' );
	$num = number_format_i18n( $num_milestones );
	
	echo "<tr>";
	$text = _n( 'Milestone', 'Milestones', $num_milestones );
	if ( current_user_can( 'manage_categories' ) ) {
		$num = "<a href='edit-tags.php?taxonomy=ticket_milestone&post_type=ticket'>$num</a>";
		$text = "<a href='edit-tags.php?taxonomy=ticket_milestone&post_type=ticket'>$text</a>";
	}
	echo '<td class="first b b-tags">' . $num . '</td>';
	echo '<td class="t tags">' . $text . '</td>';
	echo "</tr>";
}

/** Register the ticket shortcode. */
add_shortcode( 'ticket', 'quality_link_ticket_shortcode' );

/** Tell WordPress to run shortcodes on tickets. */
add_filter( 'get_comment_text', 'do_shortcode');

/**
 * Create a shortcode so tickets can be easily
 * called when creating/updating tickets.
 *
 * [ticket id=""] where id is the ID of the post (ticket).
 *
 * @since Quality Control 0.1.4
 */
function quality_link_ticket_shortcode( $atts, $content = null )
{
	global $quality_options;
	
	extract(shortcode_atts(array(
		'id' => 0,
		'before' => '',
		'after' => '',
	), $atts));
	
	$ticket = get_post( absint( $id ) );
		
	if( $ticket->post_status != 'publish' )
		return apply_filters( 'quality_invalid_ticket_shortcode', '#' . $id );
		
	if( empty( $content ) )
		$content = '#' . $ticket->ID;
		
	if( quality_ticket_status( $ticket->ID ) == $quality_options[ 'ticket_resolved_state' ] )
		$class = quality_ticket_status( $ticket->ID, 'slug' );
	
	$link = $before;
	$link .= sprintf( 
		'<a href="%1$s" title="%4$s" class="link-to-ticket %3$s">%2$s</a>', 
		get_permalink( $ticket->ID ), 
		$content, 
		$class, 
		$ticket->post_title . ' [' . quality_ticket_status( $ticket->ID, 'name' ) . ']' 
	);
	$link .= $after;
	
	return apply_filters( 'quality_link_ticket_shortcode', $link );
}