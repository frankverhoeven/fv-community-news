<?php if (!$this->submissions) : ?>

<p><?php _e('No submissions found.', 'fvcn'); ?></p>

<?php else : ?>

<div class="fvcn_SubmissionsArchive">
	<div class="fvcn_PageLinks">
		<?php echo $this->pageLinks; ?>
	</div>
	
	<?php $this->render('ListSubmissions'); ?>
	
	<div class="fvcn_PageLinks">
		<?php echo $this->pageLinks; ?>
	</div>
</div>

<?php endif; ?>