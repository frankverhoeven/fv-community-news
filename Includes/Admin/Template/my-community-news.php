<div class="wrap">
	<h2><?php _e('My Submissions', 'fvcn'); ?></h2>
	<p><?php _e('Below is a list of all the approved Community News you have submitted.', 'fvcn'); ?></p>
	
	<?php if (fvcn_has_posts($this->options)) : ?>
	
	<div class="tablenav">
		<div class="tablenav-pages">
			<?php echo $this->pagination; ?>
		</div>
		<br class="clear" />
	</div>
	
	<table class="widefat comments fixed" cellspacing="0">
		<thead>
			<tr>
				<th scope="col" id="author" class="manage-column column-author" style="width: 35%;"><?php _e('Author' , 'fvcn'); ?></th>
				<th scope="col" id="comment" class="manage-column column-comment"><?php _e('Post' , 'fvcn'); ?></th>
			</tr>
		</thead>
		
		<tfoot>
			<tr>
				<th scope="col"  class="manage-column column-author"><?php _e('Author' , 'fvcn'); ?></th>
				<th scope="col"  class="manage-column column-comment"><?php _e('Post' , 'fvcn'); ?></th>
			</tr>
		</tfoot>
		
		<?php while (fvcn_posts()) : fvcn_the_post(); ?>
		<tbody id="the-comment-list">
			<tr class="approved">
				<td class="author column-author">
					<strong><?php echo get_avatar(fvcn_get_post_author_email(), 32); ?> <?php fvcn_post_author(); ?></strong><br />
					<a href="mailto:<?php fvcn_post_author_email(); ?>"><?php fvcn_post_author_email(); ?></a><br />
					<?php fvcn_post_link( fvcn_get_post_url() ); ?>
				</td>
				<td class="comment column-comment">
					<div id="submitted-on">
						<strong><?php fvcn_post_title(); ?></strong>
						- <i><?php fvcn_post_date(__('Y/m/d \a\t g:i A', 'fvcn')); ?></i>
					</div>
					
					<?php fvcn_post_content(); ?>
				</td>
			</tr>
		</tbody>
		<?php endwhile; ?>
	</table>
	
	<div class="tablenav">
		<div class="tablenav-pages">
			<?php echo $this->pagination; ?>
		</div>
		<br class="clear" />
	</div>
	
	<?php else : ?>
	
	<br class="clear" />
	<p><?php _e('No posts found.', 'fvcn'); ?></p>
	
	<?php endif; ?>
	
	<h2><?php _e('Add News', 'fvcn'); ?></h2>
	<div id="fvcn-form"><?php fvcn_form(); ?></div>
	
	<p id="fvcn-form-message">
		<?php fvcn_form_message(); ?>
	</p>
</div>
