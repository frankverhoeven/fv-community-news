<?php if ($this->mustBeLoggedIn && !is_user_logged_in()) : ?>

<p><?php _e('You must be logged in to add a submission.', 'fvcn'); ?></p>

<?php elseif ($this->submissionAdded) : ?>

<p><?php _e('Your submission has been added, thank you!', 'fvcn'); ?>

<?php else : ?>

<form name="fvcn-add-submission" id="fvcn-add-submission" action="" method="post">
		
	<p>
		<label for="fvcn_User"><?php _e('Name', 'fvcn'); ?><sup title="<?php _e('Required for valid form validation.', 'fvcn'); ?>">*</sup></label><br />
		<input type="text" name="fvcn_User" id="fvcn_User" value="<?php echo esc_attr( $this->form->getElement('fvcn_User')->getValue() ); ?>" />
		<?php echo $this->form->getElement('fvcn_User')->getMessage(); ?>
	</p>
	<p>
		<label for="fvcn_Email"><?php _e('Email', 'fvcn'); ?><sup title="<?php _e('Required for valid form validation.', 'fvcn'); ?>">*</sup></label><br />
		<input type="text" name="fvcn_Email" id="fvcn_Email" value="<?php echo esc_attr( $this->form->getElement('fvcn_Email')->getValue() ); ?>" />
		<?php echo $this->form->getElement('fvcn_Email')->getMessage(); ?>
	</p>
	<p>
		<label for="fvcn_Title"><?php _e('Title', 'fvcn'); ?><sup title="<?php _e('Required for valid form validation.', 'fvcn'); ?>">*</sup></label><br />
		<input type="text" name="fvcn_Title" id="fvcn_Title" value="<?php echo esc_attr( $this->form->getElement('fvcn_Title')->getValue() ); ?>" />
		<?php echo $this->form->getElement('fvcn_Title')->getMessage(); ?>
	</p>
	<p>
		<label for="fvcn_Location"><?php _e('URL', 'fvcn'); ?></label><br />
		<input type="text" name="fvcn_Location" id="fvcn_Location" value="<?php echo esc_attr( $this->form->getElement('fvcn_Location')->getValue() ); ?>" />
	</p>
	
	<?php if ($this->captcha) : ?>
	
	<p>
		<label for="fvcn_Captcha"><?php _e('Captcha', 'fvcn'); ?><sup title="<?php _e('Required for valid form validation.', 'fvcn'); ?>">*</sup></label><br />
		<img src="<?php echo get_option('home'); ?>/?fvcn-captcha=true" id="fvcn_CaptchaImage" alt="Captcha" /><br />
		<input type="text" name="fvcn_Captcha" id="fvcn_Captcha" value="" />
		<?php echo $this->form->getElement('fvcn_Captcha')->getMessage(); ?>
	</p>
	
	<?php endif; ?>
	
	<p>
		<label for="fvcn_Description"><?php _e('Description', 'fvcn'); ?><sup title="<?php _e('Required for valid form validation.', 'fvcn'); ?>">*</sup></label><br />
		<textarea name="fvcn_Description" id="fvcn_Description" cols="20" rows="3"><?php echo esc_attr( $this->form->getElement('fvcn_Description')->getValue() ); ?></textarea>
		<?php echo $this->form->getElement('fvcn_Description')->getMessage(); ?>
	</p>
	
		<input type="hidden" name="fvcn" id="fvcn" value="<?php echo get_option('home'); ?>/" />
		<input type="hidden" name="fvcn-action" id="fvcn-action" value="AddSubmission" />
		<?php wp_nonce_field('fvcn_AddSubmissionNonce', 'fvcn_AddSubmissionNonce'); ?>
	
	<div style="display: none !important;">
		<p>
			<label for="fvcn_Phone"><?php _e('Phone Number', 'fvcn'); ?><sup title="<?php _e('Required for valid form validation.', 'fvcn'); ?>">*</sup></label><br />
			<input type="text" name="fvcn_Phone" id="fvcn_Phone" value="" />
			<?php echo $this->form->getElement('fvcn_Phone')->getMessage(); ?>
		</p>
	</div>
	
	<div id="fvcn_Response"><?php if ($this->validationError) { echo '<p>' . $this->validationError . '</p>'; } ?></div>
	<p>
		<input type="submit" name="fvcn_Submit" id="fvcn_Submit" value="<?php _e('Submit News', 'fvcn'); ?>" class="button" />
	</p>
		
</form>

<div id="fvcn_AjaxResponse" style="display: none;"></div>

<div id="fvcn_Loader" style="display: none;">
	<p><img src="<?php echo WP_PLUGIN_URL . FVCN_PLUGINDIR; ?>/public/images/loading.gif" alt="" style="margin-right: 3px;" /><?php _e('Loading', 'fvcn'); ?></p>
</div>

<?php endif; ?>
