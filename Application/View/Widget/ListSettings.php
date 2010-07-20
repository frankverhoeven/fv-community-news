<p>
	<label for="<?php echo $this->wpWidget->get_field_id('fvcn_SubmissionsTitle'); ?>"><?php _e('Title', 'fvcn'); ?></label>
	<input type="text" id="<?php echo $this->wpWidget->get_field_id('fvcn_SubmissionsTitle'); ?>" name="<?php echo $this->wpWidget->get_field_name('fvcn_SubmissionsTitle'); ?>" value="<?php echo esc_attr($this->instance['fvcn_SubmissionsTitle']); ?>" class="widefat" />
</p>
