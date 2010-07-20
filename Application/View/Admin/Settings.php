<?php
$settings = $this->settings;

?>
<div class="wrap">
	<h2><?php _e('Community News Settings', 'fvcn'); ?></h2>
	
	
	<div id="fvcn-tabs-container">
		<ul class="subsubsub">
			<li><a href="#fvcn-general" class="current"><?php _e('General', 'fvcn'); ?></a> |</li>
			<li><a href="#fvcn-antispam"><?php _e('Spam Protection', 'fvcn'); ?></a> |</li>
			<li><a href="#fvcn-template"><?php _e('Template', 'fvcn'); ?></a> |</li>
			<li><a href="#fvcn-rss"><?php _e('RSS', 'fvcn'); ?></a> |</li>
			<li><a href="#fvcn-appearance"><?php _e('Appearance', 'fvcn'); ?></a></li>
		</ul>
		<br class="clear" />
		
		<form name="fvcn-settings" action="admin.php?page=fvcn-admin-settings" method="post">
			<?php wp_nonce_field('fvcn_AdminSettings'); ?>
			<input type="hidden" name="fvcn_Admin_Request" id="fvcn_Admin_Request" value="Settings" />
			
			<div id="fvcn-general" class="fvcn-tab-content current">
				<h3><?php _e('General Settings', 'fvcn'); ?></h3>
				<p><?php _e('General Settings', 'fvcn'); ?></p>
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php _e('Before a submission appears', 'fvcn'); ?></th>
						<td><fieldset>
								<legend class="hidden"><?php _e('Before a submission appears', 'fvcn'); ?></legend>
								<label for="fvcn_AlwaysAdmin">
									<input type="checkbox" name="fvcn_AlwaysAdmin" id="fvcn_AlwaysAdmin" value="1"<?php checked($settings->get('AlwaysAdmin'), true); ?> />
									<span class="description"><?php _e('An administrator must always approve the submission.', 'fvcn'); ?></span></label>
								<br />
								<label for="fvcn_PreviousApproved">
									<input type="checkbox" name="fvcn_PreviousApproved" id="fvcn_PreviousApproved" value="1"<?php checked($settings->get('PreviousApproved'), true); ?> />
									<span class="description"><?php _e('Submission author must have a previously approved submission.', 'fvcn'); ?></span></</label>
							</fieldset></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('E-mail me whenever', 'fvcn'); ?></th>
						<td><fieldset>
								<legend class="hidden"><?php _e('E-mail me whenever', 'fvcn'); ?></legend>
								<label for="fvcn_MailOnSubmission">
									<input type="checkbox" name="fvcn_MailOnSubmission" id="fvcn_MailOnSubmission" value="1"<?php checked($settings->get('MailOnSubmission'), true); ?> />
									<span class="description"><?php _e('Anyone posts a submission.', 'fvcn'); ?></span></label>
								<br />
								<label for="fvcn_MailOnModeration">
									<input type="checkbox" name="fvcn_MailOnModeration" id="fvcn_MailOnModeration" value="1"<?php checked($settings->get('MailOnModeration'), true); ?> />
									<span class="description"><?php _e('A submission is held for moderation.', 'fvcn'); ?></span></label>
							</fieldset></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('My Submissions', 'fvcn'); ?></th>
						<td><fieldset>
								<legend class="hidden"><?php _e('My Submissions', 'fvcn'); ?></legend>
								<label for="fvcn_MySubmissions">
									<input type="checkbox" name="fvcn_MySubmissions" id="fvcn_MySubmissions" value="1"<?php checked($settings->get('MySubmissions'), true); ?> />
									<span class="description"><?php _e('Add a `My Submissions` page where registered users could view and add their submissions.', 'fvcn'); ?></span></label>
							</fieldset></td>
					</tr>
				</table>
			</div>
			<div id="fvcn-antispam" class="fvcn-tab-content">
				<h3><?php _e('Spam Protection', 'fvcn'); ?></h3>
				<p><?php _e('Get rid of those spammers.', 'fvcn'); ?></p>
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php _e('Akismet', 'fvcn'); ?></th>
						<td><fieldset>
								<legend class="hidden"><?php _e('Akismet', 'fvcn'); ?></legend>
								<label for="fvcn_AkismetEnabled">
									<input type="checkbox" name="fvcn_AkismetEnabled" id="fvcn_AkismetEnabled" value="1"<?php checked($settings->get('AkismetEnabled'), true); ?> />
									<span class="description"><?php _e('Enable Akismet spam protection.', 'fvcn'); ?></span></label>
								<br />
							</fieldset></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="fvcn_AkismetApiKey"><?php _e('WordPress.com API Key', 'fvcn'); ?></label></th>
						<td><input type="text" name="fvcn_AkismetApiKey" id="fvcn_AkismetApiKey" value="<?php echo $settings->get('AkismetApiKey'); ?>" class="code" /> <span class="description"><a href="http://wordpress.com/api-keys/" target="_blank"><?php _e('Get a key', 'fvcn'); ?></a> (<a href="http://faq.wordpress.com/2005/10/19/api-key/" target="_blank"><?php _e('What is this?', 'fvcn'); ?></a>)</span></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Authentication', 'fvcn'); ?></th>
						<td><fieldset>
								<legend class="hidden"><?php _e('Authentication', 'fvcn'); ?></legend>
								<label for="fvcn_LoggedIn">
									<input type="checkbox" name="fvcn_LoggedIn" id="fvcn_LoggedIn" value="1"<?php checked($settings->get('LoggedIn'), true); ?> />
									<span class="description"><?php _e('Submission author must be logged in.', 'fvcn'); ?></span></label>
								<br />
							</fieldset></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Enable Captcha', 'fvcn'); ?></th>
						<td><fieldset>
								<legend class="hidden"><?php _e('Enable a Captcha Image', 'fvcn'); ?></legend>
								<label for="fvcn_CaptchaEnabled">
									<input type="checkbox" name="fvcn_CaptchaEnabled" id="fvcn_CaptchaEnabled" value="1"<?php checked($settings->get('CaptchaEnabled'), true); ?> />
									<span class="description"><?php _e('Enable the use of a captcha.', 'fvcn'); ?></span></label>
								<br />
								<label for="fvcn_HideCaptchaLoggedIn">
									<input type="checkbox" name="fvcn_HideCaptchaLoggedIn" id="fvcn_HideCaptchaLoggedIn" value="1"<?php checked($settings->get('HideCaptchaLoggedIn'), true); ?> />
									<span class="description"><?php _e('Remove captcha for users who are already logged in.', 'fvcn'); ?></span></label>
								<br />
							</fieldset></td>
					</tr>
				</table>
			</div>
			<div id="fvcn-template" class="fvcn-tab-content">
				<h3><?php _e('Template', 'fvcn'); ?></h3>
				<p><?php _e('These settings could be overwritten with values in your template tags.', 'fvcn'); ?></p>
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><label for="fvcn_NumSubmissions"><?php _e('Number of Submissions', 'fvcn'); ?></label></th>
						<td><input type="text" name="fvcn_NumSubmissions" id="fvcn_NumSubmissions" value="<?php echo $settings->get('NumSubmissions'); ?>" size="2" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Submission Template', 'fvcn'); ?></th>
						<td><fieldset>
								<legend class="hidden"><?php _e('Submission Template', 'fvcn'); ?></legend>
								<p>
									<label for="fvcn_SubmissionTemplate"><span class="description"><?php _e('The template for a single submission.<br />You can use the following tags: <strong>%submission_author%, %submission_author_email%, %submission_title%, %submission_url%, %submission_description%, %submission_date%, %submission_image%</strong>.', 'fvcn'); ?></span></label>
								</p>
								<p>
									<textarea name="fvcn_SubmissionTemplate" id="fvcn_SubmissionTemplate" cols="60" rows="10" style="width: 98%; font-size: 12px;" class="code"><?php echo stripslashes($settings->get('SubmissionTemplate')); ?></textarea>
							</p>
							</fieldset></td>
					</tr>
				</table>
			</div>
			<div id="fvcn-rss" class="fvcn-tab-content">
				<h3><?php _e('RSS', 'fvcn'); ?></h3>
				<p><?php _e('Configure your Community News RSS 2.0 feed.', 'fvcn'); ?></p>
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php _e('Enable RSS Feed', 'fvcn'); ?></th>
						<td><fieldset>
								<legend class="hidden"><?php _e('Enable the RSS Feed', 'fvcn'); ?></legend>
								<label for="fvcn_RssEnabled">
									<input type="checkbox" name="fvcn_RssEnabled" id="fvcn_RssEnabled" value="1"<?php checked($settings->get('RssEnabled'), true); ?> />
									<span class="description"><?php _e('Enable the RSS 2.0 Feed.', 'fvcn'); ?></span></label>
								<br />
							</fieldset></td>
					</tr>
				</table>
			</div>
			<div id="fvcn-appearance" class="fvcn-tab-content">
				<h3><?php _e('Appearance', 'fvcn'); ?></h3>
				<p><?php _e('Change the look of the plugin.', 'fvcn'); ?></p>
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php _e('Include StyleSheet', 'fvcn'); ?></th>
						<td><fieldset>
								<legend class="hidden"><?php _e('Include StyleSheet', 'fvcn'); ?></legend>
								<label for="fvcn_IncStyle">
									<input type="checkbox" name="fvcn_IncStyle" id="fvcn_IncStyle" value="1"<?php checked($settings->get('IncStyle'), true); ?> />
									<span class="description"><?php _e('Include a simple stylesheet to change the look of this plugin.', 'fvcn'); ?></span></label>
								<br />
							</fieldset></td>
					</tr>
				</table>
			</div>
			<p class="submit">
				<input type="submit" class="button-primary" name="Submit" value="<?php _e('Save Changes', 'fvcn'); ?>" />
			</p>
		</form>
	</div>
</div>
