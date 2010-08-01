
<?php if ($this->submissions) : ?>

<div id="the-comment-list" class="list:comment">
	
	<?php $class = 'even'; ?>
	<?php foreach ($this->submissions as $submission) : ?>
	
	<div id="submission-<?php echo $submission->id; ?>" class="comment <?php echo $class; ?> comment-item depth-1 <?php echo (1==$submission->approved?'approved':'unapproved'); ?>">
		<?php echo get_avatar( $submission->email, 50 ); ?>
		
		<div class="dashboard-comment-wrap">
			<h4 class="comment-meta">
				<?php printf(__('%1$s from %2$s %3$s', 'fvcn'),
					'<cite class="comment-author"><a href="' . apply_filters('fvcn_Location', $submission->location) . '" class="url">' . apply_filters('fvcn_Title', $submission->title) . '</a></cite>',
					'<cite class="comment-author"><a href="mailto:' . apply_filters('fvcn_Email', $submission->email) . '" class="url">' . apply_filters('fvcn_User', $submission->name) . '</a></cite>',
					'<span class="approve">' . __('[Pending]', 'fvcn') . '</span>'); ?>
				<?php if ($this->tracking) : ?><small><?php printf(__('(%d views)', 'fvcn'), $submission->views); ?></small><br /><?php endif; ?>
			</h4>
			
			<blockquote><p><?php echo apply_filters('fvcn_Description', $submission->description); ?></p></blockquote>
			<p class="row-actions">
				<span class="approve"><a href="<?php echo esc_url(wp_nonce_url(
														add_query_arg(array(
															'fvcn_Admin_Request'=>'ManageSubmissions',
															'fvcn-action'=>'approve',
															'fvcn-submission-id'=>(string)$submission->id
														)), 'fvcn_ManageSubmissions')); ?>"><?php _e('Approve' , 'fvcn'); ?></a></span>
				<span class="unapprove"><a href="<?php echo esc_url(wp_nonce_url(
														add_query_arg(array(
															'fvcn_Admin_Request'=>'ManageSubmissions',
															'fvcn-action'=>'unapprove',
															'fvcn-submission-id'=>(string)$submission->id
														)), 'fvcn_ManageSubmissions')); ?>"><?php _e('Unapprove' , 'fvcn'); ?></a></span>
				<span class="edit"> | <a href="<?php echo esc_url(
															add_query_arg(array(
																'fvcn-action'=>'edit',
																'fvcn-submission-id'=>(string)$submission->id
															),
															remove_query_arg(
																array('apage', 'fvcn_Admin_Request', '_wpnonce'),
																'admin.php?page=fvcn-admin'
															)
														)); ?>"><?php _e('Edit' , 'fvcn'); ?></a></span>
				<span class="spam"> | <a href="<?php echo esc_url(wp_nonce_url(
														add_query_arg(array(
															'fvcn_Admin_Request'=>'ManageSubmissions',
															'fvcn-action'=>'spam',
															'fvcn-submission-id'=>(string)$submission->id
														)), 'fvcn_ManageSubmissions')); ?>"><?php _e('Spam' , 'fvcn'); ?></a></span>
				<span class="trash"> | <a href="<?php echo esc_url(wp_nonce_url(
														add_query_arg(array(
															'fvcn_Admin_Request'=>'ManageSubmissions',
															'fvcn-action'=>'delete',
															'fvcn-submission-id'=>(string)$submission->id
														)), 'fvcn_ManageSubmissions')); ?>"><?php _e('Delete' , 'fvcn'); ?></a></span>
			</p>
		</div>
	</div>
	
	<?php $class = ($class=='even') ? 'odd alt' : 'even';; ?>
	<?php endforeach; ?>
	
	<p class="textright">
		<a href="admin.php?page=fvcn-admin" class="button"><?php _e('View all', 'fvcn'); ?></a>
	</p>
	
</div>

<?php else : ?>

<p><?php _e('No submissions yet.', 'fvcn'); ?></p>

<?php endif; ?>