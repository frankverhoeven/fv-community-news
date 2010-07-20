<?php if (!$this->submissions) : ?>

<p><?php _e('No submissions found.', 'fvcn'); ?></p>

<?php else : ?>

<ul id="fvcn-submissions-list">
	
	<?php
	foreach ($this->submissions as $submission) {
		$output = $this->format;
		
		$output = str_replace('%submission_author%', apply_filters('fvcn_User', $submission->Name), $output);
		$output = str_replace('%submission_author_email%', apply_filters('fvcn_Email', $submission->Email), $output);
		$output = str_replace('%submission_title%', apply_filters('fvcn_Title', $submission->Title), $output);
		$output = str_replace('%submission_url%', apply_filters('fvcn_Location', $submission->Location), $output);
		$output = str_replace('%submission_description%', apply_filters('fvcn_Description', $submission->Description), $output);
		$output = str_replace('%submission_date%', mysql2date(get_option('date_format') . ' @ ' . get_option('time_format'), $submission->Date), $output);
		
		// Empty Links
		$output = preg_replace('/<a href=""(.*?)>(.*?)<\/a>/', '\\2', $output);
		$output = preg_replace('/<a href="http:\/\/"(.*?)>(.*?)<\/a>/', '\\2', $output);
		
		echo $output;
	}
	?>
	
</ul>

<?php endif; ?>