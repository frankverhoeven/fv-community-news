<div class="wrap">
	<h2><?php _e('Edit Submission', 'fvcn'); ?></h2>
	
	<?php if ($this->submission) : ?>
	
	<?php
	$submission = $this->submission;
	$action = esc_url(
		get_option('siteurl') . '/wp-admin/admin.php?page=fvcn-admin&amp;fvcn-action=edit&amp;fvcn-submission-id=' . $submission->id
	);
	?>
	
	<?php if ($this->message) : ?><div id="moderated" class="updated"><p><?php echo $this->message; ?></p></div><?php endif; ?>
	
	<form name="fvcn-edit-submission" method="post" action="<?php echo $action; ?>">
		<?php wp_nonce_field('fvcn_EditSubmission'); ?>
		<input type="hidden" name="fvcn_Admin_Request" id="fvcn_Admin_Request" value="EditSubmission" />
		<input type="hidden" name="fvcn-submission-id" id="fvcn-submission-id" value="<?php echo $submission->id; ?>" />
		
		<div id="poststuff" class="metabox-holder has-right-sidebar">
			<div id="side-info-column" class="inner-sidebar">
				<div id="submitdiv" class="stuffbox" ><!-- save box -->
					<h3><span class='hndle'><?php _e('Status', 'fvcn'); ?></span></h3>
					<div class="inside">
						<div class="submitbox" id="submitcomment">
							<div id="minor-publishing">
								<div id="minor-publishing-actions">
									<div id="preview-action">
										<a class="preview button" href="<?php echo esc_url(get_option('siteurl') . '/wp-admin/admin.php?page=fvcn-admin'); ?>"><?php _e('Back', 'fvcn'); ?></a>
									</div>
									<div class="clear"></div>
								</div>
								
								<div id="misc-publishing-actions">
									<div class="misc-pub-section" id="comment-status-radio">
										<label class="approved">
											<input type="radio"<?php checked($submission->approved, '1'); ?> name="fvcn-submission-approved" value="1" /><?php _e('Approved', 'fvcn') ?>
										</label><br />
										<label class="waiting">
											<input type="radio"<?php checked($submission->approved, '0'); ?> name="fvcn-submission-approved" value="0" /><?php _e('Pending', 'fvcn'); ?>
										</label><br />
										<label class="spam">
											<input type="radio"<?php checked($submission->approved, 'spam'); ?> name="fvcn-submission-approved" value="spam" /><?php _e('Spam', 'fvcn'); ?>
										</label>
									</div>
									
									<div class="misc-pub-section curtime misc-pub-section-last">
										<span id="timestamp"><?php printf(__('Submitted on: <b>%1$s</b>', 'fvcn'), date_i18n(__('M j, Y @ G:i', 'fvcn'), strtotime($submission->date))); ?></span>
									</div>
								</div>
								<div class="clear"></div>
							</div>
							
							<div id="major-publishing-actions">
								<div id="delete-action">
									<a class="submitdelete deletion" href=""><?php _e('Delete', 'fvcn'); ?></a>
								</div>
								<div id="publishing-action">
									<input type="submit" name="save" value="<?php esc_attr_e('Save', 'fvcn'); ?>" tabindex="4" class="button-primary" />
								</div>
								<div class="clear"></div>
							</div>
						</div>
					</div>
				</div><!-- /save box -->
			</div>
			
			<div id="post-body">
				<div id="post-body-content">
					<div id="namediv" class="stuffbox">
						<h3><label for="name"><?php _e('Details', 'fvcn'); ?></label></h3>
						<div class="inside">
							<table class="form-table editcomment">
								<tbody>
									<tr valign="top">
										<td class="first"><?php _e('Author:', 'fvcn'); ?></td>
										<td><input type="text" name="fvcn-submission-user" size="30" value="<?php echo esc_attr($submission->user); ?>" tabindex="1" id="name" /></td>
									</tr>
									<tr valign="top">
										<td class="first"><?php _e('E-mail:', 'fvcn'); ?></td>
										<td><input type="text" name="fvcn-submission-email" size="30" value="<?php echo esc_attr($submission->email); ?>" tabindex="2" id="email" /></td>
									</tr>
									<tr valign="top">
										<td class="first"><?php _e('Title:', 'fvcn'); ?></td>
										<td><input type="text" name="fvcn-submission-title" size="30" value="<?php echo esc_attr($submission->title); ?>" tabindex="3" id="title" /></td>
									</tr>
									<tr valign="top">
										<td class="first">
										<?php
											if ('' != $submission->location && 'http://' != $submission->location) {
												$link = '<a href="' . $submission->location . '" rel="external nofollow" target="_blank">' . __('visit site', 'fvcn') . '</a>';
												printf(__('URL (%s):', 'fvcn'), apply_filters('get_comment_author_link', $link));
											} else {
												_e('URL:');
											}
										?>
										</td>
										<td><input type="text" id="fvcn-submission-location" name="fvcn-submission-location" size="30" class="code" value="<?php echo esc_attr($submission->location); ?>" tabindex="4" /></td>
									</tr>
								</tbody>
							</table>
							<br />
						</div>
					</div>
					
					<div id="postdiv" class="postarea">
						<?php
						add_filter('user_can_richedit', create_function ('$a', 'return false;') , 50);	// Disable visual editor
						the_editor($submission->description, 'content', 'newcomment_author_url', false, 5);
						?>
					</div>
				</div>
			</div>
		</div>
	</form>
	<script type="text/javascript">
		try {
			document.getElementById('name').focus();
		} catch (e) { }
	</script>
	
	<?php else : ?>
	
	<div id="moderated" class="error"><p><?php _e('Invallid submission selected', 'fvcn'); ?></p></div>
	<p><a href="<?php echo esc_url(get_option('siteurl') . '/wp-admin/admin.php?page=fvcn-admin'); ?>"><?php _e('&laquo; Go Back', 'fvcn'); ?></a></p>
	
	<?php endif; ?>
	
</div>