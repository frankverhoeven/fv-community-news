<p>
	<label for="<?php echo $this->wpWidget->get_field_id('fvcn_SubmissionsTitle'); ?>"><?php _e('Title', 'fvcn'); ?></label>
	<input type="text" id="<?php echo $this->wpWidget->get_field_id('fvcn_SubmissionsTitle'); ?>" name="<?php echo $this->wpWidget->get_field_name('fvcn_SubmissionsTitle'); ?>" value="<?php echo esc_attr($this->instance['fvcn_SubmissionsTitle']); ?>" class="widefat" />
</p>
<p>
	<label for="<?php echo $this->wpWidget->get_field_id('fvcn_NumSubmissionsWidget'); ?>"><?php _e('Number of submissions', 'fvcn'); ?></label>
	<input type="text" id="<?php echo $this->wpWidget->get_field_id('fvcn_NumSubmissionsWidget'); ?>" name="<?php echo $this->wpWidget->get_field_name('fvcn_NumSubmissionsWidget'); ?>" value="<?php echo esc_attr($this->instance['fvcn_NumSubmissionsWidget']); ?>" class="widefat" />
</p>
<p>
	<label for="<?php echo $this->wpWidget->get_field_id('fvcn_SubmissionTemplateWidget'); ?>"><?php _e('Template', 'fvcn'); ?></label>
	<textarea id="<?php echo $this->wpWidget->get_field_id('fvcn_SubmissionTemplateWidget'); ?>" name="<?php echo $this->wpWidget->get_field_name('fvcn_SubmissionTemplateWidget'); ?>" rows="7" cols="20" class="widefat"><?php echo $this->instance['fvcn_SubmissionTemplateWidget']; ?></textarea>
</p>
