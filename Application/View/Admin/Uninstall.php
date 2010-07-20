<div class="wrap">
	<h2><?php _e('Community News Uninstall', 'fvcn'); ?></h2>
	
	<?php if ($this->errorMessage) : ?><div id="moderated" class="error"><p><?php echo $this->errorMessage; ?></p></div><?php endif; ?>
	
	<form method="post" action="admin.php?page=fvcn-admin-uninstall">
		<?php wp_nonce_field('fvcn_AdminUninstall'); ?>
			<input type="hidden" name="fvcn_Admin_Request" id="fvcn_Admin_Request" value="Uninstall" />
		
		<p><?php _e('After the uninstall, you should manually remove the plugin files.', 'fvcn'); ?></p>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e('Remove Settings', 'fvcn'); ?></th>
				<td><fieldset>
						<legend class="hidden"><?php _e('Remove Settings', 'fvcn'); ?></legend>
						<label for="fvcn_RemoveSettings">
							<input type="checkbox" name="fvcn_RemoveSettings" id="fvcn_RemoveSettings" value="1" checked="checked" />
							<span class="setting-description"><?php _e('Remove the plugin settings.', 'fvcn'); ?></span></label>
						<br />
					</fieldset></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Remove Data', 'fvcn'); ?></th>
				<td><fieldset>
						<legend class="hidden"><?php _e('Remove Data', 'fvcn'); ?></legend>
						<label for="fvcn_RemoveData">
							<input type="checkbox" name="fvcn_RemoveData" id="fvcn_RemoveData" value="1" />
							<span class="setting-description"><?php _e('Remove the plugin data (Submissions).', 'fvcn'); ?></span></label>
						<br />
					</fieldset></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="fvcn_ConfirmUninstall"><?php _e('Confirm', 'fvcn'); ?></label></th>
				<td><input type="text" name="fvcn_ConfirmUninstall" id="fvcn_ConfirmUninstall" value="" />
				<?php $code = mt_rand(111, 999); ?>
				<span class="setting-description"><strong><?php _e('Code:', 'fvcn'); echo ' ' . $code; ?></strong>
				<?php _e('Please type the code to confirm your uninstall.', 'fvcn'); ?></span>
				<input type="hidden" name="fvcn_ConfirmCode" value="<?php echo $code; ?>" /></td>
			</tr>
		</table>
		<p class="submit">
			<input type="submit" class="button-primary" name="Submit" value="<?php _e('Uninstall', 'fvcn'); ?>" />
		</p>
	</form>
</div>
