<?php

$form = FvCommunityNews_Registry::getInstance()->forms['uninstall'];

?>

<div class="wrap">
	<h2><?php _e('Community News Uninstall', 'fvcn'); ?></h2>
	
	<?php if ($form->hasMessage()) : ?>
	
	<div id="fvcn-settings-updated" class="updated">
		<p><strong><?php echo $form->getMessage(); ?></strong></p>
	</div>
	
	<?php endif; ?>
	
	<?php if ($form->isProcessed()) : ?>
		
		<p><?php _e('The plugin successfull uninstalled itself, now go to <a href="./plugins.php">the plugin admin panel</a> to delete it.', 'fvcn'); ?></p>
		
	<?php else : ?>
		
		<?php echo $form->render(); ?>
		
	<?php endif; ?>
</div><!--/.wrap-->
