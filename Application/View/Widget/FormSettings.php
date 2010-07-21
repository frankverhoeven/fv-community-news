<p>
	<label for="<?php echo $this->wpWidget->get_field_id('fvcn_FormTitle'); ?>"><?php _e('Title', 'fvcn'); ?></label>
	<input type="text" id="<?php echo $this->wpWidget->get_field_id('fvcn_FormTitle'); ?>" name="<?php echo $this->wpWidget->get_field_name('fvcn_FormTitle'); ?>" value="<?php echo esc_attr($this->instance['fvcn_FormTitle']); ?>" class="widefat" />
</p>
<p>
	<label for="<?php echo $this->wpWidget->get_field_id('fvcn_FormDescription'); ?>"><?php _e('Description', 'fvcn'); ?></label>
	<textarea id="<?php echo $this->wpWidget->get_field_id('fvcn_FormDescription'); ?>" name="<?php echo $this->wpWidget->get_field_name('fvcn_FormDescription'); ?>" rows="7" cols="20" class="widefat"><?php echo $this->instance['fvcn_FormDescription']; ?></textarea>
</p>
