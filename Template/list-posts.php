
<?php if (fvcn_has_posts()) : ?>
	
	<ul class="fvcn-posts-list">
		<?php while (fvcn_posts()) : fvcn_the_post(); ?>
			
			<li>
				<h4><?php fvcn_post_link( fvcn_get_post_title() ); ?></h4>
				<?php fvcn_post_content(); ?>
				<small><?php fvcn_post_date(); ?></small>
			</li>
			
		<?php endwhile; ?>
	</ul>
	
<?php else : ?>
	
	<p><?php _e('There was no community news found.', 'fvcn'); ?></p>
	
<?php endif; ?>
