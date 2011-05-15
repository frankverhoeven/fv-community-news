<?php echo $this->args['before_widget']; ?>
	
	<?php echo $this->args['before_title'] . $this->title . $this->args['after_title']; ?>
	
	<?php if (!fvcn_form_processed())
		fvcn_form(); ?>
	
	<p id="fvcn-form-message">
		<?php fvcn_form_message(); ?>
	</p>
	
<?php echo $this->args['after_widget']; ?>
