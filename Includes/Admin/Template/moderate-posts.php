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
						array('apage', 'fvcn', 'fvcn-action', 'fvcn-post-id', 'fvcn-nonce')
					))
				); ?>"<?php echo ('all'==$this->approved?' class="current"':''); ?>><?php _e('Show All', 'fvcn'); ?></a> |</li>
		<li><a href="<?php echo esc_url(
					add_query_arg('approved', '1', remove_query_arg(
						array('apage', 'fvcn', 'fvcn-action', 'fvcn-post-id', 'fvcn-nonce')
					))
				); ?>"<?php echo ('1'==$this->approved?' class="current"':''); ?>><?php _e('Approved', 'fvcn'); ?></a> |</li>
		<li><a href="<?php echo esc_url(
					add_query_arg('approved', '0', remove_query_arg(
						array('apage', 'fvcn', 'fvcn-action', 'fvcn-post-id', 'fvcn-nonce')
					))
				); ?>"<?php echo ('0'==$this->approved?' class="current"':''); ?>><?php _e('Pending', 'fvcn'); ?> <span class="count">(<span id="fvcn-pending-count"><?php echo $this->numModeration; ?></span>)</span></a> |</li>
		<li><a href="<?php echo esc_url(
					add_query_arg('approved', 'spam', remove_query_arg(
						array('apage', 'fvcn', 'fvcn-action', 'fvcn-post-id', 'fvcn-nonce')
					))
				); ?>"<?php echo ('spam'==$this->approved?' class="current"':''); ?>><?php _e('Spam', 'fvcn'); ?> <span class="count">(<span id="fvcn-spam-count"><?php echo $this->numSpam; ?></span>)</span></a></li>
	</ul>
	
	<?php if (fvcn_has_posts($this->options)) : ?>
		
		<form id="fvcn-submissions-form" action="<?php echo $action; ?>" method="post">
			<?php wp_nonce_field('fvcn-nonce', 'fvcn-nonce'); ?>
			<input type="hidden" name="fvcn" id="fvcn" value="fvcn-moderate-posts" />
			
			<div class="tablenav">
				<div class="tablenav-pages">
					<?php echo $this->pagination; ?>
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
						<th scope="col" id="comment" class="manage-column column-comment"><?php _e('Post' , 'fvcn'); ?></th>
					</tr>
				</thead>
				
				<tfoot>
					<tr>
						<th scope="col"  class="manage-column column-cb check-column"><input type="checkbox" /></th>
						<th scope="col"  class="manage-column column-author"><?php _e('Author' , 'fvcn'); ?></th>
						<th scope="col"  class="manage-column column-comment"><?php _e('Post' , 'fvcn'); ?></th>
					</tr>
				</tfoot>
				
				<tbody id="the-comment-list">
					
					<?php while (fvcn_posts()) : fvcn_the_post(); ?>
						
						<tr id="post-<?php fvcn_post_id(); ?>" class="<?php echo ('spam'==fvcn_get_post_approved()?'spam':('1'==fvcn_get_post_approved()?'approved':'unapproved')); ?> fvcn-post">
							<th scope="row" class="check-column"><input type="checkbox" name="fvcn-post-id[]" value="<?php fvcn_post_id(); ?>" /></th>
							<td class="author column-author">
								<strong><?php echo get_avatar(fvcn_get_post_author_email(), 32); ?> <?php fvcn_post_author(); ?></strong>
								<?php
								if (FvCommunityNews_Settings::getInstance()->Tracking) {
									echo '<em> - ' . fvcn_get_post_views() . ' ' . __('views', 'fvcn') . '</em>';
								}
								?>
								<br /><a href="mailto:<?php fvcn_post_author_email(); ?>"><?php fvcn_post_author_email(); ?></a><br />
								<?php fvcn_post_link( fvcn_get_post_url() ); ?>
							</td>
							<td class="comment column-comment">
								<div id="submitted-on">
									<strong><?php fvcn_post_title(); ?></strong>
									- <i><?php fvcn_post_date(__('Y/m/d \a\t g:i A', 'fvcn')); ?></i>
								</div>
								
								<?php fvcn_post_content(); ?>
								
								<div class="row-actions fvcn-actions">
									<?php if ('spam' != fvcn_get_post_approved()) : ?>
										<span class="approve"><a href="<?php fvcn_post_approve_link(); ?>"><?php _e('Approve' , 'fvcn'); ?></a></span>
										<span class="unapprove"><a href="<?php fvcn_post_unapprove_link(); ?>"><?php _e('Unapprove' , 'fvcn'); ?></a></span>
										<span class="edit"> | <a href="<?php fvcn_post_edit_link(); ?>"><?php _e('Edit' , 'fvcn'); ?></a></span>
										<span class="spam"> | <a href="<?php fvcn_post_spam_link(); ?>"><?php _e('Spam' , 'fvcn'); ?></a></span>
										<span class="trash"> | <a href="<?php fvcn_post_delete_link(); ?>"><?php _e('Delete' , 'fvcn'); ?></a></span>
									<?php else : ?>
										<span class="unspam"><a href="<?php fvcn_post_unspam_link(); ?>"><?php _e('Not Spam' , 'fvcn'); ?></a></span>
										<span class="edit"> | <a href="<?php fvcn_post_edit_link(); ?>"><?php _e('Edit' , 'fvcn'); ?></a></span>
										<span class="trash"> | <a href="<?php fvcn_post_delete_link(); ?>"><?php _e('Delete' , 'fvcn'); ?></a></span>
									<?php endif; ?>
								</div>
							</td>
						</tr>
						
					<?php endwhile; ?>
					
				</tbody>
			</table>
			
			<div class="tablenav">
				<div class="tablenav-pages">
					<?php echo $this->pagination; ?>
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
		<p><?php _e('No Community News was found.', 'fvcn'); ?></p>
		
	<?php endif; ?>
	
</div>
