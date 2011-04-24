<?php

$form = FvCommunityNews_Registry::getInstance()->forms['settings'];

?>

<div class="wrap">
	<h2><?php _e('Community News Settings', 'fvcn'); ?></h2>
	
	<?php if ($form->hasMessage()) : ?>
	
	<div id="fvcn-settings-updated" class="updated">
		<p><strong><?php echo $form->getMessage(); ?></strong></p>
	</div>
	
	<?php endif; ?>
	
	<div id="fvcn-tabs-container">
		<ul class="subsubsub">
			<li><a href="#fvcn-general" class="current"><?php _e('General', 'fvcn'); ?></a> |</li>
			<li><a href="#fvcn-antispam"><?php _e('Spam Protection', 'fvcn'); ?></a> |</li>
			<li><a href="#fvcn-appearance"><?php _e('Appearance', 'fvcn'); ?></a></li>
		</ul>
		<br class="clear" />
		
		<?php echo $form->render(); ?>
		
	</div><!--/#fvcn-tabs-container-->
</div><!--/.wrap-->
