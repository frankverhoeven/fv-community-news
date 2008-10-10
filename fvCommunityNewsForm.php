<?php if (get_option('fvcn_loggedIn') && !is_user_logged_in()) : ?>
	
	<!-- Submission author must be logged in. //-->
	<p>You must be logged in to add a submission.</p>
	
<?php elseif (fvCommunityNewsSubmitted()) : ?>
	
	<?php if (fvCommunityNewsAwaitingModeration()) : ?>
	
	<!-- A new submission has been added and is awaiting moderation. //-->
	<p>Your submission has been added to the moderation queue and will appear soon. Thank you!</p>
	
	<?php else : ?>
	
	<!-- A new submission has been added and is approved. //-->
	<p>Your submission has been added. Thank you!</p>
	
	<?php endif; ?>
	
<?php else : ?>

<!-- No submission has been submited, or errors occured. //-->
<form action="" method="post" name="fvCommunityNewsForm" id="fvCommunityNewsForm">
	<fieldset>
		<label for="fvCommunityNewsName">Name <em title="Required">*</em></label>
		<input type="text" name="fvCommunityNewsName" id="fvCommunityNewsName" value="<?php echo fvCommunityNewsGetValue('fvCommunityNewsName'); ?>" /><br />
		
		<label for="fvCommunityNewsEmail">Email <em title="Required">*</em></label>
		<input type="text" name="fvCommunityNewsEmail" id="fvCommunityNewsEmail" value="<?php echo fvCommunityNewsGetValue('fvCommunityNewsEmail'); ?>" /><br />
		
		<label for="fvCommunityNewsTitle">Post Title <em title="Required">*</em></label>
		<input type="text" name="fvCommunityNewsTitle" id="fvCommunityNewsTitle" value="<?php echo fvCommunityNewsGetValue('fvCommunityNewsTitle'); ?>" /><br />
		
		<label for="fvCommunityNewsLocation">Post URL <em title="Required">*</em></label>
		<input type="text" name="fvCommunityNewsLocation" id="fvCommunityNewsLocation" value="<?php echo fvCommunityNewsGetValue('fvCommunityNewsLocation'); ?>" /><br />
		
		<?php if (fvCommunityNewsCaptcha()) : ?>
		
		<label for="fvCommunityNewsCaptcha">Captcha <em title="Required">*</em></label>
		<img src="<?php echo get_option('home'); ?>/?fvCommunityNewsCaptcha=true" id="fvCommunityNewsCaptchaImage" alt="Captcha" />
		<script type="text/javascript">
			document.write('<br /><small><a href="javascript:;" onclick="fvCommunityNewsReloadCaptcha();">Give me an other image</a></small>');
		</script>
		<br />To prevent spam, please type the text (all <strong>uppercase</strong>) from this image in the textbox below.<br />
		<input type="text" name="fvCommunityNewsCaptcha" id="fvCommunityNewsCaptcha" value="" />
		
		<?php endif; ?>
		
		<label for="fvCommunityNewsDescription">Description <em title="Required">*</em></label>
		<textarea name="fvCommunityNewsDescription" id="fvCommunityNewsDescription"><?php echo fvCommunityNewsGetValue('fvCommunityNewsDescription'); ?></textarea><br />
		
		<input type="hidden" name="fvCommunityNews" id="fvCommunityNews" value="true" />
		<?php wp_nonce_field('fvCommunityNews_addSubmission'); ?>
		
		<div style="display: none;">
			<label for="fvCommunityNewsPhone">Phone Number <em title="Required">*</em></label>
			<input type="text" name="fvCommunityNewsPhone" id="fvCommunityNewsPhone" value="" />
		</div>
		
		<span id="fvCommunityNewsErrorResponse"><?php echo fvCommunityNewsSubmitError(); ?></span>
		<input type="submit" name="fvCommunityNewsSubmit" id="fvCommunityNewsSubmit" value="Post News" />
	</fieldset>
</form>

<div id="fvCommunityNewsAjaxResponse" style="display: none;"></div>

<div id="fvCommunityNewsLoader" style="display: none;">
	<p><img src="<?php echo get_option('home'); ?>/wp-content/plugins/fv-community-news/images/loading.gif" alt="" style="margin-right: 3px;" />Loading...</p>
</div>

<?php endif; ?>