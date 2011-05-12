
<?php echo $this->args['before_widget']; ?>
	
	<?php echo $this->args['before_title'] . $this->title . $this->args['after_title']; ?>
	
	<?php if (fvcn_has_posts(array('num'=>$this->num))) : ?>
		
		<ul class="fvcn-posts-list">
			<?php while (fvcn_posts()) : fvcn_the_post(); ?>
				
				<li>
					<h4><?php fvcn_post_link( fvcn_get_post_title() ); ?></h4>
					<?php fvcn_post_excerpt(); ?>
				</li>
				
			<?php endwhile; ?>
		</ul>
		
	<?php else : ?>
		
		<p><?php _e('There was no community news found.', 'fvcn'); ?></p>
		
	<?php endif; ?>
	
<?php echo $this->args['after_widget']; ?>
