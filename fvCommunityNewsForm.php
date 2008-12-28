<?php
/**
 * @package 	FV Community News
 * @version 	1.3
 * @author 		Frank Verhoeven
 * @copyright 	Coyright (c) 2008, Frank Verhoeven
 */

$errorFields = fvCommunityNewsGetErrorFields();

if (get_option('fvcn_loggedIn') && !is_user_logged_in()) : ?>
	
	<!-- Submission author must be logged in. //-->
	<p><?php echo get_option('fvcn_responseLoggedIn') ?></p>
	
<?php elseif (fvCommunityNewsSubmitted()) : ?>
	
	<?php if (fvCommunityNewsAwaitingModeration()) : ?>
	
	<!-- A new submission has been added and is awaiting moderation. //-->
	<p><?php echo get_option('fvcn_responseModeration') ?></p>
	
	<?php else : ?>
	
	<!-- A new submission has been added and is approved. //-->
	<p><?php echo get_option('fvcn_responseSuccess') ?></p>
	
	<?php endif ?>
	
<?php else : ?>

<!-- No submission has been submited, or errors occured. //-->
<form action="" method="post" name="fvCommunityNewsForm" id="fvCommunityNewsForm" enctype="multipart/form-data">
	<label for="fvCommunityNewsName"><?php _e('Name', 'fvcn') ?> <em title="<?php _e('Required for valid form validation.', 'fvcn') ?>">*</em></label>
	<input type="text" name="fvCommunityNewsName" id="fvCommunityNewsName" value="<?php echo fvCommunityNewsGetValue('fvCommunityNewsName') ?>" class="<?php if (in_array('fvCommunityNewsName', $errorFields)) echo 'error' ?>" /><br />
	
	<label for="fvCommunityNewsEmail"><?php _e('Email', 'fvcn') ?> <em title="<?php _e('Required for valid form validation.', 'fvcn') ?>">*</em></label>
	<input type="text" name="fvCommunityNewsEmail" id="fvCommunityNewsEmail" value="<?php echo fvCommunityNewsGetValue('fvCommunityNewsEmail') ?>" class="<?php if (in_array('fvCommunityNewsEmail', $errorFields)) echo 'error' ?>" /><br />
	
	<label for="fvCommunityNewsTitle"><?php _e('Post Title', 'fvcn') ?> <em title="<?php _e('Required for valid form validation.', 'fvcn') ?>">*</em></label>
	<input type="text" name="fvCommunityNewsTitle" id="fvCommunityNewsTitle" value="<?php echo fvCommunityNewsGetValue('fvCommunityNewsTitle') ?>" class="<?php if (in_array('fvCommunityNewsTitle', $errorFields)) echo 'error' ?>" /><br />
	
	<label for="fvCommunityNewsLocation"><?php _e('Post URL', 'fvcn') ?></label>
	<input type="text" name="fvCommunityNewsLocation" id="fvCommunityNewsLocation" value="<?php echo fvCommunityNewsGetValue('fvCommunityNewsLocation') ?>" class="<?php if (in_array('fvCommunityNewsLocation', $errorFields)) echo 'error' ?>" /><br />
	
	<?php if (fvCommunityNewsCaptcha()) : ?>
	
	<label for="fvCommunityNewsCaptcha"><?php _e('Captcha', 'fvcn') ?> <em title="<?php _e('Required for valid form validation.', 'fvcn') ?>">*</em></label>
	<img src="<?php echo get_option('home') ?>/?fvCommunityNewsCaptcha=true" id="fvCommunityNewsCaptchaImage" alt="Captcha" />
	<script type="text/javascript">
		document.write('<br /><small><a href="#" onclick="fvCommunityNewsReloadCaptcha(); return false;"><?php _e('Give me an other image', 'fvcn') ?></a></small><img src="<?php echo WP_PLUGIN_URL ?>/fv-community-news/images/loading-small.gif" id="fvCommunityNewsCaptchaLoader" style="display:none;margin-left:2px" />');
	</script>
	<br /><?php _e('To prevent spam, please type the text (all <strong>uppercase</strong>) from this image in the textbox below.', 'fvcn') ?><br />
	<input type="text" name="fvCommunityNewsCaptcha" id="fvCommunityNewsCaptcha" value="" class="<?php if (in_array('fvCommunityNewsCaptcha', $errorFields)) echo 'error' ?>" />
	
	<?php
	endif;
	if (get_option('fvcn_uploadImage')) :
	?>
	
	<label for="fvCommunityNewsImageCheck"><?php _e('Image', 'fvcn') ?></label>
	<input type="checkbox" name="fvCommunityNewsImageCheck" id="fvCommunityNewsImageCheck" style="width: auto" />
	<input type="file" name="fvCommunityNewsImage" id="fvCommunityNewsImage" value="" class="<?php if (in_array('fvCommunityNewsImage', $errorFields)) echo 'error' ?>" onchange="document.getElementById('fvCommunityNewsImageCheck').checked='true';" style="width: auto;" />
	<input type="hidden" name="max_file_size" id="max_file_size" value="2048000" />
	
	<?php endif ?>
	
	<label for="fvCommunityNewsDescription"><?php _e('Description', 'fvcn') ?> <em title="<?php _e('Required for valid form validation.', 'fvcn') ?>">*</em></label>
	<textarea name="fvCommunityNewsDescription" id="fvCommunityNewsDescription" class="<?php if (in_array('fvCommunityNewsDescription', $errorFields)) echo 'error' ?>"><?php echo fvCommunityNewsGetValue('fvCommunityNewsDescription') ?></textarea><br />
	
	<input type="hidden" name="fvCommunityNews" id="fvCommunityNews" value="<?php echo get_option('home') ?>/" />
	<?php wp_nonce_field('fvCommunityNews_addSubmission') ?>
	
	<div style="display: none;">
		<label for="fvCommunityNewsPhone"><?php _e('Phone Number', 'fvcn') ?> <em title="<?php _e('Required for valid form validation.', 'fvcn') ?>">*</em></label>
		<input type="text" name="fvCommunityNewsPhone" id="fvCommunityNewsPhone" value="" />
	</div>
	
	<span id="fvCommunityNewsErrorResponse"><?php echo fvCommunityNewsSubmitError() ?></span>
	<input type="submit" name="fvCommunityNewsSubmit" id="fvCommunityNewsSubmit" value="<?php _e('Submit News', 'fvcn') ?>" />
</form>

<div id="fvCommunityNewsAjaxResponse" style="display: none;"></div>

<div id="fvCommunityNewsLoader" style="display: none;">
	<p><img src="<?php echo WP_PLUGIN_URL ?>/fv-community-news/images/loading.gif" alt="" style="margin-right: 3px;" /><?php _e('Loading', 'fvcn') ?>...</p>
</div>

<?php endif ?>