<?php
class quality_cat_tax extends WP_Widget 
{
	function quality_cat_tax() 
	{
		parent::WP_Widget( 
			'cat-tax', 
			'Taxonomy Widget', 
			array( 
				'description' => 'Create a list of any taxonomy.' 
			) 
		);
	}

	function form( $instance ) 
	{
		$title = esc_attr( $instance[ 'title' ] );
		$taxonomy = esc_attr( $instance[ 'taxonomy' ] );
		$show_rss = esc_attr( $instance[ 'show_rss' ] );
		$show_count = esc_attr( $instance[ 'show_count' ] );
?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">
				<?php _e( 'Title:', 'quality' ); ?>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
			</label>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'taxonomy' ); ?>">
				<?php _e( 'Taxonomy:', 'quality' ); ?>
				<select id="<?php echo $this->get_field_id( 'taxonomy' ); ?>" name="<?php echo $this->get_field_name( 'taxonomy' ); ?>">
				<?php 
					$taxes = get_taxonomies( array( 'public' => true ), 'object' );				
					foreach( $taxes as $tax ) :
						if( $tax->name == $taxonomy )
							$selected = ' selected="selected"';
						else
							$selected = '';
				?>
					<option value="<?php echo $tax->name; ?>"<?php echo $selected; ?>><?php echo $tax->labels->singular_name; ?></option>
				<?php endforeach; ?>
				</select>
			</label>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'show_rss' ); ?>">
				<?php 
					if( $show_rss == "on" )
						$checked = ' checked="checked"';
					else
						$checked = '';
				?>
				<input id="<?php echo $this->get_field_id( 'show_rss' ); ?>" name="<?php echo $this->get_field_name( 'show_rss' ); ?>" type="checkbox"<?php echo $checked; ?> />
				<?php _e( 'Show RSS Link:', 'quality' ); ?>
			</label>
				<br />
			<label for="<?php echo $this->get_field_id( 'show_count' ); ?>">
				<?php 
					if( $show_count == "on" )
						$checked = ' checked="checked"';
					else
						$checked = '';
				?>
				<input id="<?php echo $this->get_field_id( 'show_count' ); ?>" name="<?php echo $this->get_field_name( 'show_count' ); ?>" type="checkbox"<?php echo $checked; ?> />
				<?php _e( 'Show Taxonomy Count:', 'quality' ); ?>
			</label>
		</p>
<?php
	}

	function update( $new_instance, $old_instance )
	{
		$instance = $old_instance;
		
		$instance[ 'title' ] = strip_tags( $new_instance[ 'title' ] );
		$instance[ 'taxonomy' ] = strip_tags( $new_instance[ 'taxonomy' ] );
		$instance[ 'show_rss' ] = strip_tags( $new_instance[ 'show_rss' ] );
		$instance[ 'show_count' ] = strip_tags( $new_instance[ 'show_count' ] );
       
		return $instance;
	}

	function widget( $args, $instance ) 
	{
		extract( $args );
?>
		<?php echo $before_widget; ?>
			<?php echo $before_title; ?><?php echo $instance[ 'title' ]; ?><?php echo $after_title; ?>
			<ul>
				<?php
					$taxes = get_categories( array( 
						'hide_empty' => 0,
						'taxonomy' => $instance[ 'taxonomy' ],
						'orderby' => 'name'
					) );
					if( $taxes ) : foreach( $taxes as $tax ) : 
				?>
						<li>
							<a href="<?php echo get_term_link( $tax, $instance[ 'taxonomy' ] ); ?>?feed=rss2" class="rss">
								<img src="<?php bloginfo( 'template_directory' ); ?>/images/rss.gif" alt="RSS" />
							</a>
							<a href="<?php echo get_term_link( $tax, $instance[ 'taxonomy' ] ); ?>" title="<?php printf( __( 'View all tickets marked %s', 'quality' ), $status->name ); ?>">
								<?php echo $tax->name; ?>
								<?php if( isset( $instance[ 'show_count' ] ) ) : ?><small>(<?php echo $tax->count; ?>)</small><?php endif; ?>
							</a>
						</li>
				<?php endforeach; else : ?>
				
					<li><?php _e( 'No Results', 'quality' ); ?></li>
				
				<?php endif; ?>
			</ul> 
		<?php echo $after_widget; ?>
<?php
	}
}

register_widget( 'quality_cat_tax' );
?>