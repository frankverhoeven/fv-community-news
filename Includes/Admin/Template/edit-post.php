<?php

$form = FvCommunityNews_Registry::getInstance()->forms['edit-post'];

?>

<div class="wrap">
	<h2><?php _e('Edit Post', 'fvcn'); ?></h2>
	
	<?php if ($form->hasPost()) : ?>
	
	
	<?php if ($form->hasMessage()) : ?>
		<div id="moderated" class="updated"><p><?php echo $form->getMessage(); ?></p></div>
	<?php endif; ?>
	
	<form name="<?php echo $form->getName(); ?>" method="post" action="<?php fvcn_post_edit_link(); ?>">
		<?php
		echo $form->getElement('fvcn')->render();
		echo $form->getElement('fvcn-nonce-edit-post')->render();
		?>
		
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
											<input type="radio"<?php checked($form->getElement('fvcn-approved')->getValue(), '1'); ?> name="fvcn-approved" value="1" /><?php _e('Approved', 'fvcn') ?>
										</label><br />
										<label class="waiting">
											<input type="radio"<?php checked($form->getElement('fvcn-approved')->getValue(), '0'); ?> name="fvcn-approved" value="0" /><?php _e('Pending', 'fvcn'); ?>
										</label><br />
										<label class="spam">
											<input type="radio"<?php checked($form->getElement('fvcn-approved')->getValue(), 'spam'); ?> name="fvcn-approved" value="spam" /><?php _e('Spam', 'fvcn'); ?>
										</label>
										<?php if ($form->getElement('fvcn-approved')->hasErrors()) echo $form->getElement('fvcn-approved')->renderErrors(); ?>
									</div>
									
									<div class="misc-pub-section curtime misc-pub-section-last">
										<span id="timestamp"><?php printf(__('Submitted on: <b>%1$s</b>', 'fvcn'), date_i18n(__('M j, Y @ G:i', 'fvcn'), strtotime(fvcn_get_post_date()))); ?></span>
									</div>
								</div>
								<div class="clear"></div>
							</div>
							
							<div id="major-publishing-actions">
								<div id="delete-action">
									<a class="submitdelete deletion" href="<?php fvcn_post_delete_link(); ?>"><?php _e('Delete', 'fvcn'); ?></a>
								</div>
								<div id="publishing-action">
									<input type="submit" name="fvcn-submit" value="<?php esc_attr_e('Save', 'fvcn'); ?>" tabindex="4" class="button-primary" />
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
										<td>
											<input type="text" name="fvcn-author" size="30" value="<?php echo esc_attr($form->getElement('fvcn-author')->getValue()); ?>" tabindex="1" id="name" />
											<?php if ($form->getElement('fvcn-author')->hasErrors()) echo $form->getElement('fvcn-author')->renderErrors(); ?>
										</td>
									</tr>
									<tr valign="top">
										<td class="first"><?php _e('E-mail:', 'fvcn'); ?></td>
										<td>
											<input type="text" name="fvcn-author-email" size="30" value="<?php echo esc_attr($form->getElement('fvcn-author-email')->getValue()); ?>" tabindex="2" id="email" />	
											<?php if ($form->getElement('fvcn-author-email')->hasErrors()) echo $form->getElement('fvcn-author-email')->renderErrors(); ?>
										</td>
									</tr>
									<tr valign="top">
										<td class="first"><?php _e('Title:', 'fvcn'); ?></td>
										<td>
											<input type="text" name="fvcn-title" size="30" value="<?php echo esc_attr($form->getElement('fvcn-title')->getValue()); ?>" tabindex="3" id="title" />
											<?php if ($form->getElement('fvcn-title')->hasErrors()) echo $form->getElement('fvcn-title')->renderErrors(); ?>
										</td>
									</tr>
									<tr valign="top">
										<td class="first">
										<?php
											if ('' != $form->getElement('fvcn-url')->getValue() && 'http://' != $form->getElement('fvcn-url')->getValue()) {
												$link = '<a href="' . $form->getElement('fvcn-url')->getValue() . '" rel="external nofollow" target="_blank">' . __('visit site', 'fvcn') . '</a>';
												printf(__('URL (%s):', 'fvcn'), apply_filters('get_comment_author_link', $link));
											} else {
												_e('URL:');
											}
										?>
										</td>
										<td>
											<input type="text" name="fvcn-url" size="30" class="code" value="<?php echo esc_attr($form->getElement('fvcn-url')->getValue()); ?>" tabindex="4" />
											<?php if ($form->getElement('fvcn-url')->hasErrors()) echo $form->getElement('fvcn-url')->renderErrors(); ?>
										</td>
									</tr>
								</tbody>
							</table>
							<br />
						</div>
					</div>
					
					<div id="postdiv" class="postarea">
						<?php
						add_filter('user_can_richedit', '__return_false' , 50);	// Disable visual editor
						the_editor($form->getElement('content')->getValue(), 'content', 'fvcn-url');
						
						if ($form->getElement('content')->hasErrors()) echo $form->getElement('content')->renderErrors();
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
	
	<div id="moderated" class="error"><p><?php _e('Invallid post selected', 'fvcn'); ?></p></div>
	<p><a href="<?php echo esc_url(get_option('siteurl') . '/wp-admin/admin.php?page=fvcn-admin'); ?>"><?php _e('&laquo; Go Back', 'fvcn'); ?></a></p>
	
	<?php endif; ?>
	
</div>
