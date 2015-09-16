<?php
/**
 * The template for displaying the footer.
 *
 * @package Quality_Control
 * @since Quality Control 0.1
 */
?>
		</div><!-- End #content --> 
		
		<div id="footer">
			
			<ul>
				<li><?php _e( 'Powered by', 'quality' ); ?> <a href="http://getqualitycontrol.com">Quality Control</a> <?php _e( 'and', 'quality' ); ?> <a href="http://wordpress.org">WordPress</a></li>
				
				<li class="alignright"><?php echo get_num_queries(); ?> queries. <?php timer_stop(1); ?> seconds.</li>
			</ul>
			
		</div>
	
	</div><!-- End #container --> 

	<?php wp_footer();?>
</body>
</html>
