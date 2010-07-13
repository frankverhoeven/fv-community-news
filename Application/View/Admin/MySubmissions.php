<div class="wrap">
	<h2><?php _e('My Submissions', 'fvcn'); ?></h2>
	
	<?php if ($this->submissions) : ?>
	
	<div class="tablenav">
		<div class="tablenav-pages">
			<?php echo $this->pageLinks; ?>
		</div>
		<br class="clear" />
	</div>
	
	<table class="widefat comments fixed" cellspacing="0">
		<thead>
			<tr>
				<th scope="col" id="author" class="manage-column column-author" style="width: 35%;"><?php _e('Author' , 'fvcn'); ?></th>
				<th scope="col" id="comment" class="manage-column column-comment"><?php _e('Submission' , 'fvcn'); ?></th>
			</tr>
		</thead>
		
		<tfoot>
			<tr>
				<th scope="col"  class="manage-column column-author"><?php _e('Author' , 'fvcn'); ?></th>
				<th scope="col"  class="manage-column column-comment"><?php _e('Submission' , 'fvcn'); ?></th>
			</tr>
		</tfoot>
		
		<?php foreach ($this->submissions as $submission) : ?>
		<tbody id="the-comment-list">
			<tr class="<?php echo (1==$submission->approved?'approved':'unapproved'); ?>">
				<td class="author column-author">
					<strong><?php echo get_avatar($submission->email, 32); ?> <?php echo apply_filters('fvcn_User', $submission->user); ?></strong><br />
					<a href="mailto:<?php echo apply_filters('fvcn_Email', $submission->email); ?>"><?php echo apply_filters('fvcn_Email', $submission->email); ?></a><br />
					<a href="<?php echo apply_filters('fvcn_Location', $submission->location); ?>"><?php echo apply_filters('fvcn_Location', $submission->location); ?></a><br />
				</td>
				<td class="comment column-comment">
					<div id="submitted-on"><a href="<?php echo apply_filters('fvcn_Location', $submission->location); ?>">
						<strong><?php echo apply_filters('fvcn_Title', $submission->title); ?></strong>
						- <i><?php echo mysql2date(__('Y/m/d \a\t g:i A'), $submission->date); ?></i>
					</a></div>
					
					<?php echo apply_filters('fvcn_Description', $submission->description); ?>
				</td>
			</tr>
		</tbody>
		<?php endforeach; ?>
	</table>
	
	<div class="tablenav">
		<div class="tablenav-pages">
			<?php echo $this->pageLinks; ?>
		</div>
		<br class="clear" />
	</div>
	<br class="clear" />
	
	<?php else : ?>
	
	<br class="clear" />
	<p><?php _e('No approved submissions found.', 'fvcn'); ?></p>
	
	<?php endif; ?>
</div>
