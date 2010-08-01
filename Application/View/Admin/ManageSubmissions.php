<?php
$action = esc_url(
	get_option('siteurl') . '/wp-admin/admin.php?page=fvcn-admin' . (isset($_GET['approved'])?'&approved=' . $_GET['approved']:'') . (isset($_GET['apage'])?'&apage=' . $_GET['apage']:'')
);						
?>
<div class="wrap">
	<h2><?php _e('Manage Community News', 'fvcn'); ?></h2>
	
	<?php if ($this->message) : ?><div id="moderated" class="updated"><p><?php echo $this->message; ?></p></div><?php endif; ?>
	
	<ul class="subsubsub">
		<li><a href="<?php echo esc_url(
					add_query_arg('approved', 'all', remove_query_arg(
						array('apage', 'fvcn_Admin_Request', 'fvcn-action', 'fvcn-submission-id', '_wpnonce')
					))
				); ?>"<?php echo ('all'==$this->approvedStatus?' class="current"':''); ?>><?php _e('Show All', 'fvcn'); ?></a> |</li>
		<li><a href="<?php echo esc_url(
					add_query_arg('approved', '1', remove_query_arg(
						array('apage', 'fvcn_Admin_Request', 'fvcn-action', 'fvcn-submission-id', '_wpnonce')
					))
				); ?>"<?php echo ('1'==$this->approvedStatus?' class="current"':''); ?>><?php _e('Approved', 'fvcn'); ?></a> |</li>
		<li><a href="<?php echo esc_url(
					add_query_arg('approved', '0', remove_query_arg(
						array('apage', 'fvcn_Admin_Request', 'fvcn-action', 'fvcn-submission-id', '_wpnonce')
					))
				); ?>"<?php echo ('0'==$this->approvedStatus?' class="current"':''); ?>><?php _e('Pending', 'fvcn'); ?> <span class="count">(<?php echo $this->numModeration; ?>)</span></a> |</li>
		<li><a href="<?php echo esc_url(
					add_query_arg('approved', 'spam', remove_query_arg(
						array('apage', 'fvcn_Admin_Request', 'fvcn-action', 'fvcn-submission-id', '_wpnonce')
					))
				); ?>"<?php echo ('spam'==$this->approvedStatus?' class="current"':''); ?>><?php _e('Spam', 'fvcn'); ?> <span class="count">(<?php echo $this->numSpam; ?>)</span></a></li>
	</ul>
	
	<?php if ($this->submissions) : ?>
	
	<form id="fvcn-submissions-form" action="<?php echo $action; ?>" method="post">
		<?php wp_nonce_field('fvcn_ManageSubmissions'); ?>
		<input type="hidden" name="fvcn_Admin_Request" id="fvcn_Admin_Request" value="ManageSubmissions" />
		
		<div class="tablenav">
			<div class="tablenav-pages">
				<?php echo $this->pageLinks; ?>
			</div>
			
			<div class="alignleft actions">
				<select name="fvcn-action">
					<option value="-1" selected="selected"><?php _e('Bulk Actions'); ?></option>
					<option value="approve"><?php _e('Approve' , 'fvcn'); ?></option>
					<option value="unapprove"><?php _e('Unapprove' , 'fvcn'); ?></option>
					<option value="spam"><?php _e('Spam' , 'fvcn'); ?></option>
					<option value="delete"><?php _e('Delete' , 'fvcn'); ?></option>
				</select>
				<input type="submit" name="do-bulk-action" id="do-bulk-action" value="<?php esc_attr_e('Apply'); ?>" class="button-secondary apply" />
			</div>
			<br class="clear" />
		</div>
		
		<table class="widefat comments fixed" cellspacing="0">
			<thead>
				<tr>
					<th scope="col" id="cb" class="manage-column column-cb check-column"><input type="checkbox" /></th>
					<th scope="col" id="author" class="manage-column column-author" style="width: 35%;"><?php _e('Author' , 'fvcn'); ?></th>
					<th scope="col" id="comment" class="manage-column column-comment"><?php _e('Submission' , 'fvcn'); ?></th>
				</tr>
			</thead>
			
			<tfoot>
				<tr>
					<th scope="col"  class="manage-column column-cb check-column"><input type="checkbox" /></th>
					<th scope="col"  class="manage-column column-author"><?php _e('Author' , 'fvcn'); ?></th>
					<th scope="col"  class="manage-column column-comment"><?php _e('Submission' , 'fvcn'); ?></th>
				</tr>
			</tfoot>
			
			<?php foreach ($this->submissions as $submission) : ?>
			<tbody id="the-comment-list">
				<tr id="submission-<?php echo $submission->id; ?>" class="<?php echo (1==$submission->approved?'approved':'unapproved'); ?>">
					<th scope="row" class="check-column"><input type="checkbox" name="fvcn-submission-id[]" value="<?php echo $submission->id; ?>" /></th>
					<td class="author column-author">
						<strong><?php echo get_avatar($submission->email, 32); ?> <?php echo apply_filters('fvcn_User', $submission->name); ?></strong>
						<?php if ($this->tracking) : ?><em>- <?php printf(__('%d views', 'fvcn'), $submission->views); ?></em><?php endif; ?><br />
						<a href="mailto:<?php echo apply_filters('fvcn_Email', $submission->email); ?>"><?php echo apply_filters('fvcn_Email', $submission->email); ?></a><br />
						<a href="<?php echo apply_filters('fvcn_Location', $submission->location); ?>"><?php echo apply_filters('fvcn_Location', $submission->location); ?></a><br />
					</td>
					<td class="comment column-comment">
						<div id="submitted-on"><a href="<?php echo apply_filters('fvcn_Location', $submission->location); ?>">
							<strong><?php echo apply_filters('fvcn_Title', $submission->title); ?></strong>
							- <i><?php echo mysql2date(__('Y/m/d \a\t g:i A', 'fvcn'), $submission->date); ?></i>
						</a></div>
						
						<?php echo apply_filters('fvcn_Description', $submission->description); ?>
						
						<div class="row-actions">
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
																			array('apage', 'fvcn_Admin_Request', '_wpnonce')
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
						</div>
					</td>
				</tr>
			</tbody>
			<?php endforeach; ?>
		</table>
		
		<div class="tablenav">
			<div class="tablenav-pages">
				<?php echo $this->pageLinks; ?>
			</div>
			
			<div class="alignleft actions">
				<select name="fvcn-action-2">
					<option value="-1" selected="selected"><?php _e('Bulk Actions'); ?></option>
					<option value="approve"><?php _e('Approve' , 'fvcn'); ?></option>
					<option value="unapprove"><?php _e('Unapprove' , 'fvcn'); ?></option>
					<option value="spam"><?php _e('Spam' , 'fvcn'); ?></option>
					<option value="delete"><?php _e('Delete' , 'fvcn'); ?></option>
				</select>
				<input type="submit" name="do-bulk-action-2" id="do-bulk-action-2" value="<?php esc_attr_e('Apply'); ?>" class="button-secondary apply" />
			</div>
			<br class="clear" />
		</div>
		<br class="clear" />
	</form>
	
	<?php else : ?>
	
	<br class="clear" />
	<p><?php _e('No submissions found.', 'fvcn'); ?></p>
	
	<?php endif; ?>
</div>
